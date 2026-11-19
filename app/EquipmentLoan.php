<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class EquipmentLoan extends Model
{
    const STATUS_ON_LOAN = 'On Loan';
    const STATUS_RETURNED = 'Returned';
    const STATUS_OVERDUE = 'Overdue';

    public static $statuses = [
        self::STATUS_ON_LOAN => 'On Loan',
        self::STATUS_RETURNED => 'Returned',
        self::STATUS_OVERDUE => 'Overdue',
    ];

    protected $fillable = [
        'client_name', 'client_id', 'equipment_id', 'hub_id', 'borrow_date',
        'expected_return_date', 'actual_return_date', 'status', 'condition_out',
        'condition_in', 'handled_by', 'notes',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
    ];

    public function equipment()
    {
        return $this->belongsTo(Item::class, 'equipment_id');
    }

    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function handler()
    {
        return $this->belongsTo(Employee::class, 'handled_by');
    }

    public function isOverdue()
    {
        return $this->status !== self::STATUS_RETURNED
            && $this->expected_return_date
            && $this->expected_return_date->isPast();
    }

    public function refreshOverdueStatus()
    {
        if ($this->isOverdue()) {
            $this->status = self::STATUS_OVERDUE;
            $this->save();
        }

        return $this;
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', [self::STATUS_ON_LOAN, self::STATUS_OVERDUE]);
    }

    public function scopeOverdue($query)
    {
        return $query->open()->whereDate('expected_return_date', '<', Carbon::today());
    }

    public function getStatusBadgeAttribute()
    {
        $map = [
            self::STATUS_ON_LOAN => 'info',
            self::STATUS_RETURNED => 'success',
            self::STATUS_OVERDUE => 'danger',
        ];

        return $map[$this->status] ?? 'secondary';
    }
}
