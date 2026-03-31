@extends('sale.panel.layout')
@section('title', 'Parties')
@section('topnav_actions')
    <a href="{{ route('sale.panel.party.create') }}"><i class="fas fa-plus"></i></a>
@endsection

@section('content')
<!-- SEARCH -->
<form method="GET" action="{{ route('sale.panel.parties') }}">
    <div class="sp-search">
        <i class="fas fa-search"></i>
        <input type="text" name="search" value="{{ $search }}" placeholder="Search party name or mobile...">
    </div>
    <!-- FILTER TABS -->
    <div class="sp-filter-tabs">
        <a href="{{ route('sale.panel.parties', ['filter'=>'all','search'=>$search]) }}"
           class="sp-filter-tab {{ $filter==='all' ? 'active' : '' }}">All</a>
        <a href="{{ route('sale.panel.parties', ['filter'=>'visited','search'=>$search]) }}"
           class="sp-filter-tab {{ $filter==='visited' ? 'active' : '' }}">Visited</a>
        <a href="{{ route('sale.panel.parties', ['filter'=>'pending','search'=>$search]) }}"
           class="sp-filter-tab {{ $filter==='pending' ? 'active' : '' }}">Pending</a>
    </div>
</form>

@forelse($parties as $party)
@php
    $initials = strtoupper(substr($party->business_name, 0, 2));
    $colors   = ['#6259ca','#22c55e','#3b82f6','#f59e0b','#ef4444','#8b5cf6','#db2777'];
    $color    = $colors[$party->id % count($colors)];
    $due      = max(0, (float)($party->credit_limit ?? 0));
@endphp
<a href="{{ route('sale.panel.party.show', $party->id) }}" class="sp-party-item">
    <div class="sp-party-avatar" style="background:{{ $color }}20; color:{{ $color }};">{{ $initials }}</div>
    <div style="flex:1; min-width:0;">
        <div class="sp-party-name">{{ $party->business_name }}</div>
        <div class="sp-party-meta">
            <i class="fas fa-phone" style="font-size:10px;"></i> {{ $party->mobile }}
            @if($party->route) · <i class="fas fa-route" style="font-size:10px;"></i> {{ $party->route }} @endif
        </div>
    </div>
    <div class="sp-due-badge">
        @if($due > 0)
            <div class="amount">₹{{ number_format($due, 0) }}</div>
            <div class="label">Credit Limit</div>
        @else
            <span class="sp-badge sp-badge-success">Active</span>
        @endif
    </div>
</a>
@empty
<div class="sp-empty">
    <i class="fas fa-users"></i>
    <p>No parties found.<br><a href="{{ route('sale.panel.party.create') }}" style="color:var(--sp-primary);">Add your first party</a></p>
</div>
@endforelse

{{ $parties->withQueryString()->links('vendor.pagination.simple-bootstrap-5') }}
@endsection
