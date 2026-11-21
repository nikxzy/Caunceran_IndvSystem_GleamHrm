@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">Inventory &amp; Asset Catalog</h3>
        <p class="text-muted small mb-0">Packaging supplies and store machinery across both hubs</p>
      </div>
      <div class="d-flex flex-wrap" style="gap: .5rem;">
        <button class="btn btn-outline-primary" data-toggle="modal" data-target="#adjustStockModal">
          <i class="fas fa-sliders-h mr-1"></i> Adjust Stock
        </button>
        <button class="btn btn-primary" data-toggle="modal" data-target="#createItemModal">
          <i class="fas fa-plus mr-1"></i> New Catalog Item
        </button>
      </div>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    @php
      $lowCount = 0;
      $totalOnHand = 0;
      $totalReserved = 0;
      $allItems = \App\Item::with(['stocks' => function ($query) use ($hubId) {
          $query->where('hub_id', $hubId);
      }])->get();
      foreach ($allItems as $catalogItem) {
          $row = $catalogItem->stocks->first();
          $qty = $row ? $row->quantity : 0;
          $totalOnHand += $qty;
          $totalReserved += $row ? $row->reserved_quantity : 0;
          if ($qty <= $catalogItem->reorder_level) { $lowCount++; }
      }
    @endphp

    <div class="row mb-3">
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-box-open"></i></div>
          <div><span class="peams-stat-label">Catalog SKUs</span><span class="peams-stat-value">{{ $items->total() }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#ecfdf5;color:#047857;"><i class="fas fa-warehouse"></i></div>
          <div><span class="peams-stat-label">Units On Hand</span><span class="peams-stat-value">{{ number_format($totalOnHand) }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-lock"></i></div>
          <div><span class="peams-stat-label">Reserved</span><span class="peams-stat-value">{{ number_format($totalReserved) }}</span></div>
        </div>
      </div>
      <div class="col-6 col-lg-3 mb-3">
        <div class="peams-stat-card">
          <div class="peams-stat-icon" style="background:#fee2e2;color:#b91c1c;"><i class="fas fa-exclamation-triangle"></i></div>
          <div><span class="peams-stat-label">Low Stock Alerts</span><span class="peams-stat-value">{{ $lowCount }}</span></div>
        </div>
      </div>
    </div>

    @if($lowCount > 0)
      <div class="alert alert-warning d-flex align-items-center">
        <i class="fas fa-bell mr-2"></i>
        <span><strong>{{ $lowCount }} item(s)</strong> are at or below their reorder level. Raise a purchase order to replenish.</span>
        <a href="{{ route('peams.purchase-orders.create') }}" class="btn btn-sm btn-warning ml-auto">Create PO</a>
      </div>
    @endif

    <div class="card peams-card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap:.75rem;">
        <h5 class="card-title m-0"><i class="fas fa-clipboard-list text-primary mr-2"></i> Stock by Hub</h5>
        <form method="GET" action="{{ route('peams.inventory.index') }}" class="d-flex flex-wrap align-items-center" style="gap:.5rem;">
          <select name="hub_id" class="form-control form-control-sm" onchange="this.form.submit()">
            @foreach($hubs as $hub)
              <option value="{{ $hub->id }}" @if((int) $hubId === (int) $hub->id) selected @endif>{{ $hub->name }}</option>
            @endforeach
          </select>
          <select name="type" class="form-control form-control-sm" onchange="this.form.submit()">
            <option value="">All types</option>
            <option value="packaging" @if($type === 'packaging') selected @endif>Packaging</option>
            <option value="equipment" @if($type === 'equipment') selected @endif>Equipment</option>
          </select>
          <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search SKU or name">
          <label class="mb-0 small text-muted d-flex align-items-center" style="gap:.35rem;">
            <input type="checkbox" name="low" value="1" @if($lowOnly) checked @endif onchange="this.form.submit()"> Low only
          </label>
          <button class="btn btn-sm btn-primary" type="submit"><i class="fas fa-search"></i></button>
        </form>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-modern mb-0">
            <thead>
              <tr>
                <th>SKU</th>
                <th>Item</th>
                <th>Category</th>
                <th class="text-right">On Hand</th>
                <th class="text-right">Reserved</th>
                <th class="text-right">Available</th>
                <th class="text-right">Reorder</th>
                <th class="text-center">Status</th>
                <th class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($items as $catalogItem)
                @php
                  $stock = $catalogItem->stocks->first();
                  $qty = $stock ? $stock->quantity : 0;
                  $reserved = $stock ? $stock->reserved_quantity : 0;
                  $available = max(0, $qty - $reserved);
                  if ($qty <= 0) { $tone = 'danger'; $label = 'Out of Stock'; }
                  elseif ($qty <= $catalogItem->reorder_level) { $tone = 'warning'; $label = 'Low Stock'; }
                  elseif ($reserved > 0) { $tone = 'primary'; $label = 'Reserved'; }
                  else { $tone = 'success'; $label = 'In Stock'; }
                @endphp
                <tr>
                  <td><code class="text-primary font-weight-bold">{{ $catalogItem->sku }}</code></td>
                  <td>
                    <span class="font-weight-bold text-dark d-block">{{ $catalogItem->name }}</span>
                    <small class="text-muted">{{ ucfirst($catalogItem->type) }} &middot; {{ $catalogItem->unit }}</small>
                  </td>
                  <td><span class="badge badge-soft-secondary">{{ $catalogItem->category ?? 'Uncategorized' }}</span></td>
                  <td class="text-right font-weight-bold">{{ number_format($qty) }}</td>
                  <td class="text-right text-muted">{{ number_format($reserved) }}</td>
                  <td class="text-right font-weight-bold text-dark">{{ number_format($available) }}</td>
                  <td class="text-right text-muted">{{ number_format($catalogItem->reorder_level) }}</td>
                  <td class="text-center"><span class="badge badge-soft-{{ $tone }}">{{ $label }}</span></td>
                  <td class="text-right text-nowrap">
                    <button class="btn btn-sm btn-outline-primary peams-action-btn" title="Adjust"
                      data-toggle="modal" data-target="#adjustStockModal"
                      data-item="{{ $catalogItem->id }}" data-hub="{{ $hubId }}"><i class="fas fa-sliders-h"></i></button>
                    <button class="btn btn-sm btn-outline-secondary peams-action-btn" title="Edit"
                      data-toggle="modal" data-target="#editItemModal{{ $catalogItem->id }}"><i class="fas fa-pen"></i></button>
                  </td>
                </tr>
              @empty
                <tr><td colspan="9" class="text-center py-4 text-muted">No catalog items match the current filters.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      @if($items->hasPages())
        <div class="card-footer bg-white border-0">{{ $items->links() }}</div>
      @endif
    </div>

    <div class="card peams-card">
      <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-exchange-alt text-primary mr-2"></i> Recent Stock Movements</h5></div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover table-modern mb-0">
            <thead>
              <tr><th>When</th><th>Item</th><th>Type</th><th class="text-right">Qty</th><th>Hub</th><th>Notes</th></tr>
            </thead>
            <tbody>
              @forelse($movements as $movement)
                <tr>
                  <td class="text-muted small">{{ $movement->created_at ? $movement->created_at->format('M d, Y h:i A') : '—' }}</td>
                  <td class="font-weight-bold">{{ optional($movement->item)->name ?? '—' }}</td>
                  <td><span class="badge badge-soft-{{ $movement->quantity >= 0 ? 'success' : 'danger' }}">{{ str_replace('_', ' ', $movement->type) }}</span></td>
                  <td class="text-right font-weight-bold {{ $movement->quantity >= 0 ? 'text-success' : 'text-danger' }}">{{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity) }}</td>
                  <td class="small text-muted">{{ optional($movement->hub)->name ?? '—' }}</td>
                  <td class="small text-muted">{{ $movement->notes ?? '—' }}</td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No stock movements recorded yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Create item --}}
<div class="modal fade" id="createItemModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" method="POST" action="{{ route('peams.inventory.store') }}">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-box-open text-primary mr-2"></i> New Catalog Item</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4 form-group"><label>SKU *</label><input name="sku" class="form-control" required value="{{ old('sku') }}"></div>
          <div class="col-md-8 form-group"><label>Item name *</label><input name="name" class="form-control" required value="{{ old('name') }}"></div>
          <div class="col-md-4 form-group"><label>Type *</label>
            <select name="type" class="form-control" required>
              <option value="packaging">Packaging</option><option value="equipment">Equipment</option>
            </select>
          </div>
          <div class="col-md-4 form-group"><label>Category</label>
            <input name="category" class="form-control" list="categoryList" value="{{ old('category') }}">
            <datalist id="categoryList">
              <option value="Beverage Packaging"><option value="Bakery Packaging"><option value="Store Machinery Assets">
            </datalist>
          </div>
          <div class="col-md-4 form-group"><label>Unit *</label><input name="unit" class="form-control" value="{{ old('unit', 'pc') }}" required></div>
          <div class="col-md-4 form-group"><label>Unit cost</label><input type="number" step="0.01" min="0" name="unit_cost" class="form-control" value="{{ old('unit_cost', 0) }}"></div>
          <div class="col-md-4 form-group"><label>Reorder level</label><input type="number" min="0" name="reorder_level" class="form-control" value="{{ old('reorder_level', 0) }}"></div>
          <div class="col-md-4 form-group"><label>Service interval (days)</label><input type="number" min="1" name="service_interval_days" class="form-control" value="{{ old('service_interval_days') }}"></div>
          <div class="col-12 form-group"><label>Description</label><textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea></div>
          <div class="col-md-4"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="is_loanable" name="is_loanable" value="1"><label class="custom-control-label" for="is_loanable">Loanable equipment</label></div></div>
          <div class="col-md-4"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="preventive" name="requires_preventive_maintenance" value="1"><label class="custom-control-label" for="preventive">Preventive maintenance</label></div></div>
          <div class="col-md-4"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked><label class="custom-control-label" for="is_active">Active</label></div></div>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-dismiss="modal" type="button">Cancel</button><button class="btn btn-primary" type="submit">Save Item</button></div>
    </form>
  </div>
</div>

{{-- Adjust stock --}}
<div class="modal fade" id="adjustStockModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('peams.inventory.adjust') }}" id="adjustStockForm">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-sliders-h text-primary mr-2"></i> Stock Count Adjustment</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group"><label>Hub *</label>
          <select name="hub_id" id="adjustHub" class="form-control" required>
            @foreach($hubs as $hub)<option value="{{ $hub->id }}" @if((int)$hubId === (int)$hub->id) selected @endif>{{ $hub->name }}</option>@endforeach
          </select>
        </div>
        <div class="form-group"><label>Item *</label>
          <select name="item_id" id="adjustItem" class="form-control" required>
            @foreach($items as $catalogItem)<option value="{{ $catalogItem->id }}">{{ $catalogItem->sku }} — {{ $catalogItem->name }}</option>@endforeach
          </select>
        </div>
        <div class="form-group"><label>New quantity on hand *</label><input type="number" min="0" name="quantity" id="adjustQuantity" class="form-control" required></div>
        <div class="form-group"><label>Reason / notes</label><input name="notes" class="form-control" placeholder="Cycle count, damage, correction..."></div>
        <p class="small text-muted mb-0"><i class="fas fa-info-circle mr-1"></i>Adjustments are logged to the stock movement ledger.</p>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-dismiss="modal" type="button">Cancel</button><button class="btn btn-primary" type="submit">Save Adjustment</button></div>
    </form>
  </div>
</div>

@foreach($items as $catalogItem)
  <div class="modal fade" id="editItemModal{{ $catalogItem->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <form class="modal-content" method="POST" action="{{ route('peams.inventory.update', $catalogItem) }}">
        @csrf @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Edit {{ $catalogItem->name }}</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-4 form-group"><label>SKU *</label><input name="sku" class="form-control" value="{{ $catalogItem->sku }}" required></div>
            <div class="col-md-8 form-group"><label>Name *</label><input name="name" class="form-control" value="{{ $catalogItem->name }}" required></div>
            <div class="col-md-4 form-group"><label>Type *</label>
              <select name="type" class="form-control"><option value="packaging" @if($catalogItem->type === 'packaging') selected @endif>Packaging</option><option value="equipment" @if($catalogItem->type === 'equipment') selected @endif>Equipment</option></select>
            </div>
            <div class="col-md-4 form-group"><label>Category</label><input name="category" class="form-control" value="{{ $catalogItem->category }}"></div>
            <div class="col-md-4 form-group"><label>Unit</label><input name="unit" class="form-control" value="{{ $catalogItem->unit }}" required></div>
            <div class="col-md-4 form-group"><label>Unit cost</label><input type="number" step="0.01" min="0" name="unit_cost" class="form-control" value="{{ $catalogItem->unit_cost }}"></div>
            <div class="col-md-4 form-group"><label>Reorder level</label><input type="number" min="0" name="reorder_level" class="form-control" value="{{ $catalogItem->reorder_level }}"></div>
            <div class="col-md-4 form-group"><label>Service interval (days)</label><input type="number" min="1" name="service_interval_days" class="form-control" value="{{ $catalogItem->service_interval_days }}"></div>
            <div class="col-12 form-group"><label>Description</label><textarea name="description" class="form-control" rows="2">{{ $catalogItem->description }}</textarea></div>
            <div class="col-md-4"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="loan-{{ $catalogItem->id }}" name="is_loanable" value="1" @if($catalogItem->is_loanable) checked @endif><label class="custom-control-label" for="loan-{{ $catalogItem->id }}">Loanable</label></div></div>
            <div class="col-md-4"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="prev-{{ $catalogItem->id }}" name="requires_preventive_maintenance" value="1" @if($catalogItem->requires_preventive_maintenance) checked @endif><label class="custom-control-label" for="prev-{{ $catalogItem->id }}">Preventive</label></div></div>
            <div class="col-md-4"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="act-{{ $catalogItem->id }}" name="is_active" value="1" @if($catalogItem->is_active) checked @endif><label class="custom-control-label" for="act-{{ $catalogItem->id }}">Active</label></div></div>
          </div>
        </div>
        <div class="modal-footer"><button class="btn btn-light" data-dismiss="modal" type="button">Cancel</button><button class="btn btn-primary" type="submit">Update Item</button></div>
      </form>
    </div>
  </div>
@endforeach
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  $('#adjustStockModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    if (!button || !button.data('item')) { return; }
    $('#adjustItem').val(button.data('item'));
    $('#adjustHub').val(button.data('hub'));
    $('#adjustQuantity').val('');
  });
});
</script>
@endsection
