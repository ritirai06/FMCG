@extends('delivery_panel.layout')

@section('page_title', 'Transaction — ' . $invoice->invoice_number)

@push('styles')
<style>
:root {
  --dp-blue: #2563EB;
  --dp-blue-light: #EFF6FF;
  --dp-green: #16a34a;
}
.txn-detail-grid { display: grid; grid-template-columns: 1fr 320px; gap: 18px; }
.txn-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); padding: 18px 20px; margin-bottom: 14px; }
.txn-meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.txn-meta-item .lbl { font-size: 11px; color: var(--muted); font-weight: 700; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 3px; }
.txn-meta-item .val { font-size: 14px; font-weight: 600; }
.s-chip { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; }
.s-pending   { background: #fef3c7; color: #b45309; }
.s-confirmed { background: #dbeafe; color: var(--dp-blue); }
.s-dispatched{ background: #ede9fe; color: #6d28d9; }
.s-delivered { background: #dcfce7; color: var(--dp-green); }
.s-cancelled { background: #fee2e2; color: #b91c1c; }
.dp-btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; border-radius: 10px; font-size: 14px; font-weight: 700; border: none; cursor: pointer; text-decoration: none; transition: all .15s; }
.dp-btn-primary { background: var(--dp-blue); color: #fff; width: 100%; justify-content: center; }
.dp-btn-primary:hover { background: #1d4ed8; color: #fff; }
.dp-btn-map { background: var(--dp-green); color: #fff; width: 100%; justify-content: center; }
.dp-btn-map:hover { background: #15803d; color: #fff; }
.sticky-sidebar { position: sticky; top: 80px; }
@media(max-width: 992px) { .txn-detail-grid { grid-template-columns: 1fr; } .sticky-sidebar { position: static; } }
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
  $subtotal = $invoice->items->sum(fn($i) => (float)$i->total);
  $party    = optional($invoice->party);
  $storeLat = $party->latitude ?? null;
  $storeLng = $party->longitude ?? null;
  $storeAddr= $party->address ?? null;
  $mapUrl   = ($storeLat && $storeLng)
    ? 'https://maps.google.com/?q='.$storeLat.','.$storeLng
    : ($storeAddr ? 'https://maps.google.com/?q='.urlencode($storeAddr) : null);
  $directionsUrl = ($storeLat && $storeLng)
    ? 'https://www.google.com/maps/dir/?api=1&destination='.$storeLat.','.$storeLng
    : ($storeAddr ? 'https://www.google.com/maps/dir/?api=1&destination='.urlencode($storeAddr) : null);
@endphp

<div class="d-flex align-items-center mb-4">
  <a href="{{ route('delivery.panel.transactions') }}" class="btn btn-sm btn-outline-secondary me-3"><i class="fas fa-arrow-left"></i></a>
  <h4 class="mb-0 fw-bold">{{ $invoice->invoice_number }}</h4>
  <span class="s-chip {{ $chipClass($invoice->status) }} ms-3">{{ ucfirst($invoice->status) }}</span>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="txn-detail-grid">
  {{-- Main --}}
  <div>
    {{-- Party / Customer details --}}
    <div class="txn-card">
      <h6 class="fw-bold mb-3"><i class="fas fa-store me-2 text-primary"></i>Customer / Party</h6>
      <div class="txn-meta-grid">
        <div class="txn-meta-item"><div class="lbl">Store Name</div><div class="val">{{ $party->store_name ?? 'N/A' }}</div></div>
        <div class="txn-meta-item"><div class="lbl">Phone</div>
          <div class="val">
            @if($party->phone)
              <a href="tel:{{ $party->phone }}" class="text-primary">{{ $party->phone }}</a>
            @else N/A @endif
          </div>
        </div>
        <div class="txn-meta-item" style="grid-column:span 2"><div class="lbl">Address</div><div class="val">{{ $storeAddr ?? 'N/A' }}</div></div>
      </div>
      @if($directionsUrl)
      <a href="{{ $directionsUrl }}" target="_blank" class="dp-btn dp-btn-map mt-3">
        <i class="fas fa-route"></i> Get Directions
      </a>
      @endif
    </div>

    {{-- Items (readonly) --}}
    <div class="txn-card">
      <h6 class="fw-bold mb-3"><i class="fas fa-box me-2 text-primary"></i>Items</h6>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr><th>Item</th><th class="text-center">Qty</th><th class="text-end">Price</th><th class="text-end">Total</th></tr>
          </thead>
          <tbody>
          @forelse($invoice->items as $item)
            <tr>
              <td>
                <div class="fw-semibold">{{ $item->item_name ?? optional($item->product)->product_name ?? 'Item' }}</div>
              </td>
              <td class="text-center">{{ $item->quantity }}</td>
              <td class="text-end">Rs {{ number_format((float)$item->price, 2) }}</td>
              <td class="text-end fw-semibold">Rs {{ number_format((float)$item->total, 2) }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted py-4">No items.</td></tr>
          @endforelse
          </tbody>
          <tfoot class="table-light">
            <tr><td colspan="3" class="text-end">Subtotal</td><td class="text-end fw-semibold">Rs {{ number_format($subtotal, 2) }}</td></tr>
            @if((float)$invoice->tax > 0)
            <tr><td colspan="3" class="text-end">Tax</td><td class="text-end">+ Rs {{ number_format((float)$invoice->tax, 2) }}</td></tr>
            @endif
            @if((float)$invoice->discount > 0)
            <tr><td colspan="3" class="text-end">Discount</td><td class="text-end text-danger">- Rs {{ number_format((float)$invoice->discount, 2) }}</td></tr>
            @endif
            <tr>
              <td colspan="3" class="text-end fw-bold fs-6">Total</td>
              <td class="text-end fw-bold fs-5 text-primary">Rs {{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  {{-- Sidebar --}}
  <div class="sticky-sidebar">
    {{-- Invoice info --}}
    <div class="txn-card">
      <h6 class="fw-bold mb-3">Invoice Info</h6>
      <div class="txn-meta-grid">
        <div class="txn-meta-item"><div class="lbl">Date</div><div class="val">{{ optional($invoice->date)->format('d M Y') ?? '—' }}</div></div>
        <div class="txn-meta-item"><div class="lbl">Due</div><div class="val">{{ optional($invoice->due_date)->format('d M Y') ?? '—' }}</div></div>
        <div class="txn-meta-item"><div class="lbl">Created By</div><div class="val">{{ optional($invoice->createdBy)->name ?? '—' }}</div></div>
        <div class="txn-meta-item"><div class="lbl">Amount</div><div class="val text-primary">Rs {{ number_format($invoice->total_amount, 2) }}</div></div>
      </div>
      @if($invoice->notes)
      <div class="mt-3 p-2 bg-light rounded"><small class="text-muted">{{ $invoice->notes }}</small></div>
      @endif
    </div>

    {{-- Status update --}}
    <div class="txn-card">
      <h6 class="fw-bold mb-3">Update Status</h6>
      <form method="POST" action="{{ route('delivery.panel.transaction.status', $invoice->id) }}">
        @csrf
        <div class="mb-3">
          <label class="form-label fw-semibold">Select Status</label>
          <select class="form-select" name="status" required>
            <option value="">Choose...</option>
            <option value="confirmed"  @selected($invoice->status === 'confirmed')>✅ Confirmed</option>
            <option value="dispatched" @selected($invoice->status === 'dispatched')>🚚 Dispatched</option>
            <option value="delivered"  @selected($invoice->status === 'delivered')>📦 Delivered</option>
          </select>
        </div>
        <button type="submit" class="dp-btn dp-btn-primary">
          <i class="fas fa-save"></i> Save Status
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
