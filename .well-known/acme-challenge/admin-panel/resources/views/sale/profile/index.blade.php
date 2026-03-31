@extends('sale.layout')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="me-auto">
                <h3 class="mb-1">Profile</h3>
                <p class="text-muted mb-0">Account and performance details fetched from live data.</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 flex-wrap mb-4">
                            <img
                                src="{{ $salesPerson?->avatar_path ? asset('storage/' . $salesPerson->avatar_path) : 'https://ui-avatars.com/api/?name=' . urlencode($salesPerson?->name ?? $user?->name ?? 'User') . '&background=0D8ABC&color=fff' }}"
                                alt="Profile"
                                style="width:84px;height:84px;border-radius:50%;object-fit:cover;border:3px solid #e9ecef;"
                            >
                            <div>
                                <h4 class="mb-1">{{ $salesPerson?->name ?? $user?->name ?? 'N/A' }}</h4>
                                <p class="mb-1 text-muted">{{ ucfirst($user?->role ?? 'sales') }} | {{ $salesRegion ?? 'N/A' }}</p>
                                <span class="badge {{ ($user?->status ?? false) ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ($user?->status ?? false) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Full Name</small>
                                <div class="fw-semibold">{{ $salesPerson?->name ?? $user?->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Email</small>
                                <div class="fw-semibold">{{ $salesPerson?->email ?? $user?->email ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Phone</small>
                                <div class="fw-semibold">{{ $salesPerson?->phone ?? $user?->phone ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Company</small>
                                <div class="fw-semibold">{{ $companyName ?? 'SalePanel' }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">City / Region</small>
                                <div class="fw-semibold">{{ $salesPerson?->city?->city_name ?? $salesRegion ?? 'N/A' }}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">GST Number</small>
                                <div class="fw-semibold">{{ $companySettings?->gst_number ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Compensation Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-6 col-md-3">
                                <small class="text-muted d-block">Base Salary</small>
                                <div class="fw-semibold">&#8377;{{ number_format((float)($salesPerson?->base_salary ?? 0), 0) }}</div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <small class="text-muted d-block">Allowance</small>
                                <div class="fw-semibold">&#8377;{{ number_format((float)($salesPerson?->allowance ?? 0), 0) }}</div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <small class="text-muted d-block">Bonus</small>
                                <div class="fw-semibold">{{ (float)($salesPerson?->bonus_percent ?? 0) }}%</div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <small class="text-muted d-block">Incentive</small>
                                <div class="fw-semibold">{{ (float)($salesPerson?->incentive_percent ?? 0) }}%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Performance Snapshot</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Total Orders</span>
                            <span class="fw-semibold">{{ number_format($profileStats['total_orders'] ?? 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Delivered Orders</span>
                            <span class="fw-semibold text-success">{{ number_format($profileStats['delivered_orders'] ?? 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Pending Orders</span>
                            <span class="fw-semibold text-warning">{{ number_format($profileStats['pending_orders'] ?? 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Total Revenue</span>
                            <span class="fw-semibold">&#8377;{{ number_format((float)($profileStats['total_revenue'] ?? 0), 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Present Days (Month)</span>
                            <span class="fw-semibold">{{ number_format($profileStats['present_days_this_month'] ?? 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between pt-2">
                            <span class="text-muted">Attendance Records (Month)</span>
                            <span class="fw-semibold">{{ number_format($profileStats['attendance_records_this_month'] ?? 0) }}</span>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Info</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Target Sales</span>
                            <span class="fw-semibold">{{ number_format((float)($salesPerson?->target_sales ?? 0), 0) }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted">Company Email</span>
                            <span class="fw-semibold">{{ $companySettings?->company_email ?? $companySettings?->email ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex justify-content-between pt-2">
                            <span class="text-muted">Company Phone</span>
                            <span class="fw-semibold">{{ $companySettings?->company_phone ?? $companySettings?->phone ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
