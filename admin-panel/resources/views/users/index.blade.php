@extends('layouts.app')

@section('title', 'User Management')

@push('styles')
<style>
.um-kpis { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:18px; }
.um-kpi  { background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:16px 18px; box-shadow:var(--shadow); }
.um-kpi .lbl { font-size:11px; color:var(--muted); font-weight:700; text-transform:uppercase; letter-spacing:.05em; }
.um-kpi .val { font-size:26px; font-weight:800; margin-top:4px; line-height:1; }
.um-kpi .icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:18px; margin-bottom:10px; }
.icon-admin    { background:#eff6ff; color:#2563eb; }
.icon-sales    { background:#f0fdf4; color:#16a34a; }
.icon-delivery { background:#fef3c7; color:#b45309; }
.icon-all      { background:#f1f5f9; color:#475569; }

.um-toolbar { display:grid; grid-template-columns:1fr auto auto auto; gap:10px; margin-bottom:14px; align-items:center; }

.role-badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; }
.role-admin    { background:#eff6ff; color:#2563eb; }
.role-sales    { background:#f0fdf4; color:#16a34a; }
.role-delivery { background:#fef3c7; color:#b45309; }

.toast-wrap { position:fixed; top:20px; right:20px; z-index:9999; min-width:280px; }

@media(max-width:992px){ .um-kpis{grid-template-columns:repeat(2,1fr);} .um-toolbar{grid-template-columns:1fr;} }
</style>
@endpush

@section('content')

{{-- Toast notifications --}}
<div class="toast-wrap" id="toastWrap">
  @if(session('success'))
  <div class="alert alert-success d-flex align-items-center gap-2 shadow" role="alert">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
  </div>
  @endif
  @if(session('error'))
  <div class="alert alert-danger d-flex align-items-center gap-2 shadow" role="alert">
    <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
  </div>
  @endif
</div>

{{-- KPIs --}}
<div class="um-kpis">
  <div class="um-kpi">
    <div class="icon icon-all"><i class="bi bi-people-fill"></i></div>
    <div class="lbl">Total Users</div>
    <div class="val">{{ $counts['all'] }}</div>
  </div>
  <div class="um-kpi">
    <div class="icon icon-admin"><i class="bi bi-shield-fill"></i></div>
    <div class="lbl">Admins</div>
    <div class="val" style="color:#2563eb">{{ $counts['admin'] }}</div>
  </div>
  <div class="um-kpi">
    <div class="icon icon-sales"><i class="bi bi-person-badge-fill"></i></div>
    <div class="lbl">Sales</div>
    <div class="val" style="color:#16a34a">{{ $counts['sales'] }}</div>
  </div>
  <div class="um-kpi">
    <div class="icon icon-delivery"><i class="bi bi-truck-front-fill"></i></div>
    <div class="lbl">Delivery</div>
    <div class="val" style="color:#b45309">{{ $counts['delivery'] }}</div>
  </div>
</div>

{{-- Toolbar --}}
<form method="GET" action="{{ route('users.index') }}" class="um-toolbar">
  <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search name or email...">
  <select class="form-select" name="role" style="min-width:140px;">
    <option value="">All Roles</option>
    <option value="admin"    @selected(request('role')==='admin')>Admin</option>
    <option value="sales"    @selected(request('role')==='sales')>Sales</option>
    <option value="delivery" @selected(request('role')==='delivery')>Delivery</option>
  </select>
  <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Search</button>
  <button type="button" class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#addUserModal">
    <i class="bi bi-person-plus-fill"></i> Add User
  </button>
</form>

{{-- Table --}}
<div class="table-card">
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Created</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
      @forelse($users as $u)
        <tr>
          <td class="text-muted" style="font-size:12px;">{{ $u->id }}</td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div style="width:34px;height:34px;border-radius:50%;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">
                {{ strtoupper(substr($u->name, 0, 1)) }}
              </div>
              <span class="fw-semibold">{{ $u->name }}</span>
              @if($u->id === auth()->id())
                <span class="badge bg-secondary" style="font-size:10px;">You</span>
              @endif
            </div>
          </td>
          <td class="text-muted">{{ $u->email }}</td>
          <td>
            <span class="role-badge role-{{ $u->role }}">
              @if($u->role === 'admin') <i class="bi bi-shield-fill me-1"></i>
              @elseif($u->role === 'sales') <i class="bi bi-person-badge-fill me-1"></i>
              @else <i class="bi bi-truck-front-fill me-1"></i>
              @endif
              {{ ucfirst($u->role) }}
            </span>
          </td>
          <td class="text-muted" style="font-size:12px;">{{ $u->created_at->format('d M Y') }}</td>
          <td class="text-end">
            <button class="btn btn-sm btn-outline-primary"
              data-bs-toggle="modal" data-bs-target="#editModal{{ $u->id }}"
              title="Edit">
              <i class="bi bi-pencil"></i>
            </button>
            @if($u->id !== auth()->id())
            <form method="POST" action="{{ route('users.destroy', $u->id) }}" class="d-inline"
              onsubmit="return confirm('Delete {{ addslashes($u->name) }}? This cannot be undone.')">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
            </form>
            @endif
          </td>
        </tr>

        {{-- Edit Modal --}}
        <div class="modal fade" id="editModal{{ $u->id }}" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <form method="POST" action="{{ route('users.update', $u->id) }}">
                @csrf @method('PUT')
                <div class="modal-header">
                  <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit User</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" value="{{ $u->name }}" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="{{ $u->email }}" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select class="form-select" name="role" required>
                      <option value="admin"    @selected($u->role==='admin')>Admin</option>
                      <option value="sales"    @selected($u->role==='sales')>Sales</option>
                      <option value="delivery" @selected($u->role==='delivery')>Delivery</option>
                    </select>
                  </div>
                  <hr>
                  <p class="text-muted small mb-2">Leave password blank to keep unchanged.</p>
                  <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <div class="input-group">
                      <input type="password" class="form-control pw-field" name="password" placeholder="Min 6 characters">
                      <button type="button" class="btn btn-outline-secondary pw-toggle"><i class="bi bi-eye"></i></button>
                    </div>
                  </div>
                  <div class="mb-0">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-group">
                      <input type="password" class="form-control pw-field" name="password_confirmation" placeholder="Repeat password">
                      <button type="button" class="btn btn-outline-secondary pw-toggle"><i class="bi bi-eye"></i></button>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      @empty
        <tr><td colspan="6" class="text-center text-muted py-5">No users found.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-3">{{ $users->links() }}</div>
</div>

{{-- Add User Modal --}}
<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Add New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Enter full name" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="user@example.com" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select class="form-select" name="role" required>
              <option value="">Select role...</option>
              <option value="admin">Admin</option>
              <option value="sales">Sales</option>
              <option value="delivery">Delivery</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
              <input type="password" class="form-control pw-field" name="password" placeholder="Min 6 characters" required>
              <button type="button" class="btn btn-outline-secondary pw-toggle"><i class="bi bi-eye"></i></button>
            </div>
          </div>
          <div class="mb-0">
            <label class="form-label">Confirm Password</label>
            <div class="input-group">
              <input type="password" class="form-control pw-field" name="password_confirmation" placeholder="Repeat password" required>
              <button type="button" class="btn btn-outline-secondary pw-toggle"><i class="bi bi-eye"></i></button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-person-check-fill me-1"></i>Create User</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
// Auto-dismiss toast after 4s
setTimeout(() => {
  document.querySelectorAll('#toastWrap .alert').forEach(el => {
    el.style.transition = 'opacity .5s';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 500);
  });
}, 4000);

// Eye toggle for all password fields
document.addEventListener('click', function(e) {
  const btn = e.target.closest('.pw-toggle');
  if (!btn) return;
  const input = btn.closest('.input-group').querySelector('.pw-field');
  const icon  = btn.querySelector('i');
  input.type  = input.type === 'password' ? 'text' : 'password';
  icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
});

// Re-open add modal on validation error
@if($errors->any())
  document.addEventListener('DOMContentLoaded', () => {
    new bootstrap.Modal(document.getElementById('addUserModal')).show();
  });
@endif
</script>
@endpush
