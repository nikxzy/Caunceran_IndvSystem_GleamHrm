<?php

namespace App\Http\Controllers;

use App\AccessAuditLog;
use App\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccessAuditController extends Controller
{
    public function index(Request $request)
    {
        $event = $request->get('event');
        $search = $request->get('q');

        $logs = AccessAuditLog::with(['employee', 'actor'])
            ->when($event, function ($query) use ($event) {
                $query->where('event', $event);
            })
            ->when($search, function ($query) use ($search) {
                $query->where('description', 'like', '%'.$search.'%');
            })
            ->latest()
            ->paginate(20)
            ->appends($request->all());

        $activeUsers = Employee::with(['branch', 'hub', 'roles'])
            ->where('is_active', true)
            ->where('last_login_at', '>=', Carbon::now()->subMinutes(30))
            ->orderByDesc('last_login_at')
            ->get();

        $inactiveAccounts = Employee::with(['branch', 'roles'])
            ->where(function ($query) {
                $query->where('is_active', false)
                    ->orWhereNull('last_login_at')
                    ->orWhere('last_login_at', '<', Carbon::now()->subDays(90));
            })
            ->orderBy('last_login_at')
            ->take(10)
            ->get();

        $overPrivileged = Employee::with('roles')
            ->where(function ($query) {
                $query->whereHas('roles', function ($role) {
                    $role->where('name', 'admin');
                })->orWhereHas('permissions');
            })
            ->get()
            ->filter(function ($employee) {
                return $employee->hasRole('admin')
                    || $employee->permissions->count() > 25;
            });

        $stats = [
            'active_sessions' => $activeUsers->count(),
            'total_users' => Employee::count(),
            'roles' => Role::count(),
            'permissions' => Permission::count(),
            'inactive' => $inactiveAccounts->count(),
            'flagged' => $overPrivileged->count(),
        ];

        $eventCounts = AccessAuditLog::select('event')
            ->selectRaw('count(*) as total')
            ->groupBy('event')
            ->orderByDesc('total')
            ->get();

        $lastLogins = Employee::with(['branch', 'hub', 'roles'])
            ->orderByDesc('last_login_at')
            ->take(10)
            ->get();

        return view('peams.audit.index', compact(
            'logs', 'activeUsers', 'inactiveAccounts', 'overPrivileged',
            'stats', 'eventCounts', 'lastLogins', 'event', 'search'
        ));
    }
}
