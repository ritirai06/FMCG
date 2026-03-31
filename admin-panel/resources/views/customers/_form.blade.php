@php $editing = isset($customer); @endphp

<form action="{{ $editing ? route('customers.update', $customer) : route('customers.store') }}"
      method="POST" enctype="multipart/form-data">
    @csrf
    @if($editing) @method('PUT') @endif

    {{-- ── GENERAL DETAILS ── --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white fw-600">
            <i class="bi bi-person-lines-fill me-2"></i>General Details
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Business Name <span class="text-danger">*</span></label>
                    <input type="text" name="business_name" class="form-control @error('business_name') is-invalid @enderror"
                           value="{{ old('business_name', $customer->business_name ?? '') }}" required>
                    @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Code</label>
                    <input type="text" name="code" class="form-control"
                           value="{{ old('code', $customer->code ?? '') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="Active"  {{ old('status', $customer->status ?? 'Active') === 'Active'   ? 'selected' : '' }}>Active</option>
                        <option value="Inactive"{{ old('status', $customer->status ?? '') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Contact Person</label>
                    <input type="text" name="contact_person" class="form-control"
                           value="{{ old('contact_person', $customer->contact_person ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Mobile</label>
                    <input type="text" name="mobile" class="form-control @error('mobile') is-invalid @enderror"
                           value="{{ old('mobile', $customer->mobile ?? '') }}">
                    @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $customer->email ?? '') }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12">
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="verified" id="verified"
                               {{ old('verified', $customer->verified ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="verified">Verified Customer</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── OTHER DETAILS ── --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white fw-600">
            <i class="bi bi-info-circle me-2"></i>Other Details
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Route</label>
                    <select name="route" class="form-select">
                        <option value="">-- Select Route --</option>
                        @foreach(['Route A','Route B','Route C','Route D'] as $r)
                            <option value="{{ $r }}" {{ old('route', $customer->route ?? '') === $r ? 'selected' : '' }}>{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Group</label>
                    <input type="text" name="group_name" class="form-control"
                           value="{{ old('group_name', $customer->group_name ?? '') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Geolocation</label>
                    <div class="input-group">
                        <input type="text" name="geolocation" id="geolocationField" class="form-control" placeholder="lat,lng"
                               value="{{ old('geolocation', $customer->geolocation ?? '') }}">
                        <button class="btn btn-outline-primary" type="button" id="captureGeoBtn">
                            <i class="bi bi-geo-alt-fill me-1"></i>Get Location
                        </button>
                    </div>
                    <small id="geoStatus" class="text-muted d-block mt-1">Use current location to auto-fill coordinates.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Billing Address</label>
                    <textarea name="billing_address" class="form-control" rows="2">{{ old('billing_address', $customer->billing_address ?? '') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Shipping Address</label>
                    <textarea name="shipping_address" class="form-control" rows="2">{{ old('shipping_address', $customer->shipping_address ?? '') }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">GSTIN</label>
                    <input type="text" name="gstin" class="form-control @error('gstin') is-invalid @enderror"
                           value="{{ old('gstin', $customer->gstin ?? '') }}" maxlength="20">
                    @error('gstin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Opening Balance (₹)</label>
                    <input type="number" step="0.01" name="opening_balance" class="form-control"
                           value="{{ old('opening_balance', $customer->opening_balance ?? 0) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Credit Period (Days)</label>
                    <input type="number" name="credit_period" class="form-control"
                           value="{{ old('credit_period', $customer->credit_period ?? 0) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Credit Limit (₹)</label>
                    <input type="number" step="0.01" name="credit_limit" class="form-control"
                           value="{{ old('credit_limit', $customer->credit_limit ?? 0) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Credit Bill Limit (₹)</label>
                    <input type="number" step="0.01" name="credit_bill_limit" class="form-control"
                           value="{{ old('credit_bill_limit', $customer->credit_bill_limit ?? 0) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">State of Supply</label>
                    <select name="state_of_supply" class="form-select">
                        <option value="">-- Select State --</option>
                        @foreach(['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal','Delhi','Jammu & Kashmir','Ladakh'] as $state)
                            <option value="{{ $state }}" {{ old('state_of_supply', $customer->state_of_supply ?? '') === $state ? 'selected' : '' }}>{{ $state }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ── DOCUMENTS ── --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white fw-600">
            <i class="bi bi-file-earmark-arrow-up me-2"></i>Documents
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Upload Document (PDF / Image)</label>
                    <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    @if($editing && $customer->document_path)
                        <small class="text-muted mt-1 d-block">
                            Current: <a href="{{ asset('storage/'.$customer->document_path) }}" target="_blank">View Document</a>
                        </small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-gradient px-4">
            <i class="bi bi-save me-1"></i>{{ $editing ? 'Update Customer' : 'Save Customer' }}
        </button>
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('captureGeoBtn');
    const input = document.getElementById('geolocationField');
    const status = document.getElementById('geoStatus');
    if (!btn || !input || !status) return;

    btn.addEventListener('click', function () {
        if (!navigator.geolocation) {
            status.textContent = 'Geolocation is not supported in this browser.';
            status.className = 'text-danger d-block mt-1';
            return;
        }

        btn.disabled = true;
        status.textContent = 'Fetching your current location...';
        status.className = 'text-muted d-block mt-1';

        navigator.geolocation.getCurrentPosition(
            function (pos) {
                const lat = pos.coords.latitude.toFixed(6);
                const lng = pos.coords.longitude.toFixed(6);
                input.value = lat + ',' + lng;
                status.textContent = 'Location captured successfully.';
                status.className = 'text-success d-block mt-1';
                btn.disabled = false;
            },
            function () {
                status.textContent = 'Could not fetch location. Please allow location access.';
                status.className = 'text-danger d-block mt-1';
                btn.disabled = false;
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    });
});
</script>
