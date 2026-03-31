@extends('delivery_panel.layout')
@section('page_title', 'Attendance')

@push('styles')
<style>
    .att-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr)); gap:12px; margin-bottom:20px; }
    .att-stat { background:#fff; border:1px solid var(--border); border-radius:var(--radius); padding:14px 16px; box-shadow:var(--shadow); }
    .att-stat-val { font-size:26px; font-weight:800; }
    .att-stat-lbl { font-size:12px; color:var(--muted); margin-top:2px; }

    .cal-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:6px; }
    .cal-day { border-radius:8px; padding:8px 4px; text-align:center; font-size:12px; font-weight:600; border:1px solid var(--border); }
    .cal-day.present  { background:#dcfce7; border-color:#86efac; color:#16a34a; }
    .cal-day.absent   { background:#fee2e2; border-color:#fca5a5; color:#dc2626; }
    .cal-day.late     { background:#fef3c7; border-color:#fde68a; color:#d97706; }
    .cal-day.leave    { background:#ede9fe; border-color:#c4b5fd; color:#7c3aed; }
    .cal-day.future   { background:#f8fafc; color:#cbd5e1; border-color:#f1f5f9; }
    .cal-day.today    { outline:2px solid var(--primary); outline-offset:1px; }
    .cal-day-num      { font-size:13px; font-weight:700; }
    .cal-day-del      { font-size:10px; margin-top:2px; }

    .month-nav { display:flex; align-items:center; gap:12px; }
    .month-nav a { color:var(--primary); text-decoration:none; font-weight:700; padding:4px 10px; border:1px solid var(--border); border-radius:6px; font-size:13px; }
    .month-nav a:hover { background:var(--primary-light); }
</style>
@endpush

@section('content')
@php
    $monthName = \Carbon\Carbon::create($year, $month)->format('F Y');
    $prevMonth = $month == 1 ? 12 : $month - 1;
    $prevYear  = $month == 1 ? $year - 1 : $year;
    $nextMonth = $month == 12 ? 1 : $month + 1;
    $nextYear  = $month == 12 ? $year + 1 : $year;
    $today     = now()->toDateString();
@endphp

{{-- SUMMARY STATS --}}
<div class="att-grid">
    <div class="att-stat">
        <div class="att-stat-val" style="color:#16a34a;">{{ $summary['present'] }}</div>
        <div class="att-stat-lbl">Present</div>
    </div>
    <div class="att-stat">
        <div class="att-stat-val" style="color:#dc2626;">{{ $summary['absent'] }}</div>
        <div class="att-stat-lbl">Absent</div>
    </div>
    <div class="att-stat">
        <div class="att-stat-val" style="color:#d97706;">{{ $summary['late'] }}</div>
        <div class="att-stat-lbl">Late</div>
    </div>
    <div class="att-stat">
        <div class="att-stat-val" style="color:#7c3aed;">{{ $summary['leave'] }}</div>
        <div class="att-stat-lbl">Leave</div>
    </div>
    <div class="att-stat">
        <div class="att-stat-val" style="color:var(--primary);">{{ $summary['completed_deliveries'] }}</div>
        <div class="att-stat-lbl">Deliveries Done</div>
    </div>
</div>

<div class="row g-3">
    {{-- CALENDAR --}}
    <div class="col-lg-8">
        <div class="dp-card">
            <div class="dp-card-title">
                <div class="month-nav">
                    <a href="{{ route('delivery.panel.attendance', ['month'=>$prevMonth,'year'=>$prevYear]) }}">‹</a>
                    <span style="font-size:15px;font-weight:700;">{{ $monthName }}</span>
                    <a href="{{ route('delivery.panel.attendance', ['month'=>$nextMonth,'year'=>$nextYear]) }}">›</a>
                </div>
                <span style="font-size:12px;color:var(--muted);">Today: {{ $todayStatus }}</span>
            </div>

            {{-- Day headers --}}
            <div class="cal-grid" style="margin-bottom:6px;">
                @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
                <div style="text-align:center;font-size:11px;font-weight:700;color:var(--muted);padding:4px;">{{ $d }}</div>
                @endforeach
            </div>

            {{-- Blank offset for first day of month --}}
            @php $firstDow = \Carbon\Carbon::create($year,$month,1)->dayOfWeek; @endphp
            <div class="cal-grid">
                @for($i=0;$i<$firstDow;$i++)
                <div></div>
                @endfor
                @foreach($calendarDays as $day)
                @php
                    $dateStr = $day['date']->toDateString();
                    $isFuture = $day['date']->gt(now());
                    $isToday  = $dateStr === $today;
                    $cls = $isFuture ? 'future' : strtolower($day['status']);
                @endphp
                <div class="cal-day {{ $cls }} {{ $isToday ? 'today' : '' }}">
                    <div class="cal-day-num">{{ $day['date']->day }}</div>
                    @if(!$isFuture)
                    <div class="cal-day-del">{{ $day['deliveries_count'] > 0 ? $day['deliveries_count'].'📦' : '' }}</div>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Legend --}}
            <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:14px;font-size:11px;font-weight:600;">
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:3px;background:#dcfce7;border:1px solid #86efac;margin-right:4px;"></span>Present</span>
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:3px;background:#fee2e2;border:1px solid #fca5a5;margin-right:4px;"></span>Absent</span>
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:3px;background:#fef3c7;border:1px solid #fde68a;margin-right:4px;"></span>Late</span>
                <span><span style="display:inline-block;width:10px;height:10px;border-radius:3px;background:#ede9fe;border:1px solid #c4b5fd;margin-right:4px;"></span>Leave</span>
            </div>
        </div>
    </div>

    {{-- CHECK IN/OUT --}}
    <div class="col-lg-4">
        <div class="dp-card" style="margin-bottom:14px;">
            <div class="dp-card-title">Today's Attendance</div>
            <div style="font-size:13px;color:var(--muted);margin-bottom:12px;">
                Status: <strong style="color:{{ $todayStatus==='Present'?'#16a34a':($todayStatus==='Late'?'#d97706':'#dc2626') }};">{{ $todayStatus }}</strong>
                @if($todayCompletedDeliveries > 0)
                · {{ $todayCompletedDeliveries }} deliveries
                @endif
            </div>

            {{-- Check In triggers modal --}}
            <button type="button" onclick="openAttModal('check_in')" class="dp-btn dp-btn-success" style="width:100%;justify-content:center;margin-bottom:8px;">
                <i class="fas fa-sign-in-alt"></i> Check In
            </button>

            {{-- Check Out triggers modal --}}
            <button type="button" onclick="openAttModal('check_out')" class="dp-btn dp-btn-ghost" style="width:100%;justify-content:center;margin-bottom:8px;">
                <i class="fas fa-sign-out-alt"></i> Check Out
            </button>

            <form method="POST" action="{{ route('delivery.panel.attendance.mark') }}">
                @csrf
                <input type="hidden" name="status" value="Leave">
                <input type="hidden" name="action_type" value="update">
                <button type="submit" class="dp-btn dp-btn-ghost" style="width:100%;justify-content:center;">
                    <i class="fas fa-umbrella-beach"></i> Mark Leave
                </button>
            </form>
        </div>

        {{-- Recent records --}}
        <div class="dp-card">
            <div class="dp-card-title">Recent Records</div>
            @forelse($records as $rec)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border);font-size:13px;">
                <div>
                    <div class="fw-bold">{{ \Carbon\Carbon::parse($rec->date)->format('d M') }}</div>
                    @if($rec->time_in)<div class="muted small">In: {{ $rec->time_in }}{{ $rec->time_out ? ' · Out: '.$rec->time_out : '' }}</div>@endif
                </div>
                <span class="dp-badge dp-badge-{{ $rec->status==='Present'?'delivered':($rec->status==='Late'?'dispatched':($rec->status==='Leave'?'assigned':'failed')) }}">
                    {{ $rec->status }}
                </span>
            </div>
            @empty
            <div class="muted small" style="text-align:center;padding:12px;">No records this month.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    #attModal { display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,.55); align-items:center; justify-content:center; }
    #attModal.open { display:flex; }
    #attModalBox { background:#fff; border-radius:16px; padding:24px; width:92%; max-width:400px; box-shadow:0 8px 32px rgba(0,0,0,.18); }
    #attModalBox h5 { font-size:16px; font-weight:700; margin-bottom:16px; }
    .att-step { margin-bottom:14px; }
    .att-step label { font-size:13px; font-weight:600; display:block; margin-bottom:6px; }
    #attLocStatus { font-size:12px; color:#6b7280; margin-top:4px; }
    #attMapLink { font-size:12px; color:#2563eb; display:none; margin-top:4px; }
    #attCameraWrap video, #attCameraWrap canvas { width:100%; border-radius:10px; border:1px solid #e5e7eb; }
    #attPhotoStatus { font-size:12px; color:#6b7280; margin-top:4px; }
</style>

{{-- Attendance Check-In/Out Modal --}}
<div id="attModal">
    <div id="attModalBox">
        <h5 id="attModalTitle">Check In</h5>

        <form id="attForm" method="POST" action="{{ route('delivery.panel.attendance.mark') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="status" value="Present">
            <input type="hidden" name="action_type" id="attActionType" value="check_in">
            <input type="hidden" name="latitude"  id="attLat">
            <input type="hidden" name="longitude" id="attLng">
            <input type="file"   name="attendance_image" id="attPhotoInput" accept="image/*" style="display:none;">

            {{-- Step 1: Location --}}
            <div class="att-step">
                <label><i class="fas fa-map-marker-alt text-danger me-1"></i> Your Location</label>
                <button type="button" id="attGetLocBtn" style="background:#f0fdf4;border:1px solid #86efac;color:#16a34a;border-radius:8px;padding:7px 14px;font-size:13px;font-weight:600;cursor:pointer;width:100%;">
                    <i class="fas fa-crosshairs me-1"></i> Capture My Location
                </button>
                <div id="attLocStatus">Not captured yet</div>
                <a id="attMapLink" href="#" target="_blank"><i class="fas fa-external-link-alt me-1"></i>View on Map</a>
            </div>

            {{-- Step 2: Live Photo --}}
            <div class="att-step">
                <label><i class="fas fa-camera text-primary me-1"></i> Live Photo</label>
                <div id="attCameraWrap">
                    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:8px;">
                        <button type="button" id="attOpenCamBtn" style="background:#eff6ff;border:1px solid #93c5fd;color:#2563eb;border-radius:8px;padding:7px 14px;font-size:13px;font-weight:600;cursor:pointer;">
                            <i class="fas fa-video me-1"></i> Open Camera
                        </button>
                        <button type="button" id="attSnapBtn" style="display:none;background:#f0fdf4;border:1px solid #86efac;color:#16a34a;border-radius:8px;padding:7px 14px;font-size:13px;font-weight:600;cursor:pointer;">
                            <i class="fas fa-camera me-1"></i> Take Photo
                        </button>
                        <button type="button" id="attRetakeBtn" style="display:none;background:#fef2f2;border:1px solid #fca5a5;color:#dc2626;border-radius:8px;padding:7px 14px;font-size:13px;font-weight:600;cursor:pointer;">
                            <i class="fas fa-redo me-1"></i> Retake
                        </button>
                    </div>
                    <video id="attVideo" autoplay playsinline style="display:none;"></video>
                    <canvas id="attCanvas" style="display:none;"></canvas>
                </div>
                <div id="attPhotoStatus">No photo taken</div>
            </div>

            <div style="display:flex;gap:10px;margin-top:4px;">
                <button type="submit" id="attSubmitBtn" style="flex:1;background:#16a34a;color:#fff;border:none;border-radius:10px;padding:11px;font-size:14px;font-weight:700;cursor:pointer;">
                    <i class="fas fa-check me-1"></i> Confirm
                </button>
                <button type="button" onclick="closeAttModal()" style="flex:1;background:#f1f5f9;color:#374151;border:none;border-radius:10px;padding:11px;font-size:14px;font-weight:600;cursor:pointer;">
                    Cancel
                </button>
            </div>
            <div id="attValidationMsg" style="display:none;margin-top:10px;background:#fef2f2;border:1px solid #fca5a5;color:#dc2626;border-radius:8px;padding:9px 12px;font-size:13px;font-weight:600;"></div>
        </form>
    </div>
</div>

<script>
let attCamStream = null;

function openAttModal(actionType) {
    document.getElementById('attActionType').value = actionType;
    document.getElementById('attModalTitle').textContent = actionType === 'check_in' ? 'Check In' : 'Check Out';
    document.getElementById('attModal').classList.add('open');
    // Reset state
    document.getElementById('attLat').value = '';
    document.getElementById('attLng').value = '';
    document.getElementById('attLocStatus').textContent = 'Not captured yet';
    document.getElementById('attLocStatus').style.color = '#6b7280';
    document.getElementById('attMapLink').style.display = 'none';
    document.getElementById('attPhotoInput').value = '';
    document.getElementById('attPhotoStatus').textContent = 'No photo taken';
    document.getElementById('attPhotoStatus').style.color = '#6b7280';
    document.getElementById('attVideo').style.display = 'none';
    document.getElementById('attCanvas').style.display = 'none';
    document.getElementById('attSnapBtn').style.display = 'none';
    document.getElementById('attRetakeBtn').style.display = 'none';
    document.getElementById('attOpenCamBtn').style.display = '';
    if (attCamStream) { attCamStream.getTracks().forEach(t => t.stop()); attCamStream = null; }
}

function closeAttModal() {
    document.getElementById('attModal').classList.remove('open');
    if (attCamStream) { attCamStream.getTracks().forEach(t => t.stop()); attCamStream = null; }
}

// GPS
document.getElementById('attGetLocBtn').addEventListener('click', function() {
    const status = document.getElementById('attLocStatus');
    if (!navigator.geolocation) { status.textContent = 'Geolocation not supported.'; return; }
    status.textContent = 'Fetching location...';
    navigator.geolocation.getCurrentPosition(function(pos) {
        const lat = pos.coords.latitude.toFixed(7);
        const lng = pos.coords.longitude.toFixed(7);
        document.getElementById('attLat').value = lat;
        document.getElementById('attLng').value = lng;
        status.textContent = '\u2705 ' + lat + ', ' + lng;
        status.style.color = '#16a34a';
        const link = document.getElementById('attMapLink');
        link.href = 'https://maps.google.com/?q=' + lat + ',' + lng;
        link.style.display = 'block';
    }, function() {
        status.textContent = 'Location access denied. Please allow and retry.';
        status.style.color = '#dc2626';
    });
});

// Camera
document.getElementById('attOpenCamBtn').addEventListener('click', function() {
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
        .then(function(stream) {
            attCamStream = stream;
            const video = document.getElementById('attVideo');
            video.srcObject = stream;
            video.style.display = 'block';
            document.getElementById('attSnapBtn').style.display = '';
            document.getElementById('attOpenCamBtn').style.display = 'none';
            document.getElementById('attCanvas').style.display = 'none';
            document.getElementById('attPhotoStatus').textContent = 'Camera ready. Take your photo.';
        })
        .catch(function() {
            document.getElementById('attPhotoStatus').textContent = 'Camera access denied.';
            document.getElementById('attPhotoStatus').style.color = '#dc2626';
        });
});

document.getElementById('attSnapBtn').addEventListener('click', function() {
    const video  = document.getElementById('attVideo');
    const canvas = document.getElementById('attCanvas');
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    canvas.style.display = 'block';
    video.style.display  = 'none';
    document.getElementById('attSnapBtn').style.display   = 'none';
    document.getElementById('attRetakeBtn').style.display = '';
    if (attCamStream) { attCamStream.getTracks().forEach(t => t.stop()); attCamStream = null; }
    canvas.toBlob(function(blob) {
        const file = new File([blob], 'attendance.jpg', { type: 'image/jpeg' });
        const dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('attPhotoInput').files = dt.files;
        document.getElementById('attPhotoStatus').textContent = '\u2705 Photo captured.';
        document.getElementById('attPhotoStatus').style.color = '#16a34a';
    }, 'image/jpeg', 0.85);
});

document.getElementById('attRetakeBtn').addEventListener('click', function() {
    document.getElementById('attCanvas').style.display = 'none';
    document.getElementById('attRetakeBtn').style.display = 'none';
    document.getElementById('attOpenCamBtn').style.display = '';
    document.getElementById('attPhotoInput').value = '';
    document.getElementById('attPhotoStatus').textContent = 'No photo taken';
    document.getElementById('attPhotoStatus').style.color = '#6b7280';
});

// Validate before submit: location + photo both required for check_in
document.getElementById('attForm').addEventListener('submit', function(e) {
    const actionType = document.getElementById('attActionType').value;
    if (actionType !== 'check_in') return; // check_out: no block

    const lat   = document.getElementById('attLat').value.trim();
    const photo = document.getElementById('attPhotoInput').files.length;
    const msg   = document.getElementById('attValidationMsg');
    const errors = [];

    if (!lat)   errors.push('📍 Location capture karna zaroori hai.');
    if (!photo) errors.push('📷 Live photo lena zaroori hai.');

    if (errors.length) {
        e.preventDefault();
        msg.innerHTML = errors.join('<br>');
        msg.style.display = 'block';
        return;
    }
    msg.style.display = 'none';
});
</script>
@endpush
