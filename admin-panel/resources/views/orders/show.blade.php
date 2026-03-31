@extends('layouts.app')

@section('title', 'Order Details')
@section('page_title', 'Order #'.$order->order_number)

@section('navbar_right')
  @if(auth()->user()->isAdmin())
  <a href="{{ route('orders.index') }}" class="btn btn-outline-primary btn-sm">
    <i class="bi bi-arrow-left me-1"></i>Back to Orders
  </a>
  @elseif(auth()->user()->isDelivery())
  <a href="{{ route('delivery.panel.orders') }}" class="btn btn-outline-primary btn-sm">
    <i class="bi bi-arrow-left me-1"></i>Back to Orders
  </a>
  @endif
@endsection

@section('content')
<div class="container-fluid">
    <!-- Success Message -->
    @if($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Order Info -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Status:</strong> <span class="badge bg-{{ $order->status === 'Delivered' ? 'success' : 'warning' }}">{{ $order->status }}</span></p>
                            <p><strong>Order Date:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                            <p><strong>Created By:</strong> {{ $order->createdBy?->name ?? 'Unknown' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Store:</strong> {{ $order->store?->store_name ?? 'N/A' }}</p>
                            <p><strong>Total Amount:</strong> <span class="fs-5 fw-bold text-success">₹{{ number_format($order->total_amount, 2) }}</span></p>
                            @if($order->assignedDelivery)
                            <p><strong>Assigned To:</strong> {{ $order->assignedDelivery->name }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $order->customer_name }}</p>
                    <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td>₹{{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>₹{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td><strong>₹{{ number_format($order->total_amount, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="col-md-4">
            <!-- Assign Delivery Agent (Admin only) -->
            @if(auth()->check() && auth()->user()->isAdmin())
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-motorcycle me-2"></i>Assign Delivery Agent</h5>
                </div>
                <div class="card-body">
                    @if($order->assignedDeliveryPerson)
                    <div class="alert alert-success py-2 mb-3">
                        <i class="fas fa-check-circle me-1"></i>
                        <strong>Assigned:</strong> {{ $order->assignedDeliveryPerson->name }}<br>
                        <small class="text-muted"><i class="fas fa-phone me-1"></i>{{ $order->assignedDeliveryPerson->phone ?? 'N/A' }}</small>
                    </div>
                    @endif

                    @php
                        $storeLocalityId = $order->store?->locality_id ?? null;
                        $storeCityId     = $order->store?->city_id ?? null;
                        $storeLat        = $order->store?->latitude ?? null;
                        $storeLng        = $order->store?->longitude ?? null;
                        $storeAddress    = $order->store?->address ?? null;
                        $storeMapUrl     = ($storeLat && $storeLng)
                            ? 'https://maps.google.com/?q='.$storeLat.','.$storeLng
                            : ($storeAddress ? 'https://maps.google.com/?q='.urlencode($storeAddress) : null);
                    @endphp

                    @if($storeMapUrl)
                    <div class="mb-3 p-2 rounded" style="background:#f0fdf4;border:1px solid #86efac;">
                        <div class="fw-semibold small mb-1"><i class="fas fa-store text-success me-1"></i>Delivery Store Location</div>
                        <div class="small text-muted mb-1">{{ $storeAddress ?? 'Address not set' }}</div>
                        <a href="{{ $storeMapUrl }}" target="_blank" class="small text-success fw-semibold">
                            <i class="fas fa-map-marker-alt me-1"></i>Open in Google Maps
                        </a>
                    </div>
                    @endif

                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Delivery Agent
                            @if($storeLocalityId || $storeCityId)
                                <span class="badge bg-info text-dark ms-1" style="font-size:10px;">Locality Filtered</span>
                            @endif
                        </label>
                        <select id="deliveryAgentSelect" class="form-select form-select-sm">
                            <option value="">Loading agents...</option>
                        </select>
                    </div>
                    <div id="agentInfo" class="mb-2" style="display:none;">
                        <small class="text-muted"><i class="fas fa-phone me-1"></i><span id="agentPhone"></span> &nbsp; <i class="fas fa-motorcycle me-1"></i><span id="agentVehicle"></span></small>
                    </div>
                    <button type="button" class="btn btn-dark btn-sm w-100" id="assignAgentBtn">
                        <i class="fas fa-user-check me-1"></i> Assign Agent
                    </button>
                    <div id="assignMsg" class="mt-2"></div>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    @if(auth()->check() && auth()->user()->isAdmin())
                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-edit"></i> Edit Order
                    </a>
                    @endif

                    @if($order->status !== 'Delivered' && auth()->check() && auth()->user()->isAdmin())
                    <form action="{{ route('orders.cancel', $order) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-times-circle"></i> Cancel Order
                        </button>
                    </form>
                    @endif

                    @if($order->status === 'Out for Delivery' && auth()->check() && auth()->user()->isDelivery())
                    <form action="{{ route('delivery.panel.orders.status', $order) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="Delivered">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check-double"></i> Mark Delivered
                        </button>
                    </form>
                    @endif

                    <hr>

                    <h6>Order Timeline</h6>
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <p><strong>Created</strong><br><small>{{ $order->created_at->format('d M Y H:i') }}</small></p>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker @if(in_array($order->status, ['Approved', 'Packed', 'Out for Delivery', 'Delivered'])) bg-success @else bg-secondary @endif"></div>
                            <p><strong>{{ $order->status }}</strong><br><small>{{ $order->updated_at->format('d M Y H:i') }}</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->check() && auth()->user()->isAdmin())
<script>
(function () {
    const localityId = {{ $order->store?->locality_id ?? 'null' }};
    const cityId     = {{ $order->store?->city_id ?? 'null' }};
    const orderId    = {{ $order->id }};
    const assignUrl  = '{{ route('orders.assignDelivery', $order) }}';
    const agentsUrl  = '{{ route('orders.deliveryAgents') }}';
    const csrfToken  = '{{ csrf_token() }}';

    const select      = document.getElementById('deliveryAgentSelect');
    const agentInfo   = document.getElementById('agentInfo');
    const agentPhone  = document.getElementById('agentPhone');
    const agentVehicle= document.getElementById('agentVehicle');
    const assignBtn   = document.getElementById('assignAgentBtn');
    const assignMsg   = document.getElementById('assignMsg');

    let agents = [];

    // Load agents filtered by locality/city
    const params = new URLSearchParams();
    if (localityId) params.set('locality_id', localityId);
    else if (cityId) params.set('city_id', cityId);

    fetch(agentsUrl + '?' + params.toString())
        .then(r => r.json())
        .then(data => {
            agents = data;
            select.innerHTML = '<option value="">-- Select Delivery Agent --</option>';
            data.forEach(a => {
                const opt = document.createElement('option');
                opt.value = a.id;
                opt.textContent = a.name + (a.phone ? ' (' + a.phone + ')' : '');
                select.appendChild(opt);
            });
            // Pre-select if already assigned
            const preAssigned = {{ $order->assigned_delivery_person_id ?? 'null' }};
            if (preAssigned) select.value = preAssigned;
        });

    select.addEventListener('change', function () {
        const agent = agents.find(a => a.id == this.value);
        if (agent) {
            agentPhone.textContent  = agent.phone  || 'N/A';
            agentVehicle.textContent= agent.vehicle || 'N/A';
            agentInfo.style.display = 'block';
        } else {
            agentInfo.style.display = 'none';
        }
    });

    assignBtn.addEventListener('click', function () {
        const dpId = select.value;
        if (!dpId) { assignMsg.innerHTML = '<small class="text-danger">Please select an agent.</small>'; return; }
        assignBtn.disabled = true;
        assignBtn.textContent = 'Assigning...';

        fetch(assignUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ delivery_person_id: dpId }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const msg = data.message || 'Assigned successfully';
                const hasWarning = msg.toLowerCase().includes('warning');
                assignMsg.innerHTML = '<small class="text-' + (hasWarning ? 'warning' : 'success') + '">'
                    + '<i class="fas fa-' + (hasWarning ? 'exclamation-triangle' : 'check-circle') + ' me-1"></i>'
                    + msg + '</small>';
                setTimeout(() => location.reload(), hasWarning ? 3000 : 1200);
            } else {
                assignMsg.innerHTML = '<small class="text-danger">' + (data.message || 'Failed') + '</small>';
            }
        })
        .catch(() => { assignMsg.innerHTML = '<small class="text-danger">Request failed.</small>'; })
        .finally(() => { assignBtn.disabled = false; assignBtn.innerHTML = '<i class="fas fa-user-check me-1"></i> Assign Agent'; });
    });
})();
</script>
@endif

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline-item {
    display: flex;
    margin-bottom: 20px;
}

.timeline-marker {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    margin-right: 15px;
    margin-top: 5px;
    flex-shrink: 0;
}

.timeline-item p {
    margin: 0;
    font-size: 0.9rem;
}
</style>
@endsection
