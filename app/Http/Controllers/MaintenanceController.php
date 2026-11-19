<?php

namespace App\Http\Controllers;

use App\Employee;
use App\Hub;
use App\Item;
use App\MaintenanceLog;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $type = $request->get('type');

        $logs = MaintenanceLog::with(['item', 'hub', 'reporter', 'assignee'])
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->latest()
            ->paginate(15)
            ->appends($request->all());

        $dueAssets = Item::with(['stocks.hub'])
            ->dueForService(7)
            ->orderBy('next_service_at')
            ->get();

        $openIncidents = MaintenanceLog::outstanding()->count();
        $overdue = MaintenanceLog::outstanding()
            ->whereNotNull('scheduled_date')
            ->whereDate('scheduled_date', '<', Carbon::today())
            ->count();

        return view('peams.maintenance.index', compact('logs', 'dueAssets', 'openIncidents', 'overdue', 'status', 'type'));
    }

    public function create()
    {
        $hubs = Hub::active()->orderBy('name')->get();
        $assets = Item::active()->orderBy('name')->get();
        $technicians = Employee::orderBy('firstname')->get();

        return view('peams.maintenance.create', compact('hubs', 'assets', 'technicians'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'item_id' => 'required|exists:items,id',
            'hub_id' => 'required|exists:hubs,id',
            'type' => 'required|in:preventive,corrective,incident',
            'assigned_to' => 'nullable|exists:employees,id',
            'scheduled_date' => 'nullable|date',
            'description' => 'required|string',
        ]);

        $log = MaintenanceLog::create([
            'item_id' => $data['item_id'],
            'hub_id' => $data['hub_id'],
            'type' => $data['type'],
            'status' => MaintenanceLog::STATUS_OPEN,
            'reported_by' => auth()->id(),
            'assigned_to' => $data['assigned_to'] ?? null,
            'scheduled_date' => $data['scheduled_date'] ?? null,
            'description' => $data['description'],
        ]);

        AuditLogger::record('maintenance_logged', 'Maintenance log #'.$log->id.' created', null, $log);
        Session::flash('success', 'Maintenance / incident log created.');

        return redirect()->route('peams.maintenance.index');
    }

    public function updateStatus(Request $request, MaintenanceLog $maintenance)
    {
        $data = $request->validate([
            'status' => 'required|in:Open,In Progress,Completed',
            'resolution_notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'completed_date' => 'nullable|date',
        ]);

        $maintenance->update([
            'status' => $data['status'],
            'resolution_notes' => $data['resolution_notes'] ?? $maintenance->resolution_notes,
            'cost' => $data['cost'] ?? $maintenance->cost,
            'completed_date' => $data['status'] === MaintenanceLog::STATUS_COMPLETED
                ? ($data['completed_date'] ?? now()->toDateString())
                : $maintenance->completed_date,
        ]);

        if ($data['status'] === MaintenanceLog::STATUS_COMPLETED) {
            $item = $maintenance->item;
            if ($item) {
                $completedAt = Carbon::parse($maintenance->completed_date);
                $item->last_serviced_at = $completedAt;
                if ($item->service_interval_days) {
                    $item->next_service_at = $completedAt->copy()->addDays($item->service_interval_days);
                }
                $item->save();
            }
        }

        AuditLogger::record('maintenance_updated', 'Maintenance log #'.$maintenance->id.' marked '.$data['status'], null, $maintenance);
        Session::flash('success', 'Maintenance log updated.');

        return redirect()->route('peams.maintenance.index');
    }

    public function schedulePreventive(Request $request)
    {
        $data = $request->validate([
            'item_id' => 'required|exists:items,id',
            'hub_id' => 'required|exists:hubs,id',
            'scheduled_date' => 'required|date',
        ]);

        $item = Item::findOrFail($data['item_id']);
        $log = MaintenanceLog::create([
            'item_id' => $item->id,
            'hub_id' => $data['hub_id'],
            'type' => MaintenanceLog::TYPE_PREVENTIVE,
            'status' => MaintenanceLog::STATUS_OPEN,
            'reported_by' => auth()->id(),
            'scheduled_date' => $data['scheduled_date'],
            'description' => 'Scheduled preventive maintenance for '.$item->name,
        ]);

        $item->next_service_at = $data['scheduled_date'];
        $item->save();

        AuditLogger::record('preventive_scheduled', 'Preventive maintenance scheduled for '.$item->name, null, $log);
        Session::flash('success', 'Preventive maintenance scheduled.');

        return redirect()->route('peams.maintenance.index');
    }
}
