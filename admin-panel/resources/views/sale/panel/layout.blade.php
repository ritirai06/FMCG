<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Sales Panel')</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --primary: #2563EB;
  --primary-dark: #1D4ED8;
  --primary-light: #EFF6FF;
  --sidebar-bg: #1E293B;
  --bg: #F8FAFC;
  --card-bg: #FFFFFF;
  --text: #0F172A;
  --muted: #64748B;
  --border: #E2E8F0;
  --radius: 10px;
  --sidebar-w: 240px;
  --topbar-h: 60px;
  --shadow: 0 1px 3px rgba(0,0,0,.08);
  --shadow-md: 0 4px 6px rgba(0,0,0,.07);
  --success: #16A34A;
  --warning: #CA8A04;
  --danger: #DC2626;
  --info: #2563EB;
  /* aliases used by child views */
  --sp-primary: #2563EB;
  --sp-primary-light: #EFF6FF;
  --sp-muted: #64748B;
  --sp-border: #E2E8F0;
  --sp-card: #FFFFFF;
  --sp-bg: #F8FAFC;
  --sp-bottom-h: 0px;
}
*, *::before, *::after { box-sizing: border-box; }
body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; overflow-x: hidden; font-size: 14px; }

/* ── SIDEBAR ── */
.sidebar {
  position: fixed; top: 0; left: 0; height: 100%; width: var(--sidebar-w);
  background: var(--sidebar-bg); color: #fff;
  overflow-y: auto; z-index: 1000; transition: left .3s;
  display: flex; flex-direction: column;
}
.sidebar::-webkit-scrollbar { width: 4px; }
.sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 4px; }
.sidebar-header {
  display: flex; align-items: center; gap: 10px;
  padding: 18px 16px; border-bottom: 1px solid rgba(255,255,255,.08); flex-shrink: 0;
}
.sidebar-logo { width: 34px; height: 34px; border-radius: 8px; background: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 16px; color: #fff; flex-shrink: 0; }
.sidebar-title { font-size: 14px; font-weight: 700; color: #fff; }
.sidebar-subtitle { font-size: 11px; color: rgba(255,255,255,.4); }
.sidebar-nav { padding: 10px 8px; flex: 1; }
.nav-label { font-size: 10px; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; color: rgba(255,255,255,.3); padding: 10px 8px 4px; display: block; }
.sidebar-link {
  display: flex; align-items: center; gap: 10px;
  color: rgba(255,255,255,.7); padding: 9px 10px;
  border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 13.5px;
  transition: all .2s; margin-bottom: 2px;
}
.sidebar-link i { width: 18px; text-align: center; font-size: 15px; flex-shrink: 0; }
.sidebar-link:hover { background: rgba(37,99,235,.15); color: #fff; }
.sidebar-link.active { background: rgba(37,99,235,.25); color: #fff; box-shadow: inset 3px 0 0 var(--primary); }
.sidebar-divider { border-color: rgba(255,255,255,.08); margin: 8px 10px; }

/* ── TOPBAR ── */
.topbar {
  position: fixed; top: 0; left: var(--sidebar-w); right: 0;
  height: var(--topbar-h); background: var(--card-bg);
  border-bottom: 1px solid var(--border); box-shadow: var(--shadow);
  z-index: 900; display: flex; align-items: center; padding: 0 20px; gap: 12px;
}
.topbar-title { font-size: 16px; font-weight: 700; flex: 1; color: var(--text); }
.topbar-back { color: var(--muted); font-size: 18px; text-decoration: none; }
.topbar-back:hover { color: var(--primary); }
.topbar-actions { display: flex; align-items: center; gap: 8px; }
.topbar-actions a {
  color: var(--muted); font-size: 15px; text-decoration: none;
  width: 34px; height: 34px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  border: 1px solid var(--border); background: var(--bg); transition: all .2s;
}
.topbar-actions a:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

/* ── MAIN ── */
.main-wrap { margin-left: var(--sidebar-w); margin-top: var(--topbar-h); padding: 20px; min-height: calc(100vh - var(--topbar-h)); }

/* ── TOGGLE ── */
.toggle-btn {
  display: none; background: none; color: var(--muted); border: none;
  font-size: 22px; cursor: pointer; padding: 4px; flex-shrink: 0;
}
.sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 999; }

/* ── STAT CARDS ── */
.stat-card {
  background: var(--card-bg); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 18px 20px;
  box-shadow: var(--shadow); display: flex; align-items: center; gap: 14px;
  transition: box-shadow .2s, transform .2s;
}
.stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
.stat-icon {
  width: 46px; height: 46px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  background: var(--primary-light); color: var(--primary); font-size: 20px; flex-shrink: 0;
}
.stat-val { font-size: 22px; font-weight: 700; line-height: 1; color: var(--text); }
.stat-lbl { font-size: 12px; color: var(--muted); margin-top: 3px; }

/* ── CARD SP ── */
.card-sp {
  background: var(--card-bg); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 18px 20px;
  box-shadow: var(--shadow); margin-bottom: 16px;
}

/* ── SECTION HEADER ── */
.sec-hdr { font-size: 14px; font-weight: 700; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; color: var(--text); }
.sec-hdr a { font-size: 12.5px; color: var(--primary); text-decoration: none; font-weight: 600; }

/* ── MENU GRID ── */
.menu-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px; margin-bottom: 20px; }
.menu-item {
  background: var(--card-bg); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 18px 8px 14px;
  display: flex; flex-direction: column; align-items: center; gap: 8px;
  text-decoration: none; color: var(--text);
  box-shadow: var(--shadow); transition: box-shadow .2s, transform .2s;
}
.menu-item:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); color: var(--primary); }
.menu-icon {
  width: 48px; height: 48px; border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; color: #fff;
}
.menu-label { font-size: 12px; font-weight: 600; text-align: center; color: var(--text); }

/* ── TXN ITEM ── */
.txn-item {
  display: flex; align-items: center; gap: 12px; padding: 14px 16px;
  background: var(--card-bg); border: 1px solid var(--border);
  border-radius: var(--radius); margin-bottom: 8px; box-shadow: var(--shadow);
}
.txn-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
.txn-title { font-weight: 600; font-size: 13.5px; }
.txn-sub { font-size: 12px; color: var(--muted); margin-top: 2px; }
.txn-amt { margin-left: auto; font-weight: 700; font-size: 14px; flex-shrink: 0; }

/* ── SEARCH ── */
.sp-search { position: relative; margin-bottom: 12px; }
.sp-search input {
  width: 100%; padding: 10px 14px 10px 38px;
  border-radius: 8px; border: 1px solid var(--border);
  background: var(--card-bg); font-size: 13.5px; outline: none;
  transition: border-color .2s; font-family: 'Inter', sans-serif;
}
.sp-search input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
.sp-search i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 13px; }

/* ── FILTER TABS ── */
.sp-filter-tabs { display: flex; gap: 6px; margin-bottom: 14px; flex-wrap: wrap; }
.sp-filter-tab {
  padding: 6px 14px; border-radius: 20px; font-size: 12.5px; font-weight: 600;
  border: 1.5px solid var(--border); background: var(--card-bg);
  color: var(--muted); text-decoration: none; transition: all .15s; cursor: pointer;
}
.sp-filter-tab.active, .sp-filter-tab:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

/* ── PARTY ITEM ── */
.sp-party-item {
  display: flex; align-items: center; gap: 12px; padding: 14px 16px;
  background: var(--card-bg); border: 1px solid var(--border);
  border-radius: var(--radius); margin-bottom: 8px;
  text-decoration: none; color: var(--text); box-shadow: var(--shadow); transition: all .2s;
}
.sp-party-item:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); color: var(--text); }
.sp-party-avatar { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 700; flex-shrink: 0; }
.sp-party-name { font-weight: 700; font-size: 13.5px; }
.sp-party-meta { font-size: 12px; color: var(--muted); margin-top: 2px; }
.sp-due-badge .amount { font-weight: 700; font-size: 13px; color: var(--danger); }
.sp-due-badge .label { font-size: 10px; color: var(--muted); }

/* ── BADGES ── */
.sp-badge { padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.sp-badge-success { background: #DCFCE7; color: #16A34A; }
.sp-badge-info { background: var(--primary-light); color: var(--primary-dark); }
.badge-sp { padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }

/* ── EMPTY STATE ── */
.sp-empty, .empty-state { text-align: center; padding: 40px 20px; color: var(--muted); }
.sp-empty i, .empty-state i { font-size: 40px; opacity: .3; margin-bottom: 10px; display: block; }
.sp-empty p, .empty-state p { font-size: 13.5px; }

/* ── SECTION HDR ── */
.sp-section-hdr { font-size: 13.5px; font-weight: 700; margin: 14px 0 8px; color: var(--text); }

/* ── TXN ITEM (sp-) ── */
.sp-txn-item {
  display: flex; align-items: center; gap: 12px; padding: 14px 16px;
  background: var(--card-bg); border: 1px solid var(--border);
  border-radius: var(--radius); margin-bottom: 8px; box-shadow: var(--shadow);
}
.sp-txn-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
.sp-txn-title { font-weight: 600; font-size: 13.5px; }
.sp-txn-sub { font-size: 12px; color: var(--muted); margin-top: 2px; }
.sp-txn-amount { margin-left: auto; font-weight: 700; font-size: 14px; flex-shrink: 0; }

/* ── PRODUCT CARD ── */
.sp-product-card { background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); }
.sp-product-img { width: 100%; height: 120px; object-fit: cover; background: var(--bg); display: block; }
.sp-product-img-placeholder { display: flex !important; align-items: center; justify-content: center; color: #CBD5E1; font-size: 32px; height: 120px; }
.sp-product-body { padding: 10px 12px 12px; }
.sp-product-name { font-weight: 700; font-size: 12.5px; margin-bottom: 3px; }
.sp-product-price { font-size: 15px; font-weight: 700; color: var(--primary); }
.sp-product-mrp { font-size: 11.5px; color: var(--muted); text-decoration: line-through; }
.sp-stock-badge { font-size: 11px; padding: 2px 8px; border-radius: 20px; font-weight: 600; }
.sp-qty-ctrl { display: flex; align-items: center; gap: 8px; margin-top: 8px; }
.sp-qty-btn { width: 30px; height: 30px; border-radius: 7px; border: none; background: var(--primary-light); color: var(--primary); font-size: 17px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .15s; }
.sp-qty-btn:hover { background: var(--primary); color: #fff; }
.sp-qty-val { font-weight: 700; font-size: 14px; min-width: 22px; text-align: center; }

/* ── FORMS ── */
.form-group-sp { margin-bottom: 14px; }
.form-group-sp label { font-size: 11.5px; font-weight: 700; color: var(--muted); margin-bottom: 5px; display: block; text-transform: uppercase; letter-spacing: .4px; }
.form-group-sp input, .form-group-sp select, .form-group-sp textarea {
  width: 100%; padding: 10px 12px; border-radius: 8px;
  border: 1px solid var(--border); font-size: 13.5px; outline: none;
  background: var(--card-bg); transition: border-color .2s; font-family: 'Inter', sans-serif;
}
.form-group-sp input:focus, .form-group-sp select:focus, .form-group-sp textarea:focus {
  border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.1);
}

/* ── SUMMARY BOX ── */
.summary-box, .sp-summary { background: var(--primary-light); border-radius: 10px; padding: 14px 16px; }
.summary-row, .sp-summary-row { display: flex; justify-content: space-between; font-size: 13.5px; padding: 4px 0; }
.summary-row.total, .sp-summary-row.total { font-weight: 700; font-size: 15px; border-top: 1.5px solid var(--border); margin-top: 6px; padding-top: 8px; color: var(--primary); }

/* ── SAVE BTN ── */
.save-btn, .sp-save-btn {
  width: 100%; padding: 13px; border-radius: 10px; border: none;
  background: var(--primary); color: #fff; font-size: 15px; font-weight: 600;
  cursor: pointer; transition: background .2s, transform .15s; font-family: 'Inter', sans-serif;
}
.save-btn:hover, .sp-save-btn:hover { background: var(--primary-dark); transform: translateY(-1px); }

/* ── STICKY SAVE ── */
.sp-sticky-save {
  position: fixed; bottom: 0; left: var(--sidebar-w); right: 0;
  padding: 12px 20px; background: var(--card-bg);
  border-top: 1px solid var(--border); box-shadow: 0 -2px 8px rgba(0,0,0,.06);
  z-index: 800;
}

/* ── CART ITEM ── */
.sp-cart-item {
  display: flex; align-items: center; gap: 10px; padding: 12px 14px;
  background: var(--card-bg); border: 1px solid var(--border);
  border-radius: var(--radius); margin-bottom: 8px;
}
.sp-cart-name { font-weight: 600; font-size: 13.5px; }
.sp-cart-price { font-weight: 700; font-size: 14px; color: var(--primary); flex-shrink: 0; }
.sp-remove-btn { background: none; border: none; color: var(--muted); cursor: pointer; padding: 4px; border-radius: 6px; transition: color .15s; flex-shrink: 0; }
.sp-remove-btn:hover { color: var(--danger); }

/* ── DETAIL ROW ── */
.detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border); font-size: 13.5px; }
.detail-row:last-child { border-bottom: none; }
.detail-label { color: var(--muted); }
.detail-value { font-weight: 600; text-align: right; max-width: 60%; word-break: break-word; }

/* ── ALERTS ── */
.alert-sp { padding: 12px 16px; border-radius: 8px; margin-bottom: 14px; font-size: 13.5px; font-weight: 500; display: flex; align-items: center; gap: 8px; }
.alert-success { background: #DCFCE7; color: #16A34A; }
.alert-error { background: #FEE2E2; color: #DC2626; }

/* ── ACTION BAR ── */
.action-bar { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }
.action-btn {
  flex: 1; min-width: 70px; padding: 10px 6px; border-radius: 10px; border: none;
  font-size: 11.5px; font-weight: 600; cursor: pointer; text-align: center;
  text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 5px; transition: all .15s;
}
.action-btn:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
.action-btn i { font-size: 18px; }

/* ── PARTY ITEM (alias) ── */
.party-item { display: flex; align-items: center; gap: 12px; padding: 14px 16px; background: var(--card-bg); border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 8px; text-decoration: none; color: var(--text); box-shadow: var(--shadow); transition: all .2s; }
.party-item:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); color: var(--text); }
.party-avatar { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 15px; font-weight: 700; flex-shrink: 0; }

/* ── RESPONSIVE ── */
@media (max-width: 991.98px) {
  .sidebar { left: calc(-1 * var(--sidebar-w)); }
  .sidebar.show { left: 0; }
  .sidebar-overlay.show { display: block; }
  .main-wrap { margin-left: 0; }
  .topbar { left: 0; }
  .toggle-btn { display: block; }
  .menu-grid { grid-template-columns: repeat(3, 1fr); }
  .sp-sticky-save { left: 0; }
}
@media (max-width: 767.98px) {
  .main-wrap { padding: 12px 10px 80px; }
  .sp-sticky-save { left: 0; bottom: 60px; padding: 10px 14px; }
  .stat-card { padding: 14px; }
  .stat-val { font-size: 18px; }
}
@media (max-width: 575.98px) {
  .menu-grid { grid-template-columns: repeat(2, 1fr); }
  .main-wrap { padding: 10px 8px 80px; }
  .sp-sticky-save { padding: 10px 12px; }
  .topbar { padding: 0 12px; }
}
</style>
@stack('styles')
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo"><i class="fas fa-store"></i></div>
    <div>
      <div class="sidebar-title">Sales Panel</div>
      <div class="sidebar-subtitle">Management System</div>
    </div>
  </div>
  <nav class="sidebar-nav">
    <a href="{{ route('sale.panel.dashboard') }}" class="sidebar-link {{ request()->routeIs('sale.panel.dashboard') ? 'active' : '' }}">
      <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <span class="nav-label">Main</span>
    <a href="{{ route('sale.panel.parties') }}" class="sidebar-link {{ request()->routeIs('sale.panel.part*') ? 'active' : '' }}">
      <i class="bi bi-people"></i> Parties
    </a>
    <a href="{{ route('sale.panel.items') }}" class="sidebar-link {{ request()->routeIs('sale.panel.items') ? 'active' : '' }}">
      <i class="bi bi-box-seam"></i> Items
    </a>
    <a href="{{ route('sale.panel.transactions') }}" class="sidebar-link {{ request()->routeIs('sale.panel.transactions') ? 'active' : '' }}">
      <i class="bi bi-receipt"></i> Transactions
    </a>
    <span class="nav-label">Actions</span>
    <a href="{{ route('sale.panel.sale.new') }}" class="sidebar-link {{ request()->routeIs('sale.panel.sale*') ? 'active' : '' }}">
      <i class="bi bi-plus-circle"></i> New Sale
    </a>
    <a href="{{ route('sale.panel.payment.in') }}" class="sidebar-link {{ request()->routeIs('sale.panel.payment*') ? 'active' : '' }}">
      <i class="bi bi-cash-stack"></i> Payment In
    </a>
    <a href="{{ route('sale.panel.returns') }}" class="sidebar-link {{ request()->routeIs('sale.panel.return*') ? 'active' : '' }}">
      <i class="bi bi-arrow-counterclockwise"></i> Returns
    </a>
    <span class="nav-label">More</span>
    <a href="{{ route('sale.panel.attendance') }}" class="sidebar-link {{ request()->routeIs('sale.panel.attendance') ? 'active' : '' }}">
      <i class="bi bi-calendar-check"></i> Attendance
    </a>
    <a href="{{ route('sale.panel.achievements') }}" class="sidebar-link {{ request()->routeIs('sale.panel.achievements') ? 'active' : '' }}">
      <i class="bi bi-trophy"></i> Achievements
    </a>
    <a href="{{ route('sale.panel.expenses') }}" class="sidebar-link {{ request()->routeIs('sale.panel.expenses') ? 'active' : '' }}">
      <i class="bi bi-wallet2"></i> Expenses
    </a>
    <hr class="sidebar-divider">
    <a href="{{ route('sale.logout') }}" class="sidebar-link" style="color:rgba(255,255,255,.55);">
      <i class="bi bi-box-arrow-right"></i> Logout
    </a>
  </nav>
</aside>

<!-- TOPBAR -->
<header class="topbar">
  <button class="toggle-btn" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
  @hasSection('back_url')
  <a href="@yield('back_url')" class="topbar-back"><i class="bi bi-arrow-left"></i></a>
  @endif
  <span class="topbar-title">@yield('title', 'Sales Panel')</span>
  <div class="topbar-actions">@yield('topnav_actions')</div>
</header>

<!-- MAIN -->
<main class="main-wrap">
  @if(session('success'))
  <div class="alert-sp alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
  @endif
  @if(session('error'))
  <div class="alert-sp alert-error"><i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}</div>
  @endif
  @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- MOBILE BOTTOM NAV -->
<nav style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:1050;background:#fff;border-top:1px solid var(--border);box-shadow:0 -2px 10px rgba(0,0,0,.07);padding:6px 0 max(6px,env(safe-area-inset-bottom));" id="spBottomNav">
  <div style="display:flex;justify-content:space-around;align-items:center;">
    <a href="{{ route('sale.panel.dashboard') }}" style="display:flex;flex-direction:column;align-items:center;gap:2px;padding:4px 8px;border-radius:8px;text-decoration:none;color:{{ request()->routeIs('sale.panel.dashboard') ? 'var(--primary)' : 'var(--muted)' }};font-size:10px;font-weight:600;min-width:52px;text-align:center;">
      <i class="bi bi-speedometer2" style="font-size:20px;"></i><span>Home</span>
    </a>
    <a href="{{ route('sale.panel.parties') }}" style="display:flex;flex-direction:column;align-items:center;gap:2px;padding:4px 8px;border-radius:8px;text-decoration:none;color:{{ request()->routeIs('sale.panel.part*') ? 'var(--primary)' : 'var(--muted)' }};font-size:10px;font-weight:600;min-width:52px;text-align:center;">
      <i class="bi bi-people" style="font-size:20px;"></i><span>Parties</span>
    </a>
    <a href="{{ route('sale.panel.sale.new') }}" style="display:flex;flex-direction:column;align-items:center;gap:2px;padding:4px 8px;border-radius:8px;text-decoration:none;color:{{ request()->routeIs('sale.panel.sale*') ? 'var(--primary)' : 'var(--muted)' }};font-size:10px;font-weight:600;min-width:52px;text-align:center;">
      <i class="bi bi-plus-circle-fill" style="font-size:20px;"></i><span>New Sale</span>
    </a>
    <a href="{{ route('sale.panel.transactions') }}" style="display:flex;flex-direction:column;align-items:center;gap:2px;padding:4px 8px;border-radius:8px;text-decoration:none;color:{{ request()->routeIs('sale.panel.transactions') ? 'var(--primary)' : 'var(--muted)' }};font-size:10px;font-weight:600;min-width:52px;text-align:center;">
      <i class="bi bi-receipt" style="font-size:20px;"></i><span>Txns</span>
    </a>
    <a href="#" onclick="toggleSidebar();return false;" style="display:flex;flex-direction:column;align-items:center;gap:2px;padding:4px 8px;border-radius:8px;text-decoration:none;color:var(--muted);font-size:10px;font-weight:600;min-width:52px;text-align:center;">
      <i class="bi bi-grid-3x3-gap" style="font-size:20px;"></i><span>More</span>
    </a>
  </div>
</nav>

<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('show');
  document.getElementById('sidebarOverlay').classList.toggle('show');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('show');
  document.getElementById('sidebarOverlay').classList.remove('show');
}
// Show bottom nav on mobile
if (window.innerWidth <= 767) {
  document.getElementById('spBottomNav').style.display = 'block';
}
window.addEventListener('resize', function() {
  document.getElementById('spBottomNav').style.display = window.innerWidth <= 767 ? 'block' : 'none';
});
</script>
@stack('scripts')
</body>
</html>
