@extends('sale.panel.layout')
@section('title', 'Attendance')

@section('content')
@php
    $isCheckedIn  = $todayRecord && !empty($todayRecord->time_in);
    $isCheckedOut = $todayRecord && !empty($todayRecord->time_out);
    $present      = $records->where('status','Present')->count();
    $absent       = $records->where('status','Absent')->count();
    $late         = $records->where('status','Late')->count();

    // Format time safely — stored as H:i:s string
    $fmtTime = fn($t) => $t ? \Carbon\Carbon::createFromFormat('H:i:s', $t)->format('h:i A') : null;
@endphp

{{-- Suppress layout's generic session alerts on this page --}}
@php session()->forget(['success','error']); @endphp

<!-- ══════════════ NOTIFICATION TOAST ══════════════ -->
@php
    $flashSuccess = session('att_success');
    $flashError   = session('att_error');
@endphp
@if($flashSuccess || $flashError)
<div id="attToast" style="
    position:fixed;top:72px;left:50%;transform:translateX(-50%);
    z-index:9999;min-width:280px;max-width:90vw;
    background:{{ $flashSuccess ? '#DCFCE7' : '#FEE2E2' }};
    color:{{ $flashSuccess ? '#15803D' : '#DC2626' }};
    border:1.5px solid {{ $flashSuccess ? '#86EFAC' : '#FCA5A5' }};
    border-radius:12px;padding:14px 20px;
    display:flex;align-items:center;gap:10px;
    box-shadow:0 4px 20px rgba(0,0,0,.12);font-weight:600;font-size:14px;">
    <i class="bi {{ $flashSuccess ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' }}" style="font-size:18px;flex-shrink:0;"></i>
    <span>{{ $flashSuccess ?? $flashError }}</span>
    <button onclick="document.getElementById('attToast').remove()" style="margin-left:auto;background:none;border:none;font-size:18px;cursor:pointer;color:inherit;line-height:1;">×</button>
</div>
<script>setTimeout(()=>{const t=document.getElementById('attToast');if(t)t.remove();},4000);</script>
@endif

<!-- ══════════════ HERO CARD ══════════════ -->
<div class="att-hero">
    <!-- Live clock -->
    <div class="att-clock" id="liveClock">--:--:--</div>
    <div class="att-date">{{ now()->format('l, d F Y') }}</div>

    <!-- Status pills -->
    <div class="att-pills">
        @if($todayRecord)
            @if($todayRecord->time_in)
            <span class="att-pill att-pill-in"><i class="bi bi-box-arrow-in-right"></i> IN {{ $fmtTime($todayRecord->time_in) }}</span>
            @endif
            @if($todayRecord->time_out)
            <span class="att-pill att-pill-out"><i class="bi bi-box-arrow-right"></i> OUT {{ $fmtTime($todayRecord->time_out) }}</span>
            @endif
        @else
            <span class="att-pill att-pill-pending"><i class="bi bi-clock"></i> Not marked today</span>
        @endif
    </div>

    <!-- Today's saved photos -->
    @if($todayRecord && ($todayRecord->image_in || $todayRecord->image_out))
    <div class="att-saved-photos">
        @if($todayRecord->image_in)
        <div class="att-saved-photo">
            <img src="{{ asset('storage/'.$todayRecord->image_in) }}" alt="IN" onclick="openPhoto(this.src)">
            <span class="att-photo-label att-photo-label-in">IN</span>
        </div>
        @endif
        @if($todayRecord->image_out)
        <div class="att-saved-photo">
            <img src="{{ asset('storage/'.$todayRecord->image_out) }}" alt="OUT" onclick="openPhoto(this.src)">
            <span class="att-photo-label att-photo-label-out">OUT</span>
        </div>
        @endif
    </div>
    @endif
</div>

<!-- ══════════════ CAPTURE & ACTION CARD ══════════════ -->
<div class="att-action-card">

    @if($isCheckedOut)
    <div class="att-complete-banner">
        <i class="bi bi-check-circle-fill"></i> Attendance complete!
        <small>You can still re-mark or update below.</small>
    </div>
    @endif

    <!-- Photo preview (shown after live camera capture) -->
    <div class="att-capture-section" id="photoPreviewSection" style="display:none;">
        <div class="att-capture-label" id="captureActionLabel">Photo Captured</div>
        <div class="att-selfie-ring has-img" id="attendImgBox">
            <img id="attendImgPreview" src="" alt="" class="att-selfie-preview" style="display:block;">
        </div>
        <div style="text-align:center; margin-top:8px; color:var(--muted,#64748B); font-size:12px;">
            <i class="bi bi-check-circle-fill" style="color:#22C55E;"></i> Submitting attendance...
        </div>
    </div>

    <!-- ── LOCATION MAP ── -->
    <div style="margin-bottom:14px;">
        <div style="font-size:11px;font-weight:700;color:var(--muted,#64748B);text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px;display:flex;align-items:center;gap:6px;">
            <i class="bi bi-geo-alt-fill" style="color:var(--primary,#2563EB);"></i> Location
        </div>
        <div id="attendMap" style="width:100%;height:200px;border-radius:12px;overflow:hidden;border:1px solid var(--border,#E2E8F0);background:#e8f0fe;position:relative;">
            <!-- Placeholder shown before location is captured -->
            <div id="mapPlaceholder" style="width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:var(--muted,#64748B);">
                <i class="bi bi-geo-alt" style="font-size:28px;color:#94A3B8;"></i>
                <span style="font-size:12px;font-weight:500;">Location will appear after marking</span>
            </div>
            <!-- Google Map renders here -->
            <div id="googleMap" style="width:100%;height:100%;display:none;"></div>
            <!-- Address label overlay -->
            <div id="mapAddressBar" style="display:none;position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,.55);color:#fff;font-size:11px;padding:6px 10px;backdrop-filter:blur(4px);"></div>
        </div>
        @if($todayRecord && $todayRecord->latitude && $todayRecord->longitude)
        <div style="font-size:11px;color:var(--muted,#64748B);margin-top:5px;display:flex;align-items:center;gap:4px;">
            <i class="bi bi-pin-map-fill" style="color:#16A34A;"></i>
            Last marked: {{ $todayRecord->latitude }}, {{ $todayRecord->longitude }}
        </div>
        @endif
    </div>

    <!-- ── COMMENTS ── -->
    <div class="att-comments">
        <textarea id="attendComments" rows="2" placeholder="Optional comments..."></textarea>
    </div>

    <!-- ══ CIRCLE CHECK-IN / CHECK-OUT BUTTONS ══ -->
    <div class="att-circle-btns">
        <div class="att-circle-wrap">
            <button type="button" id="btnCheckIn" onclick="markAttendance('in')" class="att-circle-btn att-circle-in">
                <div class="att-circle-icon"><i class="bi bi-box-arrow-in-right"></i></div>
            </button>
            <span class="att-circle-label">Check IN</span>
        </div>
        <div class="att-circle-wrap">
            <button type="button" id="btnCheckOut" onclick="markAttendance('out')" class="att-circle-btn att-circle-out">
                <div class="att-circle-icon"><i class="bi bi-box-arrow-right"></i></div>
            </button>
            <span class="att-circle-label">Check OUT</span>
        </div>
    </div>

    <!-- Hidden form -->
    <form method="POST" action="{{ route('sale.panel.attendance.mark') }}" id="attendForm" enctype="multipart/form-data" style="display:none;">
        @csrf
        <input type="hidden" name="action"    id="attendAction">
        <input type="hidden" name="latitude"  id="attendLat">
        <input type="hidden" name="longitude" id="attendLng">
        <input type="hidden" name="comments"  id="attendCommentsHidden">
        <input type="file"   name="attendance_image" id="attendImgFinal" style="display:none;">
    </form>
</div>

<!-- ══════════════ MONTHLY SUMMARY ══════════════ -->
<div class="att-summary-grid">
    <div class="att-summary-box att-summary-present">
        <div class="att-summary-num">{{ $present }}</div>
        <div class="att-summary-label">Present</div>
    </div>
    <div class="att-summary-box att-summary-absent">
        <div class="att-summary-num">{{ $absent }}</div>
        <div class="att-summary-label">Absent</div>
    </div>
    <div class="att-summary-box att-summary-late">
        <div class="att-summary-num">{{ $late }}</div>
        <div class="att-summary-label">Late</div>
    </div>
</div>

<!-- ══════════════ HISTORY ══════════════ -->
<div class="sp-section-hdr">History</div>

@forelse($records as $rec)
@php
    $statusMap = ['Present'=>['#DCFCE7','#16A34A'],'Absent'=>['#FEE2E2','#DC2626'],'Late'=>['#FEF9C3','#CA8A04'],'Leave'=>['#DBEAFE','#2563EB']];
    [$bg,$fg] = $statusMap[$rec->status] ?? ['#F1F5F9','#64748B'];
@endphp
<div class="sp-txn-item" style="align-items:flex-start;">
    <div class="sp-txn-icon" style="background:{{ $bg }};color:{{ $fg }};margin-top:2px;">
        <i class="bi bi-calendar-day"></i>
    </div>
    <div style="flex:1;min-width:0;">
        <div class="sp-txn-title">{{ \Carbon\Carbon::parse($rec->date)->format('d M Y') }}</div>
        <div class="sp-txn-sub">
            @if($rec->time_in)<span style="color:#16A34A;font-weight:600;">IN: {{ $fmtTime($rec->time_in) }}</span>@endif
            @if($rec->time_out) &nbsp;·&nbsp; <span style="color:#DC2626;font-weight:600;">OUT: {{ $fmtTime($rec->time_out) }}</span>@endif
        </div>
        @if($rec->image_in || $rec->image_out)
        <div style="display:flex;gap:8px;margin-top:8px;">
            @if($rec->image_in)
            <div style="text-align:center;">
                <img src="{{ asset('storage/'.$rec->image_in) }}" alt="IN" onclick="openPhoto(this.src)"
                     style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid #DCFCE7;cursor:pointer;">
                <div style="font-size:9px;color:#16A34A;margin-top:2px;font-weight:600;">IN</div>
            </div>
            @endif
            @if($rec->image_out)
            <div style="text-align:center;">
                <img src="{{ asset('storage/'.$rec->image_out) }}" alt="OUT" onclick="openPhoto(this.src)"
                     style="width:48px;height:48px;border-radius:50%;object-fit:cover;border:2px solid #FEE2E2;cursor:pointer;">
                <div style="font-size:9px;color:#DC2626;margin-top:2px;font-weight:600;">OUT</div>
            </div>
            @endif
        </div>
        @endif
    </div>
    <span class="sp-badge" style="background:{{ $bg }};color:{{ $fg }};flex-shrink:0;margin-top:2px;">{{ $rec->status }}</span>
</div>
@empty
<div class="empty-state"><i class="bi bi-calendar-x"></i><p>No attendance records</p></div>
@endforelse

{{ $records->links('vendor.pagination.simple-bootstrap-5') }}

<!-- LIGHTBOX -->
<div id="photoLightbox" onclick="closeLightbox()"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.88);z-index:9999;align-items:center;justify-content:center;">
    <img id="lightboxImg" src="" alt="" style="max-width:92vw;max-height:88vh;border-radius:12px;object-fit:contain;">
    <button onclick="closeLightbox()"
            style="position:absolute;top:16px;right:16px;background:rgba(255,255,255,.15);border:none;color:#fff;width:36px;height:36px;border-radius:50%;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
        <i class="bi bi-x-lg"></i>
    </button>
</div>

@push('styles')
<style>
/* ═══ HERO ═══ */
.att-hero {
    background: linear-gradient(135deg, #1E293B 0%, #334155 100%);
    border-radius: 16px; padding: 28px 20px 22px; text-align: center;
    color: #fff; margin-bottom: 14px; position: relative; overflow: hidden;
}
.att-hero::before {
    content:''; position:absolute; top:-60px; right:-60px;
    width:180px; height:180px; border-radius:50%;
    background:rgba(99,102,241,.15);
}
.att-clock {
    font-size: 38px; font-weight: 800; letter-spacing: 2px;
    font-variant-numeric: tabular-nums; line-height: 1;
}
.att-date { font-size: 13px; opacity: .7; margin-top: 4px; margin-bottom: 14px; }
.att-pills { display: flex; gap: 8px; justify-content: center; flex-wrap: wrap; }
.att-pill {
    font-size: 12px; font-weight: 600; padding: 4px 12px;
    border-radius: 20px; display: inline-flex; align-items: center; gap: 5px;
}
.att-pill-in      { background: rgba(34,197,94,.2);  color: #4ADE80; }
.att-pill-out     { background: rgba(239,68,68,.2);  color: #FCA5A5; }
.att-pill-pending { background: rgba(234,179,8,.15); color: #FDE047; }

.att-saved-photos { display: flex; gap: 14px; justify-content: center; margin-top: 16px; }
.att-saved-photo { position: relative; }
.att-saved-photo img {
    width: 56px; height: 56px; border-radius: 50%; object-fit: cover;
    border: 3px solid rgba(255,255,255,.25); cursor: pointer;
    transition: transform .2s;
}
.att-saved-photo img:hover { transform: scale(1.1); }
.att-photo-label {
    position: absolute; bottom: -4px; left: 50%; transform: translateX(-50%);
    font-size: 9px; font-weight: 700; padding: 1px 8px; border-radius: 10px;
    text-transform: uppercase; letter-spacing: .5px;
}
.att-photo-label-in  { background: #16A34A; color: #fff; }
.att-photo-label-out { background: #DC2626; color: #fff; }

/* ═══ ACTION CARD ═══ */
.att-action-card {
    background: var(--card-bg, #fff); border: 1px solid var(--border, #E2E8F0);
    border-radius: 16px; padding: 20px; margin-bottom: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,.04);
}
.att-complete-banner {
    background: linear-gradient(135deg, #DCFCE7, #BBF7D0); color: #15803D;
    border-radius: 10px; padding: 12px 16px; font-weight: 600; font-size: 14px;
    margin-bottom: 16px; text-align: center;
}
.att-complete-banner small { display: block; font-size: 11px; font-weight: 400; margin-top: 2px; opacity: .7; }

/* ═══ SELFIE CAPTURE ═══ */
.att-capture-section { text-align: center; margin-bottom: 16px; }
.att-capture-label {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: .5px; color: var(--muted, #64748B); margin-bottom: 12px;
}

/* Circular selfie ring */
.att-selfie-ring {
    width: 120px; height: 120px; border-radius: 50%; margin: 0 auto 14px;
    background: linear-gradient(135deg, #6366F1, #8B5CF6);
    padding: 4px; cursor: pointer; position: relative;
    box-shadow: 0 6px 24px rgba(99,102,241,.3);
    transition: transform .2s, box-shadow .2s;
}
.att-selfie-ring.has-img { background: linear-gradient(135deg, #22C55E, #16A34A); }
.att-selfie-preview {
    display: none; width: 100%; height: 100%; border-radius: 50%;
    object-fit: cover; position: absolute; top: 0; left: 0;
    border: 4px solid transparent;
}

/* ═══ LIVE CAMERA OVERLAY ═══ */
.cam-overlay {
    display: none; position: fixed; inset: 0; z-index: 99999;
    background: #000; flex-direction: column;
}
.cam-overlay.active { display: flex; }
.cam-header {
    position: absolute; top: 0; left: 0; right: 0; z-index: 2;
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px;
    background: linear-gradient(to bottom, rgba(0,0,0,.7), transparent);
}
.cam-header-title {
    color: #fff; font-size: 16px; font-weight: 700;
    display: flex; align-items: center; gap: 8px;
}
.cam-header-badge {
    font-size: 10px; font-weight: 700; padding: 3px 10px;
    border-radius: 20px; text-transform: uppercase; letter-spacing: .5px;
}
.cam-header-badge.in  { background: #22C55E; color: #fff; }
.cam-header-badge.out { background: #EF4444; color: #fff; }
.cam-close-btn {
    width: 40px; height: 40px; border-radius: 50%; border: none;
    background: rgba(255,255,255,.15); color: #fff; font-size: 18px;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    backdrop-filter: blur(8px); transition: background .2s;
}
.cam-close-btn:hover { background: rgba(255,255,255,.3); }
.cam-video-wrap {
    flex: 1; display: flex; align-items: center; justify-content: center;
    overflow: hidden; position: relative;
}
.cam-video-wrap video {
    width: 100%; height: 100%; object-fit: cover;
}
.cam-video-wrap canvas { display: none; }
.cam-controls {
    position: absolute; bottom: 0; left: 0; right: 0; z-index: 2;
    display: flex; align-items: center; justify-content: center;
    padding: 24px 20px 40px;
    background: linear-gradient(to top, rgba(0,0,0,.7), transparent);
}
.cam-capture-btn {
    width: 72px; height: 72px; border-radius: 50%;
    border: 4px solid #fff; background: transparent;
    cursor: pointer; position: relative;
    transition: transform .15s;
    box-shadow: 0 4px 20px rgba(0,0,0,.4);
}
.cam-capture-btn::after {
    content: ''; position: absolute;
    top: 4px; left: 4px; right: 4px; bottom: 4px;
    border-radius: 50%; background: #fff;
    transition: transform .15s, background .15s;
}
.cam-capture-btn:hover { transform: scale(1.08); }
.cam-capture-btn:active::after { transform: scale(.85); background: #ddd; }
.cam-switch-btn {
    position: absolute; right: 28px; bottom: 40px;
    width: 44px; height: 44px; border-radius: 50%; border: none;
    background: rgba(255,255,255,.2); color: #fff; font-size: 20px;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    backdrop-filter: blur(8px); transition: background .2s;
}
.cam-switch-btn:hover { background: rgba(255,255,255,.35); }
.cam-error {
    color: #fff; text-align: center; padding: 40px 20px;
}
.cam-error i { font-size: 48px; display: block; margin-bottom: 12px; color: #EF4444; }
.cam-error p { font-size: 14px; opacity: .8; margin-bottom: 16px; }
.cam-error button {
    padding: 10px 24px; border-radius: 10px; border: none;
    background: #6366F1; color: #fff; font-weight: 600; cursor: pointer;
}

/* ═══ MAP ═══ */
.att-map-box {
    width: 100%; height: 100px; border-radius: 10px; overflow: hidden;
    background: var(--bg, #F8FAFC); border: 1px solid var(--border, #E2E8F0);
    margin-bottom: 14px;
}
.att-map-placeholder {
    width: 100%; height: 100%; display: flex; flex-direction: column;
    align-items: center; justify-content: center; gap: 4px; color: var(--muted, #64748B);
}
.att-map-placeholder i { font-size: 22px; }
.att-map-placeholder span { font-size: 11px; }
.att-map-frame { display: none; width: 100%; height: 100%; border: none; }

/* ═══ COMMENTS ═══ */
.att-comments { margin-bottom: 18px; }
.att-comments textarea {
    width: 100%; border: 1.5px solid var(--border, #E2E8F0);
    border-radius: 10px; padding: 10px 12px; font-size: 13px;
    background: var(--bg, #F8FAFC); resize: none; transition: border-color .2s;
}
.att-comments textarea:focus { border-color: var(--primary, #2563EB); outline: none; }

/* ═══ CIRCLE CHECK IN / OUT ═══ */
.att-circle-btns {
    display: flex; justify-content: center; gap: 40px; padding: 8px 0;
}
.att-circle-wrap { text-align: center; }
.att-circle-btn {
    width: 80px; height: 80px; border-radius: 50%; border: none;
    color: #fff; cursor: pointer; display: flex;
    align-items: center; justify-content: center;
    transition: transform .2s, box-shadow .2s;
    position: relative; overflow: hidden;
}
.att-circle-btn::after {
    content: ''; position: absolute; inset: 0; border-radius: 50%;
    background: rgba(255,255,255,.1); opacity: 0; transition: opacity .2s;
}
.att-circle-btn:hover::after { opacity: 1; }
.att-circle-btn:hover { transform: scale(1.1); }
.att-circle-btn:active { transform: scale(.93); }
.att-circle-in {
    background: linear-gradient(135deg, #22C55E, #16A34A);
    box-shadow: 0 6px 24px rgba(34,197,94,.4);
}
.att-circle-in:hover { box-shadow: 0 10px 32px rgba(34,197,94,.55); }
.att-circle-out {
    background: linear-gradient(135deg, #EF4444, #DC2626);
    box-shadow: 0 6px 24px rgba(239,68,68,.4);
}
.att-circle-out:hover { box-shadow: 0 10px 32px rgba(239,68,68,.55); }
.att-circle-icon { font-size: 28px; }
.att-circle-label {
    display: block; margin-top: 8px; font-size: 12px;
    font-weight: 700; color: var(--text, #1E293B); letter-spacing: .3px;
}

/* ═══ SUMMARY ═══ */
.att-summary-grid {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 10px; margin-bottom: 20px;
}
.att-summary-box {
    background: var(--card-bg, #fff); border: 1px solid var(--border, #E2E8F0);
    border-radius: 12px; padding: 16px; text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,.03);
}
.att-summary-num { font-size: 28px; font-weight: 700; }
.att-summary-label { font-size: 11px; color: var(--muted, #64748B); margin-top: 2px; font-weight: 500; }
.att-summary-present .att-summary-num { color: #16A34A; }
.att-summary-absent  .att-summary-num { color: #DC2626; }
.att-summary-late    .att-summary-num { color: #D97706; }

/* ═══ SPINNER ═══ */
.att-spinner {
    display: inline-block; width: 18px; height: 18px;
    border: 2.5px solid rgba(255,255,255,.3);
    border-top-color: #fff; border-radius: 50%;
    animation: att-spin .6s linear infinite;
}
@keyframes att-spin { to { transform: rotate(360deg); } }

/* ═══ PULSE RING on circle buttons ═══ */
@keyframes att-pulse {
    0%   { box-shadow: 0 0 0 0 rgba(34,197,94,.4); }
    70%  { box-shadow: 0 0 0 14px rgba(34,197,94,0); }
    100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); }
}
@keyframes att-pulse-red {
    0%   { box-shadow: 0 0 0 0 rgba(239,68,68,.4); }
    70%  { box-shadow: 0 0 0 14px rgba(239,68,68,0); }
    100% { box-shadow: 0 0 0 0 rgba(239,68,68,0); }
}
.att-circle-in  { animation: att-pulse 2s infinite; }
.att-circle-out { animation: att-pulse-red 2s infinite; }
</style>
@endpush

@endsection

<!-- ═══ LIVE CAMERA FULLSCREEN OVERLAY ═══ -->
<div class="cam-overlay" id="cameraOverlay">
    <div class="cam-header">
        <div class="cam-header-title">
            <i class="bi bi-camera-video-fill"></i>
            <span id="camTitle">Take Photo</span>
            <span class="cam-header-badge in" id="camBadge">CHECK IN</span>
        </div>
        <button class="cam-close-btn" onclick="closeCamera()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="cam-video-wrap">
        <video id="camVideo" autoplay playsinline muted></video>
        <canvas id="camCanvas"></canvas>
        <div class="cam-error" id="camError" style="display:none;">
            <i class="bi bi-camera-video-off"></i>
            <p>Camera access denied or not available.<br>Please allow camera permission and try again.</p>
            <button onclick="closeCamera()">Go Back</button>
        </div>
    </div>
    <div class="cam-controls" id="camControls">
        <button class="cam-capture-btn" id="camCaptureBtn" onclick="capturePhoto()"></button>
        <button class="cam-switch-btn" id="camSwitchBtn" onclick="switchCamera()" title="Switch Camera">
            <i class="bi bi-arrow-repeat"></i>
        </button>
    </div>
</div>

@push('scripts')
<script>
// ── CLOCK ──
function updateClock() {
    document.getElementById('liveClock').textContent =
        new Date().toLocaleTimeString('en-IN', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
}
setInterval(updateClock, 1000);
updateClock();

// ═══ LIVE CAMERA SYSTEM ═══
let capturedFile = null;
let pendingAction = null;
let cameraStream = null;
let facingMode = 'user'; // 'user' = front, 'environment' = back

// Called when user clicks Check In or Check Out
function markAttendance(action) {
    pendingAction = action;
    openCamera(action);
}

// Open fullscreen camera
async function openCamera(action) {
    const overlay = document.getElementById('cameraOverlay');
    const badge = document.getElementById('camBadge');
    const title = document.getElementById('camTitle');
    const error = document.getElementById('camError');
    const controls = document.getElementById('camControls');
    const video = document.getElementById('camVideo');

    // Set UI for check-in vs check-out
    if (action === 'in') {
        badge.textContent = 'CHECK IN';
        badge.className = 'cam-header-badge in';
    } else {
        badge.textContent = 'CHECK OUT';
        badge.className = 'cam-header-badge out';
    }
    title.textContent = 'Take Live Photo';

    // Show overlay
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
    error.style.display = 'none';
    controls.style.display = 'flex';
    video.style.display = 'block';

    // Start camera stream
    try {
        await startCamera();
    } catch (err) {
        console.error('Camera error:', err);
        video.style.display = 'none';
        controls.style.display = 'none';
        error.style.display = 'block';
    }
}

async function startCamera() {
    // Stop any existing stream
    stopCamera();

    const constraints = {
        video: {
            facingMode: facingMode,
            width: { ideal: 1280 },
            height: { ideal: 720 }
        },
        audio: false
    };

    cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
    const video = document.getElementById('camVideo');
    video.srcObject = cameraStream;
    await video.play();
}

function stopCamera() {
    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
    }
    const video = document.getElementById('camVideo');
    video.srcObject = null;
}

function closeCamera() {
    stopCamera();
    const overlay = document.getElementById('cameraOverlay');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
    pendingAction = null;
}

async function switchCamera() {
    facingMode = facingMode === 'user' ? 'environment' : 'user';
    try {
        await startCamera();
    } catch(e) {
        // If switching fails, revert
        facingMode = facingMode === 'user' ? 'environment' : 'user';
        await startCamera();
    }
}

// Capture photo from live video stream
function capturePhoto() {
    const video = document.getElementById('camVideo');
    const canvas = document.getElementById('camCanvas');

    // Set canvas to video dimensions
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    // Draw current video frame to canvas
    const ctx = canvas.getContext('2d');
    // Mirror for front camera
    if (facingMode === 'user') {
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
    }
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    // Convert canvas to blob/file
    canvas.toBlob(function(blob) {
        if (!blob) return;

        // Create a File from the blob
        const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
        capturedFile = new File([blob], 'attendance_' + timestamp + '.jpg', { type: 'image/jpeg' });

        // Show preview in the page
        const preview = document.getElementById('attendImgPreview');
        preview.src = URL.createObjectURL(blob);
        preview.style.display = 'block';
        document.getElementById('attendImgBox').classList.add('has-img');

        const label = document.getElementById('captureActionLabel');
        label.textContent = pendingAction === 'in' ? '\ud83d\udcf8 Check-In Photo Captured' : '\ud83d\udcf8 Check-Out Photo Captured';
        document.getElementById('photoPreviewSection').style.display = 'block';

        // Close camera overlay
        stopCamera();
        document.getElementById('cameraOverlay').classList.remove('active');
        document.body.style.overflow = '';

        // Auto-submit attendance
        proceedAttendance(pendingAction);
    }, 'image/jpeg', 0.85);
}

// Submit attendance with captured photo
function proceedAttendance(action) {
    const btnIn  = document.getElementById('btnCheckIn');
    const btnOut = document.getElementById('btnCheckOut');
    const btn    = action === 'in' ? btnIn : btnOut;

    btnIn.disabled  = true;
    btnOut.disabled = true;
    btn.style.animation = 'none';
    btn.innerHTML = '<div class="att-spinner"></div>';

    document.getElementById('attendAction').value          = action;
    document.getElementById('attendCommentsHidden').value  = document.getElementById('attendComments').value;

    if (capturedFile) {
        try {
            const dt = new DataTransfer();
            dt.items.add(capturedFile);
            document.getElementById('attendImgFinal').files = dt.files;
        } catch(e) {}
    }

    if (!navigator.geolocation) { submitForm(); return; }

    navigator.geolocation.getCurrentPosition(
        pos => {
            document.getElementById('attendLat').value = pos.coords.latitude.toFixed(6);
            document.getElementById('attendLng').value = pos.coords.longitude.toFixed(6);
            const frame = document.getElementById('mapFrame');
            frame.src = 'https://maps.google.com/maps?q=' + pos.coords.latitude + ',' + pos.coords.longitude + '&z=15&output=embed';
            frame.style.display = 'block';
            document.getElementById('mapPlaceholder').style.display = 'none';
            submitForm();
        },
        () => submitForm(),
        { enableHighAccuracy: true, timeout: 8000 }
    );
}

function submitForm() {
    document.getElementById('attendForm').submit();
}

// ── LIGHTBOX ──
function openPhoto(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('photoLightbox').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('photoLightbox').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
</script>
@endpush
