<?php

namespace App\Services;

use App\AccessAuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * Record an access-management event.
     *
     * @param  string       $event       e.g. login, role_assigned, access_denied
     * @param  string|null  $description human readable summary
     * @param  int|null     $employeeId  subject employee (defaults to the actor)
     * @param  Model|null   $subject     optional changed model
     */
    public static function record($event, $description = null, $employeeId = null, Model $subject = null)
    {
        $actor = Auth::user();

        return AccessAuditLog::create([
            'employee_id' => $employeeId ?: ($actor ? $actor->id : null),
            'actor_id' => $actor ? $actor->id : null,
            'event' => $event,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->getKey() : null,
            'ip_address' => request() ? request()->ip() : null,
            'user_agent' => request() ? substr((string) request()->userAgent(), 0, 1000) : null,
        ]);
    }

    public static function accessDenied($description = null)
    {
        return static::record('access_denied', $description ?: 'Attempted to access a restricted route.');
    }
}
