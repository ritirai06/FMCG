<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>FMCG Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root{
  --primary:#1e40af;      /* Deep Navy Blue */
  --accent:#3b82f6;       /* Bright Blue */
  --highlight:#60a5fa;    /* Light Blue Glow */
  --bg:#f3f4f6;
  --glass:rgba(255,255,255,0.6);
  --radius:18px;
  --text-dark:#0f172a;
  --text-muted:#64748b;
}

/* BODY */
body{
  font-family:'Inter',sans-serif;
  background:linear-gradient(135deg,#dbeafe,#eef2ff);
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
  border-right:1px solid rgba(255,255,255,.15);
  box-shadow:0 10px 25px rgba(0,0,0,.3);
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
  background:rgba(255,255,255,.15);
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
.menu-item.open .submenu{
  display:flex;
  animation:fadeIn .3s ease;
}
.submenu a{
  font-size:14px;
  color:rgba(255,255,255,.8);
  padding:8px 14px;
  border-radius:10px;
  margin-bottom:4px;
}
.submenu a:hover{
  background:rgba(59,130,246,.25);
  color:#fff;
}

/* CONTENT */
.content{
  margin-left:270px;
  padding:30px;
  transition:.4s;
}
@media(max-width:992px){
  .sidebar{left:-100%;}
  .sidebar.show{left:0;}
  .content{margin-left:0;}
  .toggle-btn{
    position:fixed;top:18px;left:18px;z-index:2000;
    background:var(--accent);color:#fff;border:none;
    border-radius:10px;width:46px;height:46px;font-size:22px;
  }
}

/* TOPBAR */
.topbar{
  background:var(--glass);
  backdrop-filter:blur(14px);
  border-radius:var(--radius);
  padding:18px 24px;
  box-shadow:0 10px 40px rgba(59,130,246,.25);
  display:flex;justify-content:space-between;align-items:center;
}
.topbar h5{font-weight:700;}
.user-panel{
  display:flex;align-items:center;gap:10px;
  background:rgba(255,255,255,.7);
  border-radius:12px;padding:8px 14px;
  box-shadow:0 8px 20px rgba(59,130,246,.15);
}
.user-panel i{
  width:38px;height:38px;border-radius:10px;
  background:linear-gradient(135deg,var(--primary),var(--accent));
  display:flex;align-items:center;justify-content:center;color:#fff;
}

/* KPI */
.kpi{
  background:var(--glass);
  backdrop-filter:blur(14px);
  border-radius:var(--radius);
  padding:24px;
  box-shadow:0 15px 45px rgba(59,130,246,.2);
  transition:.3s;
  cursor:pointer;
}
.kpi:hover{transform:translateY(-6px) scale(1.02);}
.kpi span{font-size:13px;color:var(--text-muted);}
.kpi h3{font-weight:800;margin-top:4px;}
.kpi .icon{
  width:54px;height:54px;border-radius:16px;
  display:flex;align-items:center;justify-content:center;
  background:linear-gradient(135deg,var(--primary),var(--accent));
  color:#fff;font-size:22px;
}

/* ANIMATIONS */
@keyframes fadeIn{
  from{opacity:0;transform:translateY(-5px);}
  to{opacity:1;transform:translateY(0);}
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
  <div class="brand">
   @if (admin_setting() && admin_setting()->profile_image)
     <img src="{{ asset('uploads/admin/'.admin_setting()->profile_image) }}" style="width:40px;height:40px;border-radius:10px;object-fit:cover;">
   @else
     <div style="width:40px;height:40px;border-radius:10px;background:#3b82f6;display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;">A</div>
   @endif
   <span>{{ admin_setting()?->company_name ?? 'Admin Panel' }}</span>
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
    @auth
    <a href="{{ route('salary.salary_index') }}"><i class="bi bi-cash-stack"></i><span>Salary & Incentives</span></a>
    <a href="{{ route('store.store_index') }}"><i class="bi bi-shop"></i><span>Stores</span></a>
    <a href="{{ route('order_management.order_index') }}"><i class="bi bi-receipt"></i><span>Orders & Invoices</span></a>
    <a href="{{ route('report.report_index') }}"><i class="bi bi-graph-up"></i><span>Reports</span></a>
    @endauth
    <a href="{{ route('admin.settings') }}"><i class="bi bi-gear"></i><span>Settings</span></a>
  </div>
</div>