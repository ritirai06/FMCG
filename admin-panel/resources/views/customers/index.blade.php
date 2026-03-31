@extends('layouts.app')

@section('title', 'Customers')
@section('page_title', 'Customer Management')

@section('navbar_right')
  <a href="{{ route('customers.create') }}" class="btn btn-gradient btn-sm">
    <i class="bi bi-plus-circle me-1"></i> Add Customer
  </a>
@endsection

@section('content')

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="filter-card mb-3">
  <form method="GET" action="{{ route('customers.index') }}" class="row g-2 align-items-end">
    <div class="col-md-4">
      <input type="text" name="search" class="form-control" placeholder="Search name, phone..." value="{{ request('search') }}">
    </div>
    <div class="col-md-3">
      <select name="created_by_role" class="form-select">
        <option value="">All Sources</option>
        <option value="admin" {{ request('created_by_role') === 'admin' ? 'selected' : '' }}>Created by Admin</option>
        <option value="sales" {{ request('created_by_role') === 'sales' ? 'selected' : '' }}>Created by Sales</option>
      </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary w-100" type="submit"><i class="bi bi-search me-1"></i>Filter</button>
    </div>
    <div class="col-md-2">
      <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
    </div>
  </form>
</div>

<div class="table-card">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0 fw-bold"><i class="bi bi-people me-2"></i>All Customers ({{ $customers->total() }})</h6>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>Business Name</th>
          <th>Phone</th>
          <th>Contact Person</th>
          <th>Status</th>
          <th>Created By</th>
          <th>Sales Person</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($customers as $c)
        <tr>
          <td class="text-muted" style="font-size:12px;">{{ $c->id }}</td>
          <td>
            <div class="fw-semibold">{{ $c->business_name }}
              @if($c->verified)
                <i class="bi bi-patch-check-fill text-success ms-1" title="Verified"></i>
              @endif
            </div>
            @if($c->code)<small class="text-muted">{{ $c->code }}</small>@endif
          </td>
          <td>{{ $c->mobile ?? '—' }}</td>
          <td>{{ $c->contact_person ?? '—' }}</td>
          <td>
            <span class="badge {{ $c->status === 'Active' ? 'bg-success' : 'bg-secondary' }}">
              {{ $c->status ?? 'Active' }}
            </span>
          </td>
          <td>
            @if($c->created_by && $c->createdBy)
              @if($c->createdBy->role === 'admin')
                <span class="badge" style="background:#eff6ff;color:#2563eb;">
                  <i class="bi bi-shield-fill me-1"></i>Admin
                </span>
              @else
                <span class="badge" style="background:#f0fdf4;color:#16a34a;">
                  <i class="bi bi-person-badge-fill me-1"></i>Sales
                </span>
              @endif
            @else
              <span class="badge bg-secondary">Admin</span>
            @endif
          </td>
          <td>
            @if($c->created_by && $c->createdBy && $c->createdBy->role === 'sales')
              <span style="font-size:12px;">{{ $c->createdBy->name }}</span>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td class="text-end">
            <a href="{{ route('customers.edit', $c) }}" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-pencil"></i>
            </a>
            <form action="{{ route('customers.destroy', $c) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Delete this customer?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" class="text-center text-muted py-5">
            <i class="bi bi-people" style="font-size:32px;opacity:.3;display:block;margin-bottom:8px;"></i>
            No customers found.
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-3">{{ $customers->links() }}</div>
</div>
@endsection
