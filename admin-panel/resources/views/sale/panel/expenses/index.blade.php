@extends('sale.panel.layout')
@section('title', 'Expenses')
@section('back')1@endsection
@section('back_url', route('sale.panel.dashboard'))

@section('content')

@if(session('success'))
<div class="alert-sp alert-success mb-3"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert-sp alert-error mb-3"><i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first() }}</div>
@endif

<!-- SUMMARY CARD -->
<div class="card-sp d-flex align-items-center gap-3 mb-3" style="padding:18px 20px;">
    <div style="width:46px;height:46px;border-radius:12px;background:#FDF4FF;color:#7C3AED;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;">
        <i class="bi bi-wallet2"></i>
    </div>
    <div>
        <div style="font-size:22px;font-weight:700;color:var(--text);">₹{{ number_format($totalThisMonth, 2) }}</div>
        <div style="font-size:12px;color:var(--muted);">This Month's Expenses</div>
    </div>
</div>

<!-- ADD EXPENSE FORM -->
<div class="sp-section-hdr">Add Expense</div>
<div class="card-sp">
    <form method="POST" action="{{ route('sale.panel.expense.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group-sp">
            <label>Category *</label>
            <select name="category" required>
                @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ old('category')===$cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group-sp">
            <label>Amount (₹) *</label>
            <input type="number" name="amount" step="0.01" min="0.01" placeholder="0.00" required value="{{ old('amount') }}">
        </div>
        <div class="form-group-sp">
            <label>Date</label>
            <input type="date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}">
        </div>
        <div class="form-group-sp">
            <label>Receipt / Photo</label>
            <div class="img-upload-box" id="expImgBox" onclick="document.getElementById('expImg').click()">
                <i class="bi bi-camera" style="font-size:24px;color:var(--muted);"></i>
                <span style="font-size:12px;color:var(--muted);margin-top:4px;">Tap to attach receipt</span>
                <img id="expImgPreview" src="" alt="" style="display:none;width:100%;height:100%;object-fit:cover;border-radius:10px;position:absolute;top:0;left:0;">
            </div>
            <input type="file" name="receipt_image" id="expImg" accept="image/*" capture="environment" style="display:none;" onchange="previewExpImg(this)">
        </div>
        <div class="form-group-sp" style="margin-bottom:14px;">
            <label>Notes</label>
            <textarea name="notes" rows="2" placeholder="Description...">{{ old('notes') }}</textarea>
        </div>
        <button type="submit" class="sp-save-btn">
            <i class="bi bi-plus-lg me-2"></i>Add Expense
        </button>
    </form>
</div>

<!-- EXPENSE HISTORY -->
<div class="sp-section-hdr" style="margin-top:8px;">Expense History</div>

@forelse($expenses as $expense)
@php
    $catColors = ['Travel'=>['#DBEAFE','#2563EB'],'Food'=>['#DCFCE7','#16A34A'],'Accommodation'=>['#FDF4FF','#7C3AED'],'Communication'=>['#FEF9C3','#CA8A04'],'Stationery'=>['#EDE9FE','#6D28D9'],'Other'=>['#F1F5F9','#64748B']];
    [$bg,$fg] = $catColors[$expense->category] ?? ['#F1F5F9','#64748B'];
    $catIcons = ['Travel'=>'bi-car-front-fill','Food'=>'bi-cup-hot-fill','Accommodation'=>'bi-house-fill','Communication'=>'bi-phone-fill','Stationery'=>'bi-pencil-fill','Other'=>'bi-three-dots'];
    $icon = $catIcons[$expense->category] ?? 'bi-receipt';
@endphp
<div class="sp-txn-item">
    <div class="sp-txn-icon" style="background:{{ $bg }};color:{{ $fg }};"><i class="bi {{ $icon }}"></i></div>
    <div style="flex:1;min-width:0;">
        <div class="sp-txn-title">{{ $expense->category }}</div>
        <div class="sp-txn-sub">
            {{ $expense->expense_date ?? $expense->created_at?->format('d M Y') }}
            @if($expense->notes) · {{ Str::limit($expense->notes, 35) }} @endif
        </div>
    </div>
    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;">
        <div class="sp-txn-amount" style="color:#DC2626;">-₹{{ number_format($expense->amount, 2) }}</div>
        @if(!empty($expense->receipt_image))
        <a href="{{ asset('storage/'.$expense->receipt_image) }}" target="_blank"
           style="font-size:11px;color:var(--primary);text-decoration:none;">
            <i class="bi bi-image"></i> Receipt
        </a>
        @endif
    </div>
</div>
@empty
<div class="empty-state">
    <i class="bi bi-wallet2"></i>
    <p>No expenses recorded</p>
</div>
@endforelse

{{ $expenses->links('vendor.pagination.simple-bootstrap-5') }}

@push('styles')
<style>
.img-upload-box {
    position: relative;
    width: 100%; height: 100px;
    border: 2px dashed var(--border);
    border-radius: 10px;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    cursor: pointer; background: var(--bg);
    transition: border-color .2s;
    overflow: hidden;
}
.img-upload-box:hover { border-color: var(--primary); }
</style>
@endpush

@endsection

@push('scripts')
<script>
function previewExpImg(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const preview = document.getElementById('expImgPreview');
        preview.src = e.target.result;
        preview.style.display = 'block';
        document.querySelector('#expImgBox i').style.display = 'none';
        document.querySelector('#expImgBox span').style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
@endpush
