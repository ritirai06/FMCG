<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Product List | FMCG Admin</title>

<!-- Bootstrap + Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root{
  --primary:#1e3a8a;
  --accent:#3b82f6;
  --highlight:#60a5fa;
  --bg:#f1f5f9;
  --panel:#ffffff;
  --radius:18px;
  --text-dark:#0f172a;
  --text-muted:#6b7280;
}

/* BODY */
body{
  font-family:'Inter',sans-serif;
  background:linear-gradient(135deg,#e0e7ff,#f9fafb);
  color:var(--text-dark);
  min-height:100vh;
  overflow-x:hidden;
}

/* SIDEBAR */
.sidebar{
  position:fixed;
  top:0;left:0;height:100%;width:270px;
  background:linear-gradient(180deg,#0f172a,#1e3a8a,#1d4ed8);
  color:#fff;padding:28px 18px;
  box-shadow:0 8px 25px rgba(0,0,0,.4);
  overflow-y:auto;z-index:1000;
  transition:all .3s ease;
}
.sidebar::-webkit-scrollbar{width:6px;}
.sidebar::-webkit-scrollbar-thumb{background:#3b82f6;border-radius:6px;}

/* BRAND */
.brand{
  display:flex;align-items:center;gap:12px;
  font-size:20px;font-weight:800;margin-bottom:30px;
}
.brand i{
  width:46px;height:46px;border-radius:12px;
  background:rgba(255,255,255,.1);
  display:flex;align-items:center;justify-content:center;
  font-size:22px;color:#fff;
}

/* MENU */
.menu a{
  display:flex;align-items:center;gap:12px;
  color:rgba(255,255,255,.85);
  padding:10px 14px;
  border-radius:12px;
  text-decoration:none;
  font-weight:500;
  transition:.3s;
  margin-bottom:6px;
}
.menu a:hover{
  background:rgba(59,130,246,.15);
  transform:translateX(6px);
  color:#fff;
  box-shadow:inset 3px 0 0 var(--highlight);
}
.menu a.active{
  background:rgba(59,130,246,.25);
  box-shadow:inset 4px 0 0 var(--highlight);
  color:#fff;
}

/* SUBMENU */
.menu-item .submenu{
  display:none;
  flex-direction:column;
  margin-left:36px;
  padding-left:10px;
  border-left:2px solid rgba(255,255,255,.15);
  margin-top:4px;
}
.menu-item.open .submenu{display:flex;animation:fadeIn .3s ease;}
.submenu a{
  font-size:14px;
  color:rgba(255,255,255,.8);
  padding:8px 14px;
  border-radius:10px;
  margin-bottom:4px;
}
.submenu a:hover{background:rgba(59,130,246,.25);color:#fff;}

/* Content */
.content {margin-left:260px;padding:30px;}
.topbar {
  background:var(--panel);
  border-radius:var(--radius);
  padding:16px 24px;
  display:flex;align-items:center;justify-content:space-between;
  box-shadow:0 10px 40px rgba(2,6,23,.1);
}
.topbar h5{font-weight:700;}

/* Table */
.table-card {
  background:var(--panel);
  border-radius:var(--radius);
  box-shadow:0 15px 40px rgba(0,0,0,.06);
  margin-top:24px;
  overflow:hidden;
}
.table thead{background:rgba(99,102,241,0.08);font-weight:600;}
.table tbody tr:hover{background:rgba(99,102,241,.08);transform:scale(1.01);transition:all .3s;}
.status-dot{width:10px;height:10px;border-radius:50%;display:inline-block;margin-right:6px;}
.status-active{background:#22c55e;}
.status-inactive{background:#ef4444;}
.product-img{width:45px;height:45px;border-radius:12px;object-fit:cover;box-shadow:0 3px 6px rgba(0,0,0,0.15);}

/* Modal Improved UI */
.modal-content {
  border-radius:22px !important;
  background:rgba(255,255,255,0.95);
  backdrop-filter:blur(12px);
  border:none;
  box-shadow:0 15px 45px rgba(0,0,0,0.2);
}
.modal-header {
  background:linear-gradient(135deg,#6d28d9,#6366f1);
  color:#fff;
  border-radius:22px 22px 0 0;
  box-shadow:0 4px 15px rgba(99,102,241,0.4);
}
.modal-title i {
  background:#fff2;
  padding:6px;
  border-radius:8px;
}
.modal-body label {
  font-weight:600;
  color:#374151;
  font-size:14px;
}
.form-control, .form-select {
  border-radius:10px;
  padding:8px 10px;
  font-size:14px;
  border:1px solid #e5e7eb;
}
.form-control:focus, .form-select:focus {
  box-shadow:0 0 0 0.2rem rgba(99,102,241,.2);
  border-color:#6366f1;
}
#previewEditImg {
  width:90px;height:90px;
  border-radius:15px;
  object-fit:cover;
  box-shadow:0 2px 8px rgba(0,0,0,0.15);
  border:3px solid #f1f5f9;
}
.modal-footer button {
  border-radius:10px;
  font-weight:600;
}
.btn-gradient {
  background:linear-gradient(135deg,#6366f1,#8b5cf6);
  color:#fff;
}
.btn-gradient:hover {
  background:linear-gradient(135deg,#5850ec,#7c3aed);
}

/* Responsive */
@media(max-width:992px){
  .sidebar{display:none;}
  .content{margin-left:0;}
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
  <div class="brand">
   <img src="{{ asset('uploads/admin/'.admin_setting()->profile_image) }}" style="width:40px;height:40px;border-radius:10px;object-fit:cover;">
   <span>{{ admin_setting()->company_name ?? 'Admin Panel' }}</span>
  </div>

  <div class="menu">
   
    <a href="{{ route('dashboard') }}" class="active"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>

    <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Products</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="{{ route('product.index') }}"><i class="bi bi-list-ul"></i> Product List</a>
        <a href="{{ route('product.create') }}"><i class="bi bi-plus-circle"></i> Add Product</a>
        <a href="{{ route('product.status') }}"><i class="bi bi-toggle-on"></i> Product Status</a>
        <a href="#"><i class="bi bi-cash-coin"></i> Auto Margin View</a>
      </div>
    </div>
 
  <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Brands</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="{{ route('brands.index') }}"><i class="bi bi-list-ul"></i> Brands List</a>
        <a href="{{ route('brands.create') }}"><i class="bi bi-plus-circle"></i> Add Brands</a>
        <a href="{{ route('brands.status') }}"><i class="bi bi-toggle-on"></i>Brands Status </a>
     
      </div>
    </div>
     <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Categories</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="{{ route('categories.index') }}"><i class="bi bi-list-ul"></i> Categories List</a>
        <a href="{{ route('categories.create') }}"><i class="bi bi-plus-circle"></i> Add categories</a>
        <a href="{{ route('categories.status') }}"><i class="bi bi-toggle-on"></i> Categories Status</a>
        <a href="{{ route('subcategories.index') }}"><i class="bi bi-list-ul"></i> SubCategories</a>
        <a href="{{ route('subcategories.create') }}"><i class="bi bi-plus-circle"></i> Add SubCategory</a>
      </div>
    </div>
	 <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Warehouses</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="{{ route('warehouse.create') }}"><i class="bi bi-list-ul"></i> Warehouses List</a>
        <a href="{{ route('warehouse.status') }}"><i class="bi bi-plus-circle"></i> Status Warehouses</a>
       
     
      </div>
    </div>
   
    <a href="{{ route('inventory.index') }}"><i class="bi bi-layers"></i><span>Inventory</span></a>
    <a href="{{ route('cities.index') }}"><i class="bi bi-geo-alt"></i><span>Cities & Localities</span></a>
    <a href="{{ route('sales.person.index') }}"><i class="bi bi-person-badge"></i><span>Sales Persons</span></a>
    <a href="{{ route('delivery.index') }}"><i class="bi bi-truck"></i><span>Delivery Persons</span></a>
    <a href="{{ route('attendance.index') }}"><i class="bi bi-calendar-check"></i><span>Attendance</span></a>
    <a href="{{ route('salary.salary_index') }}"><i class="bi bi-cash-stack"></i><span>Salary & Incentives</span></a>
    <a href="{{ route('store.store_index') }}"><i class="bi bi-shop"></i><span>Stores</span></a>
    <a href="{{ route('order_management.order_index') }}"><i class="bi bi-receipt"></i><span>Orders & Invoices</span></a>
    <a href="{{ route('report.report_index') }}"><i class="bi bi-graph-up"></i><span>Reports</span></a>
    <a href="{{ route('admin.settings') }}"><i class="bi bi-gear"></i><span>Settings</span></a>
  </div>
</div>


<!-- CONTENT -->
<div class="content">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold">
      <i class="bi bi-list-ul me-2 text-primary"></i>Product List
    </h5>

    <a href="{{ route('product.create') }}" class="btn btn-primary btn-sm">
      <i class="bi bi-plus-circle me-1"></i>Add Product
    </a>
  </div>

  <div class="table-card p-3">
    <div class="table-responsive">
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
            <td class="d-flex align-items-center gap-3">
              <img
                src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/45' }}"
                class="product-img">

              <div>
                <div class="fw-semibold">{{ $product->name }}</div>
                <small class="text-muted">{{ $product->brand }}</small>
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
              <!-- EDIT -->
              <button
  class="btn btn-light btn-sm editBtn"
  data-id="{{ $product->id }}">
  <i class="bi bi-pencil text-primary"></i>
</button>


              <!-- DELETE -->
              <form action="{{ route('product.delete', $product->id) }}"
                    method="POST"
                    class="d-inline"
                    onsubmit="return confirm('Are you sure you want to delete this product?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-light btn-sm">
                  <i class="bi bi-trash text-danger"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-4">
              No products found
            </td>
          </tr>
        @endforelse
        </tbody>

      </table>
    </div>
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

    <div class="col-md-6">
      <label>Brand</label>
      <input type="text" name="brand" id="editBrand" class="form-control">
    </div>

    <div class="col-md-6">
      <label>Category</label>
      <input type="text" name="category" id="editCategory" class="form-control">
    </div>

    <div class="col-md-4">
      <label>Purchase Price</label>
      <input type="number" name="purchase_price" id="editPurchase" class="form-control">
    </div>

    <div class="col-md-4">
      <label>Sale Price</label>
      <input type="number" name="sale_price" id="editSale" class="form-control">
    </div>

    <div class="col-md-4">
      <label>MRP</label>
      <input type="number" name="mrp" id="editMRP" class="form-control">
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
</div>
<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('show');
}
function toggleSubmenu(el){
  el.parentElement.classList.toggle('open');
}
</script>
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
                document.getElementById('editPurchase').value = data.purchase_price;
                document.getElementById('editSale').value = data.sale_price;
                document.getElementById('editMRP').value = data.mrp;
                document.getElementById('editStatus').value = data.status;

                editModal.show();
            });
    });
});
</script>
<script>
document.getElementById('editProductForm').addEventListener('submit', function(e){
    e.preventDefault();

    let id = document.getElementById('editId').value;

    fetch('/product/update/' + id, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: new FormData(this)
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            alert('Updated Successfully');
            location.reload(); // refresh table
        }
    });
});
</script>

</body>
</html>
