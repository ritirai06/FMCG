@extends('delivery_panel.layout')

@section('page_title', 'Profile')

@php
    $zonesInput = collect($zones ?? [])->filter()->implode(', ');
@endphp

@section('content')
<div class="row g-3">
    <div class="col-xl-8">
        <div class="dp-card">
            <div class="dp-card-title">Profile</div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;">Name</label>
                    <div class="fw-bold mt-1">{{ $deliveryProfile?->name ?? $profileUser?->name ?? 'N/A' }}</div>
                </div>
                <div class="col-md-6">
                    <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;">Phone</label>
                    <div class="fw-bold mt-1">{{ $phone ?? 'N/A' }}</div>
                </div>
                <div class="col-12">
                    <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;">Assigned Zones</label>
                    <div class="mt-1">
                        @if(($zones ?? collect())->count())
                            @foreach($zones as $zone)
                                <span class="dp-badge dp-badge-assigned me-1 mb-1">{{ $zone }}</span>
                            @endforeach
                        @else
                            <span style="color:var(--muted);font-size:13px;">No assigned zones.</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="dp-btn dp-btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
                <button type="button" class="dp-btn dp-btn-ghost" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</button>
                <button type="button" class="dp-btn dp-btn-danger" data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">Logout</button>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="dp-card">
            <div class="dp-card-title">Delivery Stats</div>
            <table class="dp-table">
                <tr><td>Total Assigned</td><td class="fw-bold text-end">{{ (int) data_get($profileStats, 'total_assigned', 0) }}</td></tr>
                <tr><td>Delivered</td><td class="fw-bold text-end">{{ (int) data_get($profileStats, 'delivered', 0) }}</td></tr>
                <tr><td>Out For Delivery</td><td class="fw-bold text-end">{{ (int) data_get($profileStats, 'out_for_delivery', 0) }}</td></tr>
                <tr><td>Revenue Handled</td><td class="fw-bold text-end">&#8377;{{ number_format((float) data_get($profileStats, 'revenue_handled', 0), 2) }}</td></tr>
            </table>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('delivery.panel.profile.update') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="dp-form-group">
                        <label>Name</label>
                        <input type="text" name="name" value="{{ old('name', $deliveryProfile?->name ?? $profileUser?->name ?? '') }}" required>
                    </div>
                    <div class="dp-form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" maxlength="10" value="{{ old('phone', $phone ?? '') }}">
                    </div>
                    <div class="dp-form-group">
                        <label>Assigned Zones (comma separated)</label>
                        <input type="text" name="zones" value="{{ old('zones', $zonesInput) }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="dp-btn dp-btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="dp-btn dp-btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('delivery.panel.profile.change_password') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="dp-form-group">
                        <label>Current Password</label>
                        <div style="position:relative;">
                          <input type="password" id="cp_cur" name="current_password" style="padding-right:40px;" required>
                          <button type="button" onclick="dpToggle('cp_cur',this)" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:#94a3b8;"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                    <div class="dp-form-group">
                        <label>New Password</label>
                        <div style="position:relative;">
                          <input type="password" id="cp_new" name="password" style="padding-right:40px;" required>
                          <button type="button" onclick="dpToggle('cp_new',this)" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:#94a3b8;"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                    <div class="dp-form-group">
                        <label>Confirm New Password</label>
                        <div style="position:relative;">
                          <input type="password" id="cp_conf" name="password_confirmation" style="padding-right:40px;" required>
                          <button type="button" onclick="dpToggle('cp_conf',this)" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:#94a3b8;"><i class="bi bi-eye"></i></button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="dp-btn dp-btn-ghost" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="dp-btn dp-btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Logout Confirm Modal -->
<div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="font-size:13px;">Are you sure you want to logout?</div>
            <div class="modal-footer">
                <button type="button" class="dp-btn dp-btn-ghost" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('delivery.panel.logout') }}">
                    @csrf
                    <button type="submit" class="dp-btn dp-btn-danger">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function dpToggle(id, btn) {
  const input = document.getElementById(id);
  const icon  = btn.querySelector('i');
  input.type  = input.type === 'password' ? 'text' : 'password';
  icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
@endpush
