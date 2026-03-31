@extends('delivery_panel.layout')
@section('page_title', 'Earnings')

@php
    $month          = (int) ($month ?? now()->month);
    $year           = (int) ($year ?? now()->year);
    $baseSalary     = (float) ($baseSalary ?? 0);
    $allowances     = (float) ($allowances ?? 0);
    $monthlySalary  = (float) ($monthlySalary ?? 0);
    $incentives     = (float) ($incentives ?? 0);
    $deliveryCommission = (float) ($deliveryCommission ?? 0);
    $deliveredCount = (int) ($deliveredCount ?? 0);
    $deliveredAmount= (float) ($deliveredAmount ?? 0);
    $performanceData= $performanceData ?? collect();
    $payoutHistory  = $payoutHistory ?? collect();
    $maxDeliveries  = max(1, (int) collect($performanceData)->max('deliveries'));
@endphp

@push('styles')
<style>
    .earn-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:14px; margin-bottom:20px; }
    .earn-card { background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:18px; box-shadow:var(--shadow); }
    .earn-card-label { font-size:12px; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px; }
    .earn-card-value { font-size:26px; font-weight:800; color:var(--text); }
    .earn-card-sub { font-size:11px; color:var(--muted); margin-top:4px; }

    .perf-bar { margin-bottom:14px; }
    .perf-bar-top { display:flex; justify-content:space-between; font-size:13px; font-weight:600; margin-bottom:5px; }
    .perf-bar-track { height:8px; background:#f1f5f9; border-radius:99px; overflow:hidden; }
    .perf-bar-fill { height:100%; background:linear-gradient(90deg,#2563eb,#38bdf8); border-radius:99px; transition:width .4s ease; }

    .payout-table { width:100%; border-collapse:collapse; font-size:13px; }
    .payout-table th { padding:10px 12px; text-align:left; font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid var(--border); }
    .payout-table td { padding:11px 12px; border-bottom:1px solid var(--border); }
    .payout-table tr:last-child td { border-bottom:none; }
    .payout-table tr:hover td { background:#f8fafc; }
</style>
@endpush

@section('content')

{{-- MONTH FILTER --}}
<div class="dp-card" style="margin-bottom:20px;">
    <form method="GET" action="{{ route('delivery.panel.earnings') }}" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
        <div>
            <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;display:block;margin-bottom:5px;">Month</label>
            <select name="month" class="dp-form-group" style="padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;background:#fff;outline:none;min-width:130px;">
                @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ $month === $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create($year, $m, 1)->format('F') }}
                </option>
                @endfor
            </select>
        </div>
        <div>
            <label style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;display:block;margin-bottom:5px;">Year</label>
            <select name="year" style="padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;background:#fff;outline:none;min-width:100px;">
                @for($y = now()->year; $y >= now()->year - 4; $y--)
                <option value="{{ $y }}" {{ $year === $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="dp-btn dp-btn-primary" style="padding:9px 20px;">Apply</button>
    </form>
</div>

{{-- KPI CARDS --}}
<div class="earn-grid">
    <div class="earn-card">
        <div class="earn-card-label">Monthly Salary</div>
        <div class="earn-card-value">₹{{ number_format($monthlySalary, 2) }}</div>
        <div class="earn-card-sub">Base: ₹{{ number_format($baseSalary, 2) }} · Allowance: ₹{{ number_format($allowances, 2) }}</div>
    </div>
    <div class="earn-card">
        <div class="earn-card-label">Incentives</div>
        <div class="earn-card-value" style="color:#16a34a;">₹{{ number_format($incentives, 2) }}</div>
        <div class="earn-card-sub">Current month incentive</div>
    </div>
    <div class="earn-card">
        <div class="earn-card-label">Delivery Commission</div>
        <div class="earn-card-value" style="color:#2563eb;">₹{{ number_format($deliveryCommission, 2) }}</div>
        <div class="earn-card-sub">Current month commission</div>
    </div>
    <div class="earn-card">
        <div class="earn-card-label">Delivered Orders</div>
        <div class="earn-card-value">{{ $deliveredCount }}</div>
        <div class="earn-card-sub">Value: ₹{{ number_format($deliveredAmount, 2) }}</div>
    </div>
    <div class="earn-card">
        <div class="earn-card-label">Total Payout</div>
        <div class="earn-card-value" style="color:#d97706;">₹{{ number_format($monthlySalary + $incentives + $deliveryCommission, 2) }}</div>
        <div class="earn-card-sub">Salary + Incentive + Commission</div>
    </div>
</div>

<div class="row g-3">
    {{-- PERFORMANCE GRAPH --}}
    <div class="col-lg-6">
        <div class="dp-card" style="height:100%;">
            <div class="dp-card-title">Performance Graph <span style="font-size:11px;color:var(--muted);font-weight:500;">Last 6 months</span></div>
            @forelse($performanceData as $point)
            @php $width = round((($point['deliveries'] ?? 0) / $maxDeliveries) * 100); @endphp
            <div class="perf-bar">
                <div class="perf-bar-top">
                    <span>{{ $point['label'] ?? '-' }}</span>
                    <span style="color:var(--primary);">{{ $point['deliveries'] ?? 0 }} Deliveries · ₹{{ number_format($point['amount'] ?? 0) }}</span>
                </div>
                <div class="perf-bar-track">
                    <div class="perf-bar-fill" style="width:{{ $width }}%;"></div>
                </div>
            </div>
            @empty
            <div class="dp-empty"><i class="fas fa-chart-bar"></i><p>No performance data available.</p></div>
            @endforelse
        </div>
    </div>

    {{-- PAYOUT HISTORY --}}
    <div class="col-lg-6">
        <div class="dp-card" style="height:100%;">
            <div class="dp-card-title">Payout History</div>
            <div style="overflow-x:auto;">
                <table class="payout-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Salary</th>
                            <th>Incentive</th>
                            <th>Total Payout</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payoutHistory as $payout)
                        <tr>
                            <td>{{ \Carbon\Carbon::create($payout->year, $payout->month, 1)->format('M Y') }}</td>
                            <td>₹{{ number_format((float)$payout->base_salary + (float)$payout->allowances, 2) }}</td>
                            <td>₹{{ number_format((float)$payout->incentive, 2) }}</td>
                            <td><strong>₹{{ number_format((float)$payout->total_payout, 2) }}</strong></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align:center;padding:24px;color:var(--muted);">
                                <i class="fas fa-wallet" style="font-size:24px;opacity:.2;display:block;margin-bottom:6px;"></i>
                                No payout history available.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(is_object($payoutHistory) && method_exists($payoutHistory, 'links') && $payoutHistory->hasPages())
            <div style="padding:12px 0 0;">{{ $payoutHistory->links() }}</div>
            @endif
        </div>
    </div>
</div>

@endsection
