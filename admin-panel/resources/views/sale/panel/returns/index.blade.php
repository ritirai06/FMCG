@extends('sale.panel.layout')
@section('title', 'Returns')
@section('back')1@endsection
@section('back_url', route('sale.panel.transactions'))

@section('content')

<!-- PROCESS RETURN CARD -->
<div class="card-sp" style="margin-bottom:16px;">
    <div class="sec-hdr" style="margin-bottom:14px;">
        <span><i class="bi bi-arrow-counterclockwise me-2" style="color:var(--danger);"></i>Process Return</span>
    </div>

    <form method="POST" action="{{ route('sale.panel.return.store') }}" id="returnForm">
        @csrf

        <div class="form-group-sp">
            <label>Select Order *</label>
            <select name="order_id" id="returnOrderSelect" required onchange="loadOrderItems(this.value)">
                <option value="">-- Select Order --</option>
                @foreach($orders as $order)
                <option value="{{ $order->id }}">
                    {{ $order->order_number ?? '#'.$order->id }} — {{ $order->customer_name ?? 'N/A' }} — ₹{{ number_format($order->total_amount ?? $order->amount ?? 0) }}
                </option>
                @endforeach
            </select>
        </div>

        <div id="returnItemsSection" style="display:none;">
            <div class="form-group-sp">
                <label>Items to Return</label>
                <div id="returnItemsList"></div>
                <button type="button" onclick="addReturnRow()"
                    style="margin-top:8px;display:flex;align-items:center;gap:6px;background:var(--primary-light);color:var(--primary);border:1.5px dashed var(--primary);border-radius:8px;padding:8px 14px;font-size:13px;font-weight:600;cursor:pointer;width:100%;justify-content:center;">
                    <i class="bi bi-plus-lg"></i> Add Another Item
                </button>
            </div>

            <div class="form-group-sp">
                <label>Reason</label>
                <textarea name="reason" rows="2" placeholder="Reason for return..."></textarea>
            </div>

            <button type="submit" class="sp-save-btn">
                <i class="bi bi-arrow-counterclockwise me-2"></i>Process Return
            </button>
        </div>
    </form>
</div>

<!-- RETURN HISTORY -->
<div class="sec-hdr" style="margin-bottom:12px;">
    <span>Return History</span>
</div>

@forelse($returns as $ret)
<div class="sp-txn-item">
    <div class="sp-txn-icon" style="background:#FEE2E2;color:#DC2626;">
        <i class="bi bi-arrow-counterclockwise"></i>
    </div>
    <div style="flex:1;min-width:0;">
        <div class="sp-txn-title">Return #{{ $ret->id }}</div>
        <div class="sp-txn-sub">
            Order {{ $ret->order?->order_number ?? '#'.$ret->order_id }} ·
            {{ $ret->created_at?->format('d M Y') }}
        </div>
        @if($ret->reason)
        <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $ret->reason }}</div>
        @endif
        @if($ret->items)
        <div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:6px;">
            @foreach((array)$ret->items as $item)
            <span style="background:#FEE2E2;color:#DC2626;font-size:11px;font-weight:600;padding:2px 8px;border-radius:20px;">
                {{ $item['name'] ?? '' }} ×{{ $item['quantity'] ?? 0 }}
            </span>
            @endforeach
        </div>
        @endif
    </div>
    <div class="sp-txn-amount" style="color:#DC2626;">-₹{{ number_format($ret->total_amount ?? 0) }}</div>
</div>
@empty
<div class="empty-state">
    <i class="bi bi-arrow-counterclockwise"></i>
    <p>No returns yet</p>
</div>
@endforelse

{{ $returns->links('vendor.pagination.simple-bootstrap-5') }}

@endsection

@push('scripts')
<script>
// Build product options once
const productOptions = `@foreach($orders as $o)@endforeach`;

let returnRowCount = 0;

function makeRow(i) {
    return `
    <div class="return-row" style="display:flex;gap:8px;margin-bottom:8px;align-items:center;">
        <select name="items[${i}][product_id]"
            style="flex:2;padding:9px 10px;border-radius:8px;border:1.5px solid var(--border);font-size:13px;background:var(--card-bg);outline:none;"
            required>
            <option value="">Select Product</option>
            @foreach(\App\Models\Product::where('status','Active')->orderBy('name')->get() as $p)
            <option value="{{ $p->id }}">{{ $p->name }}</option>
            @endforeach
        </select>
        <input type="number" name="items[${i}][quantity]" min="1" value="1"
            style="width:70px;padding:9px 10px;border-radius:8px;border:1.5px solid var(--border);font-size:13px;background:var(--card-bg);outline:none;"
            required>
        <button type="button" onclick="this.closest('.return-row').remove()"
            style="background:#FEE2E2;color:#DC2626;border:none;border-radius:8px;padding:9px 12px;cursor:pointer;flex-shrink:0;">
            <i class="bi bi-trash"></i>
        </button>
    </div>`;
}

function loadOrderItems(orderId) {
    const section = document.getElementById('returnItemsSection');
    const list    = document.getElementById('returnItemsList');
    if (!orderId) { section.style.display = 'none'; return; }
    returnRowCount = 0;
    list.innerHTML = makeRow(returnRowCount++);
    section.style.display = '';
}

function addReturnRow() {
    document.getElementById('returnItemsList').insertAdjacentHTML('beforeend', makeRow(returnRowCount++));
}
</script>
@endpush
