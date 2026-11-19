<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'hub_id', 'item_id', 'type', 'quantity', 'reference_type',
        'reference_id', 'notes', 'employee_id',
    ];

    public function hub()
    {
        return $this->belongsTo(Hub::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
