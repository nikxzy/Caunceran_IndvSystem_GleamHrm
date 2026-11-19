<?php

namespace App\Services;

use App\AssetDisposal;
use App\EquipmentLoan;
use App\Hub;
use App\InventoryStock;
use App\Item;
use App\Order;
use App\PurchaseOrder;
use App\StockMovement;
use App\StockTransfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class InventoryService
{
    /**
     * Get (or create) the stock row for a hub/item pair under a row lock.
     */
    public function lockStock($hubId, $itemId)
    {
        $stock = InventoryStock::where('hub_id', $hubId)
            ->where('item_id', $itemId)
            ->lockForUpdate()
            ->first();

        if (! $stock) {
            $stock = InventoryStock::create([
                'hub_id' => $hubId,
                'item_id' => $itemId,
                'quantity' => 0,
                'reserved_quantity' => 0,
            ]);

            $stock = InventoryStock::where('id', $stock->id)->lockForUpdate()->first();
        }

        return $stock;
    }

    /**
     * Receive an approved purchase order: stock is incremented immediately.
     */
    public function receivePurchaseOrder(PurchaseOrder $purchaseOrder, $employeeId = null)
    {
        if (! $purchaseOrder->isApproved()) {
            throw new RuntimeException('Only approved purchase orders can be received.');
        }

        return DB::transaction(function () use ($purchaseOrder, $employeeId) {
            $purchaseOrder->load('items.item');

            foreach ($purchaseOrder->items as $line) {
                $stock = $this->lockStock($purchaseOrder->hub_id, $line->item_id);
                $stock->quantity += $line->quantity;
                $stock->save();

                $this->logMovement(
                    $purchaseOrder->hub_id,
                    $line->item_id,
                    'purchase_receipt',
                    $line->quantity,
                    $purchaseOrder,
                    $employeeId,
                    'Received via '.$purchaseOrder->po_number
                );
            }

            $purchaseOrder->status = PurchaseOrder::STATUS_RECEIVED;
            $purchaseOrder->received_at = now();
            $purchaseOrder->save();

            return $purchaseOrder;
        });
    }

    /**
     * Reserve stock for a sales order so it cannot be oversold.
     */
    public function reserveOrder(Order $order, $employeeId = null)
    {
        if ($order->isReserved()) {
            return $order;
        }

        return DB::transaction(function () use ($order, $employeeId) {
            $order->load('items.item');

            foreach ($order->items as $line) {
                $stock = $this->lockStock($order->hub_id, $line->item_id);
                $available = $stock->quantity - $stock->reserved_quantity;

                if ($available < $line->quantity) {
                    throw ValidationException::withMessages([
                        'stock' => sprintf(
                            'Insufficient stock for %s. Available: %d, requested: %d.',
                            $line->item->name,
                            max(0, $available),
                            $line->quantity
                        ),
                    ]);
                }

                $stock->reserved_quantity += $line->quantity;
                $stock->save();

                $this->logMovement(
                    $order->hub_id,
                    $line->item_id,
                    'reservation',
                    0,
                    $order,
                    $employeeId,
                    'Reserved '.$line->quantity.' unit(s) for '.$order->order_number
                );
            }

            $order->reserved_at = now();
            $order->save();

            return $order;
        });
    }

    /**
     * Release a reservation (e.g. cancelled order).
     */
    public function releaseOrder(Order $order, $employeeId = null)
    {
        if (! $order->isReserved()) {
            return $order;
        }

        return DB::transaction(function () use ($order, $employeeId) {
            $order->load('items');

            foreach ($order->items as $line) {
                $stock = $this->lockStock($order->hub_id, $line->item_id);
                $stock->reserved_quantity = max(0, $stock->reserved_quantity - $line->quantity);
                $stock->save();

                $this->logMovement(
                    $order->hub_id,
                    $line->item_id,
                    'reservation_release',
                    0,
                    $order,
                    $employeeId,
                    'Released '.$line->quantity.' reserved unit(s) for '.$order->order_number
                );
            }

            $order->reserved_at = null;
            $order->save();

            return $order;
        });
    }

    /**
     * Physically issue reserved stock when an order leaves the hub.
     */
    public function issueOrder(Order $order, $employeeId = null)
    {
        if (! $order->isReserved()) {
            throw new RuntimeException('This order has no stock reservation to issue.');
        }

        return DB::transaction(function () use ($order, $employeeId) {
            $order->load('items.item');

            foreach ($order->items as $line) {
                $stock = $this->lockStock($order->hub_id, $line->item_id);

                if ($stock->quantity < $line->quantity) {
                    throw ValidationException::withMessages([
                        'stock' => 'Stock for '.$line->item->name.' is no longer available.',
                    ]);
                }

                $stock->quantity -= $line->quantity;
                $stock->reserved_quantity = max(0, $stock->reserved_quantity - $line->quantity);
                $stock->save();

                $this->logMovement(
                    $order->hub_id,
                    $line->item_id,
                    'sales_issue',
                    -1 * $line->quantity,
                    $order,
                    $employeeId,
                    'Dispatched on '.$order->order_number
                );
            }

            return $order;
        });
    }

    /**
     * Deduct stock from the source hub when a transfer is dispatched.
     */
    public function dispatchTransfer(StockTransfer $transfer, $employeeId = null)
    {
        if (! $transfer->isRequested()) {
            throw new RuntimeException('Only requested transfers can be dispatched.');
        }

        return DB::transaction(function () use ($transfer, $employeeId) {
            $transfer->load('items.item');

            foreach ($transfer->items as $line) {
                $stock = $this->lockStock($transfer->from_hub_id, $line->item_id);

                if ($stock->quantity - $stock->reserved_quantity < $line->quantity) {
                    throw ValidationException::withMessages([
                        'stock' => 'Insufficient stock at '.$transfer->fromHub->name.' for '.$line->item->name.'.',
                    ]);
                }

                $stock->quantity -= $line->quantity;
                $stock->save();

                $this->logMovement(
                    $transfer->from_hub_id,
                    $line->item_id,
                    'transfer_out',
                    -1 * $line->quantity,
                    $transfer,
                    $employeeId,
                    'Transfer '.$transfer->transfer_number.' to '.$transfer->toHub->name
                );
            }

            $transfer->status = StockTransfer::STATUS_IN_TRANSIT;
            $transfer->dispatched_at = now();
            $transfer->save();

            return $transfer;
        });
    }

    /**
     * Add stock to the destination hub when a transfer arrives.
     */
    public function receiveTransfer(StockTransfer $transfer, $employeeId = null)
    {
        if (! $transfer->isInTransit()) {
            throw new RuntimeException('Only in-transit transfers can be received.');
        }

        return DB::transaction(function () use ($transfer, $employeeId) {
            $transfer->load('items.item');

            foreach ($transfer->items as $line) {
                $stock = $this->lockStock($transfer->to_hub_id, $line->item_id);
                $stock->quantity += $line->quantity;
                $stock->save();

                $this->logMovement(
                    $transfer->to_hub_id,
                    $line->item_id,
                    'transfer_in',
                    $line->quantity,
                    $transfer,
                    $employeeId,
                    'Received from '.$transfer->fromHub->name.' via '.$transfer->transfer_number
                );
            }

            $transfer->status = StockTransfer::STATUS_RECEIVED;
            $transfer->received_by = $employeeId;
            $transfer->received_at = now();
            $transfer->save();

            return $transfer;
        });
    }

    /**
     * Remove stock for a write-off / disposal.
     */
    public function dispose(AssetDisposal $disposal, $employeeId = null)
    {
        return DB::transaction(function () use ($disposal, $employeeId) {
            $stock = $this->lockStock($disposal->hub_id, $disposal->item_id);

            if ($stock->quantity < $disposal->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Cannot dispose more than the stock on hand.',
                ]);
            }

            $stock->quantity -= $disposal->quantity;
            $stock->save();

            $this->logMovement(
                $disposal->hub_id,
                $disposal->item_id,
                'disposal',
                -1 * $disposal->quantity,
                $disposal,
                $employeeId,
                'Disposal '.$disposal->reference_no
            );

            return $disposal;
        });
    }

    /**
     * Move a loanable equipment unit out of the hub.
     */
    public function loanEquipment(EquipmentLoan $loan, $employeeId = null)
    {
        return DB::transaction(function () use ($loan, $employeeId) {
            $stock = $this->lockStock($loan->hub_id, $loan->equipment_id);

            if ($stock->quantity < 1) {
                throw ValidationException::withMessages([
                    'equipment_id' => 'This equipment is not available at the selected hub.',
                ]);
            }

            $stock->quantity -= 1;
            $stock->save();

            $this->logMovement(
                $loan->hub_id,
                $loan->equipment_id,
                'loan_out',
                -1,
                $loan,
                $employeeId,
                'Loaned to '.$loan->client_name
            );

            return $loan;
        });
    }

    /**
     * Return a loaned equipment unit to the hub.
     */
    public function returnEquipment(EquipmentLoan $loan, $employeeId = null)
    {
        return DB::transaction(function () use ($loan, $employeeId) {
            $stock = $this->lockStock($loan->hub_id, $loan->equipment_id);
            $stock->quantity += 1;
            $stock->save();

            $this->logMovement(
                $loan->hub_id,
                $loan->equipment_id,
                'loan_in',
                1,
                $loan,
                $employeeId,
                'Returned by '.$loan->client_name
            );

            return $loan;
        });
    }

    /**
     * Re-count stock (stock adjustment / cycle count).
     */
    public function adjust($hubId, $itemId, $newQuantity, $employeeId = null, $notes = null)
    {
        return DB::transaction(function () use ($hubId, $itemId, $newQuantity, $employeeId, $notes) {
            $stock = $this->lockStock($hubId, $itemId);
            $delta = $newQuantity - $stock->quantity;
            $stock->quantity = $newQuantity;
            $stock->save();

            if ($delta !== 0) {
                $this->logMovement(
                    $hubId,
                    $itemId,
                    'adjustment',
                    $delta,
                    null,
                    $employeeId,
                    $notes ?: 'Stock count adjustment'
                );
            }

            return $stock;
        });
    }

    public function available($hubId, $itemId)
    {
        $stock = InventoryStock::where('hub_id', $hubId)->where('item_id', $itemId)->first();

        return $stock ? max(0, $stock->quantity - $stock->reserved_quantity) : 0;
    }

    public function logMovement($hubId, $itemId, $type, $quantity, $reference = null, $employeeId = null, $notes = null)
    {
        return StockMovement::create([
            'hub_id' => $hubId,
            'item_id' => $itemId,
            'type' => $type,
            'quantity' => (int) $quantity,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference ? $reference->id : null,
            'employee_id' => $employeeId,
            'notes' => $notes,
        ]);
    }
}
