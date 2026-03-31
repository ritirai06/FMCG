@extends('layouts.app')

@section('title', 'Orders')
@section('page_title', 'Order & Invoice Management')

@section('navbar_right')
  @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSales()))
    <a href="{{ route('orders.create') }}" class="btn btn-gradient btn-sm me-2">
      <i class="bi bi-plus-lg me-1"></i>Create Order
    </a>
  @endif
  <button class="btn btn-gradient btn-sm" data-bs-toggle="modal" data-bs-target="#invoiceModal">
    <i class="bi bi-file-earmark-plus me-1"></i>Generate Invoice
  </button>
@endsection

@section('content')

<!-- Success/Error Messages -->
@if($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-top:20px;">
    <i class="bi bi-check-circle-fill me-2"></i>{{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($message = Session::get('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-top:20px;">
    <i class="bi bi-exclamation-circle-fill me-2"></i>{{ $message }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- FILTER PANEL -->
<div class="filter-card">
  <div class="row g-2 align-items-end">
    <div class="col-12 col-sm-6 col-md-3">
      <label class="form-label">Search</label>
      <input type="text" class="form-control" id="searchInput" placeholder="Order ID or Customer..." onkeyup="filterOrders()">
    </div>
    <div class="col-6 col-sm-3 col-md-3">
      <label class="form-label">Status</label>
      <select class="form-select" id="statusFilter" onchange="filterOrders()">
        <option value="">All Status</option>
        <option value="Pending">Pending</option>
        <option value="Complete">Complete</option>
        <option value="Delivered">Delivered</option>
        <option value="Cancelled">Cancelled</option>
      </select>
    </div>
    <div class="col-6 col-sm-3 col-md-3">
      <label class="form-label">Date</label>
      <input type="date" class="form-control" id="dateFilter" onchange="filterOrders()">
    </div>
    <div class="col-12 col-md-3">
      <button class="btn btn-gradient w-100" onclick="resetFilters()">
        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
      </button>
    </div>
  </div>
</div>

<!-- ORDER TABLE CARD -->
<div class="table-card">
    <h6 class="fw-bold mb-4" style="color:var(--primary);">
        <i class="bi bi-list-check me-2"></i>Order List ({{ isset($orders) && $orders->count() > 0 ? $orders->total() : 0 }} Total)
    </h6>
    <div class="table-responsive">
        <table class="table align-middle" id="ordersTable">
            <thead>
                <tr style="border-bottom:2px solid rgba(59,130,246,.2);">
                    <th style="font-weight:700;color:var(--primary);">Order ID</th>
                    <th style="font-weight:700;color:var(--primary);">Store</th>
                    <th style="font-weight:700;color:var(--primary);">Customer</th>
                    <th style="font-weight:700;color:var(--primary);">Amount</th>
                    <th style="font-weight:700;color:var(--primary);">Status</th>
                    <th style="font-weight:700;color:var(--primary);">Delivery Agent</th>
                    <th style="font-weight:700;color:var(--primary);">Date</th>
                    <th style="font-weight:700;color:var(--primary);">Created By</th>
                    <th style="font-weight:700;color:var(--primary);">Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($orders) && $orders->count() > 0)
                    @foreach($orders as $order)
                    <tr style="border-bottom:1px solid rgba(0,0,0,.05);" class="orderRow" 
                        data-order-id="{{ $order->id }}" 
                        data-order-number="{{ $order->order_number }}"
                        data-customer="{{ $order->customer_name }}"
                        data-store="{{ $order->store?->store_name ?? 'N/A' }}"
                        data-amount="{{ $order->total_amount }}"
                        data-status="{{ $order->status }}"
                        data-date="{{ $order->created_at->format('Y-m-d') }}"
                        data-customer-phone="{{ $order->customer_phone }}"
                        data-created-by="{{ $order->createdBy?->name ?? 'Unknown' }}">
                        <td>
                            <a href="{{ route('orders.show', $order) }}" class="fw-bold" style="color:var(--accent);text-decoration:none;">
                                #{{ $order->order_number }}
                            </a>
                        </td>
                        <td>{{ $order->store?->store_name ?? 'N/A' }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td class="fw-bold">₹{{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            @if($order->status === 'Complete')
                                <span class="badge bg-success badge-status">Complete</span>
                            @elseif($order->status === 'Delivered')
                                <span class="badge bg-success badge-status">Delivered</span>
                            @elseif($order->status === 'Assigned')
                                <span class="badge bg-info badge-status">Assigned</span>
                            @elseif($order->status === 'Pending')
                                <span class="badge bg-warning badge-status" style="color:#000;">Pending</span>
                            @else
                                <span class="badge bg-danger badge-status">{{ $order->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if($order->assignedDeliveryPerson)
                                <div class="d-flex align-items-center gap-1">
                                    <i class="bi bi-person-check-fill text-success"></i>
                                    <span class="fw-semibold small">{{ $order->assignedDeliveryPerson->name }}</span>
                                </div>
                                <small class="text-muted">{{ $order->assignedDeliveryPerson->phone }}</small>
                                @if(auth()->check() && auth()->user()->isAdmin())
                                <button class="btn btn-outline-secondary btn-sm py-0 px-2 quick-assign-btn mt-1"
                                    data-order-id="{{ $order->id }}"
                                    data-store-locality="{{ $order->store?->locality_id ?? '' }}"
                                    data-store-city="{{ $order->store?->city_id ?? '' }}"
                                    style="font-size:10px;">
                                    <i class="bi bi-arrow-repeat me-1"></i>Reassign
                                </button>
                                @endif
                            @else
                                @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSales()))
                                <button class="btn btn-outline-primary btn-sm py-0 px-2 quick-assign-btn"
                                    data-order-id="{{ $order->id }}"
                                    data-store-locality="{{ $order->store?->locality_id ?? '' }}"
                                    data-store-city="{{ $order->store?->city_id ?? '' }}"
                                    style="font-size:11px;">
                                    <i class="bi bi-plus-circle me-1"></i>Assign
                                </button>
                                @else
                                <span class="text-muted small">Not assigned</span>
                                @endif
                            @endif
                        </td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td>{{ $order->createdBy?->name ?? 'Unknown' }}</td>
                        <td>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-light btn-sm" title="View Order">
                                <i class="bi bi-eye text-primary"></i>
                            </a>
                            <button type="button" class="btn btn-light btn-sm" onclick="generateInvoice({{ $order->id }}, '{{ $order->order_number }}', '{{ $order->customer_name }}', '{{ $order->customer_phone }}', '{{ $order->store?->store_name ?? 'N/A' }}', {{ $order->total_amount }}, '{{ $order->created_at->format('d M Y') }}')" title="Generate Invoice">
                                <i class="bi bi-receipt text-success"></i>
                            </button>
                            @if(auth()->check() && auth()->user()->isAdmin())
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-light btn-sm" title="Assign / Edit">
                                <i class="bi bi-person-plus text-warning"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @else
                <tr>
                    <td colspan="9" class="text-center py-5" style="color:var(--text-muted);">
                        <i class="bi bi-inbox" style="font-size:32px;margin-bottom:10px;display:block;opacity:0.5;"></i>
                        No orders found
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($orders) && $orders->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $orders->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<!-- QUICK ASSIGN DELIVERY MODAL -->
@if(auth()->check() && auth()->user()->isAdmin())
<div class="modal fade" id="quickAssignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus text-primary me-2"></i>Assign Delivery Agent</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Showing agents for this order's store locality.</p>
                <select id="qaAgentSelect" class="form-select mb-2">
                    <option value="">Loading agents...</option>
                </select>
                <div id="qaAgentInfo" class="small text-muted" style="display:none;"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" id="qaAssignBtn"><i class="bi bi-check2 me-1"></i>Assign</button>
            </div>
            <div id="qaMsg" class="px-3 pb-2"></div>
        </div>
    </div>
</div>
<script>
(function(){
    let qaOrderId = null;
    let qaAgents  = [];
    const modal   = new bootstrap.Modal(document.getElementById('quickAssignModal'));

    document.addEventListener('click', function(e){
        const btn = e.target.closest('.quick-assign-btn');
        if (!btn) return;
        qaOrderId = btn.dataset.orderId;
        const localityId = btn.dataset.storeLocality;
        const cityId     = btn.dataset.storeCity;
        document.getElementById('qaAgentSelect').innerHTML = '<option>Loading...</option>';
        document.getElementById('qaAgentInfo').style.display = 'none';
        document.getElementById('qaMsg').innerHTML = '';

        const params = new URLSearchParams();
        if (localityId) params.set('locality_id', localityId);
        else if (cityId) params.set('city_id', cityId);

        fetch('/orders/api/delivery-agents?' + params.toString())
            .then(r => r.json())
            .then(data => {
                qaAgents = data;
                const sel = document.getElementById('qaAgentSelect');
                sel.innerHTML = '<option value="">-- Select Agent --</option>';
                data.forEach(a => {
                    const o = document.createElement('option');
                    o.value = a.id;
                    o.textContent = a.name + (a.phone ? ' (' + a.phone + ')' : '');
                    sel.appendChild(o);
                });
            });
        modal.show();
    });

    document.getElementById('qaAgentSelect')?.addEventListener('change', function(){
        const a = qaAgents.find(x => x.id == this.value);
        const info = document.getElementById('qaAgentInfo');
        if (a) { info.textContent = '📞 ' + (a.phone||'N/A') + '  🏍 ' + (a.vehicle||'N/A'); info.style.display='block'; }
        else info.style.display = 'none';
    });

    document.getElementById('qaAssignBtn')?.addEventListener('click', function(){
        const dpId = document.getElementById('qaAgentSelect').value;
        if (!dpId) { document.getElementById('qaMsg').innerHTML = '<small class="text-danger">Please select an agent.</small>'; return; }
        this.disabled = true;
        this.textContent = 'Assigning...';
        const token = document.querySelector('meta[name="csrf-token"]').content;
        fetch('/orders/' + qaOrderId + '/assign-delivery', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ delivery_person_id: dpId })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Update the row in-place without full reload
                const row = document.querySelector(`tr[data-order-id="${qaOrderId}"]`);
                if (row) {
                    const dp = data.delivery_person;
                    const td = row.querySelector('td:nth-child(6)');
                    td.innerHTML = `
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-person-check-fill text-success"></i>
                            <span class="fw-semibold small">${dp.name}</span>
                        </div>
                        <small class="text-muted">${dp.phone || ''}</small>
                        <button class="btn btn-outline-secondary btn-sm py-0 px-2 quick-assign-btn mt-1"
                            data-order-id="${qaOrderId}"
                            data-store-locality=""
                            data-store-city=""
                            style="font-size:10px;">
                            <i class="bi bi-arrow-repeat me-1"></i>Reassign
                        </button>`;
                }
                bootstrap.Modal.getInstance(document.getElementById('quickAssignModal')).hide();
                document.getElementById('qaMsg').innerHTML = '';
            }
            else { document.getElementById('qaMsg').innerHTML = '<small class="text-danger">' + (data.message||'Failed') + '</small>'; }
        })
        .finally(() => { this.disabled=false; this.innerHTML='<i class="bi bi-check2 me-1"></i>Assign'; });
    });
})();
</script>
@endif

<!-- INVOICE MODAL -->
<div class="modal fade" id="invoiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-3">
            <div class="modal-header" style="border-bottom:1px solid rgba(0,0,0,.05);background:rgba(255,255,255,.5);">
                <h5 class="modal-title" style="font-weight:700;color:var(--text-dark);">
                    <i class="bi bi-file-earmark-text me-2" style="color:var(--primary);"></i>Generate Invoice
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="invoiceArea">
                <div style="background:#fff;padding:30px;border-radius:14px;box-shadow:0 5px 20px rgba(0,0,0,0.1);font-size:14px;color:#111827;">
                    <div style="border-bottom:2px solid var(--primary);margin-bottom:15px;padding-bottom:10px;">
                        <h6 style="margin-bottom:5px;font-weight:700;">FMCG Admin Panel</h6>
                        <small style="color:#6b7280;">Invoice Date: <span id="invoiceDate"></span></small>
                    </div>
                    
                    <div style="margin-bottom:20px;">
                        <strong style="display:block;margin-bottom:5px;">Invoice To:</strong>
                        <div>
                            <strong id="invoiceCustomer"></strong><br>
                            <span id="invoicePhone" style="color:#6b7280;"></span><br>
                            <span id="invoiceStore" style="color:#6b7280;"></span>
                        </div>
                    </div>

                    <table style="width:100%;border-collapse:collapse;margin:20px 0;">
                        <thead>
                            <tr style="border-bottom:2px solid #2563EB;background:#EFF6FF;">
                                <th style="text-align:left;padding:10px;font-weight:700;">Description</th>
                                <th style="text-align:right;padding:10px;font-weight:700;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="border-bottom:1px solid #e5e7eb;">
                                <td style="padding:10px;">Order: <strong id="invoiceOrderId"></strong></td>
                                <td style="text-align:right;padding:10px;"><strong id="invoiceAmount"></strong></td>
                            </tr>
                        </tbody>
                    </table>

                    <div style="text-align:right;margin-top:20px;padding-top:15px;border-top:2px solid #2563EB;">
                        <h6 style="margin:0;font-weight:700;color:var(--primary);">
                            Total Amount: <span id="invoiceTotalAmount" style="font-size:18px;"></span>
                        </h6>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid rgba(0,0,0,.05);">
                <button class="btn btn-light" data-bs-dismiss="modal" style="border:1px solid rgba(59,130,246,.2);">Close</button>
                <button class="btn btn-gradient" onclick="printInvoice()">
                    <i class="bi bi-printer me-1"></i>Print
                </button>
                <button class="btn btn-outline-primary" onclick="downloadInvoicePDF()">
                    <i class="bi bi-download me-1"></i>Download PDF
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.fw-600{font-weight:600;}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
// Generate Invoice Dynamically
function generateInvoice(orderId, orderNumber, customer, phone, store, amount, date) {
    const today = new Date().toLocaleDateString('en-GB', { year: 'numeric', month: 'short', day: 'numeric' });
    
    document.getElementById('invoiceOrderId').textContent = '#' + orderNumber;
    document.getElementById('invoiceCustomer').textContent = customer;
    document.getElementById('invoicePhone').textContent = 'Phone: ' + phone;
    document.getElementById('invoiceStore').textContent = 'Store: ' + store;
    document.getElementById('invoiceAmount').textContent = '₹' + parseFloat(amount).toFixed(2);
    document.getElementById('invoiceTotalAmount').textContent = '₹' + parseFloat(amount).toFixed(2);
    document.getElementById('invoiceDate').textContent = today;
    
    const modal = new bootstrap.Modal(document.getElementById('invoiceModal'));
    modal.show();
}

// Print Invoice
function printInvoice() {
    window.print();
}

// Download Invoice as PDF
async function downloadInvoicePDF() {
    const { jsPDF } = window.jspdf;
    const invoice = document.getElementById('invoiceArea');
    const canvas = await html2canvas(invoice);
    const imgData = canvas.toDataURL('image/png');
    const pdf = new jsPDF('p', 'mm', 'a4');
    const width = pdf.internal.pageSize.getWidth();
    const height = (canvas.height * width) / canvas.width;
    pdf.addImage(imgData, 'PNG', 0, 0, width, height);
    
    const orderNumber = document.getElementById('invoiceOrderId').textContent;
    pdf.save('Invoice_' + orderNumber + '.pdf');
}

// Filter Orders
function filterOrders() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    
    const rows = document.querySelectorAll('.orderRow');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const orderNumber = row.dataset.orderNumber.toLowerCase();
        const customer = row.dataset.customer.toLowerCase();
        const status = row.dataset.status;
        const date = row.dataset.date;
        
        let matchSearch = orderNumber.includes(searchInput) || customer.includes(searchInput);
        let matchStatus = !statusFilter || status === statusFilter;
        let matchDate = !dateFilter || date === dateFilter;
        
        if (matchSearch && matchStatus && matchDate) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show "No results" message
    const tbody = document.querySelector('#ordersTable tbody');
    let noResults = document.querySelector('.no-results');
    
    if (visibleCount === 0 && !noResults) {
        const tr = document.createElement('tr');
        tr.className = 'no-results';
        tr.innerHTML = `<td colspan="9" class="text-center py-5" style="color:var(--text-muted);">
            <i class="bi bi-inbox" style="font-size:32px;margin-bottom:10px;display:block;opacity:0.5;"></i>
            No orders found matching your search
        </td>`;
        tbody.appendChild(tr);
    } else if (visibleCount > 0 && noResults) {
        noResults.remove();
    }
}

// Reset Filters
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('dateFilter').value = '';
    
    const rows = document.querySelectorAll('.orderRow, .no-results');
    rows.forEach(row => row.style.display = '');
    
    const noResults = document.querySelector('.no-results');
    if (noResults) noResults.remove();
}
</script>

@endsection
