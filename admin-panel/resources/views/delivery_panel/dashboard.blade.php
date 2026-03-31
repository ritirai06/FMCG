@extends('delivery_panel.layout')
@section('page_title', 'Dashboard')

@php
    $stats            = $stats ?? [];
    $todayAssigned    = (int) data_get($stats, 'today_assigned', 0);
    $pendingDeliveries= (int) data_get($stats, 'pending', 0);
    $todayDelivered   = (int) data_get($stats, 'today_delivered', 0);
    $failedReturned   = (int) data_get($stats, 'failed_or_returned', 0);
    $todayEarnings    = (float) data_get($stats, 'today_revenue', 0);
    $totalRevenue     = (float) data_get($stats, 'total_revenue', 0);
    $totalAssigned    = (int) data_get($stats, 'total_assigned', 0);
    $deliveryUserName = $user?->name ?? data_get($deliveryProfile, 'name', 'Delivery Partner');
    $deliveryLocation = data_get($deliveryProfile, 'location', 'Location N/A');
@endphp

@push('styles')
<style>
    .dash-wrap { display:grid; gap:16px; }
    .heading-row { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; }
    .search-box { position:relative; width:260px; max-width:100%; }
    .search-box input { width:100%; padding:10px 12px 10px 34px; border:1px solid #e5e7eb; border-radius:10px; font-family:inherit; font-size:13px; outline:none; }
    .search-box input:focus { border-color:var(--primary); }
    .search-box i { position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#9ca3af; }

    .menu-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(110px,1fr)); gap:10px; }
    .menu-btn { border:1px solid var(--border); border-radius:var(--radius); background:var(--card); padding:14px 10px; display:grid; gap:6px; place-items:center; font-weight:700; font-size:13px; color:#0f172a; box-shadow:var(--shadow); cursor:pointer; text-decoration:none; transition:all .15s; }
    .menu-btn:hover { border-color:var(--primary); background:var(--primary-light); color:var(--primary); transform:translateY(-2px); }
    .menu-btn i { color:var(--primary); font-size:20px; }
    .menu-btn.highlight { background:linear-gradient(135deg,#2563eb,#3b82f6); color:#fff; border-color:#2563eb; }
    .menu-btn.highlight i { color:#fff; }
    .menu-btn.highlight:hover { background:linear-gradient(135deg,#1d4ed8,#2563eb); color:#fff; }

    .cards-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:12px; }
    .card-kpi { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:14px 16px; box-shadow:0 4px 16px rgba(15,23,42,.06); cursor:pointer; transition:all .15s; text-decoration:none; display:block; }
    .card-kpi:hover { border-color:var(--primary); transform:translateY(-2px); }
    .kpi-label { color:#6b7280; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.4px; }
    .kpi-value { font-size:22px; font-weight:800; margin-top:6px; display:flex; align-items:center; gap:8px; color:#0f172a; }
    .kpi-spark { width:36px; height:14px; border-radius:12px; background:linear-gradient(135deg,#ef4444,#f97316); flex-shrink:0; }
    .kpi-spark.blue  { background:linear-gradient(135deg,#2563eb,#38bdf8); }
    .kpi-spark.green { background:linear-gradient(135deg,#22c55e,#4ade80); }
    .kpi-spark.pink  { background:linear-gradient(135deg,#ec4899,#a855f7); }

    .two-col { display:grid; grid-template-columns:1.2fr 1fr; gap:14px; }
    @media(max-width:992px){ .two-col{ grid-template-columns:1fr; } }

    .panel { background:#fff; border:1px solid #e5e7eb; border-radius:12px; box-shadow:0 4px 16px rgba(15,23,42,.06); padding:16px; }
    .panel-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap; gap:8px; }
    .panel-title { font-weight:800; font-size:14px; }
    .profile-box { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
    .avatar { width:46px; height:46px; border-radius:12px; background:linear-gradient(135deg,#2563eb,#1d4ed8); color:#fff; display:grid; place-items:center; font-weight:800; font-size:16px; flex-shrink:0; }
    .d-badge { border-radius:999px; padding:3px 10px; font-weight:700; font-size:11px; white-space:nowrap; }
    .d-badge-blue       { background:#e0f2fe; color:#1d4ed8; }
    .d-badge-dispatched { background:#fef3c7; color:#d97706; }
    .d-badge-transit    { background:#dbeafe; color:#1d4ed8; }
    .d-badge-delivered  { background:#dcfce7; color:#16a34a; }
    .d-badge-failed     { background:#fee2e2; color:#dc2626; }
    .btn-primary-slim { background:#2563eb; color:#fff; border:none; border-radius:8px; padding:7px 14px; font-weight:700; font-size:12px; cursor:pointer; text-decoration:none; display:inline-block; }
    .btn-primary-slim:hover { background:#1d4ed8; color:#fff; }

    .metrics-table { width:100%; }
    .metrics-table td { padding:7px 0; font-size:13px; border-bottom:1px solid #f1f5f9; }
    .metrics-table td:last-child { text-align:right; font-weight:700; }
    .metrics-table tr:last-child td { border-bottom:none; }

    .tabs { display:flex; gap:6px; flex-wrap:wrap; }
    .tab { border:1px solid #e5e7eb; padding:6px 12px; border-radius:8px; background:#f8fafc; cursor:pointer; font-weight:600; font-size:12px; transition:all .15s; }
    .tab.active { background:#fb923c; color:#fff; border-color:#f97316; }
    .tab:hover:not(.active) { border-color:var(--primary); color:var(--primary); }

    .delivery-list { display:grid; gap:10px; margin-bottom:12px; }
    .delivery-card { border:1px solid var(--border); border-radius:var(--radius); padding:12px; background:#fff; box-shadow:var(--shadow); display:grid; gap:8px; transition:border-color .15s; }
    .delivery-card:hover { border-color:var(--primary); }
    .delivery-top { display:flex; justify-content:space-between; gap:8px; align-items:flex-start; }
    .actions { display:flex; gap:8px; flex-wrap:wrap; }
    .chip { background:#eff6ff; color:#1d4ed8; padding:6px 12px; border-radius:8px; font-weight:600; font-size:12px; border:none; cursor:pointer; transition:all .15s; display:inline-flex; align-items:center; gap:5px; }
    .chip:hover { background:#dbeafe; }
    .ghost { background:#f8fafc; border:1px solid var(--border); color:#0f172a; border-radius:8px; padding:6px 12px; font-size:12px; font-weight:600; cursor:pointer; transition:all .15s; }
    .ghost:hover { border-color:var(--primary); color:var(--primary); }
    .primary-btn { background:linear-gradient(135deg,#2563eb,#1d4ed8); color:#fff; border:none; padding:12px; border-radius:10px; font-weight:800; font-size:14px; box-shadow:var(--shadow); width:100%; cursor:pointer; transition:opacity .15s; }
    .primary-btn:hover { opacity:.9; }
    .primary-btn:disabled { opacity:.6; cursor:not-allowed; }

    .items-table { width:100%; border-collapse:collapse; font-size:13px; }
    .items-table th { padding:8px 10px; text-align:left; font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid var(--border); }
    .items-table td { padding:10px; border-bottom:1px solid var(--border); }
    .items-table tr:last-child td { border-bottom:none; }
    .items-table tr:hover td { background:#f8fafc; }

    .txn-card { border:1px solid var(--border); border-radius:8px; padding:10px 12px; margin-bottom:8px; display:flex; justify-content:space-between; align-items:center; font-size:13px; }
    .txn-card:last-child { margin-bottom:0; }

    .activity-list { display:grid; gap:10px; }
    .activity-item { border:1px solid #e5e7eb; border-radius:10px; padding:10px 12px; background:#fff; display:flex; gap:10px; align-items:flex-start; }
    .dot { width:9px; height:9px; border-radius:50%; margin-top:5px; flex-shrink:0; }
    .dot.green { background:#16a34a; }
    .dot.blue  { background:#2563eb; }
    .dot.orange{ background:#f97316; }
    .muted { color:#6b7280; }
    .small { font-size:12px; }

    .map-placeholder { height:130px; background:linear-gradient(135deg,#e0e7ff,#f8fafc); border:1px dashed var(--border); border-radius:var(--radius); display:grid; place-items:center; color:var(--muted); font-size:13px; margin-bottom:12px; }
    .attendance-actions { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:8px; }
    .loader { display:none; align-items:center; gap:8px; padding:8px 0; }
    .spinner { width:16px; height:16px; border-radius:50%; border:2px solid #e2e8f0; border-top-color:var(--primary); animation:spin .7s linear infinite; }
    @keyframes spin { to { transform:rotate(360deg); } }
</style>
@endpush

@section('content')
<div class="dash-wrap">

    {{-- HEADING --}}
    <div class="heading-row">
        <div>
            <div class="muted small">Welcome back</div>
            <h5 class="mb-0" style="font-size:18px;font-weight:800;">{{ $deliveryUserName }}</h5>
        </div>
        <div class="search-box">
            <i class="fa fa-search"></i>
            <input type="text" id="globalSearch" placeholder="Search orders, stores...">
        </div>
    </div>

    {{-- KPI CARDS — linked to real data --}}
    <div class="cards-grid">
        <a href="{{ route('delivery.panel.earnings') }}" class="card-kpi">
            <div class="kpi-label">Total Revenue</div>
            <div class="kpi-value">₹{{ number_format($totalRevenue) }} <span class="kpi-spark"></span></div>
        </a>
        <a href="{{ route('delivery.panel.orders') }}" class="card-kpi">
            <div class="kpi-label">Total Assigned</div>
            <div class="kpi-value">{{ $totalAssigned }} <span class="kpi-spark green"></span></div>
        </a>
        <a href="{{ route('delivery.panel.earnings') }}" class="card-kpi">
            <div class="kpi-label">Today Earnings</div>
            <div class="kpi-value">₹{{ number_format($todayEarnings, 2) }} <span class="kpi-spark blue"></span></div>
        </a>
        <a href="{{ route('delivery.panel.orders') }}?status=Failed" class="card-kpi">
            <div class="kpi-label">Failed / Returned</div>
            <div class="kpi-value">{{ $failedReturned }} <span class="kpi-spark pink"></span></div>
        </a>
    </div>

    {{-- MENU GRID — all buttons functional --}}
    <div class="menu-grid">
        <a href="{{ route('delivery.panel.stores') }}" class="menu-btn {{ request()->routeIs('delivery.panel.stores') ? 'highlight' : '' }}">
            <i class="fa fa-users"></i><span>Parties</span>
        </a>
        <a href="{{ route('delivery.panel.items') }}" class="menu-btn {{ request()->routeIs('delivery.panel.items') ? 'highlight' : '' }}">
            <i class="fa fa-box"></i><span>Items</span>
        </a>
        <a href="{{ route('delivery.panel.transactions') }}" class="menu-btn {{ request()->routeIs('delivery.panel.transactions*') ? 'highlight' : '' }}">
            <i class="fa fa-receipt"></i><span>Transactions</span>
        </a>
        <a href="{{ route('delivery.panel.attendance') }}" class="menu-btn {{ request()->routeIs('delivery.panel.attendance') ? 'highlight' : '' }}">
            <i class="fa fa-calendar-check"></i><span>Attendance</span>
        </a>
        <a href="{{ route('delivery.panel.earnings') }}" class="menu-btn {{ request()->routeIs('delivery.panel.earnings','delivery.panel.incentives') ? 'highlight' : '' }}">
            <i class="fa fa-trophy"></i><span>Earnings</span>
        </a>
        <a href="{{ route('delivery.panel.earnings') }}" class="menu-btn">
            <i class="fa fa-wallet"></i><span>Expenses</span>
        </a>
        <a href="{{ route('delivery.panel.orders') }}" class="menu-btn {{ request()->routeIs('delivery.panel.orders','delivery.panel.my.orders') ? 'highlight' : '' }}">
            <i class="fa fa-truck"></i><span>Deliveries</span>
        </a>
    </div>

    {{-- TODAY'S SHIPMENTS — real orders from DB --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-title">Today's Shipments</div>
            <div class="tabs" id="deliveryTabs">
                <div class="tab active" data-status="all">All</div>
                <div class="tab" data-status="Dispatched">Dispatched</div>
                <div class="tab" data-status="Out for Delivery">Out for Delivery</div>
                <div class="tab" data-status="Delivered">Delivered</div>
            </div>
        </div>
        <div class="delivery-list" id="deliveryList">
            @forelse($pendingOrders ?? [] as $order)
            <div class="delivery-card" data-status="{{ $order->status }}">
                <div class="delivery-top">
                    <div>
                        <div class="fw-bold">{{ optional($order->store)->store_name ?? 'Store #'.$order->id }}</div>
                        <div class="muted small">{{ optional($order->store)->address ?? 'No address' }}</div>
                    </div>
                    <span class="d-badge d-badge-{{ strtolower(str_replace(' ','-',$order->status)) === 'delivered' ? 'delivered' : (str_contains(strtolower($order->status),'transit') || str_contains(strtolower($order->status),'delivery') ? 'transit' : 'dispatched') }}">
                        {{ $order->status }}
                    </span>
                </div>
                <div class="muted small">Order #{{ $order->order_number ?? $order->id }} · {{ $order->created_at?->format('d M Y') }}</div>
                <div class="actions">
                    @if(optional($order->store)->phone)
                    <a href="tel:{{ $order->store->phone }}" class="chip"><i class="fa fa-phone"></i> Call</a>
                    @else
                    <button class="chip" onclick="dpToast('No phone on record')"><i class="fa fa-phone"></i> Call</button>
                    @endif
                    @php
                      $sLat  = optional($order->store)->latitude;
                      $sLng  = optional($order->store)->longitude;
                      $sAddr = optional($order->store)->address;
                      $navUrl = ($sLat && $sLng)
                        ? 'https://www.google.com/maps/dir/?api=1&destination='.$sLat.','.$sLng
                        : ($sAddr ? 'https://www.google.com/maps/dir/?api=1&destination='.urlencode($sAddr) : null);
                    @endphp
                    @if($navUrl)
                    <a href="{{ $navUrl }}" target="_blank" class="chip" style="background:#dcfce7;color:#16a34a;"><i class="fa fa-map-marker-alt"></i> Navigate</a>
                    @else
                    <button class="chip" onclick="dpToast('No location set for this store')"><i class="fa fa-map-marker-alt"></i> Navigate</button>
                    @endif
                    <a href="{{ route('delivery.panel.order.details', $order->id) }}" class="ghost">View Details</a>
                    <form method="POST" action="{{ route('delivery.panel.orders.status', $order->id) }}" style="display:inline;">
                        @csrf
                        @php
                            $nextStatus = match($order->status) {
                                'Assigned','Pending' => 'Picked',
                                'Picked' => 'Out for Delivery',
                                'Out for Delivery' => 'Delivered',
                                default => null
                            };
                        @endphp
                        @if($nextStatus)
                        <input type="hidden" name="status" value="{{ $nextStatus }}">
                        <button type="submit" class="ghost" style="border-color:#2563eb;color:#2563eb;">→ {{ $nextStatus }}</button>
                        @endif
                    </form>
                </div>
            </div>
            @empty
            <div class="muted" style="padding:16px 0;text-align:center;">
                <i class="fa fa-truck" style="font-size:28px;opacity:.2;display:block;margin-bottom:8px;"></i>
                No pending shipments today.
            </div>
            @endforelse
        </div>
        <a href="{{ route('delivery.panel.orders') }}" class="primary-btn" style="display:block;text-align:center;text-decoration:none;">
            <i class="fa fa-route me-1"></i> View All Orders
        </a>
    </div>

    {{-- PERFORMANCE + PROFILE --}}
    <div class="two-col">
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">Delivery Stats</div>
                <a href="{{ route('delivery.panel.earnings') }}" class="btn-primary-slim">Full Report</a>
            </div>
            <table class="metrics-table">
                <tr><td>Today Assigned</td><td>{{ $todayAssigned }}</td></tr>
                <tr><td>Pending Deliveries</td><td>{{ $pendingDeliveries }}</td></tr>
                <tr><td>Delivered Today</td><td>{{ $todayDelivered }}</td></tr>
                <tr><td>Failed / Returned</td><td>{{ $failedReturned }}</td></tr>
                <tr><td>Today Earnings</td><td>₹{{ number_format($todayEarnings, 2) }}</td></tr>
                <tr><td>Total Revenue Handled</td><td>₹{{ number_format($totalRevenue) }}</td></tr>
            </table>
        </div>

        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">{{ strtoupper($deliveryUserName) }}</div>
                <a href="{{ route('delivery.panel.profile') }}" class="btn-primary-slim">Edit Profile</a>
            </div>
            <div class="profile-box">
                <div class="avatar">{{ strtoupper(substr($deliveryUserName,0,2)) }}</div>
                <div>
                    <div style="font-weight:700;">{{ $deliveryUserName }}</div>
                    <div class="muted small">{{ ucfirst($user?->role ?? 'Delivery Partner') }}</div>
                    @if($deliveryProfile?->phone ?? $user?->phone)
                    <div class="muted small"><i class="fa fa-phone me-1"></i>{{ $deliveryProfile?->phone ?? $user?->phone }}</div>
                    @endif
                </div>
            </div>
            @php $zones = collect($deliveryProfile?->zones_json ?? [])->filter(); @endphp
            @if($zones->count())
            <div style="margin-top:8px;">
                @foreach($zones as $zone)
                <span class="d-badge d-badge-blue me-1 mb-1">{{ $zone }}</span>
                @endforeach
            </div>
            @endif
            <a href="{{ route('delivery.panel.attendance') }}" class="primary-btn" style="display:block;text-align:center;text-decoration:none;margin-top:12px;">
                <i class="fa fa-calendar-check me-1"></i> Mark Attendance
            </a>
        </div>
    </div>

    {{-- ITEMS SECTION --}}
    <div class="panel" id="section-items">
        <div class="panel-head">
            <div class="panel-title">Items Catalog</div>
            <span class="muted small">Read-only · from admin inventory</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="items-table">
                <thead><tr><th>Product</th><th>MRP</th><th>Rate</th><th>Stock</th></tr></thead>
                <tbody id="itemTable">
                    @forelse(\App\Models\Product::select('name','mrp','sell_price','available_units')->where('status',1)->take(10)->get() as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>₹{{ number_format($product->mrp ?? 0) }}</td>
                        <td>₹{{ number_format($product->sell_price ?? $product->mrp ?? 0) }}</td>
                        <td>
                            <span class="d-badge {{ ($product->available_units ?? 0) > 10 ? 'd-badge-delivered' : 'd-badge-dispatched' }}">
                                {{ $product->available_units ?? 0 }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="muted" style="text-align:center;padding:16px;">No products found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TRANSACTIONS SECTION --}}
    <div class="panel" id="section-transactions">
        <div class="panel-head">
            <div class="panel-title">Recent Orders / Transactions</div>
            <a href="{{ route('delivery.panel.orders') }}" class="btn-primary-slim">View All</a>
        </div>
        <div class="dp-search" style="margin-bottom:12px;">
            <i class="fa fa-search"></i>
            <input type="text" placeholder="Search order #, store..." id="txnSearch">
        </div>
        <div class="tabs" id="txnTabs" style="margin-bottom:12px;">
            <div class="tab active" data-status="all">All</div>
            <div class="tab" data-status="Pending">Pending</div>
            <div class="tab" data-status="Out for Delivery">Out for Delivery</div>
            <div class="tab" data-status="Delivered">Delivered</div>
        </div>
        <div class="loader" id="txnLoader"><div class="spinner"></div><span class="muted">Loading...</span></div>
        <div id="txnList">
            @forelse($recentOrders ?? [] as $order)
            <div class="txn-card" data-status="{{ $order->status }}" data-search="{{ strtolower(($order->order_number ?? $order->id).' '.optional($order->store)->store_name) }}">
                <div>
                    <div class="fw-bold">#{{ $order->order_number ?? $order->id }}</div>
                    <div class="muted small">{{ optional($order->store)->store_name ?? 'No Store' }} · {{ $order->created_at?->format('d M') }}</div>
                </div>
                <div style="text-align:right;">
                    <div class="fw-bold">₹{{ number_format($order->total_amount ?? $order->amount ?? 0) }}</div>
                    <span class="d-badge d-badge-{{ $order->status === 'Delivered' ? 'delivered' : ($order->status === 'Pending' ? 'dispatched' : 'transit') }}">
                        {{ $order->status }}
                    </span>
                </div>
            </div>
            @empty
            <div class="muted" style="text-align:center;padding:16px;">No recent orders.</div>
            @endforelse
        </div>
    </div>

    {{-- RECENT ACTIVITY --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-title">Recent Delivery Activity</div>
            <span class="d-badge d-badge-blue">Live</span>
        </div>
        <div class="activity-list">
            @forelse($recentOrders->take(5) ?? [] as $order)
            <div class="activity-item">
                <span class="dot {{ $order->status === 'Delivered' ? 'green' : ($order->status === 'Out for Delivery' ? 'orange' : 'blue') }}"></span>
                <div>
                    <div class="fw-bold" style="font-size:13px;">{{ optional($order->store)->store_name ?? 'Order #'.$order->id }}</div>
                    <div class="muted small">Status: {{ $order->status }} · ₹{{ number_format($order->total_amount ?? $order->amount ?? 0) }}</div>
                    <div class="muted small">{{ $order->updated_at?->diffForHumans() }}</div>
                </div>
            </div>
            @empty
            <div class="muted small">No recent activity.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Delivery tab filter (for pending orders section)
document.querySelectorAll('#deliveryTabs .tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('#deliveryTabs .tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const status = tab.dataset.status;
        document.querySelectorAll('#deliveryList .delivery-card').forEach(card => {
            card.style.display = (status === 'all' || card.dataset.status === status) ? '' : 'none';
        });
    });
});

// Transaction tab filter
document.querySelectorAll('#txnTabs .tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('#txnTabs .tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const status = tab.dataset.status;
        document.querySelectorAll('#txnList .txn-card').forEach(card => {
            card.style.display = (status === 'all' || card.dataset.status === status) ? '' : 'none';
        });
    });
});

// Transaction search
document.getElementById('txnSearch').addEventListener('input', e => {
    const q = e.target.value.toLowerCase();
    document.querySelectorAll('#txnList .txn-card').forEach(card => {
        card.style.display = card.dataset.search.includes(q) ? '' : 'none';
    });
});

// Global search — scroll to matching section
document.getElementById('globalSearch').addEventListener('input', e => {
    const q = e.target.value.toLowerCase();
    if (!q) return;
    if ('items'.includes(q)) document.getElementById('section-items').scrollIntoView({behavior:'smooth'});
    else if ('transactions orders'.includes(q)) document.getElementById('section-transactions').scrollIntoView({behavior:'smooth'});
});
</script>
@endpush
