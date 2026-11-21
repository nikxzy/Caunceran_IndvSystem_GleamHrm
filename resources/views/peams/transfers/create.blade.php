@extends('layouts.master')

@section('content')
<div class="content-header pt-3">
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
      <div>
        <h3 class="peams-page-title m-0">New Stock Transfer</h3>
        <p class="text-muted small mb-0">Request stock to move between hubs; dispatch deducts source stock, receipt adds destination stock</p>
      </div>
      <a href="{{ route('peams.transfers.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Transfers</a>
    </div>
  </div>
</div>

<div class="content">
  <div class="container-fluid">
    @include('layouts.partials.error-message')

    @php
      $stockLookup = $stocks->keyBy('item_id');
    @endphp

    <form method="POST" action="{{ route('peams.transfers.store') }}" id="transferForm">
      @csrf
      <div class="card peams-card mb-4">
        <div class="card-header"><h5 class="card-title m-0"><i class="fas fa-route text-primary mr-2"></i> Transfer Route</h5></div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-5 form-group"><label>From hub *</label>
              <select name="from_hub_id" class="form-control" required>
                @foreach($hubs as $hub)<option value="{{ $hub->id }}" @if((int)$fromHub === (int)$hub->id) selected @endif>{{ $hub->name }}</option>@endforeach
              </select>
            </div>
            <div class="col-md-2 form-group d-flex align-items-center justify-content-center"><i class="fas fa-arrow-right fa-lg text-primary"></i></div>
            <div class="col-md-5 form-group"><label>To hub *</label>
              <select name="to_hub_id" class="form-control" required>
                @foreach($hubs as $hub)<option value="{{ $hub->id }}" @if((int)$fromHub !== (int)$hub->id) selected @endif>{{ $hub->name }}</option>@endforeach
              </select>
            </div>
            <div class="col-12 form-group"><label>Notes</label><textarea name="notes" class="form-control" rows="2" placeholder="Reason for transfer, transport arrangement...">{{ old('notes') }}</textarea></div>
          </div>
        </div>
      </div>

      <div class="card peams-card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title m-0"><i class="fas fa-boxes text-primary mr-2"></i> Items to Move</h5>
          <button type="button" class="btn btn-sm btn-outline-primary" id="addTransferLine"><i class="fas fa-plus mr-1"></i>Add Item</button>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-modern mb-0" id="transferTable">
              <thead><tr><th style="min-width:300px;">Item</th><th class="text-center">Stock at Source</th><th style="width:160px;">Quantity</th><th></th></tr></thead>
              <tbody>
                <tr class="transfer-row">
                  <td>
                    <select name="items[0][item_id]" class="form-control item-select" required>
                      <option value="">Select item</option>
                      @foreach($items as $item)
                        @php $available = isset($stockLookup[$item->id]) ? $stockLookup[$item->id]->quantity - $stockLookup[$item->id]->reserved_quantity : 0; @endphp
                        <option value="{{ $item->id }}" data-stock="{{ max(0, $available) }}">{{ $item->name }} ({{ $item->sku }})</option>
                      @endforeach
                    </select>
                  </td>
                  <td class="text-center stock-cell"><span class="badge badge-soft-secondary">—</span></td>
                  <td><input type="number" min="1" name="items[0][quantity]" class="form-control" value="1" required></td>
                  <td><button type="button" class="btn btn-sm btn-outline-danger remove-line"><i class="fas fa-times"></i></button></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer bg-white text-right">
          <a href="{{ route('peams.transfers.index') }}" class="btn btn-light">Cancel</a>
          <button class="btn btn-primary" type="submit"><i class="fas fa-paper-plane mr-1"></i> Submit Transfer Request</button>
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

  function wireRow(row) {
    row.querySelector('.item-select').addEventListener('change', function () {
      const option = this.options[this.selectedIndex];
      const stock = parseInt(option.dataset.stock || 0, 10);
      row.querySelector('.stock-cell').innerHTML = option.value
        ? '<span class="badge badge-soft-' + (stock > 0 ? 'success' : 'danger') + '">' + stock + '</span>'
        : '<span class="badge badge-soft-secondary">—</span>';
    });
    row.querySelector('.remove-line').addEventListener('click', function () {
      if (document.querySelectorAll('#transferTable tbody tr').length > 1) { row.remove(); }
    });
  }

  document.querySelectorAll('#transferTable tbody tr').forEach(wireRow);

  document.getElementById('addTransferLine').addEventListener('click', function () {
    const clone = document.querySelector('#transferTable tbody tr').cloneNode(true);
    clone.querySelectorAll('select, input').forEach(function (field) {
      field.name = field.name.replace(/items\[\d+\]/, 'items[' + index + ']');
      if (field.classList.contains('item-select')) { field.selectedIndex = 0; }
      else { field.value = 1; }
    });
    clone.querySelector('.stock-cell').innerHTML = '<span class="badge badge-soft-secondary">—</span>';
    document.querySelector('#transferTable tbody').appendChild(clone);
    wireRow(clone);
    index++;
  });
});
</script>
@endsection
