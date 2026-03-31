
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
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
<div class="content">
  <div class="topbar">
    <h5><i class="bi bi-receipt text-primary me-2"></i>Orders & Invoices Management</h5>
    <div>
      <button class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#addOrderModal">
        <i class="bi bi-plus-circle me-1"></i> Add Order
      </button>
      <button class="btn btn-outline-secondary btn-sm" id="refreshOrders"><i class="bi bi-arrow-repeat"></i></button>
    </div>
  </div>

  <!-- TABS -->
  <ul class="nav nav-tabs mt-4" id="orderTabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#orders" role="tab">Orders</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#invoices" role="tab">Invoices</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#orderSummary" role="tab">Order Summary</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#invoiceSummary" role="tab">Invoice Summary</a></li>
  </ul>

  <div class="tab-content mt-4">
    <!-- ORDERS -->
    <div class="tab-pane fade show active" id="orders" role="tabpanel">
      <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold mb-0">Orders List</h6>
          <div class="d-flex gap-2">
            <input id="orderSearch" oninput="searchOrders()" class="form-control form-control-sm" placeholder="Search orders, store, customer" style="min-width:260px">
            <select id="statusFilter" class="form-select form-select-sm" onchange="searchOrders()" style="max-width:160px">
              <option value="">All Status</option>
              <option>Pending</option>
              <option>Out for Delivery</option>
              <option>Delivered</option>
            </select>
            <button class="btn btn-outline-secondary btn-sm" onclick="exportOrdersCSV()"><i class="bi bi-download"></i></button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table align-middle" id="ordersTable">
            <thead>
              <tr>
                <th style="width:40px"><input id="selectAllOrders" type="checkbox" onclick="toggleSelectAll(this)"></th>
                <th>Order ID</th>
                <th>Store</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Status</th>
                <th style="width:130px">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><input class="order-checkbox" data-order="#ORD-1001" type="checkbox"></td>
                <td>#ORD-1001</td><td>Store A</td><td>Rahul</td><td>₹2,500</td><td><span class="badge bg-success">Delivered</span></td>
                <td>
                  <button class="btn btn-light btn-sm" onclick="openViewOrder('#ORD-1001')"><i class="bi bi-eye text-primary"></i></button>
                  <button class="btn btn-light btn-sm" onclick="openCreateInvoiceModal('#ORD-1001')" title="Create Invoice"><i class="bi bi-receipt"></i></button>
                </td>
              </tr>
              <tr>
                <td><input class="order-checkbox" data-order="#ORD-1002" type="checkbox"></td>
                <td>#ORD-1002</td><td>Store B</td><td>Simran</td><td>₹1,200</td><td><span class="badge bg-warning text-dark">Out for Delivery</span></td>
                <td>
                  <button class="btn btn-light btn-sm" onclick="openViewOrder('#ORD-1002')"><i class="bi bi-eye text-primary"></i></button>
                  <button class="btn btn-light btn-sm" onclick="openCreateInvoiceModal('#ORD-1002')" title="Create Invoice"><i class="bi bi-receipt"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
          <div>
            <button class="btn btn-sm btn-primary" onclick="bulkCreateInvoices()">Create Invoice for Selected</button>
            <button class="btn btn-sm btn-outline-secondary ms-2" onclick="clearSelection()">Clear Selection</button>
          </div>
          <div class="text-muted">Showing <span id="ordersCount">2</span> orders</div>
        </div>
      </div>
    </div>

    <!-- INVOICES -->
    <div class="tab-pane fade" id="invoices" role="tabpanel">
      <div class="table-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="fw-bold mb-0">Invoices</h6>
          <div class="d-flex gap-2">
            <input id="invoiceSearch" oninput="searchInvoices()" class="form-control form-control-sm" placeholder="Search invoices, order id" style="min-width:240px">
            <button class="btn btn-outline-secondary btn-sm" onclick="exportInvoicesCSV()"><i class="bi bi-download"></i></button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table align-middle" id="invoicesTable">
            <thead>
              <tr><th>Invoice</th><th>Order ID</th><th>Date</th><th>Amount</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <tr><td>#INV-2001</td><td>#ORD-1001</td><td>2026-02-15</td><td>₹2,500</td><td><span class="badge bg-success">Paid</span></td><td><button class="btn btn-light btn-sm" onclick="openViewInvoice('#INV-2001')"><i class="bi bi-eye text-primary"></i></button></td></tr>
              <tr><td>#INV-2002</td><td>#ORD-1002</td><td>2026-02-18</td><td>₹1,200</td><td><span class="badge bg-secondary">Pending</span></td><td><button class="btn btn-light btn-sm" onclick="openViewInvoice('#INV-2002')"><i class="bi bi-eye text-primary"></i></button></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ORDER SUMMARY -->
    <div class="tab-pane fade" id="orderSummary" role="tabpanel">
      <div class="table-card">
        <h6 class="fw-bold mb-3">Order Summary</h6>
        <div class="row">
          <div class="col-md-4">
            <div class="p-3 bg-white rounded-3 shadow-sm">
              <h3 class="mb-0" id="ordersTotalCount">0</h3>
              <div class="text-muted" id="ordersTotalLabel">Total Orders</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 bg-white rounded-3 shadow-sm">
              <h3 class="mb-0" id="ordersGrossSales">₹0.00</h3>
              <div class="text-muted">Gross Sales</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 bg-white rounded-3 shadow-sm">
              <h3 class="mb-0" id="ordersOnTime">0%</h3>
              <div class="text-muted">On-time Delivery</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- INVOICE SUMMARY -->
    <div class="tab-pane fade" id="invoiceSummary" role="tabpanel">
      <div class="table-card">
        <h6 class="fw-bold mb-3">Invoice Summary</h6>
        <table class="table align-middle">
          <thead><tr><th>Month</th><th>Issued</th><th>Paid</th><th>Pending</th></tr></thead>
          <tbody id="invoiceSummaryBody">
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- ADD ORDER MODAL -->
<div class="modal fade" id="addOrderModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content p-3">
      <div class="modal-header"><h5 class="modal-title">Add Order</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Store</label><input id="add_store" type="text" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Customer</label><input id="add_customer" type="text" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Total Amount</label><input id="add_amount" type="number" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Status</label>
              <select id="add_status" class="form-select"><option>Pending</option><option>Out for Delivery</option><option>Delivered</option></select>
            </div>
          </div>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" onclick="addOrder()">Save Order</button></div>
    </div>
  </div>
</div>

<!-- VIEW ORDER MODAL -->
<div class="modal fade" id="viewOrderModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content p-3">
      <div class="modal-header"><h5 class="modal-title">Order Details</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <p><strong>Order ID:</strong> #ORD-1001</p>
        <p><strong>Store:</strong> Store A</p>
        <p><strong>Customer:</strong> Rahul</p>
        <p><strong>Items:</strong></p>
        <ul>
          <li>Product A x1 - ₹1,200</li>
          <li>Product B x2 - ₹650</li>
        </ul>
        <p><strong>Total:</strong> ₹2,500</p>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Close</button><button class="btn btn-primary">Create Invoice</button></div>
    </div>
  </div>
</div>

<!-- VIEW INVOICE MODAL -->
<div class="modal fade" id="viewInvoiceModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content p-3">
      <div class="modal-header"><h5 class="modal-title">Invoice</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <p><strong>Invoice:</strong> #INV-2001</p>
        <p><strong>Order:</strong> #ORD-1001</p>
        <p><strong>Date:</strong> 2026-02-15</p>
        <p><strong>Amount:</strong> ₹2,500</p>
        <p><strong>Status:</strong> Paid</p>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Close</button><button class="btn btn-primary">Download</button></div>
    </div>
  </div>
</div>

<!-- CREATE INVOICE MODAL -->
<div class="modal fade" id="createInvoiceModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content p-3">
      <div class="modal-header"><h5 class="modal-title">Create Invoice</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="mb-3"><label class="form-label">Order ID</label><input id="ci_order_id" class="form-control" readonly></div>
        <div class="mb-3"><label class="form-label">Invoice No</label><input id="ci_invoice_no" class="form-control" placeholder="#INV-" value="#INV-"></div>
        <div class="mb-3"><label class="form-label">Date</label><input id="ci_date" type="date" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Amount</label><input id="ci_amount" type="number" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Payment Status</label><select id="ci_status" class="form-select"><option>Pending</option><option>Paid</option></select></div>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" onclick="createInvoice()">Create Invoice</button></div>
    </div>
  </div>
</div>
</div>

<!-- EDIT ORDER MODAL -->
<div class="modal fade" id="editOrderModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content p-3">
      <div class="modal-header"><h5 class="modal-title">Edit Order</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
          <input type="hidden" id="eo_id">
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Order Number</label><input id="eo_order_number" type="text" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Store</label><input id="eo_store" type="text" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Customer</label><input id="eo_customer" type="text" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Order Date</label><input id="eo_order_date" type="date" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Total Amount</label><input id="eo_amount" type="number" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">Status</label>
              <select id="eo_status" class="form-select"><option>Pending</option><option>Out for Delivery</option><option>Delivered</option></select>
            </div>
          </div>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" onclick="updateOrder()">Save Changes</button></div>
    </div>
  </div>
</div>

<!-- EDIT INVOICE MODAL -->
<div class="modal fade" id="editInvoiceModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content p-3">
      <div class="modal-header"><h5 class="modal-title">Edit Invoice</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <input type="hidden" id="ei_id">
        <div class="mb-3"><label class="form-label">Invoice No</label><input id="ei_invoice_no" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Order ID</label><input id="ei_order_id" class="form-control" readonly></div>
        <div class="mb-3"><label class="form-label">Date</label><input id="ei_date" type="date" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Amount</label><input id="ei_amount" type="number" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Payment Status</label><select id="ei_status" class="form-select"><option>Pending</option><option>Paid</option></select></div>
      </div>
      <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" onclick="updateInvoice()">Save Changes</button></div>
    </div>
  </div>
</div>

</script>
<script>
function toggleSidebar(){document.getElementById('sidebar').classList.toggle('show');}
function toggleSubmenu(el){el.parentElement.classList.toggle('open');}

// Simple search/filter for orders
function searchOrders(){
  const q = document.getElementById('orderSearch').value.toLowerCase();
  const status = document.getElementById('statusFilter').value.toLowerCase();
  const rows = document.querySelectorAll('#ordersTable tbody tr');
  let shown = 0;
  rows.forEach(r=>{
    const text = r.innerText.toLowerCase();
    const matchesQ = !q || text.includes(q);
    const matchesStatus = !status || text.includes(status);
    if(matchesQ && matchesStatus){ r.style.display=''; shown++; } else { r.style.display='none'; }
  });
  document.getElementById('ordersCount').innerText = shown;
}

function searchInvoices(){
  const q = document.getElementById('invoiceSearch').value.toLowerCase();
  const rows = document.querySelectorAll('#invoicesTable tbody tr');
  rows.forEach(r=> r.style.display = (q===''|| r.innerText.toLowerCase().includes(q)) ? '' : 'none');
}

function toggleSelectAll(cb){
  document.querySelectorAll('.order-checkbox').forEach(c=> c.checked = cb.checked);
}
function clearSelection(){ document.querySelectorAll('.order-checkbox').forEach(c=> c.checked=false); document.getElementById('selectAllOrders').checked=false; }

function getSelectedOrders(){
  return Array.from(document.querySelectorAll('.order-checkbox:checked')).map(i=>i.dataset.order);
}

function bulkCreateInvoices(){
  const sel = getSelectedOrders();
  if(!sel.length) return alert('Select orders first');
  // create invoices sequentially and open the first created invoice view
  const created = [];
  (async function(){
    for(const orderId of sel){
      try{
        const invoiceNo = '#INV-'+(Math.floor(Math.random()*900000)+1000);
        const date = new Date().toISOString().slice(0,10);
        const amountEl = document.querySelector(`#ordersTable tbody tr input.order-checkbox[data-order-id='${orderId}']`)?.closest('tr')?.querySelectorAll('td')[4];
        const amountText = amountEl ? amountEl.innerText.replace(/[^0-9.]/g,'') : '0';
        const payload = { invoice_number: invoiceNo, order_id: orderId, date, amount: amountText, status: 'Pending' };
        const res = await fetchJson('/invoices/store', { method:'POST', body: JSON.stringify(payload) });
        if(res && res.ok){ created.push(res.data); }
        else console.warn('Invoice create failed for', orderId, res);
      }catch(e){ console.error(e); }
    }
    if(created.length){
      // refresh invoices and open the first created invoice view
      loadInvoices();
      window.open('/invoices/'+created[0].id+'/view','_blank');
      clearSelection();
    } else {
      alert('No invoices created');
    }
  })();
}

function openCreateInvoiceModal(orderId){
  document.getElementById('ci_order_id').value = orderId;
  document.getElementById('ci_invoice_no').value = '#INV-'+(Math.floor(Math.random()*900000)+1000);
  document.getElementById('ci_date').value = new Date().toISOString().slice(0,10);
  document.getElementById('ci_amount').value = '';
  var modal = new bootstrap.Modal(document.getElementById('createInvoiceModal'));
  modal.show();
}

function createInvoice(){
  const order = document.getElementById('ci_order_id').value;
  const inv = document.getElementById('ci_invoice_no').value;
  const date = document.getElementById('ci_date').value;
  const amount = document.getElementById('ci_amount').value;
  const status = document.getElementById('ci_status').value;
  // Demo: append to invoices table
  const tbody = document.querySelector('#invoicesTable tbody');
  const tr = document.createElement('tr');
  tr.innerHTML = `<td>${inv}</td><td>${order}</td><td>${date}</td><td>₹${amount}</td><td><span class="badge ${status==='Paid'? 'bg-success':'bg-secondary'}">${status}</span></td><td><button class="btn btn-light btn-sm" onclick="openViewInvoice('${inv}')"><i class="bi bi-eye text-primary"></i></button></td>`;
  tbody.prepend(tr);
  var modalEl = document.getElementById('createInvoiceModal');
  bootstrap.Modal.getInstance(modalEl).hide();
  alert('Invoice '+inv+' created for '+order);
}

function openViewOrder(orderId){
  // In a real app, fetch details. For demo, prefill modal and show.
  document.querySelector('#viewOrderModal .modal-body').innerHTML = `<p><strong>Order ID:</strong> ${orderId}</p><p><strong>Store:</strong> Sample Store</p><p><strong>Customer:</strong> Sample Customer</p><p><strong>Items:</strong></p><ul><li>Product X x1 - ₹1,000</li></ul><p><strong>Total:</strong> ₹1,000</p>`;
  var m = new bootstrap.Modal(document.getElementById('viewOrderModal'));
  m.show();
}

function openViewInvoice(invId){
  document.querySelector('#viewInvoiceModal .modal-body').innerHTML = `<p><strong>Invoice:</strong> ${invId}</p><p><strong>Order:</strong> #ORD-XXXX</p><p><strong>Date:</strong> ${new Date().toISOString().slice(0,10)}</p><p><strong>Amount:</strong> ₹0</p><p><strong>Status:</strong> Pending</p>`;
  var m = new bootstrap.Modal(document.getElementById('viewInvoiceModal'));
  m.show();
}

function exportOrdersCSV(){
  const rows = Array.from(document.querySelectorAll('#ordersTable tbody tr')).filter(r=> r.style.display!=='none');
  let csv = 'Order ID,Store,Customer,Total,Status\n';
  rows.forEach(r=>{ const cols = Array.from(r.querySelectorAll('td')).slice(1,6).map(td=>td.innerText.replace(/,/g,'')); csv+=cols.join(',')+'\n'; });
  downloadBlob(csv,'orders.csv');
}
function exportInvoicesCSV(){
  const rows = Array.from(document.querySelectorAll('#invoicesTable tbody tr')).filter(r=> r.style.display!=='none');
  let csv = 'Invoice,Order ID,Date,Amount,Status\n';
  rows.forEach(r=>{ const cols = Array.from(r.querySelectorAll('td')).slice(0,5).map(td=>td.innerText.replace(/,/g,'')); csv+=cols.join(',')+'\n'; });
  downloadBlob(csv,'invoices.csv');
}
function downloadBlob(text, filename){ const blob=new Blob([text],{type:'text/csv'}); const url=URL.createObjectURL(blob); const a=document.createElement('a'); a.href=url; a.download=filename; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url); }

</script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.addEventListener('DOMContentLoaded', ()=>{
  loadOrders();
  loadInvoices();
  loadSummaries();
});

function loadSummaries(){
  fetchJson('/orders/summary').then(res=>{ if(res && res.ok) renderOrderSummary(res.data); }).catch(e=>console.error(e));
  fetchJson('/invoices/summary').then(res=>{ if(res && res.ok) renderInvoiceSummary(res.data); }).catch(e=>console.error(e));
}

function renderOrderSummary(data){
  if(!data) return;
  document.getElementById('ordersTotalCount').innerText = data.totalOrders ?? 0;
  document.getElementById('ordersGrossSales').innerText = '₹' + Number(data.grossSales || 0).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
  document.getElementById('ordersOnTime').innerText = (data.onTimeDelivery ?? 0) + '%';
  if(data.month) document.getElementById('ordersTotalLabel').innerText = 'Total Orders ('+data.month+')';
}

function renderInvoiceSummary(rows){
  const tbody = document.getElementById('invoiceSummaryBody'); tbody.innerHTML = '';
  if(!rows || !rows.length) { tbody.innerHTML = '<tr><td colspan="4" class="text-muted">No data</td></tr>'; return; }
  rows.forEach(r=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${r.month}</td><td>${r.issued}</td><td>${r.paid}</td><td>${r.pending}</td>`;
    tbody.appendChild(tr);
  });
}

function fetchJson(url, options={}){
  options.headers = Object.assign({'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF,'Accept':'application/json','Content-Type':'application/json'}, options.headers||{});
  return fetch(url, options).then(async r=>{
    const ct = r.headers.get('content-type') || '';
    if(ct.includes('application/json')){
      const json = await r.json();
      return json;
    }
    // non-json response
    const text = await r.text();
    return { ok: false, _raw: text, status: r.status };
  }).catch(err=>{ return { ok:false, _error: err.message }; });
}

function loadOrders(){
  fetchJson('/orders/list').then(res=>{
    if(res.ok){ renderOrders(res.data); }
  }).catch(e=>console.error(e));
}

function renderOrders(orders){
  const tbody = document.querySelector('#ordersTable tbody'); tbody.innerHTML='';
  orders.forEach(o=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><input class="order-checkbox" data-order-id="${o.id}" type="checkbox"></td>
      <td>${o.order_number}</td>
      <td>${o.store ?? ''}</td>
      <td>${o.customer ?? ''}</td>
      <td>₹${Number(o.amount).toFixed(2)}</td>
      <td><span class="badge ${o.status==='Delivered'? 'bg-success': (o.status==='Out for Delivery'? 'bg-warning text-dark':'bg-secondary')}">${o.status}</span></td>
      <td>
          <button class="btn btn-light btn-sm" onclick="openViewOrder(${o.id})"><i class="bi bi-eye text-primary"></i></button>
          <button class="btn btn-light btn-sm" onclick="openEditOrder(${o.id})" title="Edit Order"><i class="bi bi-pencil text-warning"></i></button>
          <button class="btn btn-light btn-sm" onclick="deleteOrder(${o.id})" title="Delete Order"><i class="bi bi-trash text-danger"></i></button>
          <button class="btn btn-light btn-sm" onclick="openCreateInvoiceModal(${o.id})" title="Create Invoice"><i class="bi bi-receipt"></i></button>
      </td>`;
    tbody.appendChild(tr);
  });
  document.getElementById('ordersCount').innerText = orders.length;
}

function loadInvoices(){
  fetchJson('/invoices/list').then(res=>{
    if(res.ok) renderInvoices(res.data);
  }).catch(e=>console.error(e));
}

function renderInvoices(invoices){
  const tbody = document.querySelector('#invoicesTable tbody'); tbody.innerHTML='';
  invoices.forEach(i=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${i.invoice_number}</td><td>${i.order ? i.order.order_number : ''}</td><td>${i.date ? i.date.split('T')[0] : ''}</td><td>₹${Number(i.amount).toFixed(2)}</td><td><span class="badge ${i.status==='Paid'?'bg-success':'bg-secondary'}">${i.status}</span></td><td>
      <button class="btn btn-light btn-sm" onclick="openViewInvoice(${i.id})"><i class="bi bi-eye text-primary"></i></button>
      <button class="btn btn-light btn-sm" onclick="openEditInvoice(${i.id})" title="Edit Invoice"><i class="bi bi-pencil text-warning"></i></button>
      <button class="btn btn-light btn-sm" onclick="deleteInvoice(${i.id})" title="Delete Invoice"><i class="bi bi-trash text-danger"></i></button>
    </td>`;
    tbody.appendChild(tr);
  });
}

function addOrder(){
  const store = document.getElementById('add_store').value || '';
  const customer = document.getElementById('add_customer').value || '';
  const amount = document.getElementById('add_amount').value || 0;
  const status = document.getElementById('add_status').value || 'Pending';
  const order_number = '#ORD-'+(Math.floor(Math.random()*900000)+1000);
  fetchJson('/orders/store', {method:'POST', body: JSON.stringify({order_number, store, customer, order_date: (new Date()).toISOString().slice(0,10), amount, status})})
    .then(res=>{
      if(res && res.ok){
        const modal = bootstrap.Modal.getInstance(document.getElementById('addOrderModal'));
        if(modal) modal.hide();
        loadOrders();
      } else {
        console.warn('Create order response:', res);
        if(res && res.errors) {
          // show first validation error
          const first = Object.values(res.errors)[0];
          alert('Validation: '+ first[0]);
        } else if(res && res._raw){
          alert('Server error: '+ (res.status||'') + '\n'+ res._raw.substring(0,200));
        } else if(res && res._error){
          alert('Network error: '+res._error);
        } else {
          alert('Failed to create order');
        }
      }
    }).catch(e=>{ console.error(e); alert('Error creating order: '+e.message); });
}

function createInvoice(){
  const orderId = document.getElementById('ci_order_id').value;
  const invoice_no = document.getElementById('ci_invoice_no').value;
  const date = document.getElementById('ci_date').value;
  const amount = document.getElementById('ci_amount').value || 0;
  const status = document.getElementById('ci_status').value || 'Pending';
  fetchJson('/invoices/store', {method:'POST', body: JSON.stringify({invoice_number:invoice_no, order_id:orderId, date, amount, status})})
    .then(res=>{
      if(res && res.ok){
        bootstrap.Modal.getInstance(document.getElementById('createInvoiceModal')).hide();
        loadInvoices();
        // open printable invoice view in new tab
        window.open('/invoices/'+res.data.id+'/view','_blank');
      } else if(res && res.errors){ alert(JSON.stringify(res.errors)); }
    }).catch(e=>{ console.error(e); alert('Error creating invoice'); });
}

function openViewOrder(id){
  fetchJson('/orders/edit/'+id).then(res=>{
    if(res.ok){
      const o = res.data;
      document.querySelector('#viewOrderModal .modal-body').innerHTML = `<p><strong>Order ID:</strong> ${o.order_number}</p><p><strong>Store:</strong> ${o.store??''}</p><p><strong>Customer:</strong> ${o.customer}</p><p><strong>Date:</strong> ${o.order_date}</p><p><strong>Total:</strong> ₹${Number(o.amount).toFixed(2)}</p>`;
      var m = new bootstrap.Modal(document.getElementById('viewOrderModal'));
      m.show();
    }
  });
}

function openViewInvoice(id){
  fetchJson('/invoices/'+id).then(res=>{
    if(res.ok){
      const i = res.data;
      document.querySelector('#viewInvoiceModal .modal-body').innerHTML = `<p><strong>Invoice:</strong> ${i.invoice_number}</p><p><strong>Order:</strong> ${i.order ? i.order.order_number : ''}</p><p><strong>Date:</strong> ${i.date}</p><p><strong>Amount:</strong> ₹${Number(i.amount).toFixed(2)}</p><p><strong>Status:</strong> ${i.status}</p>`;
      // Wire the Download button in the modal footer to open the download route in a new tab
      const dlBtn = document.querySelector('#viewInvoiceModal .modal-footer .btn-primary');
      if(dlBtn){
        dlBtn.onclick = function(ev){
          ev.preventDefault();
          window.open('/invoices/'+i.id+'/download','_blank');
        };
      }
      var m = new bootstrap.Modal(document.getElementById('viewInvoiceModal'));
      m.show();
    }
  });
}

// keep previous helpers (search, export) unchanged
// Edit / Update / Delete handlers for Orders
function openEditOrder(id){
  fetchJson('/orders/edit/'+id).then(res=>{
    if(res && res.ok){
      const o = res.data;
      document.getElementById('eo_id').value = o.id;
      document.getElementById('eo_order_number').value = o.order_number || '';
      document.getElementById('eo_store').value = o.store || '';
      document.getElementById('eo_customer').value = o.customer || '';
      document.getElementById('eo_order_date').value = o.order_date ? o.order_date.split('T')[0] : '';
      document.getElementById('eo_amount').value = o.amount || 0;
      document.getElementById('eo_status').value = o.status || 'Pending';
      new bootstrap.Modal(document.getElementById('editOrderModal')).show();
    }
  }).catch(e=>console.error(e));
}

function updateOrder(){
  const id = document.getElementById('eo_id').value;
  const payload = {
    order_number: document.getElementById('eo_order_number').value,
    store: document.getElementById('eo_store').value,
    customer: document.getElementById('eo_customer').value,
    order_date: document.getElementById('eo_order_date').value,
    amount: document.getElementById('eo_amount').value || 0,
    status: document.getElementById('eo_status').value || 'Pending'
  };
  fetchJson('/orders/update/'+id, { method:'POST', body: JSON.stringify(payload) }).then(res=>{
    if(res && res.ok){
      bootstrap.Modal.getInstance(document.getElementById('editOrderModal')).hide();
      loadOrders(); loadSummaries();
    } else if(res && res.errors){ alert(JSON.stringify(res.errors)); }
  }).catch(e=>{ console.error(e); alert('Error updating order'); });
}

function deleteOrder(id){
  if(!confirm('Delete this order?')) return;
  fetchJson('/orders/delete/'+id, { method:'DELETE' }).then(res=>{
    if(res && res.ok){ loadOrders(); loadSummaries(); } else { alert('Delete failed'); }
  }).catch(e=>{ console.error(e); alert('Error deleting order'); });
}

// Edit / Update / Delete handlers for Invoices
function openEditInvoice(id){
  fetchJson('/invoices/'+id).then(res=>{
    if(res && res.ok){
      const i = res.data;
      document.getElementById('ei_id').value = i.id;
      document.getElementById('ei_invoice_no').value = i.invoice_number || '';
      document.getElementById('ei_order_id').value = i.order ? i.order.order_number : i.order_id || '';
      document.getElementById('ei_date').value = i.date ? i.date.split('T')[0] : '';
      document.getElementById('ei_amount').value = i.amount || 0;
      document.getElementById('ei_status').value = i.status || 'Pending';
      new bootstrap.Modal(document.getElementById('editInvoiceModal')).show();
    }
  }).catch(e=>console.error(e));
}

function updateInvoice(){
  const id = document.getElementById('ei_id').value;
  const payload = {
    invoice_number: document.getElementById('ei_invoice_no').value,
    order_id: document.getElementById('ei_order_id').value,
    date: document.getElementById('ei_date').value,
    amount: document.getElementById('ei_amount').value || 0,
    status: document.getElementById('ei_status').value || 'Pending'
  };
  fetchJson('/invoices/update/'+id, { method:'POST', body: JSON.stringify(payload) }).then(res=>{
    if(res && res.ok){
      bootstrap.Modal.getInstance(document.getElementById('editInvoiceModal')).hide();
      loadInvoices(); loadSummaries();
    } else if(res && res.errors){ alert(JSON.stringify(res.errors)); }
  }).catch(e=>{ console.error(e); alert('Error updating invoice'); });
}

function deleteInvoice(id){
  if(!confirm('Delete this invoice?')) return;
  fetchJson('/invoices/delete/'+id, { method:'DELETE' }).then(res=>{
    if(res && res.ok){ loadInvoices(); loadSummaries(); } else { alert('Delete failed'); }
  }).catch(e=>{ console.error(e); alert('Error deleting invoice'); });
}
document.getElementById('refreshOrders').addEventListener('click', ()=>{ loadOrders(); loadInvoices(); });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
