<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Admin Dashboard')</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --primary: #2563EB;
  --primary-dark: #1D4ED8;
  --primary-light: #EFF6FF;
  --sidebar-bg: #1E293B;
  --sidebar-hover: rgba(37,99,235,.15);
  --sidebar-active: rgba(37,99,235,.25);
  --sidebar-border: rgba(255,255,255,.08);
  --bg: #F8FAFC;
  --card-bg: #FFFFFF;
  --text: #0F172A;
  --muted: #64748B;
  --border: #E2E8F0;
  --radius: 10px;
  --sidebar-w: 250px;
  --topbar-h: 60px;
  --shadow: 0 1px 3px rgba(0,0,0,.08), 0 1px 2px rgba(0,0,0,.06);
  --shadow-md: 0 4px 6px rgba(0,0,0,.07), 0 2px 4px rgba(0,0,0,.06);
}

*, *::before, *::after { box-sizing: border-box; }

body {
  font-family: 'Inter', sans-serif;
  background: var(--bg);
  color: var(--text);
  min-height: 100vh;
  overflow-x: hidden;
  font-size: 14px;
}

/* ── SIDEBAR ── */
.sidebar {
  position: fixed; top: 0; left: 0; height: 100%; width: var(--sidebar-w);
  background: var(--sidebar-bg);
  color: #fff; padding: 0;
  border-right: 1px solid var(--sidebar-border);
  box-shadow: 2px 0 8px rgba(0,0,0,.12);
  overflow-y: auto; z-index: 1040;
  transition: transform .3s ease;
  display: flex; flex-direction: column;
}
.sidebar::-webkit-scrollbar { width: 4px; }
.sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 4px; }

.sidebar-header {
  display: flex; align-items: center; gap: 10px;
  padding: 16px 14px; border-bottom: 1px solid var(--sidebar-border);
  flex-shrink: 0;
}
.sidebar-logo {
  width: 34px; height: 34px; border-radius: 8px;
  background: var(--primary); display: flex; align-items: center;
  justify-content: center; font-size: 17px; color: #fff; flex-shrink: 0;
}
.sidebar-title { font-size: 14px; font-weight: 700; color: #fff; line-height: 1.2; }
.sidebar-subtitle { font-size: 11px; color: rgba(255,255,255,.45); }

.sidebar-nav { padding: 10px 8px; flex: 1; }

.nav-label {
  font-size: 10px; font-weight: 700; letter-spacing: .8px;
  text-transform: uppercase; color: rgba(255,255,255,.35);
  padding: 10px 8px 4px; display: block;
}

.nav-link-item {
  display: flex; align-items: center; gap: 10px;
  color: rgba(255,255,255,.75); padding: 9px 10px;
  border-radius: 8px; text-decoration: none;
  font-weight: 500; font-size: 13px;
  transition: all .2s; margin-bottom: 2px;
}
.nav-link-item i { width: 18px; text-align: center; font-size: 15px; flex-shrink: 0; }
.nav-link-item:hover { background: var(--sidebar-hover); color: #fff; }
.nav-link-item.active { background: var(--sidebar-active); color: #fff; box-shadow: inset 3px 0 0 var(--primary); }

/* Submenu */
.menu-item { margin-bottom: 2px; }
.menu-toggle {
  display: flex; align-items: center; gap: 10px;
  color: rgba(255,255,255,.75); padding: 9px 10px;
  border-radius: 8px; text-decoration: none;
  font-weight: 500; font-size: 13px;
  transition: all .2s; cursor: pointer; width: 100%;
  background: none; border: none;
}
.menu-toggle:hover { background: var(--sidebar-hover); color: #fff; }
.menu-toggle.active { background: var(--sidebar-active); color: #fff; box-shadow: inset 3px 0 0 var(--primary); }
.menu-toggle i:first-child { width: 18px; text-align: center; font-size: 15px; flex-shrink: 0; }
.submenu-arrow { margin-left: auto; font-size: 11px; transition: transform .3s; }

.submenu {
  max-height: 0; overflow: hidden;
  margin-left: 28px; padding-left: 10px;
  border-left: 1.5px solid rgba(255,255,255,.1);
  transition: max-height .35s ease;
}
.menu-item.open .submenu { max-height: 400px; }
.menu-item.open .submenu-arrow { transform: rotate(180deg); }
.submenu a {
  display: flex; align-items: center; gap: 8px;
  font-size: 12.5px; color: rgba(255,255,255,.65);
  padding: 7px 10px; border-radius: 6px;
  text-decoration: none; transition: all .2s; margin-bottom: 1px;
}
.submenu a:hover { background: var(--sidebar-hover); color: #fff; }
.submenu a.active { background: var(--sidebar-active); color: #fff; font-weight: 600; }

.sidebar-divider { border-color: var(--sidebar-border); margin: 8px 10px; }

/* ── TOPBAR ── */
.topbar {
  position: sticky; top: 0; z-index: 1030;
  background: var(--card-bg);
  border-bottom: 1px solid var(--border);
  box-shadow: var(--shadow);
  padding: 0 20px 0 20px;
  height: var(--topbar-h);
  display: flex; justify-content: space-between; align-items: center;
  gap: 10px;
}
.topbar-left { display: flex; align-items: center; gap: 10px; min-width: 0; }
.topbar h5 { font-weight: 700; font-size: 15px; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.topbar-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }

.user-panel {
  display: flex; align-items: center; gap: 8px;
  padding: 5px 10px; border-radius: 8px;
  border: 1px solid var(--border); background: var(--bg);
}
.user-avatar {
  width: 30px; height: 30px; border-radius: 8px;
  background: var(--primary); display: flex; align-items: center;
  justify-content: center; color: #fff; font-size: 13px; flex-shrink: 0;
}
.user-name { font-weight: 600; font-size: 13px; line-height: 1.2; }
.user-email { font-size: 11px; color: var(--muted); }

/* ── HAMBURGER ── */
.toggle-btn {
  display: none;
  background: none; border: none; color: var(--text);
  font-size: 22px; padding: 4px 6px; cursor: pointer;
  border-radius: 6px; flex-shrink: 0;
  transition: background .15s;
}
.toggle-btn:hover { background: var(--border); }

/* ── CONTENT ── */
.content { margin-left: var(--sidebar-w); min-height: 100vh; transition: margin-left .3s; }
.content-body { padding: 20px; }

/* ── SIDEBAR OVERLAY ── */
.sidebar-overlay {
  display: none; position: fixed; inset: 0;
  background: rgba(0,0,0,.45); z-index: 1039;
}
.sidebar-overlay.show { display: block; }

/* ── CARDS ── */
.card {
  border: 1px solid var(--border) !important;
  border-radius: var(--radius) !important;
  box-shadow: var(--shadow) !important;
  background: var(--card-bg);
}
.card-header {
  border-radius: calc(var(--radius) - 1px) calc(var(--radius) - 1px) 0 0 !important;
  font-weight: 600; background: var(--primary) !important;
  color: #fff !important;
  border-bottom: 1px solid var(--border) !important;
  padding: 12px 16px !important;
}
.card-header * { color: #fff !important; }

/* ── KPI CARDS ── */
.kpi {
  background: var(--card-bg);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 16px;
  box-shadow: var(--shadow);
  transition: box-shadow .2s, transform .2s;
}
.kpi:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
.kpi span { font-size: 12px; color: var(--muted); font-weight: 500; }
.kpi h3 { font-weight: 700; margin-top: 4px; font-size: 22px; }
.kpi .icon {
  width: 42px; height: 42px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  background: var(--primary-light); color: var(--primary); font-size: 19px;
  flex-shrink: 0;
}

/* ── FILTER CARD ── */
.filter-card {
  background: var(--card-bg);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 14px 16px;
  box-shadow: var(--shadow);
  margin-bottom: 16px;
}

/* ── TABLE CARD ── */
.table-card {
  background: var(--card-bg);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 16px;
  box-shadow: var(--shadow);
  margin-bottom: 16px;
}
.table-card h6 { font-weight: 700; color: var(--text); margin-bottom: 14px; }
.table { font-size: 13px; }
.table th { font-weight: 600; color: var(--muted); font-size: 11.5px; text-transform: uppercase; letter-spacing: .4px; border-bottom: 1px solid var(--border) !important; padding: 10px 12px; white-space: nowrap; }
.table td { padding: 10px 12px; border-bottom: 1px solid var(--border) !important; vertical-align: middle; color: var(--text); }
.table tbody tr:hover { background: var(--primary-light); }
.table thead tr { background: var(--bg); }

/* ── BUTTONS ── */
.btn-primary { background: var(--primary) !important; border-color: var(--primary) !important; font-weight: 500; border-radius: 8px; }
.btn-primary:hover { background: var(--primary-dark) !important; border-color: var(--primary-dark) !important; }
.btn-outline-primary { color: var(--primary) !important; border-color: var(--primary) !important; font-weight: 500; border-radius: 8px; }
.btn-outline-primary:hover { background: var(--primary) !important; color: #fff !important; }
.btn-gradient {
  background: var(--primary); color: #fff; border: none;
  border-radius: 8px; padding: 7px 14px; font-weight: 500;
  transition: background .2s, transform .15s; font-size: 13px;
}
.btn-gradient:hover { background: var(--primary-dark); color: #fff; transform: translateY(-1px); }
.btn { border-radius: 8px; font-size: 13px; }
.btn-sm { padding: 4px 10px; font-size: 12px; }

/* ── FORMS ── */
.form-control, .form-select {
  border: 1px solid var(--border) !important;
  border-radius: 8px !important;
  padding: 8px 12px !important;
  font-size: 13px !important;
  transition: border-color .2s, box-shadow .2s;
  background: #fff;
}
.form-control:focus, .form-select:focus {
  border-color: var(--primary) !important;
  box-shadow: 0 0 0 3px rgba(37,99,235,.1) !important;
  outline: none;
}
.form-label { font-size: 12px; font-weight: 600; color: var(--muted); margin-bottom: 5px; }

/* ── BADGES ── */
.badge { font-weight: 500; font-size: 11px; border-radius: 6px; padding: 3px 8px; }

/* ── MODALS ── */
.modal-content {
  border: 1px solid var(--border) !important;
  border-radius: 12px !important;
  box-shadow: 0 20px 40px rgba(0,0,0,.12) !important;
}
.modal-header {
  border-bottom: 1px solid var(--border) !important;
  padding: 14px 18px !important;
}
.modal-title { font-weight: 700; font-size: 15px; }
.modal-footer { border-top: 1px solid var(--border) !important; padding: 12px 18px !important; }

/* ── ALERTS ── */
.alert { border-radius: 8px; font-size: 13px; border: none; }

/* ── MOBILE BOTTOM NAV ── */
.mobile-bottom-nav {
  display: none;
  position: fixed; bottom: 0; left: 0; right: 0; z-index: 1050;
  background: var(--card-bg);
  border-top: 1px solid var(--border);
  box-shadow: 0 -2px 12px rgba(0,0,0,.08);
  padding: 6px 0 max(6px, env(safe-area-inset-bottom));
}
.mobile-bottom-nav .nav-items {
  display: flex; justify-content: space-around; align-items: center;
}
.mobile-bottom-nav .nav-item {
  display: flex; flex-direction: column; align-items: center; gap: 2px;
  padding: 4px 8px; border-radius: 8px; text-decoration: none;
  color: var(--muted); font-size: 10px; font-weight: 600;
  transition: color .15s; min-width: 52px; text-align: center;
}
.mobile-bottom-nav .nav-item i { font-size: 20px; }
.mobile-bottom-nav .nav-item.active { color: var(--primary); }
.mobile-bottom-nav .nav-item:hover { color: var(--primary); }

/* ── RESPONSIVE ── */
@media (max-width: 991.98px) {
  .sidebar { transform: translateX(-100%); }
  .sidebar.show { transform: translateX(0); }
  .content { margin-left: 0; }
  .topbar { padding: 0 14px; }
  .toggle-btn { display: flex; align-items: center; justify-content: center; }
  .content-body { padding: 14px; }
}

@media (max-width: 767.98px) {
  .mobile-bottom-nav { display: block; }
  .content-body { padding: 12px 10px 80px; }
  .kpi h3 { font-size: 18px; }
  .kpi { padding: 12px; }
  .table-card, .filter-card { padding: 12px; }
  .topbar h5 { font-size: 14px; }
  /* Hide user email on small screens */
  .user-email { display: none; }
  /* Stack filter rows */
  .filter-card .row > [class*="col-"] { margin-bottom: 8px; }
}

@media (max-width: 575.98px) {
  .content-body { padding: 10px 8px 80px; }
  .modal-dialog { margin: 8px; }
  .modal-content { border-radius: 10px !important; }
  /* Make tables scrollable on mobile */
  .table-responsive { -webkit-overflow-scrolling: touch; }
  /* Compact table on mobile */
  .table th, .table td { padding: 8px 10px; font-size: 12px; }
  .btn-sm { padding: 3px 8px; font-size: 11px; }
}

/* ── UTILITY ── */
.fw-600 { font-weight: 600; }
.text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
@stack('styles')
</head>
<body>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
@include('layouts.partials.sidebar')

<!-- MAIN CONTENT -->
<div class="content" id="mainContent">
  @hasSection('navbar')
    @yield('navbar')
  @else
    @include('layouts.partials.navbar')
  @endif
  <div class="content-body">
    @yield('content')
  </div>
</div>

<!-- MOBILE BOTTOM NAV -->
@auth
<nav class="mobile-bottom-nav">
  <div class="nav-items">
    @if(auth()->user()->role === 'admin')
      <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i><span>Home</span>
      </a>
      <a href="{{ route('orders.index') }}" class="nav-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
        <i class="bi bi-receipt"></i><span>Orders</span>
      </a>
      <a href="{{ route('product.index') }}" class="nav-item {{ request()->routeIs('product.*') ? 'active' : '' }}">
        <i class="bi bi-box-seam"></i><span>Products</span>
      </a>
      <a href="{{ route('store.store_index') }}" class="nav-item {{ request()->routeIs('store.*') ? 'active' : '' }}">
        <i class="bi bi-shop"></i><span>Parties</span>
      </a>
      <a href="#" class="nav-item" onclick="toggleSidebar();return false;">
        <i class="bi bi-grid-3x3-gap"></i><span>More</span>
      </a>
    @elseif(auth()->user()->role === 'sales')
      <a href="{{ route('sale.panel.dashboard') }}" class="nav-item {{ request()->routeIs('sale.panel.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i><span>Home</span>
      </a>
      <a href="{{ route('sale.panel.parties') }}" class="nav-item {{ request()->routeIs('sale.panel.parties*') ? 'active' : '' }}">
        <i class="bi bi-shop"></i><span>Parties</span>
      </a>
      <a href="{{ route('sale.panel.sale.new') }}" class="nav-item {{ request()->routeIs('sale.panel.sale.*') ? 'active' : '' }}">
        <i class="bi bi-plus-circle-fill"></i><span>New Sale</span>
      </a>
      <a href="{{ route('sale.panel.transactions') }}" class="nav-item {{ request()->routeIs('sale.panel.transactions') ? 'active' : '' }}">
        <i class="bi bi-arrow-left-right"></i><span>Txns</span>
      </a>
      <a href="#" class="nav-item" onclick="toggleSidebar();return false;">
        <i class="bi bi-grid-3x3-gap"></i><span>More</span>
      </a>
    @endif
  </div>
</nav>
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('show');
  document.getElementById('sidebarOverlay').classList.toggle('show');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('show');
  document.getElementById('sidebarOverlay').classList.remove('show');
}
function toggleSubmenu(el) {
  const parent = el.closest('.menu-item');
  const isOpen = parent.classList.contains('open');
  document.querySelectorAll('.menu-item.open').forEach(item => {
    if (item !== parent) item.classList.remove('open');
  });
  parent.classList.toggle('open', !isOpen);
}
document.addEventListener('DOMContentLoaded', function () {
  const path = window.location.pathname;
  document.querySelectorAll('.nav-link-item[href], .submenu a[href]').forEach(link => {
    const href = link.getAttribute('href');
    if (href && href !== '#' && href !== 'javascript:void(0)' && path.startsWith(href) && href.length > 1) {
      link.classList.add('active');
      const parentItem = link.closest('.menu-item');
      if (parentItem) parentItem.classList.add('open');
    }
  });
  document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
  document.body.classList.remove('modal-open');
  document.body.style.overflow = '';
  document.body.style.paddingRight = '';
});
</script>
@stack('scripts')
</body>
</html>
