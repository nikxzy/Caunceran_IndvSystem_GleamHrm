<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    const STATUS_REQUESTED = 'Transfer Requested';
    const STATUS_IN_TRANSIT = 'In Transit';
    const STATUS_RECEIVED = 'Received at Hub';

    public static $statuses = [
        self::STATUS_REQUESTED => 'Transfer Requested',
        self::STATUS_IN_TRANSIT => 'In Transit',
        self::STATUS_RECEIVED => 'Received at Hub',
    ];

    protected $fillable = [
        'transfer_number', 'from_hub_id', 'to_hub_id', 'requested_by',
        'received_by', 'status', 'dispatched_at', 'received_at', 'notes',
    ];

    protected $casts = [
        'dispatched_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(StockTransferItem::class);
    }

    public function fromHub()
    {
        return $this->belongsTo(Hub::class, 'from_hub_id');
    }

    public function toHub()
    {
        return $this->belongsTo(Hub::class, 'to_hub_id');
    }

    public function requester()
    {
        return $this->belongsTo(Employee::class, 'requested_by');
    }

    public function receiver()
    {
        return $this->belongsTo(Employee::class, 'received_by');
    }

    public function isRequested()
    {
        return $this->status === self::STATUS_REQUESTED;
    }

    public function isInTransit()
    {
        return $this->status === self::STATUS_IN_TRANSIT;
    }

    public function getStatusBadgeAttribute()
    {
        $map = [
            self::STATUS_REQUESTED => 'warning',
            self::STATUS_IN_TRANSIT => 'info',
            self::STATUS_RECEIVED => 'success',
        ];

        return $map[$this->status] ?? 'secondary';
    }
}
