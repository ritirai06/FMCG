@extends('delivery_panel.layout')

@section('page_title', 'My Orders')

@section('content')
<div class="container-fluid">
	<!-- Header -->
	<div class="d-flex align-items-center mb-4">
		<h3 class="mb-0 me-auto">My Orders</h3>
		<div>
			<a href="javascript:void(0);" class="icon-btn me-3"><i class="fas fa-envelope"></i></a>
			<a href="javascript:void(0);" class="icon-btn me-3"><i class="fas fa-phone-alt"></i></a>
			<a href="javascript:void(0);" class="icon-btn"><i class="fas fa-info"></i></a>
		</div>
	</div>

	<!-- Tabs Navigation -->
	<div class="card">
		<div class="card-header border-0">
			<ul class="nav nav-tabs" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" data-bs-toggle="tab" href="#assigned" role="tab">
						<span class="d-block d-sm-none"><i class="fas fa-cube"></i></span>
						<span class="d-none d-sm-block">🔹 Assigned</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-bs-toggle="tab" href="#picked" role="tab">
						<span class="d-block d-sm-none"><i class="fas fa-check"></i></span>
						<span class="d-none d-sm-block">🔹 Picked</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-bs-toggle="tab" href="#outfordelivery" role="tab">
						<span class="d-block d-sm-none"><i class="fas fa-truck"></i></span>
						<span class="d-none d-sm-block">🔹 Out for Delivery</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-bs-toggle="tab" href="#delivered" role="tab">
						<span class="d-block d-sm-none"><i class="fas fa-check-double"></i></span>
						<span class="d-none d-sm-block">🔹 Delivered</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-bs-toggle="tab" href="#failed" role="tab">
						<span class="d-block d-sm-none"><i class="fas fa-times"></i></span>
						<span class="d-none d-sm-block">🔹 Failed / Returned</span>
					</a>
				</li>
			</ul>
		</div>

		<!-- Tab Content -->
		<div class="card-body p-0">
			<div class="tab-content">
				<!-- Assigned Tab -->
				<div class="tab-pane fade show active" id="assigned" role="tabpanel">
					<div class="table-responsive p-3">
						<table class="table table-hover mb-0">
							<thead class="table-light">
								<tr>
									<th>Order ID</th>
									<th>Store Name</th>
									<th>Location</th>
									<th>Amount</th>
									<th>Assign Time</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								@forelse($orders->where('status', 'Pending') as $order)
									<tr>
										<td><span class="badge badge-primary light">{{ $order->order_number ?? ('ORD-'.$order->id) }}</span></td>
										<td>{{ $order->store?->store_name ?? 'N/A' }}</td>
										<td>{{ $order->store?->address ?? 'N/A' }}</td>
										<td><strong>₹{{ number_format((float)($order->total_amount ?? $order->amount ?? 0), 2) }}</strong></td>
										<td>{{ $order->created_at?->format('d-m-Y H:i A') ?? 'N/A' }}</td>
										<td>
											<form method="POST" action="{{ route('delivery.panel.orders.status', $order) }}" class="d-inline">
												@csrf
												<input type="hidden" name="status" value="Picked">
												<button class="btn btn-sm btn-primary" onclick="return confirm('Mark as picked?');">
													<i class="fas fa-check me-2"></i>Mark as Picked
												</button>
											</form>
										</td>
									</tr>
								@empty
									<tr>
										<td colspan="6" class="text-center text-muted py-4">
											<i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
											<p>No assigned orders</p>
										</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>
				</div>

				<!-- Picked Tab -->
				<div class="tab-pane fade" id="picked" role="tabpanel">
					<div class="table-responsive p-3">
						<table class="table table-hover mb-0">
							<thead class="table-light">
								<tr>
									<th>Order ID</th>
									<th>Store Name</th>
									<th>Location</th>
									<th>Amount</th>
									<th>Picked Time</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								@forelse($orders->where('status', 'Picked') as $order)
									<tr>
										<td><span class="badge badge-success light">{{ $order->order_number ?? ('ORD-'.$order->id) }}</span></td>
										<td>{{ $order->store?->store_name ?? 'N/A' }}</td>
										<td>{{ $order->store?->address ?? 'N/A' }}</td>
										<td><strong>₹{{ number_format((float)($order->total_amount ?? $order->amount ?? 0), 2) }}</strong></td>
										<td>{{ $order->updated_at?->format('d-m-Y H:i A') ?? 'N/A' }}</td>
										<td>
											<form method="POST" action="{{ route('delivery.panel.orders.status', $order) }}" class="d-inline">
												@csrf
												<input type="hidden" name="status" value="Out for Delivery">
												<button class="btn btn-sm btn-info" onclick="return confirm('Mark as out for delivery?');">
													<i class="fas fa-truck me-2"></i>Out for Delivery
												</button>
											</form>
										</td>
									</tr>
								@empty
									<tr>
										<td colspan="6" class="text-center text-muted py-4">
											<i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
											<p>No picked orders</p>
										</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>
				</div>

				<!-- Out for Delivery Tab -->
				<div class="tab-pane fade" id="outfordelivery" role="tabpanel">
					<div class="table-responsive p-3">
						<table class="table table-hover mb-0">
							<thead class="table-light">
								<tr>
									<th>Order ID</th>
									<th>Store Name</th>
									<th>Location</th>
									<th>Amount</th>
									<th>Out Time</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								@forelse($orders->where('status', 'Out for Delivery') as $order)
									<tr>
										<td><span class="badge badge-warning light">{{ $order->order_number ?? ('ORD-'.$order->id) }}</span></td>
										<td>{{ $order->store?->store_name ?? 'N/A' }}</td>
										<td>{{ $order->store?->address ?? 'N/A' }}</td>
										<td><strong>₹{{ number_format((float)($order->total_amount ?? $order->amount ?? 0), 2) }}</strong></td>
										<td>{{ $order->updated_at?->format('d-m-Y H:i A') ?? 'N/A' }}</td>
										<td>
											<div class="btn-group btn-group-sm" role="group">
												<form method="POST" action="{{ route('delivery.panel.orders.status', $order) }}" class="d-inline">
													@csrf
													<input type="hidden" name="status" value="Delivered">
													<button class="btn btn-success" onclick="return confirm('Mark as delivered?');">
														<i class="fas fa-check me-1"></i>Delivered
													</button>
												</form>
												<button class="btn btn-danger" onclick="markFailed({{ $order->id }}, '{{ $order->order_number ?? 'ORD-'.$order->id }}')">
													<i class="fas fa-times me-1"></i>Failed
												</button>
											</div>
										</td>
									</tr>
								@empty
									<tr>
										<td colspan="6" class="text-center text-muted py-4">
											<i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
											<p>No orders out for delivery</p>
										</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>
				</div>

				<!-- Delivered Tab -->
				<div class="tab-pane fade" id="delivered" role="tabpanel">
					<div class="table-responsive p-3">
						<table class="table table-hover mb-0">
							<thead class="table-light">
								<tr>
									<th>Order ID</th>
									<th>Store Name</th>
									<th>Location</th>
									<th>Amount</th>
									<th>Delivery Time</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								@forelse($orders->where('status', 'Delivered') as $order)
									<tr>
										<td><span class="badge badge-success light">{{ $order->order_number ?? ('ORD-'.$order->id) }}</span></td>
										<td>{{ $order->store?->store_name ?? 'N/A' }}</td>
										<td>{{ $order->store?->address ?? 'N/A' }}</td>
										<td><strong>₹{{ number_format((float)($order->total_amount ?? $order->amount ?? 0), 2) }}</strong></td>
										<td>{{ $order->updated_at?->format('d-m-Y H:i A') ?? 'N/A' }}</td>
										<td><span class="badge bg-success">Completed</span></td>
									</tr>
								@empty
									<tr>
										<td colspan="6" class="text-center text-muted py-4">
											<i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
											<p>No delivered orders</p>
										</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>
				</div>

				<!-- Failed / Returned Tab -->
				<div class="tab-pane fade" id="failed" role="tabpanel">
					<div class="table-responsive p-3">
						<table class="table table-hover mb-0">
							<thead class="table-light">
								<tr>
									<th>Order ID</th>
									<th>Store Name</th>
									<th>Location</th>
									<th>Amount</th>
									<th>Reason</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								@forelse($orders->where('status', 'Cancelled') as $order)
									<tr>
										<td><span class="badge badge-danger light">{{ $order->order_number ?? ('ORD-'.$order->id) }}</span></td>
										<td>{{ $order->store?->store_name ?? 'N/A' }}</td>
										<td>{{ $order->store?->address ?? 'N/A' }}</td>
										<td><strong>₹{{ number_format((float)($order->total_amount ?? $order->amount ?? 0), 2) }}</strong></td>
										<td>
											<select class="form-select form-select-sm" onchange="updateFailedReason({{ $order->id }}, this)">
												<option selected="">Choose Reason...</option>
												<option>Owner Not Available</option>
												<option>Wrong Location</option>
												<option>Order Cancelled</option>
												<option>Payment Failed</option>
												<option>Out of Stock</option>
												<option>Customer Rejected</option>
											</select>
										</td>
										<td><span class="badge bg-danger">Failed</span></td>
									</tr>
								@empty
									<tr>
										<td colspan="6" class="text-center text-muted py-4">
											<i class="fas fa-inbox fa-2x mb-2 opacity-50"></i>
											<p>No failed/returned orders</p>
										</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Hidden Form for Failed Status -->
<form id="failedForm" method="POST" style="display:none;">
	@csrf
	<input type="hidden" name="status" value="Cancelled">
	<input type="hidden" name="reason" id="failureReason">
</form>

<!-- Scripts for Order Actions -->
<script>
	function markFailed(orderId, orderNumber) {
		const reason = prompt('Enter failure reason:\n\n- Owner Not Available\n- Wrong Location\n- Order Cancelled\n- Payment Failed\n- Out of Stock\n- Customer Rejected');
		if (reason) {
			const form = document.getElementById('failedForm');
			form.action = `/delivery-panel/orders/${orderId}/status`;
			document.getElementById('failureReason').value = reason;
			form.submit();
		}
	}

	function updateFailedReason(orderId, element) {
		const reason = element.value;
		if (reason) {
			const form = document.getElementById('failedForm');
			form.action = `/delivery-panel/orders/${orderId}/status`;
			document.getElementById('failureReason').value = reason;
			if (confirm('Update reason to: ' + reason)) {
				form.submit();
			}
		}
	}
</script>

<style>
	.nav-tabs .nav-link {
		color: #666;
		border-bottom: 2px solid transparent;
		cursor: pointer;
	}
	
	.nav-tabs .nav-link:hover {
		border-color: #ddd;
	}
	
	.nav-tabs .nav-link.active {
		color: #f73a0b;
		border-bottom-color: #f73a0b;
	}
	
	.btn-group-sm .btn {
		padding: 0.25rem 0.5rem;
		font-size: 0.75rem;
	}
	
	.badge-primary.light {
		background-color: #e7f1ff;
		color: #004085;
	}
	
	.badge-success.light {
		background-color: #d4edda;
		color: #155724;
	}
	
	.badge-warning.light {
		background-color: #fff3cd;
		color: #856404;
	}
	
	.badge-danger.light {
		background-color: #f8d7da;
		color: #721c24;
	}
</style>
@endsection
