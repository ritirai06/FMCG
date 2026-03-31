@extends('delivery_panel.layout')

@section('page_title', 'Transactions')

@push('styles')
<style>
:root {
  --dp-blue: #2563EB;
  --dp-blue-light: #EFF6FF;
  --dp-green: #16a34a;
  --dp-orange: #b45309;
  --dp-purple: #6d28d9;
  --dp-red: #b91c1c;
}
.txn-toolbar {
  display: grid;
  grid-template-columns: 1fr auto auto;
  gap: 10px;
  margin-bottom: 14px;
}
.txn-kpis {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 10px;
  margin-bottom: 16px;
}
.txn-kpi {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 14px 16px;
}
.txn-kpi .lbl { font-size: 11px; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
.txn-kpi .val { font-size: 22px; font-weight: 800; margin-top: 4px; line-height: 1.1; }

.txn-card {
  background: #fff;
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 16px 18px;
  margin-bottom: 12px;
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 12px;
  align-items: start;
  transition: box-shadow .2s;
}
.txn-card:hover { box-shadow: 0 4px 16px rgba(37,99,235,.1); }
.txn-card .inv-no { font-size: 13px; font-weight: 700; color: var(--dp-blue); }
.txn-card .party  { font-size: 14px; font-weight: 600; color: #111827; margin: 2px 0; }
.txn-card .meta   { font-size: 12px; color: var(--muted); }
.txn-card .amount { font-size: 18px; font-weight: 800; color: #111827; text-align: right; }
.txn-card .actions { display: flex; gap: 8px; margin-top: 10px; flex-wrap: wrap; }

.s-chip { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
.s-pending   { background: #fef3c7; color: var(--dp-orange); }
.s-confirmed { background: #dbeafe; color: var(--dp-blue); }
.s-dispatched{ background: #ede9fe; color: var(--dp-purple); }
.s-delivered { background: #dcfce7; color: var(--dp-green); }
.s-cancelled { background: #fee2e2; color: var(--dp-red); }

.dp-btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; text-decoration: none; transition: all .15s; }
.dp-btn-primary { background: var(--dp-blue); color: #fff; }
.dp-btn-primary:hover { background: #1d4ed8; color: #fff; }
.dp-btn-ghost { background: transparent; color: var(--dp-blue); border: 1px solid var(--dp-blue); }
.dp-btn-ghost:hover { background: var(--dp-blue-light); }
.dp-btn-map { background: #16a34a; color: #fff; }
.dp-btn-map:hover { background: #15803d; color: #fff; }

.empty-state { text-align: center; padding: 60px 20px; color: var(--muted); }
.empty-state i { font-size: 48px; margin-bottom: 12px; display: block; opacity: .4; }

@media(max-width: 768px) {
  .txn-kpis { grid-template-columns: repeat(2, 1fr); }
  .txn-toolbar { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
@php
  $chipClass = fn($s) => match(strtolower((string)$s)) {
    'pending'    => 's-pending',
    'confirmed'  => 's-confirmed',
    'dispatched' => 's-dispatched',
    'delivered'  => 's-delivered',
    'cancelled'  => 's-cancelled',
    default      => 's-pending',
  };
@endphp

{{-- KPIs --}}
<div class="txn-kpis">
  <div class="txn-kpi"><div class="lbl">Total</div><div class="val">{{ $statusCounts['all'] ?? 0 }}</div></div>
  <div class="txn-kpi"><div class="lbl">Confirmed</div><div class="val" style="color:var(--dp-blue)">{{ $statusCounts['confirmed'] ?? 0 }}</div></div>
  <div class="txn-kpi"><div class="lbl">Dispatched</div><div class="val" style="color:var(--dp-purple)">{{ $statusCounts['dispatched'] ?? 0 }}</div></div>
  <div class="txn-kpi"><div class="lbl">Delivered</div><div class="val" style="color:var(--dp-green)">{{ $statusCounts['delivered'] ?? 0 }}</div></div>
</div>

{{-- Toolbar --}}
<form method="GET" action="{{ route('delivery.panel.transactions') }}" class="txn-toolbar">
  <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search invoice / party...">
  <select class="form-select" name="status" style="min-width:140px;">
    <option value="">All Status</option>
    @foreach(['pending','confirmed','dispatched','delivered','cancelled'] as $s)
      <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
    @endforeach
  </select>
  <button class="dp-btn dp-btn-primary" type="submit"><i class="fas fa-filter"></i> Filter</button>
</form>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

{{-- Invoice cards --}}
@forelse($invoices as $invoice)
@php
  $party    = optional($invoice->party);
  $storeLat = $party->latitude ?? null;
  $storeLng = $party->longitude ?? null;
  $storeAddr= $party->address ?? null;
  $mapUrl   = ($storeLat && $storeLng)
    ? 'https://maps.google.com/?q='.$storeLat.','.$storeLng
    : ($storeAddr ? 'https://maps.google.com/?q='.urlencode($storeAddr) : null);
@endphp
<div class="txn-card">
  <div>
    <div class="inv-no">{{ $invoice->invoice_number }}</div>
    <div class="party">{{ $party->store_name ?? 'N/A' }}</div>
    <div class="meta">
      <span class="s-chip {{ $chipClass($invoice->status) }}">{{ ucfirst($invoice->status) }}</span>
      &nbsp;·&nbsp; {{ optional($invoice->date)->format('d M Y') ?? '—' }}
      @if($invoice->due_date)
        &nbsp;·&nbsp; Due: {{ $invoice->due_date->format('d M Y') }}
      @endif
    </div>
    <div class="actions">
      <a href="{{ route('delivery.panel.transaction.show', $invoice->id) }}" class="dp-btn dp-btn-ghost">
        <i class="fas fa-eye"></i> View Details
      </a>
      @if($mapUrl)
      <a href="{{ $mapUrl }}" target="_blank" class="dp-btn dp-btn-map">
        <i class="fas fa-map-marker-alt"></i> Location
      </a>
      @endif
    </div>
  </div>
  <div class="text-end">
    <div class="amount">Rs {{ number_format($invoice->total_amount, 2) }}</div>
    <div class="meta mt-1">{{ $invoice->items->count() }} item(s)</div>
  </div>
</div>
@empty
<div class="empty-state">
  <i class="fas fa-receipt"></i>
  <h5>No transactions found</h5>
  <p>Invoices assigned to you will appear here.</p>
</div>
@endforelse

@if(method_exists($invoices, 'links'))
  <div class="mt-3">{{ $invoices->links() }}</div>
@endif
@endsection
