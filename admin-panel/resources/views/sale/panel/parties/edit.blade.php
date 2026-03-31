@extends('sale.panel.layout')
@section('title', 'Edit Party')
@section('back')1@endsection
@section('back_url', route('sale.panel.party.show', $party->id))

@section('content')

@if($errors->any())
<div class="alert-sp alert-error mb-3"><i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('sale.panel.party.update', $party->id) }}" id="editPartyForm">
    @csrf @method('PUT')

    <div class="sp-section-hdr">Business Info</div>
    <div class="card-sp">
        <div class="form-group-sp">
            <label>Business Name *</label>
            <input type="text" name="business_name" value="{{ old('business_name', $party->business_name) }}" placeholder="Shop / Business name" required>
        </div>
        <div class="form-group-sp">
            <label>Mobile *</label>
            <input type="tel" name="mobile" value="{{ old('mobile', $party->mobile) }}" placeholder="10-digit mobile" required>
        </div>
        <div class="form-group-sp">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $party->email) }}" placeholder="email@example.com">
        </div>
        <div class="form-group-sp">
            <label>GSTIN</label>
            <input type="text" name="gstin" value="{{ old('gstin', $party->gstin) }}" placeholder="GST number (optional)">
        </div>
        <div class="form-group-sp">
            <label>Contact Person</label>
            <input type="text" name="contact_person" value="{{ old('contact_person', $party->contact_person) }}" placeholder="Owner / Manager name">
        </div>
        <div class="form-group-sp">
            <label>Address</label>
            <textarea name="billing_address" rows="3" placeholder="Full address">{{ old('billing_address', $party->billing_address) }}</textarea>
        </div>
        <div class="form-group-sp" style="margin-bottom:0;">
            <label>Route</label>
            <input type="text" name="route" value="{{ old('route', $party->route) }}" placeholder="e.g. Route A, North Zone">
        </div>
    </div>

    <div class="sp-section-hdr">Credit Info</div>
    <div class="card-sp">
        <div class="form-group-sp">
            <label>Credit Limit (₹)</label>
            <input type="number" name="credit_limit" value="{{ old('credit_limit', $party->credit_limit) }}" min="0" step="0.01">
        </div>
        <div class="form-group-sp" style="margin-bottom:0;">
            <label>Credit Period (days)</label>
            <input type="number" name="credit_period" value="{{ old('credit_period', $party->credit_period) }}" min="0">
        </div>
    </div>

    <div style="height:80px;"></div>
</form>

<div class="sp-sticky-save">
    <button type="submit" form="editPartyForm" class="sp-save-btn">
        <i class="bi bi-floppy me-2"></i>Update Party
    </button>
</div>
@endsection
