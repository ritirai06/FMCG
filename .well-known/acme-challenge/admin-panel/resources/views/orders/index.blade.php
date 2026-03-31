@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 d-inline-block">Orders</h1>
        </div>
        <div class="col-md-4 text-end">
            @if(auth()->user()->isAdmin() || auth()->user()->isSales())
            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create Order
            </a>
            @endif
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($message = Session::get('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Orders Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order #</th>
                        <th>Store</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('orders.show', $order) }}" class="fw-bold text-primary">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td>{{ $order->store?->store_name ?? 'N/A' }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td class="fw-bold">₹{{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $order->status === 'Delivered' ? 'success' : ($order->status === 'Pending' ? 'warning' : 'info') }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td>{{ $order->createdBy?->name ?? 'Unknown' }}</td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-warning" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox"></i> No orders found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
    <div class="d-flex justify-content-end mt-4">
        {{ $orders->links() }}
    </div>
    @endif
</div>

@endsection
