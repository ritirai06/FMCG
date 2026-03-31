@extends('delivery_panel.layout')

@section('page_title', 'Stores')

@section('content')
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Store</th><th>Manager</th><th>Phone</th><th>Address</th><th>Navigate</th><th>Assigned</th><th>Delivered</th><th>Status</th></tr></thead>
            <tbody>
            @forelse($stores as $s)
                @php
                    $hasLoc = !empty($s->latitude) && !empty($s->longitude);
                    $mapsUrl = $hasLoc
                        ? 'https://www.google.com/maps?q=' . $s->latitude . ',' . $s->longitude
                        : null;
                @endphp
                <tr>
                    <td class="fw-semibold">{{ $s->store_name }}</td>
                    <td>{{ $s->manager ?? 'N/A' }}</td>
                    <td>{{ $s->phone ?? 'N/A' }}</td>
                    <td style="max-width:180px;font-size:12px;">{{ $s->address ?? 'N/A' }}</td>
                    <td>
                        @if($hasLoc)
                            <a href="{{ $mapsUrl }}" target="_blank" rel="noopener"
                               style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;background:#2563EB;color:#fff;border-radius:7px;font-size:12px;font-weight:700;text-decoration:none;white-space:nowrap;">
                                <i class="fas fa-map-marker-alt"></i> Navigate
                            </a>
                        @else
                            <span style="font-size:12px;color:#94A3B8;">Location N/A</span>
                        @endif
                    </td>
                    <td>{{ $s->assigned_orders_count }}</td>
                    <td>{{ $s->delivered_orders_count }}</td>
                    <td>{{ $s->status ? 'Active' : 'Inactive' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted">No stores linked to your orders.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
