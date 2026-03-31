@extends('layouts.app')

@section('title', 'Invoices & Transactions')

@push('styles')
<style>
.inv-kpis { display:grid; grid-template-columns:repeat(6,1fr); gap:10px; margin-bottom:18px; }
.inv-kpi  { background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:14px 16px; box-shadow:var(--shadow); }
.inv-kpi .lbl { font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
.inv-kpi .val { font-size:22px; font-weight:800; margin-top:4px; line-height:1.1; }
.inv-toolbar { display:grid; grid-template-columns:1fr auto auto auto; gap:10px; margin-bottom:14px; }
.s-chip { display:inline-flex; align-items:center; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; }
.s-pending   { background:#fef3c7; color:#b45309; }
.s-confirmed { background:#dbeafe; color:#1d4ed8; }
.s-dispatched{ background:#ede9fe; color:#6d28d9; }
.s-delivered { background:#dcfce7; color:#166534; }
.s-cancelled { background:#fee2e2; color:#b91c1c; }
@media(max-width:992px){ .inv-kpis{grid-template-columns:repeat(3,1fr);} .inv-toolbar{grid-template-columns:1fr;} }
@media(max-width:576px){ .inv-kpis{grid-template-columns:repeat(2,1fr);} }
</style>
@endpush

@section('content')
@php
  $user = auth()->user();
  $chipClass = fn($s) => match(strtolower((string)$s)) {
    'pending'    => 's-pending',
    'confirmed'  => 's-confirmed',
    'dispatched' => 's-dispatched',
    'delivered'  => 's-delivered',
    'cancelled'  => 's-cancelled',
    default      => 's-pending',
  };
@endphp

<div class="inv-kpis">
  <div class="inv-kpi"><div class="lbl">All</div><div class="val">{{ $statusCounts['all'] ?? 0 }}</div></div>
  @foreach($statuses as $s)
  <div class="inv-kpi"><div class="lbl">{{ ucfirst($s) }}</div><div class="val">{{ $statusCounts[$s] ?? 0 }}</div></div>
  @endforeach
</div>

<form method="GET" action="{{ route('invoices.web.index') }}" class="inv-toolbar">
  <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search invoice no / party...">
  <select class="form-select" name="status">
    <option value="">All Status</option>
    @foreach($statuses as $s)
      <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
    @endforeach
  </select>
  <button class="btn btn-primary" type="submit"><i class="bi bi-funnel"></i> Filter</button>
  @if(in_array($user->role, ['admin','sales']))
  <a href="{{ route('invoices.web.create') }}" class="btn btn-gradient"><i class="bi bi-plus-lg"></i> New Invoice</a>
  @endif
</form>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Invoice #</th>
          <th>Party / Store</th>
          <th>Status</th>
          <th>Amount</th>
          <th>Due Date</th>
          <th>Created By</th>
          @if($user->role === 'admin')<th>Delivery</th>@endif
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
      @forelse($invoices as $inv)
        <tr>
          <td class="fw-semibold">{{ $inv->invoice_number }}</td>
          <td>{{ optional($inv->party)->store_name ?? 'N/A' }}</td>
          <td><span class="s-chip {{ $chipClass($inv->status) }}">{{ ucfirst($inv->status) }}</span></td>
          <td class="fw-semibold">Rs {{ number_format($inv->total_amount, 2) }}</td>
          <td>{{ optional($inv->due_date)->format('d M Y') ?? '—' }}</td>
          <td>{{ optional($inv->createdBy)->name ?? '—' }}</td>
          @if($user->role === 'admin')
          <td>
            @if($inv->deliveryUser)
              <span class="badge bg-info text-dark">{{ $inv->deliveryUser->name }}</span>
            @else
              <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#assignModal{{ $inv->id }}">
                <i class="bi bi-person-plus"></i> Assign
              </button>
              <div class="modal fade" id="assignModal{{ $inv->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <form method="POST" action="{{ route('invoices.web.assign', $inv->id) }}">
                      @csrf
                      <div class="modal-header">
                        <h5 class="modal-title">Assign Delivery — {{ $inv->invoice_number }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <label class="form-label">Select Delivery Person</label>
                        <select class="form-select" name="delivery_user_id" required>
                          <option value="">Choose...</option>
                          @foreach($deliveryUsers as $du)
                            <option value="{{ $du->id }}">{{ $du->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Assign & Confirm</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            @endif
          </td>
          @endif
          <td class="text-end">
            <a href="{{ route('invoices.web.show', $inv->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
            <a href="{{ route('invoices.view', $inv->id) }}" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="bi bi-printer"></i></a>
            @if($user->role === 'admin')
            <form method="POST" action="{{ route('invoices.delete', $inv->id) }}" class="d-inline" onsubmit="return confirm('Delete this invoice?')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
            </form>
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="8" class="text-center text-muted py-5">No invoices found.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-3">{{ $invoices->links() }}</div>
</div>
@endsection
