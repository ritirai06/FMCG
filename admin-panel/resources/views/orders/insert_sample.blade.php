@extends('layouts.app')

@section('title', 'Insert Sample Orders')
@section('page_title', 'Insert Sample Orders')

@section('content')

<div class="table-card" style="max-width:600px;margin:0 auto;">
    <h6 class="fw-bold mb-4" style="color:var(--primary);">
        <i class="bi bi-database me-2"></i>Database Installer
    </h6>

    <form method="POST" action="{{ route('orders.insert-sample') }}">
        @csrf
        
        <div style="padding:20px;text-align:center;">
            <p style="color:var(--text-muted);margin-bottom:20px;">
                Click the button below to create 10 sample orders for testing the Orders & Invoice Management page.
            </p>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="text-align:left;">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>Success!</strong>
                <p class="mb-0">{{ session('success') }}</p>
                <a href="{{ route('orders.index') }}" class="btn btn-success btn-sm mt-2">
                    <i class="bi bi-arrow-right me-1"></i>View Orders
                </a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" style="position:absolute;right:10px;top:10px;"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="text-align:left;">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <strong>Error!</strong>
                <p class="mb-0">{{ session('error') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" style="position:absolute;right:10px;top:10px;"></button>
            </div>
            @endif

            <button type="submit" class="btn btn-gradient" style="padding:12px 40px;font-size:16px;">
                <i class="bi bi-plus-lg me-2"></i>Create 10 Sample Orders
            </button>

            <p style="margin-top:20px;color:var(--text-muted);font-size:14px;">
                This will create test data including:<br>
                ✓ 10 sample orders with different customers<br>
                ✓ Random amounts (₹1250 - ₹5000)<br>
                ✓ Mixed statuses (Pending/Delivered)<br>
                ✓ Recent dates (last 30 days)
            </p>
        </div>
    </form>
</div>

@endsection
