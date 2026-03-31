@extends('layouts.app')

@section('title', 'Brand Management | FMCG Admin')

@push('styles')
<style>
/* TOPBAR */
.topbar{
  background:var(--panel);
  border-radius:var(--radius);
  padding:20px 28px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  box-shadow:0 10px 25px rgba(0,0,0,.05);
  margin-bottom:24px;
}
.topbar h5{
  font-weight:700;
  display:flex;align-items:center;gap:10px;
}

/* TABLE */
.table thead{background:rgba(99,102,241,0.08);}
.table tbody tr:hover{background:rgba(99,102,241,0.05);transition:.3s;}
.brand-logo{
  width:45px;height:45px;border-radius:50%;object-fit:cover;
  box-shadow:0 3px 6px rgba(0,0,0,0.15);
}
.status-dot{width:10px;height:10px;border-radius:50%;display:inline-block;margin-right:6px;}
.status-active{background:#22c55e;}
.status-inactive{background:#ef4444;}

/* MODALS */
.modal-content{
  border-radius:20px;
  background:rgba(255,255,255,0.98);
  backdrop-filter:blur(12px);
  box-shadow:0 15px 50px rgba(0,0,0,0.2);
  border:none;
}
.modal-dialog.small{max-width:400px;}
.confirm-icon{font-size:48px;display:flex;justify-content:center;margin-bottom:15px;}
.confirm-icon .bi-exclamation-triangle{color:#ef4444;text-shadow:0 0 15px rgba(239,68,68,0.6);}
.confirm-icon .bi-check-circle{color:#22c55e;text-shadow:0 0 15px rgba(34,197,94,0.6);}

/* FORM STYLE */
.logo-upload{
  text-align:center;border:2px dashed #cbd5e1;padding:15px;border-radius:12px;
  cursor:pointer;transition:all 0.3s ease;
}
.logo-upload:hover{background:rgba(99,102,241,0.05);border-color:var(--primary);}
.logo-upload img{
  width:70px;height:70px;border-radius:50%;object-fit:cover;
  box-shadow:0 2px 6px rgba(0,0,0,0.2);
}
</style>
@endpush

@section('content')
  <div class="topbar">
    <h5><i class="bi bi-award me-2 text-primary"></i>Brand Management</h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#brandModal">
      <i class="bi bi-plus-circle me-1"></i>Add Brand
    </button>
  </div>

  <!-- TABLE -->
  <div class="mt-4 bg-white rounded-4 shadow-sm p-4">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr><th>Brand</th><th>Description</th><th>Status</th><th class="text-end">Actions</th></tr>
        </thead>
        <tbody>
@foreach($brands as $brand)
<tr id="row-{{ $brand->id }}">

<td class="d-flex align-items-center gap-3">
    <img src="{{ $brand->logo ? asset('storage/'.$brand->logo) : 'https://via.placeholder.com/45' }}" 
         class="brand-logo">
    <div>
        <div class="fw-semibold">{{ $brand->name }}</div>
        <small class="text-muted">{{ $brand->description }}</small>
    </div>
</td>

<td>{{ $brand->description }}</td>

<td>
    <span class="status-dot {{ $brand->status=='Active' ? 'status-active':'status-inactive' }}"></span>
    {{ $brand->status }}
</td>

<td class="text-end">

    <button class="btn btn-light btn-sm editBtn"
        data-id="{{ $brand->id }}">
        <i class="bi bi-pencil text-primary"></i>
    </button>

    <button class="btn btn-light btn-sm deleteBtn"
        data-id="{{ $brand->id }}">
        <i class="bi bi-trash text-danger"></i>
    </button>

</td>

</tr>
@endforeach
</tbody>
      </table>
    </div>
  </div>

<!-- ADD/EDIT BRAND MODAL -->
<div class="modal fade" id="brandModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered small">
    <div class="modal-content p-3">
      <h5 class="fw-bold mb-3" id="modalTitle">Add Brand</h5>
      <form class="brand-form" enctype="multipart/form-data">
        @csrf
        <div class="logo-upload mb-3" id="logoUpload">
          <img id="logoPreview" src="https://via.placeholder.com/70" alt="Brand Logo">
          <div class="small text-muted mt-2">Click to upload logo</div>
          <input type="file" name="logo" id="logoInput" hidden accept="image/*">
        </div>
        <div class="mb-2">
          <label class="form-label small">Brand Name</label>
          <input type="text" name="name" class="form-control" id="brandName" placeholder="Enter brand name" required>
        </div>
        <div class="mb-2">
          <label class="form-label small">Description</label>
          <textarea name="description" class="form-control" id="brandDesc" rows="2" placeholder="Enter description"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label small">Status</label>
          <select name="status" class="form-select" id="brandStatus">
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
        <div class="text-end">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- CONFIRMATION MODAL -->
<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered small">
    <div class="modal-content text-center p-3">
      <div class="confirm-icon" id="confirmIcon"></div>
      <h5 class="fw-bold mb-2" id="confirmTitle">Deactivate Brand?</h5>
      <p class="text-muted small" id="confirmText">Are you sure you want to deactivate this brand?</p>
      <div class="d-flex justify-content-center gap-2 mt-3">
        <button class="btn btn-light px-3" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-danger px-3" id="confirmBtn">Confirm</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const brandModal = new bootstrap.Modal(document.getElementById('brandModal'));
const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));

let currentBrandId = null;

// Logo preview
document.getElementById('logoUpload').onclick = () => document.getElementById('logoInput').click();
document.getElementById('logoInput').onchange = e => {
  const file = e.target.files[0];
  if (file) document.getElementById('logoPreview').src = URL.createObjectURL(file);
};

// Reset modal on open for Add
document.getElementById('brandModal').addEventListener('show.bs.modal', function(){
  if (!currentBrandId) {
    document.getElementById('modalTitle').innerText = 'Add Brand';
    document.querySelector('.brand-form').reset();
    document.getElementById('logoPreview').src = 'https://via.placeholder.com/70';
  }
});

// ================= EDIT =================
document.querySelectorAll('.editBtn').forEach(btn=>{
    btn.addEventListener('click', function(){
        currentBrandId = this.dataset.id;
        fetch(`/brands/${currentBrandId}/edit`)
        .then(res=>res.json())
        .then(data=>{
            document.getElementById('modalTitle').innerText = 'Edit Brand';
            document.getElementById('brandName').value = data.name;
            document.getElementById('brandDesc').value = data.description || '';
            document.getElementById('brandStatus').value = data.status;
            if (data.logo) document.getElementById('logoPreview').src = '/storage/' + data.logo;
            brandModal.show();
        });
    });
});

// ================= SAVE (ADD / UPDATE) =================
document.querySelector('.brand-form').addEventListener('submit', function(e){
    e.preventDefault();
    const fd = new FormData(this);
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;

    const url = currentBrandId ? `/brands/update/${currentBrandId}` : "{{ route('brands.store') }}";

    fetch(url, {
        method: 'POST',
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success || data.id || !data.errors) {
            location.reload();
        } else {
            alert(Object.values(data.errors || {}).flat().join('\n') || 'Failed');
        }
    })
    .catch(() => alert('Server error'))
    .finally(() => { btn.disabled = false; currentBrandId = null; });
});

// ================= DELETE =================
document.querySelectorAll('.deleteBtn').forEach(btn=>{
    btn.addEventListener('click', function(){
        currentBrandId = this.dataset.id;
        confirmModal.show();
    });
});

document.getElementById('confirmBtn').addEventListener('click', function(){
    fetch(`/brands/${currentBrandId}`,{
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(res=>res.json())
    .then(()=>{
        document.getElementById(`row-${currentBrandId}`).remove();
        confirmModal.hide();
        currentBrandId = null;
    });
});
</script>
@endpush
