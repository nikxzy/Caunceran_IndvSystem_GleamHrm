@extends('layouts.master')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="card border-0 shadow-sm rounded-4 mb-4 text-white overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
        <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap" style="gap:1rem;">
            <div>
                <span class="badge px-3 py-1 rounded-pill mb-2" style="background:rgba(37,99,235,.25);color:#93c5fd;">
                    <i class="fas fa-warehouse mr-1"></i> Bongabong Central Hub &amp; Roxas Branch
                </span>
                <h3 class="fw-bold mb-1 text-white">Mindoro Pack &amp; Pour Warehousing Solutions</h3>
                <p class="mb-0 text-white-50">PEAMS — Packaging &amp; Equipment Asset Management System</p>
            </div>
            <div class="text-right">
                <span class="badge px-3 py-2 rounded-pill" style="background:rgba(16,185,129,.15);color:#6ee7b7;">
                    <i class="fas fa-circle mr-2" style="font-size:8px;"></i>System Operational
                </span>
                <div class="small text-white-50 mt-2">{{ now()->format('l, F j, Y') }}</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6 col-lg-3 mb-3">
            <div class="peams-stat-card">
                <div class="peams-stat-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-box-open"></i></div>
                <div><span class="peams-stat-label">Units On Hand</span><span class="peams-stat-value">{{ number_format($stats['units_on_hand']) }}</span></div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="peams-stat-card">
                <div class="peams-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-exclamation-triangle"></i></div>
                <div><span class="peams-stat-label">Low Stock Alerts</span><span class="peams-stat-value">{{ $stats['low_stock'] }}</span></div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="peams-stat-card">
                <div class="peams-stat-icon" style="background:#d1fae5;color:#047857;"><i class="fas fa-users"></i></div>
                <div><span class="peams-stat-label">Active Workforce</span><span class="peams-stat-value">{{ $stats['active_employees'] }}</span></div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="peams-stat-card">
                <div class="peams-stat-icon" style="background:#e0e7ff;color:#4338ca;"><i class="fas fa-peso-sign"></i></div>
                <div><span class="peams-stat-label">Inventory Value</span><span class="peams-stat-value">₱{{ number_format($stats['stock_value'], 0) }}</span></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6 col-lg-3 mb-3">
            <div class="peams-stat-card">
                <div class="peams-stat-icon" style="background:#fee2e2;color:#b91c1c;"><i class="fas fa-file-signature"></i></div>
                <div><span class="peams-stat-label">Pending POs</span><span class="peams-stat-value">{{ $stats['pending_pos'] }}</span></div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="peams-stat-card">
                <div class="peams-stat-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-file-invoice"></i></div>
                <div><span class="peams-stat-label">Open Orders</span><span class="peams-stat-value">{{ $stats['open_orders'] }}</span></div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="peams-stat-card">
                <div class="peams-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-tools"></i></div>
                <div><span class="peams-stat-label">Open Maintenance</span><span class="peams-stat-value">{{ $stats['open_maintenance'] }}</span></div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="peams-stat-card">
                <div class="peams-stat-icon" style="background:#fee2e2;color:#b91c1c;"><i class="fas fa-clock"></i></div>
                <div><span class="peams-stat-label">Overdue Loans</span><span class="peams-stat-value">{{ $stats['overdue_loans'] }}</span></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-5 mb-3">
            <div class="card peams-card h-100">
                <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-warehouse text-primary mr-2"></i> Stock Distribution by Hub</h5></div>
                <div class="card-body"><div style="height:270px;position:relative;"><canvas id="hubStockChart"></canvas></div></div>
            </div>
        </div>
        <div class="col-12 col-lg-7 mb-3">
            <div class="card peams-card h-100">
                <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-chart-bar text-primary mr-2"></i> Orders by Status</h5></div>
                <div class="card-body"><div style="height:270px;position:relative;"><canvas id="orderStatusChart"></canvas></div></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-7 mb-3">
            <div class="card peams-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0"><i class="fas fa-file-invoice text-primary mr-2"></i> Recent Sales Orders</h5>
                    <a href="{{ route('peams.orders.index') }}" class="btn btn-sm btn-light border rounded-pill px-3">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-modern mb-0">
                            <thead><tr><th>Order</th><th>Client</th><th>Hub</th><th class="text-right">Total</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td><a href="{{ route('peams.orders.show', $order) }}" class="font-weight-bold text-primary">{{ $order->order_number }}</a></td>
                                        <td class="small">{{ optional($order->client)->name ?? '—' }}</td>
                                        <td class="small text-muted">{{ optional($order->hub)->name ?? '—' }}</td>
                                        <td class="text-right small font-weight-bold">₱{{ number_format($order->total_amount, 2) }}</td>
                                        <td><span class="badge badge-soft-{{ $order->status_badge }}">{{ $order->status }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No sales orders yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5 mb-3">
            <div class="card peams-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0"><i class="fas fa-wrench text-warning mr-2"></i> Service Due Soon</h5>
                    <a href="{{ route('peams.maintenance.index') }}" class="btn btn-sm btn-light border rounded-pill px-3">Open</a>
                </div>
                <div class="card-body">
                    @forelse($dueMaintenance as $asset)
                        <div class="peams-summary-row">
                            <div><span class="font-weight-bold d-block small">{{ $asset->name }}</span><small class="text-muted">{{ $asset->sku }}</small></div>
                            <span class="badge badge-soft-warning">{{ $asset->next_service_at ? $asset->next_service_at->format('M d, Y') : 'TBD' }}</span>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0 py-4">No preventive maintenance due within 14 days.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-6 mb-3">
            <div class="card peams-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0"><i class="fas fa-shield-alt text-primary mr-2"></i> Recent Access Events</h5>
                    <a href="{{ route('peams.audit.index') }}" class="btn btn-sm btn-light border rounded-pill px-3">Audit</a>
                </div>
                <div class="card-body">
                    @forelse($recentActivity as $log)
                        <div class="peams-summary-row">
                            <div><span class="small font-weight-bold d-block">{{ ucwords(str_replace('_', ' ', $log->event)) }}</span><small class="text-muted">{{ $log->description }}</small></div>
                            <small class="text-muted text-nowrap">{{ $log->created_at ? $log->created_at->diffForHumans() : '' }}</small>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0 py-4">No access events recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6 mb-3">
            <div class="card peams-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0"><i class="fas fa-exchange-alt text-primary mr-2"></i> Recent Stock Movements</h5>
                    <a href="{{ route('peams.inventory.index') }}" class="btn btn-sm btn-light border rounded-pill px-3">Inventory</a>
                </div>
                <div class="card-body">
                    @forelse($recentMovements as $movement)
                        <div class="peams-summary-row">
                            <div><span class="small font-weight-bold d-block">{{ optional($movement->item)->name ?? '—' }}</span><small class="text-muted">{{ optional($movement->hub)->name ?? '—' }} &middot; {{ str_replace('_', ' ', $movement->type) }}</small></div>
                            <span class="badge badge-soft-{{ $movement->quantity >= 0 ? 'success' : 'danger' }}">{{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}</span>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0 py-4">No stock movement recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var hubStock = {!! json_encode($hubStock) !!};
    var orderStatuses = {!! json_encode($orderStatuses) !!};

    new Chart(document.getElementById('hubStockChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: hubStock.map(function (row) { return row.label; }),
            datasets: [{ data: hubStock.map(function (row) { return row.quantity; }), backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#64748b'], borderWidth: 0 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '62%',
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { family: 'Inter' } } } }
        }
    });

    new Chart(document.getElementById('orderStatusChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: orderStatuses.map(function (row) { return row.label; }),
            datasets: [{ data: orderStatuses.map(function (row) { return row.total; }), backgroundColor: ['#f59e0b', '#2563eb', '#10b981', '#94a3b8'], borderRadius: 8, maxBarThickness: 54 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } }
        }
    });
});
</script>
@endsection
