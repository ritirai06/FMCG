@extends('layouts.app')

@section('title', 'Product List | FMCG Admin')

@push('styles')
<style>
.table-card { background:#fff; border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); margin-top:0; overflow:hidden; }
.table thead { background:var(--bg); }
.status-dot { width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:6px; }
.status-active { background:#16A34A; }
.status-inactive { background:#DC2626; }
.product-img { width:40px; height:40px; border-radius:8px; object-fit:cover; flex-shrink:0; }
.modal-header { background:var(--primary) !important; color:#fff !important; border-radius:calc(var(--radius) - 1px) calc(var(--radius) - 1px) 0 0 !important; }
.modal-header .btn-close { filter:invert(1); }

/* Mobile product cards */
.product-mobile-card {
  display:none;
  background:#fff; border:1px solid var(--border); border-radius:var(--radius);
  padding:12px; margin-bottom:10px; box-shadow:var(--shadow);
}
@media (max-width: 767px) {
  .product-desktop-table { display:none; }
  .product-mobile-card { display:flex; gap:12px; align-items:flex-start; }
  .product-mobile-info { flex:1; min-width:0; }
  .product-mobile-name { font-weight:700; font-size:13px; margin-bottom:2px; }
  .product-mobile-meta { font-size:11px; color:var(--muted); }
  .product-mobile-price { font-weight:700; font-size:14px; color:var(--primary); }
  .product-mobile-actions { display:flex; gap:6px; margin-top:8px; }
}
</style>
@endpush

@section('page_title', 'Product List')
@section('navbar_right')
  <a href="{{ route('product.create') }}" class="btn btn-gradient btn-sm">
    <i class="bi bi-plus-circle me-1"></i>Add Product
  </a>
@endsection

@section('content')
  <div class="table-card p-3">
    <!-- Desktop Table -->
    <div class="table-responsive product-desktop-table">
      <table class="table align-middle">
        <thead class="table-light">
          <tr>
            <th>Product</th>
            <th>SKU</th>
            <th>Category</th>
            <th>Price</th>
            <th>Margin</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
        @forelse($products as $product)
          <tr>
            <td>
              <div class="d-flex align-items-center gap-3">
                <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/45' }}" class="product-img">
                <div>
                  <div class="fw-semibold">{{ $product->name }}</div>
                  <small class="text-muted">{{ $product->brand }}</small>
                </div>
              </div>
            </td>
            <td>{{ $product->sku }}</td>
            <td>{{ $product->category }}</td>
            <td>₹{{ $product->sale_price }}</td>
            <td>₹{{ $product->margin }}</td>
            <td>
              <span class="status-dot {{ $product->status == 'Active' ? 'status-active' : 'status-inactive' }}"></span>
              {{ $product->status }}
            </td>
            <td class="text-end">
              <button class="btn btn-light btn-sm editBtn" data-id="{{ $product->id }}"><i class="bi bi-pencil text-primary"></i></button>
              <form action="{{ route('product.delete', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')">
                @csrf @method('DELETE')
                <button class="btn btn-light btn-sm"><i class="bi bi-trash text-danger"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center text-muted py-4">No products found</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <!-- Mobile Cards -->
    @forelse($products as $product)
    <div class="product-mobile-card">
      <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/45' }}" class="product-img">
      <div class="product-mobile-info">
        <div class="product-mobile-name">{{ $product->name }}</div>
        <div class="product-mobile-meta">{{ $product->brand }} · {{ $product->category }} · {{ $product->sku }}</div>
        <div class="product-mobile-price">₹{{ $product->sale_price }}</div>
        <div style="margin-top:4px;">
          <span class="status-dot {{ $product->status == 'Active' ? 'status-active' : 'status-inactive' }}"></span>
          <span style="font-size:12px;">{{ $product->status }}</span>
        </div>
        <div class="product-mobile-actions">
          <button class="btn btn-light btn-sm editBtn" data-id="{{ $product->id }}"><i class="bi bi-pencil text-primary"></i> Edit</button>
          <form action="{{ route('product.delete', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete?')">
            @csrf @method('DELETE')
            <button class="btn btn-light btn-sm"><i class="bi bi-trash text-danger"></i></button>
          </form>
        </div>
      </div>
    </div>
    @empty
    @endforelse
  </div>

<!-- EDIT PRODUCT MODAL -->
<div class="modal fade" id="editProductModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Product</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <form id="editProductForm">
          @csrf
          <input type="hidden" id="editId">

          <div class="row g-3">
            <div class="col-md-6">
              <label>Product Name</label>
              <input type="text" name="name" id="editName" class="form-control">
            </div>

            <div class="col-md-6">
              <label>SKU</label>
              <input type="text" name="sku" id="editSKU" class="form-control">
            </div>

            <div class="col-md-4">
              <label>Brand</label>
              <input type="text" name="brand" id="editBrand" class="form-control">
            </div>

            <div class="col-md-4">
              <label>Category</label>
              <input type="text" name="category" id="editCategory" class="form-control">
            </div>

            <div class="col-md-4">
              <label>Unit</label>
              <select name="unit" id="editUnit" class="form-select">
                <option value="PCS">PCS</option>
                <option value="BOX">BOX</option>
                <option value="BUNCH">BUNCH</option>
                <option value="KG">KG</option>
              </select>
            </div>

            <div class="col-md-4">
              <label>Purchase Price</label>
              <input type="number" name="purchase_price" id="editPurchase" class="form-control" step="0.01">
            </div>

            <div class="col-md-4">
              <label>Sell Price</label>
              <input type="number" name="sell_price" id="editSell" class="form-control" step="0.01">
            </div>

            <div class="col-md-4">
              <label>MRP</label>
              <input type="number" name="mrp" id="editMRP" class="form-control" step="0.01">
            </div>

            <div class="col-md-3">
              <label>GST %</label>
              <input type="number" name="gst_percent" id="editGST" class="form-control" step="0.01">
            </div>

            <div class="col-md-3">
              <label>HSN Code</label>
              <input type="text" name="hsn_code" id="editHSN" class="form-control">
            </div>

            <div class="col-md-6">
              <label>Status</label>
              <select name="status" id="editStatus" class="form-select">
                <option>Active</option>
                <option>Inactive</option>
              </select>
            </div>
          </div>

          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              Cancel
            </button>
            <button type="submit" class="btn btn-gradient">
              Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));

document.querySelectorAll('.editBtn').forEach(button => {
    button.addEventListener('click', function () {
        let id = this.getAttribute('data-id');

        fetch("{{ url('/product') }}/" + id + "/fetch")
            .then(response => response.json())
            .then(data => {
                document.getElementById('editId').value = data.id;
                document.getElementById('editName').value = data.name;
                document.getElementById('editSKU').value = data.sku;
                document.getElementById('editBrand').value = data.brand;
                document.getElementById('editCategory').value = data.category;
                document.getElementById('editUnit').value = data.unit || 'PCS';
                document.getElementById('editPurchase').value = data.purchase_price;
                document.getElementById('editSell').value = data.sell_price || data.sale_price;
                document.getElementById('editMRP').value = data.mrp;
                document.getElementById('editGST').value = data.gst_percent || 0;
                document.getElementById('editHSN').value = data.hsn_code || '';
                document.getElementById('editStatus').value = data.status;

                editModal.show();
            });
    });
});

document.getElementById('editProductForm').addEventListener('submit', function(e){
    e.preventDefault();

    let id = document.getElementById('editId').value;
    const fd = new FormData(this);

    fetch('/product/update/' + id, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: fd
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            alert('Updated Successfully');
            location.reload();
        }
    });
});
</script>
@endpush
