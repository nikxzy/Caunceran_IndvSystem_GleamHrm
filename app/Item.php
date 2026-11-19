<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    const TYPE_PACKAGING = 'packaging';
    const TYPE_EQUIPMENT = 'equipment';

    protected $fillable = [
        'sku', 'name', 'type', 'category', 'unit', 'description', 'unit_cost',
        'reorder_level', 'is_loanable', 'requires_preventive_maintenance',
        'service_interval_days', 'last_serviced_at', 'next_service_at', 'is_active',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'is_loanable' => 'boolean',
        'requires_preventive_maintenance' => 'boolean',
        'is_active' => 'boolean',
        'last_serviced_at' => 'date',
        'next_service_at' => 'date',
    ];

    public function stocks()
    {
        return $this->hasMany(InventoryStock::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function loans()
    {
        return $this->hasMany(EquipmentLoan::class, 'equipment_id');
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }

    public function disposals()
    {
        return $this->hasMany(AssetDisposal::class);
    }

    public function isEquipment()
    {
        return $this->type === self::TYPE_EQUIPMENT;
    }

    public function scopeEquipment($query)
    {
        return $query->where('type', self::TYPE_EQUIPMENT);
    }

    public function scopePackaging($query)
    {
        return $query->where('type', self::TYPE_PACKAGING);
    }

    public function scopeLoanable($query)
    {
        return $query->where('is_loanable', true);
    }

    public function scopeDueForService($query, $days = 7)
    {
        return $query->where('requires_preventive_maintenance', true)
            ->whereNotNull('next_service_at')
            ->whereDate('next_service_at', '<=', Carbon::now()->addDays($days));
    }

    public function totalStock()
    {
        return (int) $this->stocks()->sum('quantity');
    }
}
