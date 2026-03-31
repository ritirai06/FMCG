
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Product Status | FMCG Admin</title>

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

/* CONTENT */
.content{margin-left:260px;padding:30px;}
.topbar{
  background:var(--panel);
  border-radius:var(--radius);
  padding:16px 24px;
  display:flex;align-items:center;justify-content:space-between;
  box-shadow:0 10px 40px rgba(2,6,23,.1);
}
.topbar h5{font-weight:700;}

/* PANEL */
.panel{
  background:var(--panel);
  border-radius:var(--radius);
  padding:24px;
  box-shadow:0 15px 40px rgba(0,0,0,.06);
}

/* STATUS DOT */
.status-dot{
  width:10px;height:10px;border-radius:50%;display:inline-block;margin-right:6px;
}
.status-active{background:var(--success);}
.status-inactive{background:var(--danger);}

/* TABLE */
.table thead{background:rgba(99,102,241,0.08);}
.table tbody tr{
  transition:all .3s;
}
.table tbody tr:hover{
  background:rgba(99,102,241,0.05);
  transform:scale(1.01);
}
.product-img{
  width:46px;height:46px;border-radius:10px;object-fit:cover;
  box-shadow:0 3px 6px rgba(0,0,0,0.15);
}

/* SWITCH */
.form-switch .form-check-input{
  width:50px;height:26px;
  background-color:#d1d5db;
  border:none;
  transition:.4s;
}
.form-switch .form-check-input:checked{
  background-color:var(--success);
}

/* PAGINATION */
.pagination .page-link{
  border:none;border-radius:10px;
  margin:0 2px;color:var(--primary-dark);
  box-shadow:0 2px 6px rgba(0,0,0,.1);
}
.pagination .page-item.active .page-link{
  background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;
}

/* RESPONSIVE */
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


<!-- MAIN CONTENT -->
<div class="content">
  <div class="topbar">
    <h5><i class="bi bi-toggle-on text-primary me-2"></i>Product Status Control</h5>
    <button class="btn btn-primary btn-sm"><i class="bi bi-arrow-clockwise me-1"></i>Refresh</button>
  </div>

  <!-- FILTERS -->
  <div class="panel mt-3">
    <div class="row g-3 align-items-center">
      <div class="col-md-6">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" id="searchProduct" class="form-control" placeholder="Search product...">
        </div>
      </div>
      <div class="col-md-3">
        <select class="form-select" id="filterStatus">
          <option>All Status</option>
          <option>Active</option>
          <option>Inactive</option>
        </select>
      </div>
    </div>
  </div>
<div class="row mt-4">
    <div class="col-md-4">
        <div class="panel text-center">
            <h6>Total Products</h6>
            <h3 class="fw-bold text-primary">{{ $totalProducts }}</h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="panel text-center">
            <h6>Active Products</h6>
            <h3 class="fw-bold text-success">{{ $activeProducts }}</h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="panel text-center">
            <h6>Inactive Products</h6>
            <h3 class="fw-bold text-danger">{{ $inactiveProducts }}</h3>
        </div>
    </div>
</div>

  <!-- TABLE -->
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
        <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/46' }}" 
             class="product-img">
        <div>
            <div class="fw-semibold">{{ $product->name }}</div>
            <small class="text-muted">{{ $product->brand }}</small>
        </div>
    </td>

    <td>{{ $product->category }}</td>
    <td>₹{{ $product->sale_price }}</td>

    <td>
        <span class="status-dot {{ $product->status == 'Active' ? 'status-active' : 'status-inactive' }}"></span>
        {{ $product->status }}
    </td>

    <td class="text-end">
        <div class="form-check form-switch d-inline-block">
            <input 
                class="form-check-input statusToggle"
                type="checkbox"
                data-id="{{ $product->id }}"
                {{ $product->status == 'Active' ? 'checked' : '' }}>
        </div>
    </td>
</tr>
@endforeach
</tbody>

      </table>
    </div>

    <!-- PAGINATION -->
    <nav class="mt-3">
      <ul class="pagination justify-content-end mb-0">
        <li class="page-item disabled"><a class="page-link">Prev</a></li>
        <li class="page-item active"><a class="page-link">1</a></li>
        <li class="page-item"><a class="page-link">2</a></li>
        <li class="page-item"><a class="page-link">Next</a></li>
      </ul>
    </nav>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.statusToggle').forEach(toggle => {
    toggle.addEventListener('change', function () {

        let productId = this.dataset.id;
        let status = this.checked ? 'Active' : 'Inactive';

        fetch("{{ route('product.status.toggle') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                id: productId,
                status: status
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){

                // Update row status text without reload
                let row = this.closest('tr');
                let statusCell = row.querySelector('td:nth-child(4)');

                statusCell.innerHTML =
                    `<span class="status-dot ${status == 'Active' ? 'status-active' : 'status-inactive'}"></span> ${status}`;

                updateCards(); // update summary cards
            }
        });

    });
});

</script>
<script>document.getElementById('filterStatus').addEventListener('change', function(){

    let selected = this.value;

    document.querySelectorAll("tbody tr").forEach(row => {

        let statusText = row.querySelector("td:nth-child(4)").innerText.trim();

        if(selected === "All Status"){
            row.style.display = "";
        }
        else if(statusText === selected){
            row.style.display = "";
        }
        else{
            row.style.display = "none";
        }

    });

});
document.getElementById('searchProduct').addEventListener('keyup', function(){

    let value = this.value.toLowerCase();

    document.querySelectorAll("tbody tr").forEach(row => {

        let productName = row.querySelector(".fw-semibold").innerText.toLowerCase();

        if(productName.includes(value)){
            row.style.display = "";
        } else {
            row.style.display = "none";
        }

    });

});
function updateCards() {

    let activeCount = 0;
    let inactiveCount = 0;

    document.querySelectorAll('.statusToggle').forEach(toggle => {
        if(toggle.checked){
            activeCount++;
        } else {
            inactiveCount++;
        }
    });

    document.querySelector('.text-success').innerText = activeCount;
    document.querySelector('.text-danger').innerText = inactiveCount;
    document.querySelector('.text-primary').innerText = activeCount + inactiveCount;
}

</script>
</body>
</html>
