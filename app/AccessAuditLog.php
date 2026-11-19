<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccessAuditLog extends Model
{
    protected $fillable = [
        'employee_id', 'actor_id', 'event', 'description', 'subject_type',
        'subject_id', 'ip_address', 'user_agent',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function actor()
    {
        return $this->belongsTo(Employee::class, 'actor_id');
    }

    public function getEventBadgeAttribute()
    {
        $map = [
            'login' => 'success',
            'logout' => 'secondary',
            'role_assigned' => 'info',
            'role_revoked' => 'warning',
            'permission_changed' => 'primary',
            'user_activated' => 'success',
            'user_deactivated' => 'danger',
            'access_denied' => 'danger',
        ];

        return $map[$this->event] ?? 'secondary';
    }
}
