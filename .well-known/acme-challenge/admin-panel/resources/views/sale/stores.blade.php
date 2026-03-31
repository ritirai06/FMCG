<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stores - {{ $companyName ?? 'Sales Panel' }}</title>
    <link href="{{ asset('sale_assets/vendor/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('sale_assets/css/style.css') }}" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #02022d 0%, #6C8EFF 100%); min-height: 100vh; }
        .container-main { margin-top: 20px; margin-bottom: 20px; }
        .card { background: rgba(255, 255, 255, 0.95); border: none; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
        .card-header { background: linear-gradient(135deg, #02022d 0%, #6C8EFF 100%); color: white; }
        .store-card { background: white; border-left: 4px solid #02022d; padding: 15px; margin-bottom: 15px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .store-card h5 { color: #02022d; margin-bottom: 5px; }
        .store-info { font-size: 13px; color: #666; margin: 3px 0; }
    </style>
</head>
<body>
    <div class="container-main">
        <div class="row mb-4">
            <div class="col-12">
                <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                    <h3 style="color: #02022d;">{{ $companyName ?? 'Sales' }} - Stores</h3>
                    <p style="color: #666; margin: 0;">Sales Person: {{ $salesPerson?->name ?? 'N/A' }} | Region: {{ $salesRegion ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Assigned Stores</h4>
                        <div>
                            <a href="/sale/store/create" class="btn btn-primary btn-sm me-2">+ Create Store</a>
                            <a href="/sale/dashboard" class="btn btn-light btn-sm">Back to Dashboard</a>
                        </div>
                    </div>
                    <div class="card-body">
                        @forelse($stores as $store)
                        <div class="store-card">
                            <div class="row">
                                <div class="col-md-3">
                                    <h5>{{ $store->store_name }}</h5>
                                    <div class="store-info"><strong>Code:</strong> {{ $store->code }}</div>
                                    <div class="store-info"><strong>Manager:</strong> {{ $store->manager ?? 'N/A' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="store-info"><strong>Address:</strong> {{ $store->address ?? 'N/A' }}</div>
                                    <div class="store-info"><strong>City:</strong> {{ $store->city ?? 'N/A' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="store-info"><strong>Phone:</strong> {{ $store->phone ?? 'N/A' }}</div>
                                    <div class="store-info"><strong>Email:</strong> {{ $store->email ?? 'N/A' }}</div>
                                </div>
                                <div class="col-md-3 text-end">
                                    <span class="badge bg-success p-2">Active</span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-info" role="alert">
                            No stores assigned yet.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('sale_assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>