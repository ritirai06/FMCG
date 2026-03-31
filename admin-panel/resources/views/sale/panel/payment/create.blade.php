@extends('sale.panel.layout')
@section('title', 'Payment In')
@section('back')1@endsection
@section('back_url', route('sale.panel.parties'))

@section('content')

@if($errors->any())
<div class="alert-sp alert-error mb-3"><i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('sale.panel.payment.store') }}" id="paymentForm">
    @csrf

    <!-- SELECT PARTY -->
    <div class="sp-section-hdr">Select Party</div>
    <div class="card-sp">
        <div class="form-group-sp" style="margin-bottom:0;">
            <label>Party *</label>
            <select name="customer_id" id="partySelect" required onchange="loadOrders(this.value)">
                <option value="">-- Select Party --</option>
                @foreach($parties as $p)
                <option value="{{ $p->id }}" {{ $party && $party->id==$p->id ? 'selected' : '' }}>
                    {{ $p->business_name }} ({{ $p->mobile }})
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- UNSETTLED INVOICES -->
    <div id="invoicesSection" style="{{ $party ? '' : 'display:none;' }}">
        <div class="sp-section-hdr">Unsettled Invoices</div>
        <div id="invoicesList">
            @foreach($unsettledOrders as $order)
            <label class="invoice-row {{ old('order_id')==$order->id ? 'selected' : '' }}">
                <input type="radio" name="order_id" value="{{ $order->id }}" {{ old('order_id')==$order->id ? 'checked' : '' }} onchange="highlightInvoice()">
                <div style="flex:1;">
                    <div style="font-weight:600;font-size:13.5px;">{{ $order->order_number ?? '#'.$order->id }}</div>
                    <div style="font-size:12px;color:var(--muted);">{{ $order->created_at?->format('d M Y') }}</div>
                </div>
                <div style="font-weight:700;color:var(--primary);">₹{{ number_format($order->total_amount ?? $order->amount ?? 0) }}</div>
            </label>
            @endforeach
            @if($unsettledOrders->isEmpty() && $party)
            <div class="empty-state" style="padding:20px;">
                <i class="bi bi-check-circle" style="color:#16A34A;"></i>
                <p>No unsettled invoices</p>
            </div>
            @endif
        </div>
    </div>

    <!-- PAYMENT DETAILS -->
    <div class="sp-section-hdr">Payment Details</div>
    <div class="card-sp">
        <div class="form-group-sp">
            <label>Amount (₹) *</label>
            <input type="number" name="amount" id="payAmount" step="0.01" min="0.01" placeholder="0.00" required value="{{ old('amount') }}">
        </div>

        <div class="form-group-sp" style="margin-bottom:0;">
            <label>Payment Type *</label>
            <div class="pay-type-grid">
                @foreach(['Cash'=>'bi-cash-stack','Cheque'=>'bi-file-earmark-text','Online'=>'bi-phone','Coupon'=>'bi-ticket-perforated'] as $type => $icon)
                <label class="pay-type-label {{ old('payment_type','Cash')===$type ? 'selected' : '' }}">
                    <input type="radio" name="payment_type" value="{{ $type }}"
                        {{ old('payment_type','Cash')===$type ? 'checked' : '' }}
                        onchange="highlightPayType()">
                    <i class="bi {{ $icon }}"></i>
                    <span>{{ $type }}</span>
                </label>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card-sp" style="margin-top:0;">
        <div class="form-group-sp" style="margin-bottom:0;">
            <label>Notes</label>
            <textarea name="notes" rows="3" placeholder="Optional notes...">{{ old('notes') }}</textarea>
        </div>
    </div>

    <div style="height:80px;"></div>
</form>

<div class="sp-sticky-save">
    <button type="submit" form="paymentForm" class="sp-save-btn">
        <i class="bi bi-check-lg me-2"></i>Save Payment
    </button>
</div>

@push('styles')
<style>
.invoice-row {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 16px; background: var(--card-bg);
    border: 1.5px solid var(--border); border-radius: var(--radius);
    margin-bottom: 8px; cursor: pointer; transition: all .15s;
}
.invoice-row:hover { border-color: var(--primary); background: var(--primary-light); }
.invoice-row.selected { border-color: var(--primary); background: var(--primary-light); }
.invoice-row input[type="radio"] { accent-color: var(--primary); width: 16px; height: 16px; flex-shrink: 0; }

.pay-type-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
}
.pay-type-label {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 14px; border-radius: var(--radius);
    border: 1.5px solid var(--border); cursor: pointer;
    transition: all .15s; background: var(--card-bg);
    font-size: 13.5px; font-weight: 600; color: var(--text);
}
.pay-type-label i { font-size: 18px; color: var(--muted); }
.pay-type-label input[type="radio"] { accent-color: var(--primary); width: 16px; height: 16px; flex-shrink: 0; }
.pay-type-label:hover { border-color: var(--primary); background: var(--primary-light); }
.pay-type-label:hover i { color: var(--primary); }
.pay-type-label.selected { border-color: var(--primary); background: var(--primary-light); }
.pay-type-label.selected i { color: var(--primary); }

@media (max-width: 480px) {
    .pay-type-grid { grid-template-columns: 1fr 1fr; }
}
</style>
@endpush

@endsection

@push('scripts')
<script>
function loadOrders(customerId) {
    const section = document.getElementById('invoicesSection');
    const list    = document.getElementById('invoicesList');
    if (!customerId) { section.style.display = 'none'; return; }
    section.style.display = '';
    list.innerHTML = '<div style="text-align:center;padding:20px;color:var(--muted);"><i class="bi bi-arrow-repeat"></i> Loading...</div>';
    fetch('{{ url("sale/panel/api/customer") }}/' + customerId + '/orders')
        .then(r => r.json())
        .then(orders => {
            if (!orders.length) {
                list.innerHTML = '<div class="empty-state" style="padding:20px;"><i class="bi bi-check-circle" style="color:#16A34A;font-size:32px;"></i><p>No unsettled invoices</p></div>';
                return;
            }
            list.innerHTML = orders.map(o => `
                <label class="invoice-row" onclick="this.classList.add('selected');document.querySelectorAll('.invoice-row').forEach(r=>r!==this&&r.classList.remove('selected'))">
                    <input type="radio" name="order_id" value="${o.id}" onchange="highlightInvoice()">
                    <div style="flex:1;">
                        <div style="font-weight:600;font-size:13.5px;">${o.order_number || '#'+o.id}</div>
                        <div style="font-size:12px;color:var(--muted);">${o.created_at ? o.created_at.substring(0,10) : ''}</div>
                    </div>
                    <div style="font-weight:700;color:var(--primary);">₹${parseFloat(o.total_amount||o.amount||0).toLocaleString('en-IN',{minimumFractionDigits:2})}</div>
                </label>
            `).join('');
        })
        .catch(() => {
            list.innerHTML = '<div class="alert-sp alert-error">Failed to load orders.</div>';
        });
}

function highlightPayType() {
    document.querySelectorAll('.pay-type-label').forEach(l => {
        const checked = l.querySelector('input').checked;
        l.classList.toggle('selected', checked);
    });
}

function highlightInvoice() {
    document.querySelectorAll('.invoice-row').forEach(l => {
        const checked = l.querySelector('input[type="radio"]');
        l.classList.toggle('selected', checked && checked.checked);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    highlightPayType();
    highlightInvoice();
});
</script>
@endpush
