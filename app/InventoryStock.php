<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InventoryStock extends Model
{
    protected $fillable = ['hub_id', 'item_id', 'quantity', 'reserved_quantity'];

    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
    ];

    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function getAvailableAttribute()
    {
        return max(0, $this->quantity - $this->reserved_quantity);
    }

    public function getIsLowStockAttribute()
    {
        return $this->item && $this->quantity <= $this->item->reorder_level;
    }

    public function scopeForHub($query, $hubId)
    {
        return $query->where('hub_id', $hubId);
    }
}
