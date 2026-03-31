@extends('layouts.app')

@section('title', 'Add SubCategory')

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
    <h5><i class="bi bi-folder-plus me-2 text-primary"></i>Add SubCategory</h5>
  </div>

  <div class="assign-card">
    <div class="row g-4">
      <!-- LEFT SIDE INFO PANEL -->
      <div class="col-lg-5">
        <div class="card shadow-sm border-0 rounded-4 p-4">
          <h6 class="mb-3 text-primary"><i class="bi bi-info-circle me-2"></i>SubCategory Overview</h6>
          <div class="mb-3">
            <small class="text-muted">Total SubCategories</small>
            <h3 class="fw-bold">{{ $total ?? 0 }}</h3>
          </div>
          <div class="mb-3">
            <small class="text-success">Active</small>
            <h3 class="fw-bold text-success">{{ $active ?? 0 }}</h3>
          </div>
          <div>
            <small class="text-danger">Inactive</small>
            <h3 class="fw-bold text-danger">{{ $inactive ?? 0 }}</h3>
          </div>
        </div>
      </div>

      <!-- RIGHT SIDE FORM -->
      <div class="col-lg-7">  
        @if($errors->any())
          <div class="alert alert-danger">
            @foreach($errors->all() as $error)
              <div>{{ $error }}</div>
            @endforeach
          </div>
        @endif

        <form action="{{ route('subcategories.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <h6 class="mb-3">SubCategory Details</h6>

          <div class="mb-3">
            <label class="form-label">Select Category</label>
            <select name="category_id" class="form-control" required>
              <option value="">-- Select Category --</option>
              @if(isset($categories))
                @foreach($categories as $category)
                  <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
              @endif
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">SubCategory Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter subcategory name" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Write short description..."></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Upload Image</label>
            <input type="file" name="image" class="form-control">
          </div>

          <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" name="status" checked>
            <label class="form-check-label">Active</label>
          </div>

          <div class="text-end">
            <button type="reset" class="btn btn-light me-2">Cancel</button>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save SubCategory</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
