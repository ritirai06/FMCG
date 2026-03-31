<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $companyName ?? 'SalesOn' }} · Delivery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #eff6ff;
            --success: #16a34a;
            --warning: #d97706;
            --danger: #dc2626;
            --bg: #f1f5f9;
            --card: #ffffff;
            --border: #e2e8f0;
            --muted: #64748b;
            --text: #0f172a;
            --radius: 12px;
            --shadow: 0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.06);
            --sidebar-w: 240px;
            --topbar-h: 60px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; overflow-x: hidden; }

        /* ── SIDEBAR ── */
        .dp-sidebar {
            position: fixed; top: 0; left: 0; height: 100%; width: var(--sidebar-w);
            background: #0f172a; color: #fff;
            display: flex; flex-direction: column;
            z-index: 1000; transition: left .3s ease;
            overflow-y: auto;
        }
        .dp-sidebar::-webkit-scrollbar { width: 4px; }
        .dp-sidebar::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }

        .dp-brand {
            padding: 20px 18px 16px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            display: flex; align-items: center; gap: 10px;
        }
        .dp-brand-icon {
            width: 38px; height: 38px; border-radius: 10px;
            background: var(--primary); display: flex; align-items: center;
            justify-content: center; font-size: 17px; flex-shrink: 0;
        }
        .dp-brand-name { font-size: 15px; font-weight: 700; line-height: 1.2; }
        .dp-brand-sub { font-size: 11px; color: #94a3b8; }

        .dp-user-pill {
            margin: 12px 14px;
            background: rgba(255,255,255,.06);
            border-radius: 10px; padding: 10px 12px;
            display: flex; align-items: center; gap: 10px;
        }
        .dp-user-avatar {
            width: 34px; height: 34px; border-radius: 8px;
            background: var(--primary); display: flex; align-items: center;
            justify-content: center; font-size: 13px; font-weight: 700; flex-shrink: 0;
        }
        .dp-user-name { font-size: 13px; font-weight: 600; }
        .dp-user-role { font-size: 11px; color: #94a3b8; }

        .dp-nav { padding: 8px 10px; flex: 1; }
        .dp-nav-label {
            font-size: 10px; font-weight: 700; letter-spacing: .8px;
            text-transform: uppercase; color: #475569;
            padding: 10px 8px 4px;
        }
        .dp-nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 8px;
            color: #94a3b8; text-decoration: none;
            font-size: 13px; font-weight: 500;
            transition: all .15s; margin-bottom: 2px;
        }
        .dp-nav-link i { width: 16px; text-align: center; font-size: 14px; }
        .dp-nav-link:hover { background: rgba(255,255,255,.07); color: #fff; }
        .dp-nav-link.active { background: var(--primary); color: #fff; }
        .dp-nav-link .badge-count {
            margin-left: auto; background: #ef4444; color: #fff;
            border-radius: 20px; padding: 1px 7px; font-size: 10px; font-weight: 700;
        }

        .dp-sidebar-footer {
            padding: 12px 14px;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .dp-logout-btn {
            display: flex; align-items: center; gap: 8px;
            width: 100%; padding: 9px 12px; border-radius: 8px;
            background: rgba(239,68,68,.12); color: #f87171;
            border: none; font-size: 13px; font-weight: 600;
            cursor: pointer; transition: all .15s;
        }
        .dp-logout-btn:hover { background: rgba(239,68,68,.22); color: #fca5a5; }

        /* ── TOPBAR ── */
        .dp-topbar {
            position: fixed; top: 0; left: var(--sidebar-w); right: 0;
            height: var(--topbar-h);
            background: #fff; border-bottom: 1px solid var(--border);
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
            display: flex; align-items: center; padding: 0 20px; gap: 12px;
            z-index: 900;
        }
        .dp-hamburger {
            display: none; background: none; border: none;
            font-size: 20px; color: var(--muted); cursor: pointer; padding: 4px;
        }
        .dp-topbar-title { font-size: 16px; font-weight: 700; flex: 1; }
        .dp-topbar-actions { display: flex; align-items: center; gap: 8px; }
        .dp-topbar-btn {
            width: 36px; height: 36px; border-radius: 8px;
            background: var(--bg); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            color: var(--muted); font-size: 14px; text-decoration: none;
            transition: all .15s; cursor: pointer;
        }
        .dp-topbar-btn:hover { background: var(--primary-light); color: var(--primary); border-color: var(--primary); }
        .dp-trip-badge {
            padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;
            background: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0;
        }
        .dp-trip-badge.active { background: #fef3c7; color: #d97706; border-color: #fde68a; }

        /* ── MAIN ── */
        .dp-main {
            margin-left: var(--sidebar-w);
            margin-top: var(--topbar-h);
            padding: 24px;
            min-height: calc(100vh - var(--topbar-h));
        }

        /* ── OVERLAY ── */
        .dp-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,.4); z-index: 999;
        }
        .dp-overlay.show { display: block; }

        /* ── CARDS ── */
        .dp-card {
            background: var(--card); border-radius: var(--radius);
            border: 1px solid var(--border); box-shadow: var(--shadow);
            padding: 18px;
        }
        .dp-card-title {
            font-size: 14px; font-weight: 700; margin-bottom: 14px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .dp-card-title a { font-size: 12px; color: var(--primary); text-decoration: none; font-weight: 600; }

        /* ── STAT CARDS ── */
        .dp-stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .dp-stat {
            background: var(--card); border-radius: var(--radius);
            border: 1px solid var(--border); box-shadow: var(--shadow);
            padding: 16px; display: flex; align-items: center; gap: 12px;
        }
        .dp-stat-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }
        .dp-stat-val { font-size: 22px; font-weight: 800; line-height: 1; }
        .dp-stat-lbl { font-size: 12px; color: var(--muted); margin-top: 3px; }

        /* ── MENU GRID ── */
        .dp-menu-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
        .dp-menu-item {
            background: var(--card); border-radius: var(--radius);
            border: 1px solid var(--border); box-shadow: var(--shadow);
            padding: 16px 10px; display: flex; flex-direction: column;
            align-items: center; gap: 8px; text-decoration: none;
            color: var(--text); transition: all .15s; cursor: pointer;
        }
        .dp-menu-item:hover { border-color: var(--primary); background: var(--primary-light); color: var(--primary); transform: translateY(-2px); }
        .dp-menu-item.highlight { background: var(--primary); border-color: var(--primary); color: #fff; }
        .dp-menu-item.highlight:hover { background: var(--primary-dark); }
        .dp-menu-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .dp-menu-label { font-size: 12px; font-weight: 600; text-align: center; }

        /* ── DELIVERY CARDS ── */
        .dp-delivery-card {
            background: var(--card); border-radius: var(--radius);
            border: 1px solid var(--border); box-shadow: var(--shadow);
            padding: 14px; transition: all .15s;
        }
        .dp-delivery-card:hover { border-color: var(--primary); box-shadow: 0 4px 20px rgba(37,99,235,.12); }
        .dp-delivery-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; margin-bottom: 8px; }
        .dp-delivery-shop { font-size: 14px; font-weight: 700; }
        .dp-delivery-loc { font-size: 12px; color: var(--muted); margin-top: 2px; }
        .dp-delivery-meta { font-size: 11px; color: var(--muted); margin-bottom: 10px; }
        .dp-delivery-actions { display: flex; gap: 8px; flex-wrap: wrap; }

        /* ── BADGES ── */
        .dp-badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 700;
        }
        .dp-badge-dispatched { background: #fef3c7; color: #d97706; }
        .dp-badge-transit { background: #dbeafe; color: #1d4ed8; }
        .dp-badge-delivered { background: #dcfce7; color: #16a34a; }
        .dp-badge-failed { background: #fee2e2; color: #dc2626; }
        .dp-badge-pending { background: #f1f5f9; color: #64748b; }
        .dp-badge-assigned { background: #ede9fe; color: #7c3aed; }

        /* ── BUTTONS ── */
        .dp-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; border-radius: 8px; font-size: 12px;
            font-weight: 600; border: none; cursor: pointer; transition: all .15s;
            text-decoration: none;
        }
        .dp-btn-primary { background: var(--primary); color: #fff; }
        .dp-btn-primary:hover { background: var(--primary-dark); color: #fff; }
        .dp-btn-outline { background: #fff; color: var(--primary); border: 1px solid var(--primary); }
        .dp-btn-outline:hover { background: var(--primary-light); }
        .dp-btn-ghost { background: var(--bg); color: var(--text); border: 1px solid var(--border); }
        .dp-btn-ghost:hover { border-color: var(--primary); color: var(--primary); }
        .dp-btn-success { background: #16a34a; color: #fff; }
        .dp-btn-success:hover { background: #15803d; }
        .dp-btn-danger { background: #dc2626; color: #fff; }
        .dp-btn-danger:hover { background: #b91c1c; }
        .dp-btn-full { width: 100%; justify-content: center; padding: 12px; font-size: 14px; }

        /* ── TABS ── */
        .dp-tabs { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px; }
        .dp-tab {
            padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 600;
            border: 1.5px solid var(--border); background: #fff;
            color: var(--muted); cursor: pointer; transition: all .15s;
        }
        .dp-tab.active, .dp-tab:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

        /* ── SEARCH ── */
        .dp-search { position: relative; margin-bottom: 14px; }
        .dp-search input {
            width: 100%; padding: 9px 14px 9px 36px;
            border-radius: 8px; border: 1px solid var(--border);
            background: #fff; font-size: 13px; outline: none;
            font-family: 'Inter', sans-serif; transition: border-color .15s;
        }
        .dp-search input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
        .dp-search i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 13px; }

        /* ── TABLE ── */
        .dp-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .dp-table th { padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; border-bottom: 1px solid var(--border); }
        .dp-table td { padding: 11px 12px; border-bottom: 1px solid var(--border); }
        .dp-table tr:last-child td { border-bottom: none; }
        .dp-table tr:hover td { background: var(--bg); }

        /* ── FORM ── */
        .dp-form-group { margin-bottom: 14px; }
        .dp-form-group label { display: block; font-size: 11px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .4px; margin-bottom: 5px; }
        .dp-form-group input, .dp-form-group select, .dp-form-group textarea {
            width: 100%; padding: 10px 12px; border-radius: 8px;
            border: 1px solid var(--border); font-size: 13px; outline: none;
            font-family: 'Inter', sans-serif; background: #fff; transition: border-color .15s;
        }
        .dp-form-group input:focus, .dp-form-group select:focus, .dp-form-group textarea:focus {
            border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,.1);
        }

        /* ── MAP ── */
        .dp-map {
            height: 160px; border-radius: 10px; overflow: hidden;
            border: 1px solid var(--border); background: #e0e7ff;
            display: flex; align-items: center; justify-content: center;
            color: var(--muted); font-size: 13px; margin-bottom: 12px;
        }
        .dp-map iframe { width: 100%; height: 100%; border: none; }

        /* ── EMPTY STATE ── */
        .dp-empty { text-align: center; padding: 32px 16px; color: var(--muted); }
        .dp-empty i { font-size: 36px; opacity: .3; display: block; margin-bottom: 8px; }
        .dp-empty p { font-size: 13px; }

        /* ── SPINNER ── */
        .dp-spinner {
            width: 20px; height: 20px; border-radius: 50%;
            border: 3px solid var(--border); border-top-color: var(--primary);
            animation: dp-spin .7s linear infinite; display: inline-block;
        }
        @keyframes dp-spin { to { transform: rotate(360deg); } }

        /* ── TOAST ── */
        #dp-toast {
            position: fixed; bottom: 24px; left: 50%;
            transform: translateX(-50%) translateY(120%);
            background: #0f172a; color: #fff;
            padding: 11px 20px; border-radius: 10px;
            font-size: 13px; font-weight: 600;
            box-shadow: 0 8px 24px rgba(0,0,0,.2);
            z-index: 9999; opacity: 0; transition: all .25s ease;
            display: flex; align-items: center; gap: 8px;
        }
        #dp-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
        #dp-toast.success { background: #16a34a; }
        #dp-toast.error { background: #dc2626; }
        #dp-toast.warning { background: #d97706; }

        /* ── ALERTS ── */
        .dp-alert { padding: 12px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .dp-alert-success { background: #dcfce7; color: #16a34a; }
        .dp-alert-error { background: #fee2e2; color: #dc2626; }

        /* ── SECTION HEADER ── */
        .dp-sec-hdr { font-size: 13px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .5px; margin: 20px 0 10px; }

        /* ── RESPONSIVE ── */
        @media (max-width: 1024px) {
            .dp-stat-grid { grid-template-columns: repeat(2, 1fr); }
            .dp-menu-grid { grid-template-columns: repeat(4, 1fr); }
        }
        @media (max-width: 768px) {
            .dp-sidebar { left: -100%; }
            .dp-sidebar.open { left: 0; }
            .dp-topbar { left: 0; }
            .dp-main { margin-left: 0; padding: 16px 12px 80px; }
            .dp-hamburger { display: flex; }
            .dp-stat-grid { grid-template-columns: repeat(2, 1fr); }
            .dp-menu-grid { grid-template-columns: repeat(4, 1fr); }
        }
        @media (max-width: 480px) {
            .dp-stat-grid { grid-template-columns: repeat(2, 1fr); }
            .dp-menu-grid { grid-template-columns: repeat(4, 1fr); }
            .dp-main { padding: 10px 8px 80px; }
            .dp-topbar { padding: 0 12px; }
            .dp-topbar-title { font-size: 14px; }
        }
        /* Mobile bottom nav */
        .dp-mobile-nav {
            display: none;
            position: fixed; bottom: 0; left: 0; right: 0; z-index: 1050;
            background: #fff; border-top: 1px solid var(--border);
            box-shadow: 0 -2px 10px rgba(0,0,0,.07);
            padding: 6px 0 max(6px, env(safe-area-inset-bottom));
        }
        .dp-mobile-nav .nav-row { display: flex; justify-content: space-around; }
        .dp-mobile-nav .nav-btn {
            display: flex; flex-direction: column; align-items: center; gap: 2px;
            padding: 4px 8px; border-radius: 8px; text-decoration: none;
            color: var(--muted); font-size: 10px; font-weight: 600; min-width: 52px;
        }
        .dp-mobile-nav .nav-btn i { font-size: 20px; }
        .dp-mobile-nav .nav-btn.active { color: var(--primary); }
        @media (max-width: 768px) { .dp-mobile-nav { display: block; } }
    </style>
    @stack('styles')
</head>
<body>

<!-- SIDEBAR OVERLAY -->
<div class="dp-overlay" id="dpOverlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="dp-sidebar" id="dpSidebar">
    @if($user ?? null)
    <div class="dp-user-pill" style="margin-top:20px;">
        <div class="dp-user-avatar">{{ strtoupper(substr($user->name ?? 'D', 0, 2)) }}</div>
        <div>
            <div class="dp-user-name">{{ $user->name ?? 'Delivery Partner' }}</div>
            <div class="dp-user-role">Delivery Panel</div>
        </div>
    </div>
    @endif

    <nav class="dp-nav">
        <div class="dp-nav-label">Main</div>
        <a href="{{ route('delivery.panel.dashboard') }}" class="dp-nav-link {{ request()->routeIs('delivery.panel.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        <a href="{{ route('delivery.panel.orders') }}" class="dp-nav-link {{ request()->routeIs('delivery.panel.orders','delivery.panel.my.orders') ? 'active' : '' }}">
            <i class="fas fa-truck"></i> Deliveries
            @php $pendingCount = $stats['pending'] ?? 0; @endphp
            @if($pendingCount > 0)<span class="badge-count">{{ $pendingCount }}</span>@endif
        </a>
        <a href="{{ route('delivery.panel.transactions') }}" class="dp-nav-link {{ request()->routeIs('delivery.panel.transactions','delivery.panel.transaction.*') ? 'active' : '' }}">
            <i class="fas fa-receipt"></i> Transactions
        </a>
        <a href="{{ route('delivery.panel.stores') }}" class="dp-nav-link {{ request()->routeIs('delivery.panel.stores') ? 'active' : '' }}">
            <i class="fas fa-store"></i> Stores
        </a>

        <div class="dp-nav-label">Activity</div>
        <a href="{{ route('delivery.panel.attendance') }}" class="dp-nav-link {{ request()->routeIs('delivery.panel.attendance') ? 'active' : '' }}">
            <i class="fas fa-calendar-check"></i> Attendance
        </a>
        <a href="{{ route('delivery.panel.items') }}" class="dp-nav-link {{ request()->routeIs('delivery.panel.items') ? 'active' : '' }}">
            <i class="fas fa-box"></i> Items
        </a>
        <a href="{{ route('delivery.panel.earnings') }}" class="dp-nav-link {{ request()->routeIs('delivery.panel.earnings','delivery.panel.incentives') ? 'active' : '' }}">
            <i class="fas fa-wallet"></i> Earnings
        </a>
        <a href="{{ route('delivery.panel.profile') }}" class="dp-nav-link {{ request()->routeIs('delivery.panel.profile*') ? 'active' : '' }}">
            <i class="fas fa-user-circle"></i> Profile
        </a>
    </nav>

    <div class="dp-sidebar-footer">
        <form method="POST" action="{{ route('delivery.panel.logout') }}">
            @csrf
            <button type="submit" class="dp-logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</aside>

<!-- TOPBAR -->
<header class="dp-topbar">
    <button class="dp-hamburger" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
    <span class="dp-topbar-title">@yield('page_title', 'Dashboard')</span>
    <div class="dp-topbar-actions">
        <span class="dp-trip-badge" id="globalTripBadge">● Off Duty</span>
        <a href="{{ route('delivery.panel.profile') }}" class="dp-topbar-btn">
            <i class="fas fa-user"></i>
        </a>
    </div>
</header>

<!-- MAIN -->
<main class="dp-main">
    @if(session('success'))
    <div class="dp-alert dp-alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="dp-alert dp-alert-error"><i class="fas fa-exclamation-circle"></i>{{ session('error') }}</div>
    @endif
    @yield('content')
</main>

<!-- TOAST -->
<div id="dp-toast"></div>

<!-- MOBILE BOTTOM NAV -->
<nav class="dp-mobile-nav">
  <div class="nav-row">
    <a href="{{ route('delivery.panel.dashboard') }}" class="nav-btn {{ request()->routeIs('delivery.panel.dashboard') ? 'active' : '' }}">
      <i class="fas fa-chart-line"></i><span>Home</span>
    </a>
    <a href="{{ route('delivery.panel.orders') }}" class="nav-btn {{ request()->routeIs('delivery.panel.orders','delivery.panel.my.orders') ? 'active' : '' }}">
      <i class="fas fa-truck"></i><span>Orders</span>
    </a>
    <a href="{{ route('delivery.panel.order.details') }}" class="nav-btn {{ request()->routeIs('delivery.panel.order.details') ? 'active' : '' }}">
      <i class="fas fa-map-marker-alt"></i><span>Navigate</span>
    </a>
    <a href="{{ route('delivery.panel.attendance') }}" class="nav-btn {{ request()->routeIs('delivery.panel.attendance') ? 'active' : '' }}">
      <i class="fas fa-calendar-check"></i><span>Attend</span>
    </a>
    <a href="#" onclick="toggleSidebar();return false;" class="nav-btn">
      <i class="fas fa-bars"></i><span>More</span>
    </a>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
    document.getElementById('dpSidebar').classList.toggle('open');
    document.getElementById('dpOverlay').classList.toggle('show');
}
function closeSidebar() {
    document.getElementById('dpSidebar').classList.remove('open');
    document.getElementById('dpOverlay').classList.remove('show');
}
function dpToast(msg, type = '') {
    const el = document.getElementById('dp-toast');
    el.textContent = msg;
    el.className = type ? `show ${type}` : 'show';
    setTimeout(() => el.className = '', 2800);
}
// Restore trip state
if (localStorage.getItem('dp_trip_active') === '1') {
    const badge = document.getElementById('globalTripBadge');
    if (badge) { badge.textContent = '● On Trip'; badge.classList.add('active'); }
}
</script>
@stack('scripts')
</body>
</html>
