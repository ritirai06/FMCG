@extends('layouts.app')

@section('title', 'Reports & Analytics')

@push('styles')
<style>
.card-box { background:#fff; border-radius:10px; padding:18px 20px; box-shadow:0 1px 3px rgba(0,0,0,.08); margin-bottom:18px; border:1px solid #E2E8F0; }
.chart-container { position:relative; height:280px; }
.kpi-card { background:#fff; border-radius:10px; padding:16px 18px; box-shadow:0 1px 3px rgba(0,0,0,.08); border:1px solid #E2E8F0; height:100%; }
.kpi-card .kpi-label { font-size:11.5px; font-weight:600; color:#64748B; text-transform:uppercase; letter-spacing:.5px; }
.kpi-card .kpi-value { font-size:22px; font-weight:700; color:#0F172A; margin:4px 0 0; }
.kpi-card .kpi-icon { width:40px; height:40px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0; }
.section-divider { font-size:12px; font-weight:700; color:#2563EB; text-transform:uppercase; letter-spacing:.6px; border-left:3px solid #2563EB; padding-left:10px; margin:24px 0 14px; }
#marginTable th { background:#F8FAFC; color:#64748B; font-size:11.5px; font-weight:600; text-transform:uppercase; letter-spacing:.4px; }
#marginTable td, #ordersTable td { font-size:13px; }
.margin-positive { color:#16a34a; font-weight:700; }
.margin-negative { color:#dc2626; font-weight:700; }
.no-data-msg { text-align:center; padding:50px 20px; color:#94a3b8; }
.no-data-msg i { font-size:36px; display:block; margin-bottom:10px; }
</style>
@endpush

@section('page_title', 'Reports & Analytics')
@section('navbar_right')
    <button class="btn btn-outline-primary btn-sm" id="exportBtn">
        <i class="bi bi-download me-1"></i>Export Excel
    </button>
@endsection

@section('content')

{{-- FILTERS --}}
<div class="card-box">
    <div class="row g-2 align-items-end">
        <div class="col-md-2 col-6">
            <label class="form-label fw-semibold" style="font-size:12px;">Start Date</label>
            <input type="date" id="fStart" class="form-control form-control-sm" value="{{ date('Y-m-d', strtotime('-29 days')) }}">
        </div>
        <div class="col-md-2 col-6">
            <label class="form-label fw-semibold" style="font-size:12px;">End Date</label>
            <input type="date" id="fEnd" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
        </div>
        <div class="col-md-2 col-6">
            <label class="form-label fw-semibold" style="font-size:12px;">Warehouse</label>
            <select id="fWarehouse" class="form-select form-select-sm">
                <option value="">All Warehouses</option>
                @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 col-6">
            <label class="form-label fw-semibold" style="font-size:12px;">City</label>
            <select id="fCity" class="form-select form-select-sm">
                <option value="">All Cities</option>
                @foreach($cities as $city)
                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 col-6">
            <label class="form-label fw-semibold" style="font-size:12px;">Sales Person</label>
            <select id="fSalesPerson" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach($salesPersons as $sp)
                    <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 col-6">
            <label class="form-label fw-semibold" style="font-size:12px;">Delivery Person</label>
            <select id="fDelivery" class="form-select form-select-sm">
                <option value="">All</option>
                @foreach($deliveryPersons as $dp)
                    <option value="{{ $dp->id }}">{{ $dp->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 col-6">
            <label class="form-label fw-semibold" style="font-size:12px;">Order Status</label>
            <select id="fStatus" class="form-select form-select-sm">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div class="col-md-2 col-6">
            <label class="form-label fw-semibold" style="font-size:12px;">Category</label>
            <select id="fCategory" class="form-select form-select-sm">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 col-6">
            <label class="form-label fw-semibold" style="font-size:12px;">Brand</label>
            <select id="fBrand" class="form-select form-select-sm">
                <option value="">All Brands</option>
                @foreach($brands as $b)
                    <option value="{{ $b->name }}">{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 col-6">
            <label class="form-label fw-semibold" style="font-size:12px;">Product</label>
            <select id="fProduct" class="form-select form-select-sm">
                <option value="">All Products</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 col-6">
            <label class="form-label fw-semibold" style="font-size:12px;">Min Amount (₹)</label>
            <input type="number" id="fMinAmt" class="form-control form-control-sm" placeholder="0" min="0">
        </div>
        <div class="col-md-2 col-6">
            <label class="form-label fw-semibold" style="font-size:12px;">Max Amount (₹)</label>
            <input type="number" id="fMaxAmt" class="form-control form-control-sm" placeholder="Any" min="0">
        </div>
        <div class="col-md-2 col-12 d-flex gap-2">
            <button class="btn btn-primary btn-sm flex-fill" id="applyFilter">
                <i class="bi bi-funnel me-1"></i>Apply
            </button>
            <button class="btn btn-outline-secondary btn-sm" id="resetFilter" title="Reset filters">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
</div>

{{-- KPI CARDS --}}
<div class="row g-3 mb-2">
    <div class="col-md-2 col-6">
        <div class="kpi-card d-flex align-items-center gap-3">
            <div class="kpi-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="bi bi-receipt"></i></div>
            <div><div class="kpi-label">Total Orders</div><div class="kpi-value" id="kpiOrders">—</div></div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="kpi-card d-flex align-items-center gap-3">
            <div class="kpi-icon" style="background:#f0fdf4;color:#16a34a;"><i class="bi bi-currency-rupee"></i></div>
            <div><div class="kpi-label">Total Sales</div><div class="kpi-value" id="kpiSales">—</div></div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="kpi-card d-flex align-items-center gap-3">
            <div class="kpi-icon" style="background:#f0fdf4;color:#16a34a;"><i class="bi bi-bar-chart-line"></i></div>
            <div><div class="kpi-label">Total Margin</div><div class="kpi-value" id="kpiMargin">—</div></div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="kpi-card d-flex align-items-center gap-3">
            <div class="kpi-icon" style="background:#fefce8;color:#ca8a04;"><i class="bi bi-clock-history"></i></div>
            <div><div class="kpi-label">Pending</div><div class="kpi-value" id="kpiPending">—</div></div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="kpi-card d-flex align-items-center gap-3">
            <div class="kpi-icon" style="background:#eff6ff;color:#1d4ed8;"><i class="bi bi-calendar-day"></i></div>
            <div><div class="kpi-label">Today's Margin</div><div class="kpi-value" id="kpiTodayMargin">—</div></div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="kpi-card d-flex align-items-center gap-3">
            <div class="kpi-icon" style="background:#fdf4ff;color:#9333ea;"><i class="bi bi-building"></i></div>
            <div>
                <div class="kpi-label">Top Warehouse</div>
                <div class="kpi-value" id="kpiTopWh" style="font-size:15px;">—</div>
                <div class="small text-muted" id="kpiTopWhMargin"></div>
            </div>
        </div>
    </div>
</div>

{{-- CHARTS ROW 1 --}}
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-box">
            <div class="fw-bold mb-3" style="font-size:14px;"><i class="bi bi-graph-up me-1 text-primary"></i>Sales Trend</div>
            <div class="chart-container"><canvas id="salesChart"></canvas></div>
            <div id="salesNoData" class="no-data-msg" style="display:none;"><i class="bi bi-graph-up"></i>No data</div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-box">
            <div class="fw-bold mb-3" style="font-size:14px;"><i class="bi bi-pie-chart me-1 text-warning"></i>Order Status</div>
            <div class="chart-container"><canvas id="orderChart"></canvas></div>
            <div id="statusNoData" class="no-data-msg" style="display:none;"><i class="bi bi-pie-chart"></i>No data</div>
        </div>
    </div>
</div>

{{-- CHARTS ROW 2 --}}
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-box">
            <div class="fw-bold mb-3" style="font-size:14px;"><i class="bi bi-graph-up me-1 text-success"></i>Daily Margin — Warehouse-wise</div>
            <div class="chart-container" style="height:320px;"><canvas id="marginLineChart"></canvas></div>
            <div id="marginLineNoData" class="no-data-msg" style="display:none;"><i class="bi bi-bar-chart-line"></i>No margin data</div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-box">
            <div class="fw-bold mb-3" style="font-size:14px;"><i class="bi bi-building me-1 text-success"></i>Warehouse-wise Margin</div>
            <div class="chart-container" style="height:320px;"><canvas id="marginBarChart"></canvas></div>
            <div id="marginBarNoData" class="no-data-msg" style="display:none;"><i class="bi bi-building"></i>No data</div>
        </div>
    </div>
</div>

{{-- MARGIN TABLE --}}
<div class="card-box">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="fw-bold" style="font-size:14px;"><i class="bi bi-table me-1 text-primary"></i>Margin Detail — Date × Warehouse</div>
        <span class="badge bg-primary" id="marginRowCount">0 rows</span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle table-hover" id="marginTable">
            <thead>
                <tr><th>Date</th><th>Warehouse</th><th>Orders</th><th>Revenue</th><th>Margin</th></tr>
            </thead>
            <tbody id="marginTableBody">
                <tr><td colspan="5" class="text-center text-muted py-4">Loading…</td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- ORDERS TABLE --}}
<div class="card-box">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="fw-bold" style="font-size:14px;"><i class="bi bi-list-ul me-1 text-primary"></i>Orders</div>
        <span class="badge bg-secondary" id="ordersRowCount">0 rows</span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle table-hover" id="ordersTable">
            <thead class="table-light">
                <tr><th>Date</th><th>Order #</th><th>Customer</th><th>Sales Person</th><th>City</th><th>Amount</th><th>Status</th></tr>
            </thead>
            <tbody id="ordersTableBody">
                <tr><td colspan="7" class="text-center text-muted py-4">Loading…</td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const COLORS = ['#3b82f6','#16a34a','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899','#84cc16'];
let salesChart = null, orderChart = null, marginLineChart = null, marginBarChart = null;

const fmt = n => '₹' + Number(n).toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2});

function buildParams() {
    const p = new URLSearchParams({
        start_date: document.getElementById('fStart').value,
        end_date:   document.getElementById('fEnd').value,
    });
    const add = (id, key) => { const v = document.getElementById(id).value; if (v) p.set(key, v); };
    add('fWarehouse',  'warehouse_id');
    add('fCity',       'city');
    add('fSalesPerson','sales_person');
    add('fDelivery',   'delivery_person');
    add('fStatus',     'order_status');
    add('fCategory',   'category');
    add('fBrand',      'brand');
    add('fProduct',    'product_id');
    add('fMinAmt',     'min_amount');
    add('fMaxAmt',     'max_amount');
    return p;
}

function loadAnalytics() {
    fetch('/reports/analytics?' + buildParams())
        .then(r => r.json())
        .then(data => {
            updateKPIs(data.kpi);
            updateSalesChart(data.trend);
            updateStatusChart(data.status_break);
            updateLineChart(data.margin_daily);
            updateBarChart(data.margin_daily);
            updateMarginTable(data.margin_daily);
            updateOrdersTable(data.orders);
        })
        .catch(console.error);
}

function updateKPIs(k) {
    document.getElementById('kpiOrders').innerText      = k.total_orders.toLocaleString('en-IN');
    document.getElementById('kpiSales').innerText       = fmt(k.total_sales);
    document.getElementById('kpiMargin').innerText      = fmt(k.total_margin);
    document.getElementById('kpiPending').innerText     = k.pending.toLocaleString('en-IN');
    document.getElementById('kpiTodayMargin').innerText = fmt(k.today_margin);
    document.getElementById('kpiTopWh').innerText       = k.top_warehouse || '—';
    document.getElementById('kpiTopWhMargin').innerText = k.top_warehouse_margin ? fmt(k.top_warehouse_margin) : '';
}

function updateSalesChart(trend) {
    const el = document.getElementById('salesChart');
    const nd = document.getElementById('salesNoData');
    if (!trend.length) { el.style.display='none'; nd.style.display='block'; if(salesChart){salesChart.destroy();salesChart=null;} return; }
    el.style.display='block'; nd.style.display='none';
    const labels = trend.map(r => r.date), data = trend.map(r => parseFloat(r.total));
    if (salesChart) salesChart.destroy();
    salesChart = new Chart(el, {
        type: 'line',
        data: { labels, datasets: [{ label:'Sales', data, backgroundColor:'rgba(59,130,246,.15)', borderColor:'#3b82f6', borderWidth:2, tension:.4, fill:true, pointRadius:3 }] },
        options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
    });
}

function updateStatusChart(statusBreak) {
    const el = document.getElementById('orderChart');
    const nd = document.getElementById('statusNoData');
    const labels = Object.keys(statusBreak), values = Object.values(statusBreak);
    if (!labels.length) { el.style.display='none'; nd.style.display='block'; if(orderChart){orderChart.destroy();orderChart=null;} return; }
    el.style.display='block'; nd.style.display='none';
    if (orderChart) orderChart.destroy();
    orderChart = new Chart(el, {
        type: 'doughnut',
        data: { labels, datasets: [{ data: values, backgroundColor:['#16a34a','#facc15','#ef4444','#3b82f6','#8b5cf6'] }] },
        options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}} }
    });
}

function updateLineChart(daily) {
    const el = document.getElementById('marginLineChart');
    const nd = document.getElementById('marginLineNoData');
    if (!daily.length) { el.style.display='none'; nd.style.display='block'; if(marginLineChart){marginLineChart.destroy();marginLineChart=null;} return; }
    el.style.display='block'; nd.style.display='none';
    const warehouses = [...new Set(daily.map(r => r.warehouse || 'Unassigned'))];
    const dates = [...new Set(daily.map(r => r.date))].sort();
    const datasets = warehouses.map((wh, i) => {
        const map = {};
        daily.filter(r => (r.warehouse||'Unassigned') === wh).forEach(r => { map[r.date] = parseFloat(r.total_margin); });
        return { label:wh, data:dates.map(d => map[d]??0), borderColor:COLORS[i%COLORS.length], backgroundColor:COLORS[i%COLORS.length]+'22', borderWidth:2, tension:.4, fill:false, pointRadius:3 };
    });
    if (marginLineChart) marginLineChart.destroy();
    marginLineChart = new Chart(el, {
        type: 'line',
        data: { labels:dates, datasets },
        options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom',labels:{boxWidth:12,font:{size:11}}}}, scales:{y:{beginAtZero:true,ticks:{callback:v=>'₹'+v.toLocaleString('en-IN')}}} }
    });
}

function updateBarChart(daily) {
    const el = document.getElementById('marginBarChart');
    const nd = document.getElementById('marginBarNoData');
    if (!daily.length) { el.style.display='none'; nd.style.display='block'; if(marginBarChart){marginBarChart.destroy();marginBarChart=null;} return; }
    el.style.display='block'; nd.style.display='none';
    const byWh = {};
    daily.forEach(r => { const k = r.warehouse||'Unassigned'; byWh[k] = (byWh[k]||0) + parseFloat(r.total_margin); });
    const labels = Object.keys(byWh), values = Object.values(byWh);
    if (marginBarChart) marginBarChart.destroy();
    marginBarChart = new Chart(el, {
        type: 'bar',
        data: { labels, datasets:[{ label:'Margin', data:values, backgroundColor:labels.map((_,i)=>COLORS[i%COLORS.length]+'cc'), borderRadius:6, borderSkipped:false }] },
        options: { responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true,ticks:{callback:v=>'₹'+v.toLocaleString('en-IN')}}} }
    });
}

function updateMarginTable(daily) {
    const tbody = document.getElementById('marginTableBody');
    document.getElementById('marginRowCount').innerText = daily.length + ' rows';
    if (!daily.length) { tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4">No data</td></tr>'; return; }
    tbody.innerHTML = daily.map(r => {
        const m = parseFloat(r.total_margin), cls = m >= 0 ? 'margin-positive' : 'margin-negative';
        return `<tr><td>${r.date}</td><td>${r.warehouse||'<span class="text-muted">Unassigned</span>'}</td><td>${r.order_count}</td><td>${fmt(r.revenue)}</td><td class="${cls}">${fmt(m)}</td></tr>`;
    }).join('');
}

function updateOrdersTable(orders) {
    const tbody = document.getElementById('ordersTableBody');
    document.getElementById('ordersRowCount').innerText = orders.length + ' rows';
    if (!orders.length) { tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">No orders found</td></tr>'; return; }
    const badge = s => {
        const map = { delivered:'success', pending:'warning text-dark', confirmed:'info', cancelled:'danger' };
        return `<span class="badge bg-${map[s?.toLowerCase()]||'secondary'}">${s||'—'}</span>`;
    };
    tbody.innerHTML = orders.map(o => `<tr>
        <td>${o.date}</td>
        <td>${o.order_number||'—'}</td>
        <td>${o.customer_name||o.store||'—'}</td>
        <td>${o.sp_name||'—'}</td>
        <td>${o.city_name||'—'}</td>
        <td>₹${Number(o.amount).toLocaleString('en-IN',{minimumFractionDigits:2})}</td>
        <td>${badge(o.status)}</td>
    </tr>`).join('');
}

// Dependent dropdowns: category → brand → product
document.getElementById('fCategory').addEventListener('change', function() {
    const cat = this.value;
    const brandSel = document.getElementById('fBrand');
    const prodSel  = document.getElementById('fProduct');
    brandSel.innerHTML = '<option value="">All Brands</option>';
    prodSel.innerHTML  = '<option value="">All Products</option>';
    if (!cat) return;
    fetch('/reports/filter/brands?category=' + encodeURIComponent(cat))
        .then(r => r.json())
        .then(brands => brands.forEach(b => brandSel.insertAdjacentHTML('beforeend', `<option value="${b.name}">${b.name}</option>`)));
    fetch('/reports/filter/products?category=' + encodeURIComponent(cat))
        .then(r => r.json())
        .then(prods => prods.forEach(p => prodSel.insertAdjacentHTML('beforeend', `<option value="${p.id}">${p.name}</option>`)));
});

document.getElementById('fBrand').addEventListener('change', function() {
    const brand = this.value, cat = document.getElementById('fCategory').value;
    const prodSel = document.getElementById('fProduct');
    prodSel.innerHTML = '<option value="">All Products</option>';
    const params = new URLSearchParams();
    if (cat)   params.set('category', cat);
    if (brand) params.set('brand', brand);
    fetch('/reports/filter/products?' + params)
        .then(r => r.json())
        .then(prods => prods.forEach(p => prodSel.insertAdjacentHTML('beforeend', `<option value="${p.id}">${p.name}</option>`)));
});

// Reset
document.getElementById('resetFilter').addEventListener('click', () => {
    document.getElementById('fStart').value      = '{{ date("Y-m-d", strtotime("-29 days")) }}';
    document.getElementById('fEnd').value        = '{{ date("Y-m-d") }}';
    ['fWarehouse','fCity','fSalesPerson','fDelivery','fStatus','fCategory','fBrand','fProduct'].forEach(id => document.getElementById(id).value = '');
    document.getElementById('fMinAmt').value = '';
    document.getElementById('fMaxAmt').value = '';
    // Restore full brand/product lists
    document.getElementById('fBrand').innerHTML  = '<option value="">All Brands</option>' + @json($brands->map(fn($b)=>['name'=>$b->name]))->map(b=>`<option value="${b.name}">${b.name}</option>`).join('');
    document.getElementById('fProduct').innerHTML = '<option value="">All Products</option>' + @json($products->map(fn($p)=>['id'=>$p->id,'name'=>$p->name]))->map(p=>`<option value="${p.id}">${p.name}</option>`).join('');
    loadAnalytics();
});

document.getElementById('applyFilter').addEventListener('click', loadAnalytics);
document.getElementById('exportBtn').addEventListener('click', () => { window.location = '/reports/margin/export?' + buildParams(); });

loadAnalytics();
</script>
@endpush
