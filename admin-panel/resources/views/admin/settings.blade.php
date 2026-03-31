@extends('layouts.app')

@section('title', 'Admin Settings')
@section('page_title', 'Admin Settings')

@section('content')

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
  @csrf

  <div class="row g-4">

    {{-- Company Info --}}
    <div class="col-lg-8">
      <div class="card mb-4">
        <div class="card-header"><i class="bi bi-building me-2"></i>Company Information</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Company Name</label>
              <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $setting->company_name ?? '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">GST Number</label>
              <input type="text" name="gst_number" class="form-control" value="{{ old('gst_number', $setting->gst_number ?? '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Company Email</label>
              <input type="email" name="company_email" class="form-control" value="{{ old('company_email', $setting->company_email ?? '') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Company Phone</label>
              <input type="text" name="company_phone" class="form-control" value="{{ old('company_phone', $setting->company_phone ?? '') }}">
            </div>
            <div class="col-12">
              <label class="form-label">Company Address</label>
              <textarea name="company_address" class="form-control" rows="2">{{ old('company_address', $setting->company_address ?? '') }}</textarea>
            </div>
          </div>
        </div>
      </div>

      {{-- Localization --}}
      <div class="card mb-4">
        <div class="card-header"><i class="bi bi-globe me-2"></i>Localization</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Currency</label>
              <select name="currency" class="form-select">
                @foreach(['INR','USD','EUR','GBP','AED'] as $cur)
                  <option value="{{ $cur }}" {{ old('currency', $setting->currency ?? 'INR') === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Language</label>
              <select name="language" class="form-select">
                @foreach(['English','Hindi','Tamil','Telugu','Marathi'] as $lang)
                  <option value="{{ $lang }}" {{ old('language', $setting->language ?? 'English') === $lang ? 'selected' : '' }}>{{ $lang }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Timezone</label>
              <select name="timezone" class="form-select">
                @foreach(['Asia/Kolkata','UTC','America/New_York','Europe/London','Asia/Dubai'] as $tz)
                  <option value="{{ $tz }}" {{ old('timezone', $setting->timezone ?? 'Asia/Kolkata') === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
      </div>

      {{-- Preferences --}}
      <div class="card mb-4">
        <div class="card-header"><i class="bi bi-toggles me-2"></i>Preferences</div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="email_notifications" id="emailNotif" value="1"
                  {{ old('email_notifications', $setting->email_notifications ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="emailNotif">Email Notifications</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="dark_mode" id="darkMode" value="1"
                  {{ old('dark_mode', $setting->dark_mode ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="darkMode">Dark Mode</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintMode" value="1"
                  {{ old('maintenance_mode', $setting->maintenance_mode ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="maintMode">Maintenance Mode</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- Profile / Logo --}}
    <div class="col-lg-4">
      <div class="card mb-4">
        <div class="card-header"><i class="bi bi-person-circle me-2"></i>Admin Profile</div>
        <div class="card-body text-center">
          @php $img = $setting->profile_image ?? null; @endphp
          <img src="{{ $img ? asset('uploads/admin/'.$img) : 'https://ui-avatars.com/api/?name=Admin&background=2563EB&color=fff&size=100' }}"
               class="rounded-circle mb-3" width="90" height="90" style="object-fit:cover;border:3px solid var(--border);" id="avatarPreview">
          <div class="mb-3">
            <label class="form-label">Profile / Logo Image</label>
            <input type="file" name="profile_image" class="form-control" accept="image/*"
                   onchange="document.getElementById('avatarPreview').src=URL.createObjectURL(this.files[0])">
          </div>
          <div class="mb-3">
            <label class="form-label">Admin Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $setting->name ?? '') }}">
          </div>
          <div class="mb-3">
            <label class="form-label">Admin Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $setting->email ?? '') }}">
          </div>
          <div>
            <label class="form-label">Admin Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $setting->phone ?? '') }}">
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="d-flex gap-2 mb-4">
    <button type="submit" class="btn btn-primary text-white px-4"><i class="bi bi-save me-1"></i>Save Settings</button>
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary px-4">Cancel</a>
  </div>

</form>
@endsection
