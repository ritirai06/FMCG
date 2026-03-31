@extends('delivery_panel.layout')

@section('page_title', 'Stores')

@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Store</th><th>Manager</th><th>Phone</th><th>Assigned Orders</th><th>Delivered</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($stores as $s)
                <tr>
                    <td>{{ $s->store_name }}</td>
                    <td>{{ $s->manager ?? 'N/A' }}</td>
                    <td>{{ $s->phone ?? 'N/A' }}</td>
                    <td>{{ $s->assigned_orders_count }}</td>
                    <td>{{ $s->delivered_orders_count }}</td>
                    <td>{{ $s->status ? 'Active' : 'Inactive' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted">No stores linked to your orders.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
