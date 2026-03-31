@extends('layouts.app')

@section('title', 'Invoice — ' . $invoice->invoice_number)

@push('styles')
<style>
.inv-detail-grid { display:grid; grid-template-columns:1fr 340px; gap:20px; }
.inv-card { background:#fff; border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow); padding:20px; margin-bottom:16px; }
.inv-meta-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.inv-meta-item .lbl { font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase; letter-spacing:.04em; margin-bottom:3px; }
.inv-meta-item .val { font-size:14px; font-weight:600; }
.s-chip { display:inline-flex; align-items:center; padding:4px 12px; border-radius:999px; font-size:12px; font-weight:700; }
.s-pending   { background:#fef3c7; color:#b45309; }
.s-confirmed { background:#dbeafe; color:#1d4ed8; }
.s-dispatched{ background:#ede9fe; color:#6d28d9; }
.s-delivered { background:#dcfce7; color:#166534; }
.s-cancelled { background:#fee2e2; color:#b91c1c; }
.sticky-sidebar { position:sticky; top:80px; }
@media(max-width:992px){ .inv-detail-grid{grid-template-columns:1fr;} .sticky-sidebar{position:static;} }
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
@endphp

<div class="d-flex align-items-center mb-4">
  <a href="{{ route('invoices.web.index') }}" class="btn btn-sm btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i></a>
  <h4 class="mb-0 fw-bold">{{ $invoice->invoice_number }}</h4>
  <span class="s-chip {{ $chipClass($invoice->status) }} ms-3">{{ ucfirst($invoice->status) }}</span>
  <div class="ms-auto d-flex gap-2">
    <a href="{{ route('invoices.view', $invoice->id) }}" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="bi bi-printer me-1"></i>Print</a>
    <a href="{{ route('invoices.download', $invoice->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-download me-1"></i>PDF</a>
  </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="inv-detail-grid">
  {{-- Main --}}
  <div>
    {{-- Invoice meta --}}
    <div class="inv-card">
      <div class="inv-meta-grid">
        <div class="inv-meta-item"><div class="lbl">Invoice No</div><div class="val">{{ $invoice->invoice_number }}</div></div>
        <div class="inv-meta-item"><div class="lbl">Party / Store</div><div class="val">{{ optional($invoice->party)->store_name ?? '—' }}</div></div>
        <div class="inv-meta-item"><div class="lbl">Invoice Date</div><div class="val">{{ optional($invoice->date)->format('d M Y') ?? '—' }}</div></div>
        <div class="inv-meta-item"><div class="lbl">Due Date</div><div class="val">{{ optional($invoice->due_date)->format('d M Y') ?? '—' }}</div></div>
        <div class="inv-meta-item"><div class="lbl">Created By</div><div class="val">{{ optional($invoice->createdBy)->name ?? '—' }}</div></div>
        <div class="inv-meta-item"><div class="lbl">Delivery Person</div><div class="val">{{ optional($invoice->deliveryUser)->name ?? 'Not assigned' }}</div></div>
        @if($invoice->notes)
        <div class="inv-meta-item" style="grid-column:span 2"><div class="lbl">Notes</div><div class="val">{{ $invoice->notes }}</div></div>
        @endif
      </div>
    </div>

    {{-- Items table --}}
    <div class="inv-card">
      <h6 class="fw-bold mb-3">Items</h6>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead class="table-light">
            <tr><th>Item</th><th class="text-center">Qty</th><th class="text-end">Price</th><th class="text-end">Total</th></tr>
          </thead>
          <tbody>
          @forelse($invoice->items as $item)
            <tr>
              <td>
                <div class="fw-semibold">{{ $item->item_name ?? optional($item->product)->product_name ?? 'Item' }}</div>
                @if($item->product)<small class="text-muted">SKU: {{ $item->product->sku ?? '—' }}</small>@endif
              </td>
              <td class="text-center">{{ $item->quantity }}</td>
              <td class="text-end">Rs {{ number_format((float)$item->price, 2) }}</td>
              <td class="text-end fw-semibold">Rs {{ number_format((float)$item->total, 2) }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted py-4">No items on this invoice.</td></tr>
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
            <tr><td colspan="3" class="text-end fw-bold fs-6">Total</td><td class="text-end fw-bold fs-6 text-primary">Rs {{ number_format($invoice->total_amount, 2) }}</td></tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>

  {{-- Sidebar --}}
  <div class="sticky-sidebar">
    {{-- Status update --}}
    <div class="inv-card">
      <h6 class="fw-bold mb-3">Update Status</h6>
      <form method="POST" action="{{ route('invoices.web.status', $invoice->id) }}">
        @csrf
        <div class="mb-3">
          <select class="form-select" name="status" required>
            @foreach(['pending','confirmed','dispatched','delivered','cancelled'] as $s)
              @php
                $disabled = false;
                if($user->role === 'delivery' && !in_array($s, ['confirmed','dispatched','delivered'])) $disabled = true;
                if($user->role === 'sales' && $s === 'delivered') $disabled = true;
              @endphp
              <option value="{{ $s }}" @selected($invoice->status === $s) @disabled($disabled)>{{ ucfirst($s) }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-arrow-repeat me-1"></i>Update Status</button>
      </form>
    </div>

    {{-- Assign delivery (admin only) --}}
    @if($user->role === 'admin')
    <div class="inv-card">
      <h6 class="fw-bold mb-3">Assign Delivery</h6>
      <form method="POST" action="{{ route('invoices.web.assign', $invoice->id) }}">
        @csrf
        <div class="mb-3">
          <select class="form-select" name="delivery_user_id" required>
            <option value="">Select delivery person...</option>
            @foreach($deliveryUsers as $du)
              <option value="{{ $du->id }}" @selected($invoice->assigned_delivery == $du->id)>{{ $du->name }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-person-check me-1"></i>Assign & Confirm</button>
      </form>
    </div>
    @endif

    {{-- Linked order --}}
    @if($invoice->order)
    <div class="inv-card">
      <h6 class="fw-bold mb-3">Linked Order</h6>
      <div class="d-flex justify-content-between align-items-center">
        <span class="fw-semibold">{{ $invoice->order->order_number ?? ('#' . $invoice->order->id) }}</span>
        <span class="badge bg-secondary">{{ $invoice->order->status }}</span>
      </div>
      <a href="{{ route('orders.show', $invoice->order->id) }}" class="btn btn-sm btn-outline-secondary mt-2 w-100">View Order</a>
    </div>
    @endif
  </div>
</div>
@endsection
