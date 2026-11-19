<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name', 'contact_person', 'email', 'phone', 'address', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function loans()
    {
        return $this->hasMany(EquipmentLoan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
