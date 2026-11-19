<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveryProof extends Model
{
    protected $fillable = [
        'order_id', 'received_by_name', 'verification_code', 'signature_path',
        'photo_path', 'delivered_by', 'delivered_at', 'notes',
    ];

    protected $casts = ['delivered_at' => 'datetime'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function deliverer()
    {
        return $this->belongsTo(Employee::class, 'delivered_by');
    }
}
