@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">New Sales Order</h3>
        <p class="text-muted small mb-0">Confirming this order reserves stock immediately to prevent overselling</p>
      </div>
      <a href="{{ route('peams.orders.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to Orders</a>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    @php
      $stockMap = \App\InventoryStock::where('hub_id', $selectedHub)->pluck('quantity', 'item_id');
      $reservedMap = \App\InventoryStock::where('hub_id', $selectedHub)->pluck('reserved_quantity', 'item_id');
    @endphp

    <form method="POST" action="{{ route('peams.orders.store') }}" id="orderForm">
      @csrf
      <div class="card peams-card mb-4">
        <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-user-tie text-primary mr-2"></i> Order Details</h5></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 form-group">
              <label>Client *</label>
              <select name="client_id" class="form-control" required>
                <option value="">Select client</option>
                @foreach($clients as $client)
                  <option value="{{ $client->id }}" @if(old('client_id') == $client->id) selected @endif>{{ $client->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label>Fulfilling hub *</label>
              <select name="hub_id" id="hubSelect" class="form-control" required>
                @foreach($hubs as $hub)
                  <option value="{{ $hub->id }}" @if((int) old('hub_id', $selectedHub) === (int) $hub->id) selected @endif>{{ $hub->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4 form-group">
              <label>Delivery address</label>
              <input name="delivery_address" class="form-control" value="{{ old('delivery_address') }}" placeholder="Store / branch delivery point">
            </div>
            <div class="col-12 form-group">
              <label>Order notes</label>
              <textarea name="notes" class="form-control" rows="2" placeholder="Delivery instructions, preferred schedule...">{{ old('notes') }}</textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="card peams-card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title m-0"><i class="fas fa-boxes text-primary mr-2"></i> Line Items</h5>
          <button type="button" class="btn btn-sm btn-outline-primary" id="addLine"><i class="fas fa-plus mr-1"></i>Add Line</button>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-modern mb-0" id="lineTable">
              <thead><tr><th style="min-width:280px;">Item</th><th class="text-center" style="width:120px;">Available</th><th style="width:140px;">Quantity</th><th style="width:160px;">Unit Price</th><th class="text-right">Line Total</th><th></th></tr></thead>
              <tbody>
                <tr class="line-row">
                  <td>
                    <select name="items[0][item_id]" class="form-control item-select" required>
                      <option value="">Select item</option>
                      @foreach($items as $item)
                        <option value="{{ $item->id }}" data-price="{{ $item->unit_cost }}" data-stock="{{ (int) ($stockMap[$item->id] ?? 0) - (int) ($reservedMap[$item->id] ?? 0) }}">{{ $item->name }} ({{ $item->sku }})</option>
                      @endforeach
                    </select>
                  </td>
                  <td class="text-center available-cell"><span class="badge badge-soft-secondary">—</span></td>
                  <td><input type="number" name="items[0][quantity]" class="form-control qty-input" min="1" value="1" required></td>
                  <td><input type="number" step="0.01" min="0" name="items[0][unit_price]" class="form-control price-input" value="0" required></td>
                  <td class="text-right font-weight-bold line-total">₱0.00</td>
                  <td><button type="button" class="btn btn-sm btn-outline-danger remove-line"><i class="fas fa-times"></i></button></td>
                </tr>
              </tbody>
              <tfoot><tr><td colspan="4" class="text-right font-weight-bold">Order Total</td><td class="text-right font-weight-bold text-primary" id="orderTotal">₱0.00</td><td></td></tr></tfoot>
            </table>
          </div>
        </div>
        <div class="card-footer bg-white text-right">
          <a href="{{ route('peams.orders.index') }}" class="btn btn-light">Cancel</a>
          <button type="submit" class="btn btn-primary"><i class="fas fa-lock mr-1"></i>Create &amp; Reserve Stock</button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  let index = 1;

  function money(value) { return '₱' + parseFloat(value || 0).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}); }

  function recalc() {
    let total = 0;
    document.querySelectorAll('#lineTable tbody tr').forEach(function (row) {
      const qty = parseFloat(row.querySelector('.qty-input').value || 0);
      const price = parseFloat(row.querySelector('.price-input').value || 0);
      const line = qty * price;
      row.querySelector('.line-total').textContent = money(line);
      total += line;
    });
    document.getElementById('orderTotal').textContent = money(total);
  }

  function wireRow(row) {
    row.querySelector('.item-select').addEventListener('change', function () {
      const option = this.options[this.selectedIndex];
      row.querySelector('.price-input').value = option.dataset.price || 0;
      const stock = parseInt(option.dataset.stock || 0, 10);
      const cell = row.querySelector('.available-cell');
      if (!option.value) { cell.innerHTML = '<span class="badge badge-soft-secondary">—</span>'; return; }
      cell.innerHTML = '<span class="badge badge-soft-' + (stock > 0 ? 'success' : 'danger') + '">' + stock + '</span>';
      recalc();
    });
    row.querySelectorAll('.qty-input, .price-input').forEach(function (input) { input.addEventListener('input', recalc); });
    row.querySelector('.remove-line').addEventListener('click', function () {
      if (document.querySelectorAll('#lineTable tbody tr').length > 1) { row.remove(); recalc(); }
    });
  }

  document.querySelectorAll('#lineTable tbody tr').forEach(wireRow);

  document.getElementById('addLine').addEventListener('click', function () {
    const template = document.querySelector('#lineTable tbody tr');
    const clone = template.cloneNode(true);
    clone.querySelectorAll('select, input').forEach(function (field) {
      field.name = field.name.replace(/items\[\d+\]/, 'items[' + index + ']');
      if (field.classList.contains('qty-input')) { field.value = 1; }
      if (field.classList.contains('price-input')) { field.value = 0; }
      if (field.classList.contains('item-select')) { field.selectedIndex = 0; }
    });
    clone.querySelector('.available-cell').innerHTML = '<span class="badge badge-soft-secondary">—</span>';
    clone.querySelector('.line-total').textContent = '₱0.00';
    document.querySelector('#lineTable tbody').appendChild(clone);
    wireRow(clone);
    index++;
  });

  recalc();
});
</script>
@endsection
