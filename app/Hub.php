<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{
    protected $fillable = [
        'name', 'code', 'address', 'contact_person', 'contact_no', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function stocks()
    {
        return $this->hasMany(InventoryStock::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function outgoingTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'from_hub_id');
    }

    public function incomingTransfers()
    {
        return $this->hasMany(StockTransfer::class, 'to_hub_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
