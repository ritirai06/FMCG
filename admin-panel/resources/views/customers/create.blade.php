@extends('layouts.app')

@section('title', 'Create Customer')
@section('page_title', 'Create Customer')

@section('navbar_right')
  <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm">
    <i class="bi bi-arrow-left me-1"></i> Back to List
  </a>
@endsection

@section('content')
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

@include('customers._form')
@endsection
