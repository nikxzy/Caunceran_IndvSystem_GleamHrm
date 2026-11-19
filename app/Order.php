<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const STATUS_PENDING_HANDOFF = 'Pending Handoff';
    const STATUS_IN_TRANSIT = 'In Transit';
    const STATUS_DELIVERED = 'Delivered';
    const STATUS_CANCELLED = 'Cancelled';

    const PICKING_PENDING = 'Pending';
    const PICKING_GENERATED = 'Generated';
    const PICKING_PICKED = 'Picked';

    public static $statuses = [
        self::STATUS_PENDING_HANDOFF => 'Pending Handoff',
        self::STATUS_IN_TRANSIT => 'In Transit',
        self::STATUS_DELIVERED => 'Delivered',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    public static $pickingStatuses = [
        self::PICKING_PENDING => 'Pending',
        self::PICKING_GENERATED => 'Generated',
        self::PICKING_PICKED => 'Picked',
    ];

    protected $fillable = [
        'order_number', 'client_id', 'hub_id', 'created_by', 'driver_id',
        'status', 'picking_slip_status', 'verification_code', 'total_amount', 'delivery_address',
        'notes', 'reserved_at', 'picking_slip_generated_at', 'picked_at',
        'dispatched_at', 'delivered_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'reserved_at' => 'datetime',
        'picking_slip_generated_at' => 'datetime',
        'picked_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function deliveryProof()
    {
        return $this->hasOne(DeliveryProof::class);
    }

    public function isReserved()
    {
        return ! is_null($this->reserved_at);
    }

    public function canDispatch()
    {
        return $this->status === self::STATUS_PENDING_HANDOFF
            && $this->picking_slip_status === self::PICKING_PICKED;
    }

    public function getStatusBadgeAttribute()
    {
        $map = [
            self::STATUS_PENDING_HANDOFF => 'warning',
            self::STATUS_IN_TRANSIT => 'info',
            self::STATUS_DELIVERED => 'success',
            self::STATUS_CANCELLED => 'secondary',
        ];

        return $map[$this->status] ?? 'secondary';
    }

    public function getPickingBadgeAttribute()
    {
        $map = [
            self::PICKING_PENDING => 'secondary',
            self::PICKING_GENERATED => 'info',
            self::PICKING_PICKED => 'success',
        ];

        return $map[$this->picking_slip_status] ?? 'secondary';
    }
}
