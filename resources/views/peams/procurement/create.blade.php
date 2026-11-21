@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">New Purchase Order</h3>
        <p class="text-muted small mb-0">Submit for digital approval; receiving the PO updates hub stock automatically</p>
      </div>
      <a href="{{ route('peams.purchase-orders.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Purchase Orders</a>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    <form method="POST" action="{{ route('peams.purchase-orders.store') }}" id="poForm">
      @csrf
      <div class="card peams-card mb-4">
        <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-file-invoice text-primary mr-2"></i> PO Details</h5></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-4 form-group"><label>Vendor</label>
              <select name="vendor_id" class="form-control">
                <option value="">Unassigned / to be quoted</option>
                @foreach($vendors as $vendor)<option value="{{ $vendor->id }}" @if(old('vendor_id') == $vendor->id) selected @endif>{{ $vendor->company_name }}</option>@endforeach
              </select>
            </div>
            <div class="col-md-4 form-group"><label>Receiving hub *</label>
              <select name="hub_id" class="form-control" required>
                @foreach($hubs as $hub)<option value="{{ $hub->id }}">{{ $hub->name }}</option>@endforeach
              </select>
            </div>
            <div class="col-md-2 form-group"><label>Order date *</label><input type="date" name="order_date" class="form-control" required value="{{ old('order_date', now()->toDateString()) }}"></div>
            <div class="col-md-2 form-group"><label>Expected date</label><input type="date" name="expected_date" class="form-control" value="{{ old('expected_date') }}"></div>
            <div class="col-12 form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="2" placeholder="Delivery terms, quotation reference...">{{ old('notes') }}</textarea></div>
          </div>
        </div>
      </div>

      <div class="card peams-card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title m-0"><i class="fas fa-boxes text-primary mr-2"></i> Order Lines</h5>
          <button type="button" class="btn btn-sm btn-outline-primary" id="addPoLine"><i class="fas fa-plus mr-1"></i>Add Line</button>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-modern mb-0" id="poTable">
              <thead><tr><th style="min-width:280px;">Item</th><th style="width:140px;">Quantity</th><th style="width:170px;">Unit Cost</th><th class="text-right">Line Total</th><th></th></tr></thead>
              <tbody>
                <tr class="po-row">
                  <td>
                    <select name="items[0][item_id]" class="form-control item-select" required>
                      <option value="">Select item</option>
                      @foreach($items as $item)<option value="{{ $item->id }}" data-cost="{{ $item->unit_cost }}">{{ $item->name }} ({{ $item->sku }})</option>@endforeach
                    </select>
                  </td>
                  <td><input type="number" min="1" name="items[0][quantity]" class="form-control qty-input" value="1" required></td>
                  <td><input type="number" step="0.01" min="0" name="items[0][unit_cost]" class="form-control cost-input" value="0" required></td>
                  <td class="text-right font-weight-bold line-total">₱0.00</td>
                  <td><button type="button" class="btn btn-sm btn-outline-danger remove-line"><i class="fas fa-times"></i></button></td>
                </tr>
              </tbody>
              <tfoot><tr><td colspan="3" class="text-right font-weight-bold">PO Total</td><td class="text-right font-weight-bold text-primary" id="poTotal">₱0.00</td><td></td></tr></tfoot>
            </table>
          </div>
        </div>
        <div class="card-footer bg-white text-right">
          <a href="{{ route('peams.purchase-orders.index') }}" class="btn btn-light">Cancel</a>
          <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane mr-1"></i> Submit for Approval</button>
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
    document.querySelectorAll('#poTable tbody tr').forEach(function (row) {
      const qty = parseFloat(row.querySelector('.qty-input').value || 0);
      const cost = parseFloat(row.querySelector('.cost-input').value || 0);
      row.querySelector('.line-total').textContent = money(qty * cost);
      total += qty * cost;
    });
    document.getElementById('poTotal').textContent = money(total);
  }

  function wireRow(row) {
    row.querySelector('.item-select').addEventListener('change', function () {
      const option = this.options[this.selectedIndex];
      row.querySelector('.cost-input').value = option.dataset.cost || 0;
      recalc();
    });
    row.querySelectorAll('.qty-input, .cost-input').forEach(function (input) { input.addEventListener('input', recalc); });
    row.querySelector('.remove-line').addEventListener('click', function () {
      if (document.querySelectorAll('#poTable tbody tr').length > 1) { row.remove(); recalc(); }
    });
  }

  document.querySelectorAll('#poTable tbody tr').forEach(wireRow);

  document.getElementById('addPoLine').addEventListener('click', function () {
    const clone = document.querySelector('#poTable tbody tr').cloneNode(true);
    clone.querySelectorAll('select, input').forEach(function (field) {
      field.name = field.name.replace(/items\[\d+\]/, 'items[' + index + ']');
      if (field.classList.contains('item-select')) { field.selectedIndex = 0; }
      else if (field.classList.contains('qty-input')) { field.value = 1; }
      else { field.value = 0; }
    });
    clone.querySelector('.line-total').textContent = '₱0.00';
    document.querySelector('#poTable tbody').appendChild(clone);
    wireRow(clone);
    index++;
  });

  recalc();
});
</script>
@endsection
