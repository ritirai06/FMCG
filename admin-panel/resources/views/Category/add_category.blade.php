@extends('layouts.app')

@section('title', 'Add Category')

@push('styles')
<style>
/* Page-specific styles */
.assign-card {
  background: var(--panel, #fff);
  border-radius: 18px;
  box-shadow: 0 15px 40px rgba(0,0,0,.06);
  margin-top: 24px;
  padding: 30px;
}
.assign-card h6 { font-weight: 700; color: var(--primary); }
</style>
@endpush

@section('content')
  <div class="topbar">
    <h5><i class="bi bi-folder-plus me-2 text-primary"></i>Add Category</h5>
  </div>

  <div class="assign-card">
    <div class="row g-4">
      <!-- LEFT SIDE FORM -->
      <div class="col-lg-7">  
        @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
        @endif
        
        <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <h6 class="mb-3">Category Details</h6>

          <div class="mb-3">
            <label class="form-label">Category Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter category name" required>
          </div>
        
          <div class="mb-3">
            <label class="form-label">Category Description</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Write short description..."></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Upload Category Image</label>
            <input type="file" name="image" class="form-control">
          </div>

          <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" name="status" checked>
            <label class="form-check-label">Active Category</label>
          </div>

          <div class="text-end">
            <button type="reset" class="btn btn-light me-2">Cancel</button>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Category</button>
          </div>
        </form>
      </div>

      <!-- RIGHT SIDE INFO PANEL -->
      <div class="col-lg-5">
        <div class="card shadow-sm border-0 rounded-4 p-4">
          <h6 class="mb-3 text-primary"><i class="bi bi-info-circle me-2"></i>Category Overview</h6>
          <div class="mb-3">
            <small class="text-muted">Total Categories</small>
            <h3 class="fw-bold">{{ $total }}</h3>
          </div>
          <div class="mb-3">
            <small class="text-success">Active Categories</small>
            <h3 class="fw-bold text-success">{{ $active }}</h3>
          </div>
          <div>
            <small class="text-danger">Inactive Categories</small>
            <h3 class="fw-bold text-danger">{{ $inactive }}</h3>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
