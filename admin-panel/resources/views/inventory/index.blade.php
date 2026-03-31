@extends('layouts.app')

@section('title', 'Inventory Management | FMCG Admin')

@push('styles')
<style>
.nav-tabs { border-bottom: 1px solid var(--border); margin-bottom: 0; }
.nav-tabs .nav-link { border: none; border-radius: 8px 8px 0 0; color: var(--muted); font-weight: 500; font-size: 13.5px; padding: 9px 16px; transition: .2s; }
.nav-tabs .nav-link:hover { color: var(--primary); background: var(--primary-light); }
.nav-tabs .nav-link.active { background: var(--primary); color: #fff; }
.tab-content .panel { background: var(--card-bg); border: 1px solid var(--border); border-radius: 0 0 var(--radius) var(--radius); padding: 20px; box-shadow: var(--shadow); }
</style>
@endpush

@section('page_title', 'Inventory Management')

@section('content')
  <!-- NAV TABS -->
  <ul class="nav nav-tabs" id="inventoryTabs" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#list">Inventory List</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#in">Stock In</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#out">Stock Out</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#adjust">Adjustment</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#audit">Audit Logs</button></li>
  </ul>

  <!-- TAB CONTENT -->
  <div class="tab-content">
    <!-- Inventory List -->
    <div class="tab-pane fade show active" id="list">
      <div class="panel bg-white">
        <h6 class="fw-bold mb-3">Current Inventory (Warehouse-wise)</h6>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Warehouse</th>
                <th>Product</th>
                <th>SKU</th>
                <th>Available Stock</th>
                <th>Last Updated</th>
                <th>Status</th>
              </tr>
            </thead>
           <tbody>
@foreach($inventories as $inventory)
<tr>
    <td>{{ $inventory->warehouse->name }}</td>
    <td>{{ $inventory->product->name }}</td>
    <td>{{ $inventory->product->sku }}</td>
    <td>{{ $inventory->quantity }}</td>
    <td>{{ $inventory->updated_at->format('d M Y') }}</td>
    <td>
        @if($inventory->quantity > 50)
            <span class="badge bg-success">In Stock</span>
        @elseif($inventory->quantity > 0)
            <span class="badge bg-warning">Low</span>
        @else
            <span class="badge bg-danger">Out of Stock</span>
        @endif
    </td>
</tr>
@endforeach
</tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Stock In -->
    <div class="tab-pane fade" id="in">
      <div class="panel bg-white">
        <h6 class="fw-bold mb-3">Add Stock Entry</h6>
        <form action="{{ route('inventory.stockIn') }}" method="POST" class="row g-3">
          @csrf
          <div class="col-md-4">
            <label class="form-label">Warehouse</label>
            <select name="warehouse_id" class="form-select" required>
              <option value="">Select Warehouse</option>
              @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Product</label>
            <select name="product_id" class="form-select" required>
              <option value="">Select Product</option>
              @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
              @endforeach
            </select>
            <small class="current-stock text-muted"></small>
          </div>
          <div class="col-md-4">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Supplier</label>
            <input type="text" name="supplier" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">Date</label>
            <input type="date" name="date" class="form-control">
          </div>
          <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save Entry</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Stock Out -->
    <div class="tab-pane fade" id="out">
      <div class="panel bg-white">
        <h6 class="fw-bold mb-3">Stock Out</h6>
        <form action="{{ route('inventory.stockOut') }}" method="POST" class="row g-3">
          @csrf
          <div class="col-md-4">
            <label class="form-label">Warehouse</label>
            <select name="warehouse_id" class="form-select" required>
              <option value="">Select Warehouse</option>
              @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Product</label>
            <select name="product_id" class="form-select" required>
              <option value="">Select Product</option>
              @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
              @endforeach
            </select>
            <small class="current-stock text-muted"></small>
          </div>
          <div class="col-md-4">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" required>
          </div>
          <div class="col-md-12 text-end">
            <button type="submit" class="btn btn-danger"><i class="bi bi-arrow-up-circle me-1"></i>Confirm Out</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Adjustment -->
    <div class="tab-pane fade" id="adjust">
      <div class="panel bg-white">
        <h6 class="fw-bold mb-3">Adjustment History</h6>
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead class="table-light">
              <tr>
                <th>Date</th>
                <th>Warehouse</th>
                <th>Product</th>
                <th>Type</th>
                <th>Qty</th>
                <th>Reason</th>
              </tr>
            </thead>
            <tbody>
              @forelse($adjustments as $adj)
              <tr>
                <td>{{ $adj->created_at->format('d-m-Y') }}</td>
                <td>{{ $adj->warehouse->name }}</td>
                <td>{{ $adj->product->name }}</td>
                <td>
                  @if($adj->type == 'add')
                    <span class="badge bg-success">Add</span>
                  @else
                    <span class="badge bg-danger">Remove</span>
                  @endif
                </td>
                <td>{{ $adj->quantity }}</td>
                <td>{{ $adj->reason }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center text-muted">No adjustment history</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <hr class="my-4">
        <h6 class="fw-bold mb-3">Inventory Adjustment</h6>
        <form action="{{ route('inventory.adjust') }}" method="POST" class="row g-3">
          @csrf
          <div class="col-md-4">
            <label class="form-label">Warehouse</label>
            <select name="warehouse_id" class="form-select" required>
              <option value="">Select Warehouse</option>
              @foreach($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Product</label>
            <select name="product_id" class="form-select" required>
              <option value="">Select Product</option>
              @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Adjustment Type</label>
            <select name="type" class="form-select" required>
              <option value="add">Add</option>
              <option value="remove">Remove</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Reason</label>
            <input type="text" name="reason" class="form-control" required>
          </div>
          <div class="col-12 text-end">
            <button type="submit" class="btn btn-warning"><i class="bi bi-pencil-square me-1"></i> Adjust Stock</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Audit Logs -->
    <div class="tab-pane fade" id="audit">
      <div class="panel bg-white">
        <h6 class="fw-bold mb-3">Audit Logs</h6>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead><tr><th>Date</th><th>User</th><th>Action</th><th>Details</th></tr></thead>
            <tbody>
              @forelse($auditLogs as $log)
              <tr>
                <td>{{ $log->created_at->format('d M Y') }}</td>
                <td>{{ $log->user }}</td>
                <td><span class="badge bg-primary">{{ $log->action }}</span></td>
                <td>{{ $log->details }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center text-muted">No audit records found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
function loadProductsForWarehouse(selectElement) {
    const warehouseId = selectElement.value;
    const form = selectElement.closest('form');
    const tabId = selectElement.closest('.tab-pane').id;
    const productSelect = form.querySelector('select[name="product_id"]');
    const stockHint = form.querySelector('.current-stock');

    productSelect.innerHTML = '<option value="">Loading...</option>';
    if (stockHint) stockHint.textContent = '';
    if (!warehouseId) {
        productSelect.innerHTML = '<option value="">Select Product</option>';
        return;
    }

    fetch(`{{ url('inventory/warehouse') }}/${warehouseId}/products`)
        .then(r => r.json())
        .then(items => {
            productSelect.dataset.items = JSON.stringify(items);
            productSelect.innerHTML = '<option value="">Select Product</option>';
            items.forEach(item => {
                // Stock Out: skip products with 0 stock
                if (tabId === 'out' && item.quantity <= 0) return;
                const label = item.sku
                    ? `${item.name} (${item.sku}) — Stock: ${item.quantity}`
                    : `${item.name} — Stock: ${item.quantity}`;
                productSelect.innerHTML += `<option value="${item.id}">${label}</option>`;
            });
            if (productSelect.options.length === 1) {
                productSelect.innerHTML += '<option disabled>No products available</option>';
            }
        })
        .catch(() => {
            productSelect.innerHTML = '<option value="">Error loading products</option>';
        });
}

function showCurrentStock(sel) {
    const hint = sel.closest('form').querySelector('.current-stock');
    if (!hint) return;
    hint.textContent = '';
    if (!sel.value) return;
    let items = [];
    try { items = JSON.parse(sel.dataset.items || '[]'); } catch (_) {}
    const found = items.find(i => i.id == sel.value);
    if (found) hint.textContent = `Current stock in warehouse: ${found.quantity}`;
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('select[name="warehouse_id"]').forEach(el => {
        el.addEventListener('change', function () { loadProductsForWarehouse(this); });
    });
    document.querySelectorAll('select[name="product_id"]').forEach(el => {
        el.addEventListener('change', function () { showCurrentStock(this); });
    });
});
</script>
@endpush
