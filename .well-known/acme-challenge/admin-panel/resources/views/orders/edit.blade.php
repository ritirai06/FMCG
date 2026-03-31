@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Edit Order #{{ $order->order_number }}</h1>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="fas fa-exclamation-circle"></i> Errors:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Update Order Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('orders.update', $order) }}">
                        @csrf @method('PUT')

                        <!-- Current Status -->
                        <div class="mb-4">
                            <h6>Current Status: <span class="badge bg-warning">{{ $order->status }}</span></h6>
                            <p class="text-muted small">Created: {{ $order->created_at->format('d M Y H:i') }} | Updated: {{ $order->updated_at->format('d M Y H:i') }}</p>
                        </div>

                        <!-- Status Change -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Change Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="">-- Select Status --</option>
                                @forelse($statuses as $value => $label)
                                <option value="{{ $value }}" @selected(old('status') == $value || $order->status == $value)>
                                    {{ $label }}
                                </option>
                                @empty
                                @endforelse
                            </select>
                            @error('status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted d-block mt-2">
                                ℹ️ Status Flow: Pending → Approved → Packed → Out for Delivery → Delivered
                            </small>
                        </div>

                        <!-- Assign Delivery Person -->
                        <div class="mb-3">
                            <label for="assigned_delivery" class="form-label">Assign to Delivery Person</label>
                            <select name="assigned_delivery" id="assigned_delivery" class="form-select @error('assigned_delivery') is-invalid @enderror">
                                <option value="">-- No Assignment --</option>
                                @foreach($deliveryUsers as $user)
                                <option value="{{ $user->id }}" @selected(old('assigned_delivery') == $user->id || $order->assigned_delivery == $user->id)>
                                    {{ $user->name }} - {{ $user->phone }}
                                </option>
                                @endforeach
                            </select>
                            @error('assigned_delivery')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Order Summary -->
                        <div class="alert alert-info mt-4">
                            <h6 class="alert-heading">Order Summary</h6>
                            <p class="mb-0">
                                <strong>Store:</strong> {{ $order->store?->store_name }}<br>
                                <strong>Customer:</strong> {{ $order->customer_name }} ({{ $order->customer_phone }})<br>
                                <strong>Items:</strong> {{ $order->items->count() }} | 
                                <strong>Total:</strong> ₹{{ number_format($order->total_amount, 2) }}
                            </p>
                        </div>

                        <!-- Buttons -->
                        <div class="mt-4">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary float-end">
                                <i class="fas fa-save"></i> Update Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Order Details Sidebar -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($order->items as $item)
                    <div class="list-group-item">
                        <h6 class="mb-1">{{ $item->product_name }}</h6>
                        <small class="text-muted">
                            Qty: {{ $item->quantity }} × ₹{{ number_format($item->unit_price, 2) }} = 
                            <strong>₹{{ number_format($item->subtotal, 2) }}</strong>
                        </small>
                    </div>
                    @endforeach
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between">
                        <strong>Total Amount:</strong>
                        <strong class="text-success">₹{{ number_format($order->total_amount, 2) }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('status').addEventListener('change', function() {
    if(this.value === 'Out for Delivery') {
        const deliverySelect = document.getElementById('assigned_delivery');
        if(!deliverySelect.value) {
            alert('Please assign a delivery person before marking as "Out for Delivery"');
            this.value = '{{ $order->status }}';
        }
    }
});
</script>
@endsection
