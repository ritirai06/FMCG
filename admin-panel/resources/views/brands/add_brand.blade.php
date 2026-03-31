@extends('layouts.app')

@section('title', 'Add Brand')

@push('styles')
<style>
/* Page-specific styles */
.brand-card {
  background: var(--panel, #fff);
  border-radius: 18px;
  padding: 30px;
  max-width: 650px;
  margin: 40px auto;
  box-shadow: 0 15px 40px rgba(0,0,0,0.1);
}
.logo-upload {
  border: 2px dashed #cbd5e1;
  border-radius: 12px;
  text-align: center;
  padding: 25px;
  cursor: pointer;
  transition: .3s;
}
.logo-upload:hover { border-color: var(--primary); background: rgba(99,102,241,0.05); }
.logo-upload img { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; box-shadow: 0 2px 6px rgba(0,0,0,0.15); }
</style>
@endpush

@section('content')
  <div class="topbar">
    <h5><i class="bi bi-plus-circle me-2 text-primary"></i>Add Brand</h5>
    <button class="btn btn-light btn-sm" onclick="window.location.href='{{ route('brands.index') }}'">
      <i class="bi bi-arrow-left"></i> Back to List
    </button>
  </div>

  <div class="brand-card">
    <h5 class="mb-4"><i class="bi bi-shop-window me-2 text-primary"></i>Brand Details</h5>
    <form action="{{ route('brands.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="logo-upload mb-4" onclick="document.getElementById('logoInput').click()">
        <img id="logoPreview" src="https://via.placeholder.com/80" alt="Brand Logo">
        <div class="mt-2 small text-muted">Click to upload brand logo</div>
        <input type="file" name="logo" id="logoInput" hidden accept="image/*" onchange="previewImage(this)">
      </div>

      <div class="mb-3">
        <label class="form-label">Brand Name</label>
        <input type="text" name="name" class="form-control" placeholder="Enter brand name" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3" placeholder="Short description..."></textarea>
      </div>

      <div class="mb-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>
      </div>

      <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-light" onclick="history.back()">Cancel</button>
        <button type="submit" class="btn btn-primary px-4">Save Brand</button>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
<script>
function previewImage(input) {
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => document.getElementById('logoPreview').src = e.target.result;
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
@endpush
