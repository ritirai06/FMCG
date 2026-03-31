@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Order Details</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>
    </div>

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
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-edit"></i> Edit Order
                    </a>
                    @endif

                    @if($order->status !== 'Delivered' && auth()->user()->isAdmin())
                    <form action="{{ route('orders.cancel', $order) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-times-circle"></i> Cancel Order
                        </button>
                    </form>
                    @endif

                    @if($order->status === 'Out for Delivery' && auth()->user()->isDelivery())
                    <form action="{{ route('orders.updateStatus', $order) }}" method="POST">
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
