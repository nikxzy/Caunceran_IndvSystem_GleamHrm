<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    const STATUS_PENDING = 'Pending Approval';
    const STATUS_APPROVED = 'Approved';
    const STATUS_RECEIVED = 'Received';

    public static $statuses = [
        self::STATUS_PENDING => 'Pending Approval',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_RECEIVED => 'Received',
    ];

    protected $fillable = [
        'po_number', 'vendor_id', 'hub_id', 'requested_by', 'approved_by',
        'status', 'order_date', 'expected_date', 'total_amount',
        'approved_at', 'received_at', 'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_date' => 'date',
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }

    public function requester()
    {
        return $this->belongsTo(Employee::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approved_by');
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isReceived()
    {
        return $this->status === self::STATUS_RECEIVED;
    }

    public function getStatusBadgeAttribute()
    {
        $map = [
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'info',
            self::STATUS_RECEIVED => 'success',
        ];

        return $map[$this->status] ?? 'secondary';
    }
}
