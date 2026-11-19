<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    const TYPE_PREVENTIVE = 'preventive';
    const TYPE_CORRECTIVE = 'corrective';
    const TYPE_INCIDENT = 'incident';

    const STATUS_OPEN = 'Open';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_COMPLETED = 'Completed';

    public static $types = [
        self::TYPE_PREVENTIVE => 'Preventive',
        self::TYPE_CORRECTIVE => 'Corrective Repair',
        self::TYPE_INCIDENT => 'Incident / Damage',
    ];

    public static $statuses = [
        self::STATUS_OPEN => 'Open',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_COMPLETED => 'Completed',
    ];

    protected $fillable = [
        'item_id', 'hub_id', 'type', 'status', 'reported_by', 'assigned_to',
        'scheduled_date', 'completed_date', 'description', 'resolution_notes', 'cost',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }

    public function reporter()
    {
        return $this->belongsTo(Employee::class, 'reported_by');
    }

    public function assignee()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function isOpen()
    {
        return in_array($this->status, [self::STATUS_OPEN, self::STATUS_IN_PROGRESS]);
    }

    public function isDue()
    {
        return $this->isOpen() && $this->scheduled_date && $this->scheduled_date->isPast();
    }

    public function scopeOutstanding($query)
    {
        return $query->whereIn('status', [self::STATUS_OPEN, self::STATUS_IN_PROGRESS]);
    }

    public function scopeDue($query, $days = 7)
    {
        return $query->outstanding()
            ->whereDate('scheduled_date', '<=', Carbon::now()->addDays($days));
    }

    public function getStatusBadgeAttribute()
    {
        $map = [
            self::STATUS_OPEN => 'warning',
            self::STATUS_IN_PROGRESS => 'info',
            self::STATUS_COMPLETED => 'success',
        ];

        return $map[$this->status] ?? 'secondary';
    }
}
