<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetDisposal extends Model
{
    public static $types = [
        'damaged_packaging' => 'Damaged Packaging',
        'scrap_machinery' => 'Scrap Machinery',
        'obsolete' => 'Obsolete Asset',
    ];

    protected $fillable = [
        'reference_no', 'item_id', 'hub_id', 'quantity', 'type', 'reason',
        'disposal_method', 'estimated_value', 'approved_by', 'disposed_by', 'disposed_at',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'disposed_at' => 'datetime',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function disposer()
    {
        return $this->belongsTo(Employee::class, 'disposed_by');
    }
}
