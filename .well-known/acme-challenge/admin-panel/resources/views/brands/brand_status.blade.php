
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Brand Status | FMCG Admin</title>

<!-- Bootstrap + Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

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
.content{margin-left:260px;padding:30px;}
.topbar{
  background:var(--panel);
  border-radius:var(--radius);
  padding:16px 24px;
  display:flex;align-items:center;justify-content:space-between;
  box-shadow:0 10px 40px rgba(2,6,23,.1);
}
.topbar h5{font-weight:700;}

/* Table Card */
.table-card{
  background:var(--panel);
  border-radius:var(--radius);
  box-shadow:0 15px 40px rgba(0,0,0,.06);
  margin-top:24px;
  overflow:hidden;
  backdrop-filter:blur(10px);
  padding:20px;
}
.table thead{
  background:rgba(99,102,241,0.08);
  font-weight:600;
}
.table tbody tr{
  transition:all 0.3s ease;
}
.table tbody tr:hover{
  background:rgba(99,102,241,.08);
  transform:scale(1.01);
}
.brand-logo{
  width:45px;height:45px;border-radius:12px;object-fit:cover;
  box-shadow:0 3px 6px rgba(0,0,0,0.15);
}
.status-active{color:#22c55e;font-weight:600;}
.status-inactive{color:#ef4444;font-weight:600;}

/* Switch */
.form-switch .form-check-input{
  width:3em;
  height:1.5em;
  cursor:pointer;
}
.form-switch .form-check-input:checked{
  background-color:#22c55e;
  border-color:#22c55e;
}

/* Modal */
.modal-content{
  border-radius:18px;
  box-shadow:0 10px 40px rgba(0,0,0,.15);
}
.modal-header{
  border-bottom:none;
}
.modal-footer{
  border-top:none;
}
.btn-primary{
  background:linear-gradient(135deg,#6366f1,#8b5cf6);
  border:none;
}
.btn-primary:hover{
  background:linear-gradient(135deg,#4f46e5,#7c3aed);
}
.btn-light{
  background:#f1f5f9;
  border:none;
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
    <i class="bi bi-box-seam"></i>
    <span>Admin Panel</span>
  </div>

  <div class="menu">
   
    <a href="dashboard.html" class="active"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>

    <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Products</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="products.html"><i class="bi bi-list-ul"></i> Product List</a>
        <a href="product/add_product.html"><i class="bi bi-plus-circle"></i> Add Product</a>
        <a href="product/product_status.html"><i class="bi bi-toggle-on"></i> Product Status</a>
        <a href="product/product_margin.html"><i class="bi bi-cash-coin"></i> Auto Margin View</a>
      </div>
    </div>
 
  <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Brands</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="brands.html"><i class="bi bi-list-ul"></i> Brands List</a>
        <a href="brands/add_brand.html"><i class="bi bi-plus-circle"></i> Add Brands</a>
        <a href="brands/brads_status.html"><i class="bi bi-toggle-on"></i>Brands Status </a>
     
      </div>
    </div>
     <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Categories</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="categories.html"><i class="bi bi-list-ul"></i> Categories List</a>
        <a href="category/assign-products.html"><i class="bi bi-plus-circle"></i> Assign Product</a>
        <a href="category/sub_cate.html"><i class="bi bi-toggle-on"></i>Sub Categories </a>
     
      </div>
    </div>
	 <div class="menu-item">
      <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
        <i class="bi bi-box"></i><span>Warehouses</span>
        <i class="bi bi-chevron-down ms-auto small"></i>
      </a>
      <div class="submenu">
        <a href="warehouses.html"><i class="bi bi-list-ul"></i> Warehouses List</a>
        <a href="warehouse/status_warehouse.html"><i class="bi bi-plus-circle"></i> Status Warehouses</a>
       
     
      </div>
    </div>
   
    <a href="inventory.html"><i class="bi bi-layers"></i><span>Inventory</span></a>
    <a href="territory.html"><i class="bi bi-geo-alt"></i><span>Cities & Localities</span></a>
    <a href="salary_management.html"><i class="bi bi-person-badge"></i><span>Sales Persons</span></a>
    <a href="delivery_partners.html"><i class="bi bi-truck"></i><span>Delivery Partners</span></a>
    <a href="attendance_management.html"><i class="bi bi-calendar-check"></i><span>Attendance</span></a>
    <a href="salary_management.html"><i class="bi bi-cash-stack"></i><span>Salary & Incentives</span></a>
    <a href="store_management.html"><i class="bi bi-shop"></i><span>Stores</span></a>
    <a href="order_management.html"><i class="bi bi-receipt"></i><span>Orders & Invoices</span></a>
    <a href="reports_analytics.html"><i class="bi bi-graph-up"></i><span>Reports</span></a>
    <a href="admin_settings"><i class="bi bi-gear"></i><span>Settings</span></a>
  </div>
</div>


<!-- CONTENT -->
<div class="content">
    <div class="row mb-4">

    <!-- Total -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 p-4 text-center">
            <h6 class="text-muted">Total Brands</h6>
            <h2 id="totalCount" class="fw-bold">{{ $total }}</h2>
        </div>
    </div>

    <!-- Active -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 p-4 text-center">
            <h6 class="text-success">Active Brands</h6>
            <h2 id="activeCount" class="fw-bold text-success">
                {{ $active }}
            </h2>
        </div>
    </div>

    <!-- Inactive -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-4 p-4 text-center">
            <h6 class="text-danger">Inactive Brands</h6>
            <h2 id="inactiveCount" class="fw-bold text-danger">
                {{ $inactive }}
            </h2>
        </div>
    </div>

</div>

  <div class="topbar">
    <h5><i class="bi bi-toggle-on me-2 text-primary"></i>Brand Activation Management</h5>
  </div>

  <div class="table-card">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Logo</th>
            <th>Brand Name</th>
            <th>Status</th>
            <th class="text-center">Toggle</th>
          </tr>
        </thead>
        <tbody>
@foreach($brands as $brand)
<tr id="row-{{ $brand->id }}">
    <td>
        <img src="{{ asset('storage/'.$brand->logo) }}" 
             class="brand-logo">
    </td>

    <td><strong>{{ $brand->name }}</strong></td>

    <td id="status-{{ $brand->id }}">
        @if($brand->status == 'Active')
            <span class="status-active">Active</span>
        @else
            <span class="status-inactive">Inactive</span>
        @endif
    </td>

    <td class="text-center">
        <div class="form-check form-switch">
            <input class="form-check-input toggleSwitch"
                   type="checkbox"
                   data-id="{{ $brand->id }}"
                   {{ $brand->status == 'Active' ? 'checked' : '' }}>
        </div>
    </td>
</tr>
@endforeach
</tbody>

      </table>
    </div>
  </div>
</div>

<!-- CONFIRM MODAL -->
<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-primary"><i class="bi bi-exclamation-circle me-2"></i>Confirm Action</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p id="confirmText"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmBtn">Yes, Proceed</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
let selectedBrandId = null;
let selectedCheckbox = null;

const confirmModal = new bootstrap.Modal(
    document.getElementById('confirmModal')
);

const confirmText = document.getElementById('confirmText');
const confirmBtn = document.getElementById('confirmBtn');

/* ===============================
   STEP 1 → Toggle Click (Open Popup)
==================================*/
document.querySelectorAll('.toggleSwitch').forEach(switchBtn => {

    switchBtn.addEventListener('change', function(e){

        e.preventDefault();

        selectedBrandId = this.dataset.id;
        selectedCheckbox = this;

        let action = this.checked ? "activate" : "deactivate";

        confirmText.innerHTML =
            `Are you sure you want to <strong>${action}</strong> this brand?`;

        confirmModal.show();
    });

});


/* ===============================
   STEP 2 → Confirm Button Click
==================================*/
confirmBtn.addEventListener('click', function(){

    fetch("{{ route('brands.toggleStatus') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ id: selectedBrandId })
    })
    .then(res => res.json())
    .then(data => {

        if(data.success){

            let statusCell =
                document.getElementById("status-"+selectedBrandId);

            if(data.status === "Active"){
                statusCell.innerHTML =
                    '<span class="status-active">Active</span>';
                selectedCheckbox.checked = true;
            } else {
                statusCell.innerHTML =
                    '<span class="status-inactive">Inactive</span>';
                selectedCheckbox.checked = false;
            }

            // 🔥 UPDATE CARDS
            document.getElementById('totalCount').innerText = data.total;
            document.getElementById('activeCount').innerText = data.active;
            document.getElementById('inactiveCount').innerText = data.inactive;

        }

        confirmModal.hide();
    });

});
</script>

</body>
</html>
