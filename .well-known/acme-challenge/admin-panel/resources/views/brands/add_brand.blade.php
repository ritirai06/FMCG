
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Brand | FMCG Admin</title>

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
.content {margin-left:260px;padding:30px;}
.topbar {
  background:var(--panel);
  border-radius:var(--radius);
  padding:16px 24px;
  display:flex;align-items:center;justify-content:space-between;
  box-shadow:0 10px 40px rgba(2,6,23,.1);
}
.topbar h5 {font-weight:700;}

/* FORM CARD */
.brand-card {
  background:var(--panel);
  border-radius:var(--radius);
  padding:30px;
  max-width:650px;
  margin:40px auto;
  box-shadow:0 15px 40px rgba(0,0,0,0.1);
  backdrop-filter:blur(12px);
}
.brand-card h5 {
  font-weight:700;
  margin-bottom:20px;
}

/* LOGO UPLOAD */
.logo-upload {
  border:2px dashed #cbd5e1;
  border-radius:12px;
  text-align:center;
  padding:25px;
  transition:.3s;
  cursor:pointer;
}
.logo-upload:hover {
  background:rgba(99,102,241,0.05);
  border-color:var(--primary);
}
.logo-upload img {
  width:80px;height:80px;border-radius:50%;object-fit:cover;
  box-shadow:0 2px 6px rgba(0,0,0,0.25);
}
.logo-upload small {color:#6b7280;}

/* BUTTONS */
.btn-primary {
  background:linear-gradient(135deg,#6366f1,#8b5cf6);
  border:none;
}
.btn-primary:hover {
  background:linear-gradient(135deg,#4f46e5,#7c3aed);
}
.btn-cancel {
  background:#e5e7eb;
  color:#374151;
}

/* RESPONSIVE */
@media(max-width:992px){
  .sidebar{display:none;}
  .content{margin-left:0;padding:20px;}
  .brand-card{margin-top:20px;padding:20px;}
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
  <div class="topbar">
    <h5><i class="bi bi-plus-circle me-2 text-primary"></i>Add Brand</h5>
    <button class="btn btn-light btn-sm" onclick="window.location.href='brand-list.html'">
      <i class="bi bi-arrow-left"></i> Back to List
    </button>
  </div>

  <!-- ADD BRAND FORM -->
  <div class="brand-card">
    <h5><i class="bi bi-shop-window me-2 text-primary"></i>Brand Details</h5>
    <form action="{{ route('brands.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
      <div class="logo-upload mb-4" id="logoUpload">
        <img id="logoPreview" src="https://via.placeholder.com/80" alt="Brand Logo">
        <div class="mt-2 small">Click to upload brand logo</div>
        <input type="file" name="logo" id="logoInput" hidden accept="image/*">
      </div>

      <div class="mb-3">
        <label class="form-label">Brand Name</label>
        <input type="text" name="name" class="form-control" placeholder="Enter brand name" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea  name="description"  class="form-control" rows="3" placeholder="Short description..."></textarea>
      </div>

      <div class="mb-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option>Active</option>
          <option>Inactive</option>
        </select>
      </div>

      <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-cancel">Cancel</button>
        <button type="submit" class="btn btn-primary">Save Brand</button>
      </div>
    </form>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
const logoUpload = document.getElementById('logoUpload');
const logoInput = document.getElementById('logoInput');
const logoPreview = document.getElementById('logoPreview');

logoUpload.onclick = ()=>logoInput.click();
logoInput.onchange = e=>{
  const file = e.target.files[0];
  if(file) logoPreview.src = URL.createObjectURL(file);
};
</script>
<script>
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('show');
}
function toggleSubmenu(el){
  el.parentElement.classList.toggle('open');
}
</script>
</body>
</html>
