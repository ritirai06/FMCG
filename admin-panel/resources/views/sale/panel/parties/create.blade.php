@extends('sale.panel.layout')
@section('title', 'Add Party')
@section('back')1@endsection
@section('back_url', route('sale.panel.parties'))

@section('content')

@if($errors->any())
<div class="alert-sp alert-error mb-3"><i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('sale.panel.party.store') }}" id="partyForm">
    @csrf

    <!-- LOCATION -->
    <div class="sp-section-hdr">Location</div>
    <div style="border-radius:var(--radius);overflow:hidden;border:1px solid var(--border);height:160px;margin-bottom:10px;background:var(--bg);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;color:var(--muted);" id="mapContainer">
        <div id="mapPlaceholder" style="display:flex;flex-direction:column;align-items:center;gap:8px;">
            <i class="bi bi-geo-alt" style="font-size:32px;"></i>
            <span style="font-size:13px;">Tap below to get current location</span>
        </div>
        <iframe id="mapFrame" src="" style="display:none;width:100%;height:100%;border:none;"></iframe>
    </div>
    <button type="button" onclick="getLocation()" id="locBtn"
        style="width:100%;padding:11px;border-radius:10px;border:none;background:var(--primary);color:#fff;font-size:14px;font-weight:600;cursor:pointer;margin-bottom:6px;transition:background .2s;">
        <i class="bi bi-crosshair me-2"></i>Get Current Location
    </button>
    <div id="locationStatus" style="font-size:12px;color:var(--muted);margin-bottom:14px;text-align:center;min-height:18px;"></div>
    <input type="hidden" name="latitude"  id="latitude">
    <input type="hidden" name="longitude" id="longitude">

    <!-- BUSINESS INFO -->
    <div class="sp-section-hdr">Business Info</div>
    <div class="card-sp">
        <div class="form-group-sp">
            <label>Business Name *</label>
            <input type="text" name="business_name" value="{{ old('business_name') }}" placeholder="Shop / Business name" required>
        </div>
        <div class="form-group-sp">
            <label>Mobile *</label>
            <input type="tel" name="mobile" value="{{ old('mobile') }}" placeholder="10-digit mobile number" required>
        </div>
        <div class="form-group-sp">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="email@example.com">
        </div>
        <div class="form-group-sp">
            <label>GSTIN</label>
            <input type="text" name="gstin" value="{{ old('gstin') }}" placeholder="GST number (optional)">
        </div>
        <div class="form-group-sp">
            <label>Contact Person</label>
            <input type="text" name="contact_person" value="{{ old('contact_person') }}" placeholder="Owner / Manager name">
        </div>
        <div class="form-group-sp">
            <label>Address</label>
            <textarea name="billing_address" rows="3" placeholder="Full address">{{ old('billing_address') }}</textarea>
        </div>
        <div class="form-group-sp" style="margin-bottom:0;">
            <label>Route</label>
            <input type="text" name="route" value="{{ old('route') }}" placeholder="e.g. Route A, North Zone">
        </div>
    </div>

    <!-- CREDIT INFO -->
    <div class="sp-section-hdr">Credit Info</div>
    <div class="card-sp">
        <div class="form-group-sp">
            <label>Credit Limit (₹)</label>
            <input type="number" name="credit_limit" value="{{ old('credit_limit', 0) }}" min="0" step="0.01">
        </div>
        <div class="form-group-sp" style="margin-bottom:0;">
            <label>Credit Period (days)</label>
            <input type="number" name="credit_period" value="{{ old('credit_period', 0) }}" min="0">
        </div>
    </div>

    <div style="height:80px;"></div>
</form>

<div class="sp-sticky-save">
    <button type="submit" form="partyForm" class="sp-save-btn">
        <i class="bi bi-floppy me-2"></i>Save Party
    </button>
</div>

@endsection

@push('scripts')
<script>
function getLocation() {
    const status = document.getElementById('locationStatus');
    const btn    = document.getElementById('locBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i>Getting location...';
    status.textContent = '';

    if (!navigator.geolocation) {
        status.textContent = 'Geolocation not supported by your browser.';
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-crosshair me-2"></i>Get Current Location';
        return;
    }

    navigator.geolocation.getCurrentPosition(
        pos => {
            const lat = pos.coords.latitude.toFixed(6);
            const lng = pos.coords.longitude.toFixed(6);
            document.getElementById('latitude').value  = lat;
            document.getElementById('longitude').value = lng;
            status.innerHTML = '<i class="bi bi-check-circle-fill" style="color:#16A34A;"></i> Location captured: ' + lat + ', ' + lng;

            const frame       = document.getElementById('mapFrame');
            const placeholder = document.getElementById('mapPlaceholder');
            frame.src         = 'https://maps.google.com/maps?q=' + lat + ',' + lng + '&z=15&output=embed';
            frame.style.display      = 'block';
            placeholder.style.display = 'none';

            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Location Captured';
            btn.style.background = '#059669';
        },
        err => {
            status.textContent = 'Error: ' + err.message;
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-crosshair me-2"></i>Get Current Location';
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
}
</script>
@endpush
