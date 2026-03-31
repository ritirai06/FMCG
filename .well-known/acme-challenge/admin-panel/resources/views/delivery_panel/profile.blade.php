@extends('delivery_panel.layout')

@section('page_title', 'Profile')

@section('content')
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ $deliveryProfile?->avatar_path ? asset('storage/' . $deliveryProfile->avatar_path) : ('https://ui-avatars.com/api/?name=' . urlencode($deliveryProfile?->name ?? $user?->name ?? 'Delivery')) }}" style="width:80px;height:80px;border-radius:50%;object-fit:cover;">
                    <div>
                        <h4 class="mb-1">{{ $deliveryProfile?->name ?? $user?->name }}</h4>
                        <div class="text-muted">Delivery Executive</div>
                        <span class="badge bg-success">{{ $deliveryProfile?->status ?? 'Active' }}</span>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6"><small class="text-muted d-block">Email</small><div class="fw-semibold">{{ $deliveryProfile?->email ?? $user?->email ?? 'N/A' }}</div></div>
                    <div class="col-md-6"><small class="text-muted d-block">Phone</small><div class="fw-semibold">{{ $deliveryProfile?->phone ?? $user?->phone ?? 'N/A' }}</div></div>
                    <div class="col-md-6"><small class="text-muted d-block">Vehicle</small><div class="fw-semibold">{{ $deliveryProfile?->vehicle ?? 'N/A' }}</div></div>
                    <div class="col-md-6"><small class="text-muted d-block">Company</small><div class="fw-semibold">{{ $companyName }}</div></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><strong>Performance</strong></div>
            <div class="card-body">
                <div class="d-flex justify-content-between py-2 border-bottom"><span>Total Assigned</span><strong>{{ $profileStats['total_assigned'] }}</strong></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span>Delivered</span><strong>{{ $profileStats['delivered'] }}</strong></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span>Out for Delivery</span><strong>{{ $profileStats['out_for_delivery'] }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span>Revenue Handled</span><strong>&#8377;{{ number_format($profileStats['revenue_handled'],2) }}</strong></div>
            </div>
        </div>
    </div>
</div>
@endsection
