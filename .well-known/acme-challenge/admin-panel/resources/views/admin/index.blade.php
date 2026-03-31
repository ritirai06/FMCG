
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Salary & Incentive Management | FMCG Admin</title>

<!-- Bootstrap + Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

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
.content { margin-left: 250px; padding: 30px; }

/* HEADER */
.topbar {
  background: var(--glass);
  border-radius: 20px;
  padding: 18px 25px;
  display: flex; justify-content: space-between; align-items: center;
  box-shadow: 0 8px 30px rgba(0,0,0,0.1);
  backdrop-filter: blur(12px);
}
.topbar h5 { font-weight: 700; }

/* TABS */
.nav-tabs {
  background: var(--glass);
  border-radius: 15px;
  padding: 5px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.08);
  backdrop-filter: blur(10px);
}
.nav-tabs .nav-link {
  color: #334155;
  border: none;
  font-weight: 500;
  border-radius: 10px;
  transition: 0.3s;
}
.nav-tabs .nav-link.active {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff;
  box-shadow: 0 3px 10px rgba(99,102,241,0.3);
}

/* CARD */
.table-card {
  background: var(--glass);
  border-radius: 20px;
  box-shadow: 0 15px 40px rgba(0,0,0,0.08);
  padding: 25px;
  backdrop-filter: blur(12px);
  margin-top: 25px;
}
.table tbody tr:hover {
  background: rgba(99,102,241,0.08);
  transition: 0.3s;
  transform: scale(1.01);
}

/* MODAL */
.modal-content {
  border-radius: 18px;
  border: none;
  background: rgba(255,255,255,0.95);
  backdrop-filter: blur(15px);
  box-shadow: 0 10px 35px rgba(0,0,0,0.2);
}
.modal-header, .modal-footer { border: none; }
.form-control, .form-select {
  border-radius: 10px;
  border: 1px solid #e2e8f0;
}
.form-control:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 0.2rem rgba(99,102,241,0.25);
}

/* RESPONSIVE */
@media(max-width:992px){
  .sidebar{display:none;}
  .content{margin-left:0;}
}
</style>
</head>
<body>

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
<!-- CONTENT -->
<div class="content"> 
          
<form action="{{ route('admin.settings.update') }}"
method="POST"
enctype="multipart/form-data">
@csrf

<div class="row g-4">

<h6 class="fw-bold text-primary">Admin Information</h6>

<div class="col-md-6">
<label>Name</label>
<input type="text" name="name"
value="{{ $setting->name ?? '' }}"
class="form-control">
</div>

<div class="col-md-6">
<label>Email</label>
<input type="email" name="email"
value="{{ $setting->email ?? '' }}"
class="form-control">
</div>

<div class="col-md-6">
<label>Phone</label>
<input type="text" name="phone"
value="{{ $setting->phone ?? '' }}"
class="form-control">
</div>

<div class="col-md-6">
<label>Profile Image</label>
<input type="file" name="profile_image"
class="form-control">

@if(!empty($setting->profile_image))
<img src="{{ asset('uploads/admin/'.$setting->profile_image) }}"
width="80" class="mt-2">
@endif
</div>

<hr>

<h6 class="fw-bold text-primary">Company Information</h6>

<div class="col-md-6">
<input type="text" name="company_name"
value="{{ $setting->company_name ?? '' }}"
class="form-control" placeholder="Company Name">
</div>

<div class="col-md-6">
<input type="text" name="gst_number"
value="{{ $setting->gst_number ?? '' }}"
class="form-control" placeholder="GST Number">
</div>

<div class="col-md-6">
<input type="email" name="company_email"
value="{{ $setting->company_email ?? '' }}"
class="form-control" placeholder="Company Email">
</div>

<div class="col-md-6">
<input type="text" name="company_phone"
value="{{ $setting->company_phone ?? '' }}"
class="form-control" placeholder="Company Phone">
</div>

<div class="col-12">
<textarea name="company_address"
class="form-control"
placeholder="Address">{{ $setting->company_address ?? '' }}</textarea>
</div>

<hr>

<h6 class="fw-bold text-primary">Preferences</h6>

<div class="col-md-4">
<select name="currency" class="form-select">
<option {{ ($setting->currency ?? '')=='INR'?'selected':'' }}>INR</option>
<option {{ ($setting->currency ?? '')=='USD'?'selected':'' }}>USD</option>
</select>
</div>

<div class="col-md-4">
<select name="language" class="form-select">
<option {{ ($setting->language ?? '')=='English'?'selected':'' }}>English</option>
<option {{ ($setting->language ?? '')=='Hindi'?'selected':'' }}>Hindi</option>
</select>
</div>

<div class="col-md-4">
<select name="timezone" class="form-select">
<option>Asia/Kolkata</option>
</select>
</div>

</div>

<div class="text-end mt-4">
<button type="submit" class="btn btn-primary px-5">
<i class="bi bi-save"></i> Save Settings
</button>
</div>

</form>
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
