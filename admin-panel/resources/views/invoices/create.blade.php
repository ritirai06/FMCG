@extends('layouts.app')

@section('title', 'Create Invoice')

@push('styles')
<style>
.inv-form-card { background:#fff; border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); padding:24px; margin-bottom:20px; }
.item-row td { vertical-align:middle; padding:6px 8px; }
.item-row input, .item-row select { font-size:13px; }
#itemsTable tfoot td { padding:6px 8px; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center mb-4">
  <a href="{{ route('invoices.web.index') }}" class="btn btn-sm btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
  <h4 class="mb-0 fw-bold">Create New Invoice</h4>
  <span class="badge bg-primary ms-3 fs-6">{{ $nextNo }}</span>
</div>

@if($errors->any())
  <div class="alert alert-danger">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('invoices.web.store') }}" id="invoiceForm">
@csrf

<div class="row g-4">
  {{-- Left column --}}
  <div class="col-lg-8">
    <div class="inv-form-card">
      <h6 class="fw-bold mb-3">Invoice Details</h6>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Party / Store</label>
          <select class="form-select" name="party_id">
            <option value="">Select party...</option>
            @foreach($parties as $p)
              <option value="{{ $p->id }}" @selected(old('party_id') == $p->id)>{{ $p->store_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Link to Order (optional)</label>
          <input type="number" class="form-control" name="order_id" value="{{ old('order_id') }}" placeholder="Order ID">
        </div>
        <div class="col-md-6">
          <label class="form-label">Invoice Date</label>
          <input type="date" class="form-control" name="date" value="{{ old('date', now()->toDateString()) }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Due Date</label>
          <input type="date" class="form-control" name="due_date" value="{{ old('due_date') }}">
        </div>
        <div class="col-12">
          <label class="form-label">Notes</label>
          <textarea class="form-control" name="notes" rows="2" placeholder="Optional notes...">{{ old('notes') }}</textarea>
        </div>
      </div>
    </div>

    {{-- Items --}}
    <div class="inv-form-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0">Items</h6>
        <button type="button" class="btn btn-sm btn-outline-primary" id="addItemBtn"><i class="bi bi-plus-lg"></i> Add Item</button>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered" id="itemsTable">
          <thead class="table-light">
            <tr>
              <th style="width:35%">Product / Name</th>
              <th style="width:15%">Qty</th>
              <th style="width:20%">Price</th>
              <th style="width:20%">Total</th>
              <th style="width:10%"></th>
            </tr>
          </thead>
          <tbody id="itemsBody">
            <tr class="item-row">
              <td>
                <select class="form-select item-product" name="items[0][item_id]">
                  <option value="">Custom item...</option>
                  @foreach($products as $p)
                    <option value="{{ $p->id }}" data-price="{{ $p->selling_price ?? $p->price ?? 0 }}">{{ $p->product_name ?? $p->name }}</option>
                  @endforeach
                </select>
                <input type="text" class="form-control mt-1 item-name" name="items[0][item_name]" placeholder="Or type item name">
              </td>
              <td><input type="number" class="form-control item-qty" name="items[0][quantity]" value="1" min="0.01" step="0.01"></td>
              <td><input type="number" class="form-control item-price" name="items[0][price]" value="0" min="0" step="0.01"></td>
              <td><input type="number" class="form-control item-total" name="items[0][total]" value="0" readonly></td>
              <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>
            </tr>
          </tbody>
          <tfoot>
            <tr><td colspan="3" class="text-end fw-semibold">Subtotal</td><td colspan="2"><span id="subtotalDisplay">0.00</span></td></tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  {{-- Right column --}}
  <div class="col-lg-4">
    <div class="inv-form-card">
      <h6 class="fw-bold mb-3">Summary</h6>
      <div class="mb-3">
        <label class="form-label">Tax (Rs)</label>
        <input type="number" class="form-control" name="tax" id="taxInput" value="{{ old('tax', 0) }}" min="0" step="0.01">
      </div>
      <div class="mb-3">
        <label class="form-label">Discount (Rs)</label>
        <input type="number" class="form-control" name="discount" id="discountInput" value="{{ old('discount', 0) }}" min="0" step="0.01">
      </div>
      <hr>
      <div class="d-flex justify-content-between fw-bold fs-5">
        <span>Total</span>
        <span id="grandTotal">Rs 0.00</span>
      </div>
      <hr>
      <button type="submit" class="btn btn-primary w-100 mt-2"><i class="bi bi-check-lg me-1"></i> Create Invoice</button>
      <a href="{{ route('invoices.web.index') }}" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
    </div>
  </div>
</div>
</form>
@endsection

@push('scripts')
<script>
let rowIndex = 1;

function recalc() {
  let subtotal = 0;
  document.querySelectorAll('.item-row').forEach(row => {
    const qty   = parseFloat(row.querySelector('.item-qty').value) || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    const total = qty * price;
    row.querySelector('.item-total').value = total.toFixed(2);
    subtotal += total;
  });
  const tax      = parseFloat(document.getElementById('taxInput').value) || 0;
  const discount = parseFloat(document.getElementById('discountInput').value) || 0;
  document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2);
  document.getElementById('grandTotal').textContent = 'Rs ' + (subtotal + tax - discount).toFixed(2);
}

function bindRow(row) {
  row.querySelector('.item-qty').addEventListener('input', recalc);
  row.querySelector('.item-price').addEventListener('input', recalc);
  row.querySelector('.remove-row').addEventListener('click', () => { row.remove(); recalc(); });
  row.querySelector('.item-product').addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    const price = parseFloat(opt.dataset.price || 0);
    if (price > 0) row.querySelector('.item-price').value = price.toFixed(2);
    recalc();
  });
}

document.querySelectorAll('.item-row').forEach(bindRow);

document.getElementById('addItemBtn').addEventListener('click', () => {
  const tbody = document.getElementById('itemsBody');
  const tpl   = tbody.querySelector('.item-row').cloneNode(true);
  tpl.querySelectorAll('input').forEach(i => { if (i.type !== 'button') i.value = i.name.includes('quantity') ? 1 : 0; });
  tpl.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
  // Re-index names
  tpl.querySelectorAll('[name]').forEach(el => {
    el.name = el.name.replace(/\[\d+\]/, '[' + rowIndex + ']');
  });
  rowIndex++;
  tbody.appendChild(tpl);
  bindRow(tpl);
  recalc();
});

document.getElementById('taxInput').addEventListener('input', recalc);
document.getElementById('discountInput').addEventListener('input', recalc);
recalc();
</script>
@endpush
