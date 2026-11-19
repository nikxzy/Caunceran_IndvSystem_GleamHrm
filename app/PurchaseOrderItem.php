<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = ['purchase_order_id', 'item_id', 'quantity', 'unit_cost'];

    protected $casts = ['unit_cost' => 'decimal:2'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function getLineTotalAttribute()
    {
        return $this->quantity * (float) $this->unit_cost;
    }
}
