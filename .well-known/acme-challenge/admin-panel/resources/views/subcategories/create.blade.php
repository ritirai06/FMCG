
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Assign Products | FMCG Admin</title>

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

/* CONTENT */
.content{margin-left:260px;padding:30px;}
.topbar{
  background:var(--panel);
  border-radius:var(--radius);
  padding:16px 24px;
  display:flex;align-items:center;justify-content:space-between;
  box-shadow:0 10px 40px rgba(2,6,23,.1);
}

/* ASSIGN SECTION */
.assign-card{
  background:var(--panel);
  border-radius:var(--radius);
  box-shadow:0 15px 40px rgba(0,0,0,.06);
  margin-top:24px;
  padding:30px;
  backdrop-filter:blur(10px);
}
.assign-card h6{font-weight:700;color:var(--primary-dark);}
select, input[type="text"]{border-radius:10px!important;}

/* DUAL BOX */
.dual-box{
  display:flex;gap:30px;margin-top:20px;flex-wrap:wrap;
}
.dual-box .box{
  flex:1;min-width:280px;
  background:white;
  border-radius:16px;
  box-shadow:0 8px 20px rgba(0,0,0,.08);
  padding:16px;
  height:380px;
  display:flex;flex-direction:column;
}
.dual-box .box h6{font-weight:600;margin-bottom:10px;}
.product-list{
  flex:1;overflow-y:auto;
  border:1px solid #e2e8f0;
  border-radius:12px;
  padding:10px;
}
.product-item{
  background:#f8fafc;
  border-radius:10px;
  padding:8px 12px;
  margin-bottom:8px;
  display:flex;justify-content:space-between;
  align-items:center;
  transition:0.3s;
}
.product-item:hover{background:#eef2ff;transform:translateX(5px);}
.transfer-btns{
  display:flex;flex-direction:column;gap:12px;
  justify-content:center;align-items:center;
}
.transfer-btns button{
  width:45px;height:45px;border:none;border-radius:50%;
  background:linear-gradient(135deg,#6366f1,#8b5cf6);
  color:white;font-size:20px;
  transition:0.3s;
}
.transfer-btns button:hover{transform:scale(1.1);background:linear-gradient(135deg,#4f46e5,#7c3aed);}

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

<!-- CONTENT -->
<!-- CONTENT -->
<div class="content">
  <div class="topbar">
    <h5>
      <i class="bi bi-folder-plus me-2 text-primary"></i>
      Add SubCategory
    </h5>
  </div>

  <div class="assign-card">

    <div class="row g-4">

      <!-- RIGHT SIDE INFO PANEL (now left) -->
      <div class="col-lg-5">
        <div class="card shadow-sm border-0 rounded-4 p-4">
          <h6 class="mb-3 text-primary">
            <i class="bi bi-info-circle me-2"></i>
            SubCategory Overview
          </h6>

          <div class="mb-3">
            <small class="text-muted">Total SubCategories</small>
             <h3 class="fw-bold">{{ $total ?? 0 }}</h3>
          </div>

          <div class="mb-3">
            <small class="text-success">Active</small>
             <h3 class="fw-bold text-success">{{ $active ?? 0 }}</h3>
          </div>

          <div>
            <small class="text-danger">Inactive</small>
             <h3 class="fw-bold text-danger">{{ $inactive ?? 0 }}</h3>
          </div>
        </div>
      </div>

      <!-- RIGHT SIDE FORM (moved) -->
      <div class="col-lg-7">  
        @if($errors->any())
          <div class="alert alert-danger">
            @foreach($errors->all() as $error)
              <div>{{ $error }}</div>
            @endforeach
          </div>
        @endif

        <form action="{{ route('subcategories.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <h6 class="mb-3">SubCategory Details</h6>

          <div class="mb-3">
            <label class="form-label">Select Category</label>
            <select name="category_id" class="form-control" required>
              <option value="">-- Select Category --</option>
              @if(isset($categories))
                @foreach($categories as $category)
                  <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
              @endif
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">SubCategory Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter subcategory name" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Write short description..."></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Upload Image</label>
            <input type="file" name="image" class="form-control">
          </div>

          <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" name="status" checked>
            <label class="form-check-label">Active</label>
          </div>

          <div class="text-end">
            <button type="reset" class="btn btn-light me-2">Cancel</button>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-1"></i> Save SubCategory
            </button>
          </div>
        </form>
      </div>

    </div>

  </div>
</div>

<script>
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('show');
}
function toggleSubmenu(el){
  el.parentElement.classList.toggle('open');
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
