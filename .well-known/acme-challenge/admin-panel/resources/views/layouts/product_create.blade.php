<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add Product | FMCG Admin</title>

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
    /* Sidebar gradient reused for branding on right side */
    --sidebar-grad: linear-gradient(180deg,#0f172a,#1e3a8a,#1d4ed8);
    --sidebar-grad-solid: #1e3a8a;
}

/* BODY */
body{
    font-family:'Inter',sans-serif;
    background:linear-gradient(135deg,#e0e7ff,#f9fafb);
    color:var(--text-dark);
    min-height:100vh;
    overflow-x:hidden;
}

/* SIDEBAR (MUST REMAIN EXACTLY AS-IS visually) */
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

/* CONTENT (RIGHT SIDE ENHANCEMENTS) */
.content { margin-left: 240px; padding: 40px; display: flex; justify-content: center; }
.inner-content {
    width:100%;
    max-width:1200px;
    animation: fadeInUp .45s ease both;
}
@keyframes fadeInUp { from { opacity:0; transform: translateY(10px);} to { opacity:1; transform: translateY(0);} }

/* Top area */
.topbar {
    background: var(--panel);
    border-radius: var(--radius);
    padding: 14px 20px;
    display: flex; align-items: center; justify-content: space-between;
    box-shadow: 0 8px 25px rgba(2,6,23,.1);
    width: 100%;
    margin-bottom: 18px;
}

/* Tabs: use sidebar gradient for active states */
.product-tabs { margin-bottom: 18px; }
.nav-tabs .nav-link {
    border: none;
    color: #475569;
    padding: 12px 18px;
    border-radius: 12px;
    margin-right: 6px;
    transition: all .22s ease;
    background: linear-gradient(90deg, rgba(255,255,255,0.9), rgba(255,255,255,0.6));
    box-shadow: 0 6px 18px rgba(15,23,42,0.04);
}
.nav-tabs .nav-link.active {
    color: #fff;
    background: var(--sidebar-grad);
    box-shadow: 0 12px 30px rgba(2,6,23,0.18);
    transform: translateY(-4px);
    border-radius: 12px;
}

/* Section Card (refined) */
.section-card{
    background: linear-gradient(180deg, rgba(255,255,255,0.98), var(--panel));
    border-radius: 14px;
    padding: 18px;
    margin-bottom: 16px;
    box-shadow: 0 8px 24px rgba(2,6,23,0.06);
    transition: transform .22s ease, box-shadow .22s ease;
    border: 1px solid rgba(15,23,42,0.03);
    position:relative;
}
.section-card:hover{
    transform: translateY(-6px);
    box-shadow: 0 24px 60px rgba(2,6,23,0.08);
}
.section-head{
    display:flex;align-items:center;gap:12px;margin-bottom:12px;
}
.section-head .icon{
    width:44px;height:44px;border-radius:10px;
    display:flex;align-items:center;justify-content:center;
    background: var(--sidebar-grad);
    color:#fff;font-size:18px;flex:0 0 44px;
    box-shadow:0 8px 20px rgba(2,6,23,0.12);
}
.section-title{font-weight:700;color:#0f172a;}
.section-sub{font-size:13px;color:var(--text-muted);margin-left:auto}

/* Form controls */
.form-label { font-weight: 600; color: #334155; font-size: 13px; }
.form-control, .form-select {
    border-radius: 10px; border: 1px solid #e6edf6; padding: 10px 12px;
    background: #fff; color:var(--text-dark);
}
.form-control:focus, .form-select:focus {
    border-color: var(--sidebar-grad-solid);
    box-shadow: 0 0 0 6px rgba(30,58,138,0.06);
}

/* Price breakdown */
.price-breakdown {
    display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-top:10px;
}
.price-chip {
    background: linear-gradient(90deg,#fbfdff,#ffffff);
    border:1px solid #e6edf6;padding:10px 12px;border-radius:12px;font-weight:700;
    color:var(--text-dark);
    min-width:140px;text-align:center;
}

/* Uploader enhancements */
.uploader {
    border-radius:12px;border:1px dashed #e6edf6;padding:20px;background:linear-gradient(180deg,#fbfdff,#ffffff);
    text-align:center;cursor:pointer;transition:all .18s;
}
.uploader.dragover { background: linear-gradient(90deg,#eef2ff,#ffffff);box-shadow:0 12px 30px rgba(30,58,138,0.06);transform:translateY(-3px); border-color: rgba(30,58,138,0.25); }

/* Thumbs improved */
.thumb-row{display:flex;flex-wrap:wrap;gap:10px;margin-top:12px}
.thumb{width:86px;height:86px;border-radius:12px;overflow:hidden;position:relative;background:#fff;border:1px solid #eef2ff;box-shadow:0 8px 22px rgba(15,23,42,0.04)}
.thumb img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .28s}
.thumb:hover img{transform:scale(1.06)}
.thumb .rm{position:absolute;top:6px;right:6px;background:var(--sidebar-grad-solid);color:#fff;border-radius:8px;padding:6px;cursor:pointer}

/* Tags / chips */
.tags-input { display:flex;flex-wrap:wrap;gap:8px;align-items:center;min-height:44px;padding:6px;border-radius:10px;border:1px solid #e6edf6;background:#fff; }
.tag { background: linear-gradient(90deg,#eef2ff,#f8fafc); padding:6px 10px;border-radius:999px;font-weight:600;color:#334155;display:flex;gap:8px;align-items:center;font-size:13px;box-shadow: 0 6px 16px rgba(15,23,42,0.04); }
.tag .remove { cursor:pointer;color:#94a3b8;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;padding:2px }

/* Bulk area */
.bulk-card .uploader { padding:28px; border-radius:14px; background: linear-gradient(180deg,#ffffff,#fbfdff); border:1px solid rgba(30,58,138,0.06); }
.bulk-actions { display:flex;align-items:center;gap:10px;flex-wrap:wrap; }

/* Preview table */
.preview-wrapper { max-height:260px; overflow:auto; border-radius:10px; border:1px solid #e6edf6; background:#fff; box-shadow:0 6px 20px rgba(15,23,42,0.03); }
.preview-table { width:100%; border-collapse:collapse; }
.preview-table th, .preview-table td { padding:8px 10px; border-bottom:1px solid #f1f5f9; font-size:13px; text-align:left; }
.row-error { background: rgba(255,100,100,0.06); }
.badge-error { background:#fee2e2;color:#991b1b;border-radius:999px;padding:4px 8px;font-weight:700; }

/* Sticky action bar */
.sticky-actions {
    position: sticky; bottom: 18px; z-index: 60;
    display:flex; justify-content:flex-end; gap:12px; align-items:center;
    background: transparent; margin-top:10px;
}

/* Bulk results */
.bulk-results {
    margin-top:12px;
}
.results-table th { background: var(--sidebar-grad); color:#fff; }

/* Responsive tweaks */
@media(max-width:992px){
    .sidebar { display:none; }
    .content { margin-left:0; padding:20px; }
    .topbar,.form-card { width:100%; padding:20px; }
    .nav-tabs .nav-link { padding:10px 12px; margin-right:6px; }
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
     
        <a href="{{ route('dashboard') }}" class="active"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>

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