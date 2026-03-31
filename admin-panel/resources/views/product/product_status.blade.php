@extends('layouts.app')

@section('title', 'Product Status')

@push('styles')
<style>
/* Page-specific styles */
.panel {
  background: var(--panel, #fff);
  border-radius: 18px;
  padding: 24px;
  box-shadow: 0 15px 40px rgba(0,0,0,.06);
}
.status-dot {
  width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 6px;
}
.status-active { background: #16a34a; }
.status-inactive { background: #ef4444; }

.product-img {
  width: 46px; height: 46px; border-radius: 10px; object-fit: cover;
  box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}

.form-switch .form-check-input {
  width: 50px; height: 26px; background-color: #d1d5db; border: none; transition: .4s;
}
.form-switch .form-check-input:checked { background-color: #16a34a; }
</style>
@endpush

@section('content')
  <div class="topbar">
    <h5><i class="bi bi-toggle-on text-primary me-2"></i>Product Status Control</h5>
    <button class="btn btn-primary btn-sm" onclick="location.reload()"><i class="bi bi-arrow-clockwise me-1"></i>Refresh</button>
  </div>

  <div class="panel mt-4">
    <div class="row g-3 align-items-center">
      <div class="col-md-6">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" id="searchProduct" class="form-control" placeholder="Search product...">
        </div>
      </div>
      <div class="col-md-3">
        <select class="form-select" id="filterStatus">
          <option value="All Status">All Status</option>
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-4"><div class="panel text-center"><h6>Total Products</h6><h3 class="fw-bold text-primary" id="cardTotal">{{ $totalProducts }}</h3></div></div>
    <div class="col-md-4"><div class="panel text-center"><h6>Active Products</h6><h3 class="fw-bold text-success" id="cardActive">{{ $activeProducts }}</h3></div></div>
    <div class="col-md-4"><div class="panel text-center"><h6>Inactive Products</h6><h3 class="fw-bold text-danger" id="cardInactive">{{ $inactiveProducts }}</h3></div></div>
  </div>

  <div class="panel mt-4">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Product</th>
            <th>Category</th>
            <th>Price</th>
            <th>Status</th>
            <th class="text-end">Toggle</th>
          </tr>
        </thead>
        <tbody>
          @foreach($products as $product)
          <tr>
            <td class="d-flex align-items-center gap-3">
              <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/46' }}" class="product-img">
              <div>
                <div class="fw-semibold product-name">{{ $product->name }}</div>
                <small class="text-muted">{{ $product->brand }}</small>
              </div>
            </td>
            <td>{{ $product->category }}</td>
            <td>₹{{ $product->sale_price }}</td>
            <td class="status-cell">
              <span class="status-dot {{ $product->status == 'Active' ? 'status-active' : 'status-inactive' }}"></span>
              <span class="status-text">{{ $product->status }}</span>
            </td>
            <td class="text-end">
              <div class="form-check form-switch d-inline-block">
                <input class="form-check-input statusToggle" type="checkbox" data-id="{{ $product->id }}" {{ $product->status == 'Active' ? 'checked' : '' }}>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.statusToggle').forEach(toggle => {
    toggle.addEventListener('change', function () {
        let productId = this.dataset.id;
        let status = this.checked ? 'Active' : 'Inactive';
        fetch("{{ route('product.status.toggle') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Content-Type": "application/json" },
            body: JSON.stringify({ id: productId, status: status })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                let row = this.closest('tr');
                let cell = row.querySelector('.status-cell');
                cell.innerHTML = `<span class="status-dot ${status == 'Active' ? 'status-active' : 'status-inactive'}"></span> <span class="status-text">${status}</span>`;
                updateCards();
            }
        });
    });
});

document.getElementById('filterStatus').addEventListener('change', function(){
    let selected = this.value;
    document.querySelectorAll("tbody tr").forEach(row => {
        let statusText = row.querySelector(".status-text").innerText.trim();
        row.style.display = (selected === "All Status" || statusText === selected) ? "" : "none";
    });
});

document.getElementById('searchProduct').addEventListener('keyup', function(){
    let value = this.value.toLowerCase();
    document.querySelectorAll("tbody tr").forEach(row => {
        let name = row.querySelector(".product-name").innerText.toLowerCase();
        row.style.display = name.includes(value) ? "" : "none";
    });
});

function updateCards() {
    let active = 0, inactive = 0;
    document.querySelectorAll('.statusToggle').forEach(t => t.checked ? active++ : inactive++);
    document.getElementById('cardActive').innerText = active;
    document.getElementById('cardInactive').innerText = inactive;
    document.getElementById('cardTotal').innerText = active + inactive;
}
</script>
@endpush
