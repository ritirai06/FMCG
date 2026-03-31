@extends('layouts.app')

@section('title', 'Attendance Management')

@section('page_title', 'Attendance Management')

@section('navbar_right')
  <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#manualModal">
    <i class="bi bi-pencil-square me-1"></i> Manual Override
  </button>
@endsection

@section('content')

<!-- TABS -->
<ul class="nav nav-tabs mt-2" id="attendanceTabs" role="tablist">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#daily" role="tab">Daily Attendance</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#monthly" role="tab">Monthly Attendance</a></li>
    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#auto" role="tab">Auto Attendance View</a></li>
</ul>

<style>
.status-dot { width:10px;height:10px;border-radius:50%;display:inline-block;margin-right:6px; }
.status-present { background:#22c55e; }
.status-absent  { background:#ef4444; }
.status-late    { background:#f59e0b; }
.nav-tabs .nav-link { color:#334155;border:none;font-weight:500;border-radius:10px;transition:.3s; }
.nav-tabs .nav-link.active { background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;font-weight:600;box-shadow:0 3px 10px rgba(99,102,241,.3); }

/* ===== CHECK-IN / CHECK-OUT BUTTONS ===== */
.checkin-area {
    background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    border: 1px solid #bbf7d0;
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 14px;
}
.checkin-area .status-info { display:flex; align-items:center; gap:14px; }
.checkin-area .badge-live {
    background: #22c55e;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    letter-spacing:.5px;
    animation: pulse-live 2s infinite;
}
@keyframes pulse-live {
    0%,100% { opacity:1; }
    50% { opacity:.5; }
}
.btn-checkin {
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 12px 28px;
    font-size: 15px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 6px 20px rgba(34,197,94,.35);
    transition: transform .15s, box-shadow .15s;
    cursor: pointer;
    letter-spacing: .3px;
}
.btn-checkin:hover { transform: translateY(-2px); box-shadow: 0 10px 26px rgba(34,197,94,.5); color:#fff; }
.btn-checkin:active { transform: scale(.97); }
.btn-checkout {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 12px 28px;
    font-size: 15px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 6px 20px rgba(239,68,68,.35);
    transition: transform .15s, box-shadow .15s;
    cursor: pointer;
    letter-spacing: .3px;
}
.btn-checkout:hover { transform: translateY(-2px); box-shadow: 0 10px 26px rgba(239,68,68,.5); color:#fff; }
.btn-checkout:active { transform: scale(.97); }
.btn-checkin:disabled, .btn-checkout:disabled {
    opacity: .5; cursor: not-allowed; transform: none; box-shadow: none;
}
.checkin-clock { font-size:26px; font-weight:800; color:#1e293b; letter-spacing:2px; font-variant-numeric:tabular-nums; }
.checkin-date { font-size:13px; color:#64748b; font-weight:500; }

/* Image preview in modal */
.img-preview-box {
    width: 100%;
    height: 120px;
    border: 2px dashed #cbd5e1;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    background: #f8fafc;
    transition: border-color .2s, background .2s;
    overflow: hidden;
    position: relative;
}
.img-preview-box:hover { border-color: #6366f1; background: #eef2ff; }
.img-preview-box img { width:100%; height:100%; object-fit:cover; border-radius:8px; }
.img-preview-box .img-placeholder { text-align:center; color:#94a3b8; pointer-events:none; }
.img-preview-box .img-placeholder i { font-size:28px; display:block; margin-bottom:4px; }
.img-preview-box .img-placeholder span { font-size:11px; }

/* ═══ LIVE CAMERA OVERLAY ═══ */
.adm-cam-overlay {
    display: none; position: fixed; inset: 0; z-index: 99999;
    background: #000; flex-direction: column;
}
.adm-cam-overlay.active { display: flex; }
.adm-cam-header {
    position: absolute; top: 0; left: 0; right: 0; z-index: 2;
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px;
    background: linear-gradient(to bottom, rgba(0,0,0,.7), transparent);
}
.adm-cam-title {
    color: #fff; font-size: 16px; font-weight: 700;
    display: flex; align-items: center; gap: 8px;
}
.adm-cam-badge {
    font-size: 10px; font-weight: 700; padding: 3px 10px;
    border-radius: 20px; text-transform: uppercase; letter-spacing: .5px;
}
.adm-cam-badge.in  { background: #22c55e; color: #fff; }
.adm-cam-badge.out { background: #ef4444; color: #fff; }
.adm-cam-close {
    width: 40px; height: 40px; border-radius: 50%; border: none;
    background: rgba(255,255,255,.15); color: #fff; font-size: 18px;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    backdrop-filter: blur(8px);
}
.adm-cam-close:hover { background: rgba(255,255,255,.3); }
.adm-cam-video-wrap {
    flex: 1; display: flex; align-items: center; justify-content: center;
    overflow: hidden; position: relative;
}
.adm-cam-video-wrap video { width: 100%; height: 100%; object-fit: cover; }
.adm-cam-video-wrap canvas { display: none; }
.adm-cam-controls {
    position: absolute; bottom: 0; left: 0; right: 0; z-index: 2;
    display: flex; align-items: center; justify-content: center;
    padding: 24px 20px 40px;
    background: linear-gradient(to top, rgba(0,0,0,.7), transparent);
}
.adm-cam-capture {
    width: 72px; height: 72px; border-radius: 50%;
    border: 4px solid #fff; background: transparent;
    cursor: pointer; position: relative;
    transition: transform .15s;
    box-shadow: 0 4px 20px rgba(0,0,0,.4);
}
.adm-cam-capture::after {
    content: ''; position: absolute;
    top: 4px; left: 4px; right: 4px; bottom: 4px;
    border-radius: 50%; background: #fff;
    transition: transform .15s, background .15s;
}
.adm-cam-capture:hover { transform: scale(1.08); }
.adm-cam-capture:active::after { transform: scale(.85); background: #ddd; }
.adm-cam-switch {
    position: absolute; right: 28px; bottom: 40px;
    width: 44px; height: 44px; border-radius: 50%; border: none;
    background: rgba(255,255,255,.2); color: #fff; font-size: 20px;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    backdrop-filter: blur(8px);
}
.adm-cam-switch:hover { background: rgba(255,255,255,.35); }
.adm-cam-error {
    color: #fff; text-align: center; padding: 40px 20px;
}
.adm-cam-error i { font-size: 48px; display: block; margin-bottom: 12px; color: #ef4444; }
.adm-cam-error p { font-size: 14px; opacity: .8; margin-bottom: 16px; }
.adm-cam-error button {
    padding: 10px 24px; border-radius: 10px; border: none;
    background: #6366f1; color: #fff; font-weight: 600; cursor: pointer;
}
.img-preview-box.has-photo { border-color: #22c55e; border-style: solid; }
</style>

<div class="tab-content mt-4">

    <!-- DAILY -->
    <div class="tab-pane fade show active" id="daily" role="tabpanel">
        <div class="table-card">

            <!-- ===== CHECK-IN / CHECK-OUT AREA ===== -->
            <div class="checkin-area">
                <div class="status-info">
                    <div>
                        <div class="checkin-clock" id="liveClock">--:-- --</div>
                        <div class="checkin-date">{{ now()->format('l, d M Y') }}</div>
                    </div>
                    <span class="badge-live">● LIVE</span>
                </div>
                <div class="d-flex gap-3 flex-wrap">
                    <button class="btn-checkin" id="btnCheckIn" onclick="openCheckModal('in')">
                        <i class="bi bi-box-arrow-in-right fs-5"></i>
                        Check In
                    </button>
                    <button class="btn-checkout" id="btnCheckOut" onclick="openCheckModal('out')">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                        Check Out
                    </button>
                </div>
            </div>

            <h6 class="fw-bold mb-3">Today: {{ now()->format('d M Y') }}</h6>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Employee</th><th>Time In</th><th>Time Out</th><th>Image</th><th>Status</th><th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceTbody">
                        @php $todayStr = $today ?? now()->toDateString(); @endphp
                        @forelse($attendances->where('date', $todayStr) as $a)
                            <tr data-id="{{ $a->id }}">
                                <td class="emp-name">{{ $a->employee_name }}</td>
                                <td class="time-in">{{ $a->time_in ?: '--' }}</td>
                                <td class="time-out">{{ $a->time_out ?: '--' }}</td>
                                <td class="img-cell">
                                    @if($a->image_in)
                                        <img src="{{ asset('storage/'.$a->image_in) }}" alt="In" style="width:36px;height:36px;object-fit:cover;border-radius:6px;cursor:pointer;" onclick="window.open(this.src)">
                                    @else
                                        <span class="text-muted" style="font-size:12px;">—</span>
                                    @endif
                                </td>
                                <td class="status-cell">
                                    <span class="status-dot {{ $a->status=='Present' ? 'status-present' : ($a->status=='Late' ? 'status-late' : 'status-absent') }}"></span>
                                    {{ $a->status }}
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-light btn-sm edit-att" data-id="{{ $a->id }}"><i class="bi bi-pencil text-primary"></i></button>
                                    <button class="btn btn-light btn-sm delete-att" data-id="{{ $a->id }}"><i class="bi bi-trash text-danger"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No attendance for today.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MONTHLY -->
    <div class="tab-pane fade" id="monthly" role="tabpanel">
        <div class="table-card">
            <h6 class="fw-bold mb-3">Monthly Summary ({{ now()->format('F Y') }})</h6>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr><th>Employee</th><th>Present</th><th>Absent</th><th>Late</th><th>Attendance %</th></tr>
                    </thead>
                    <tbody>
                        @if(isset($monthly) && $monthly->count())
                            @foreach($monthly as $m)
                                <tr>
                                    <td>{{ $m->employee_name }}</td>
                                    <td>{{ $m->present }}</td>
                                    <td>{{ $m->absent }}</td>
                                    <td>{{ $m->late }}</td>
                                    <td>{{ intval(($m->present / max(1, $m->present + $m->absent)) * 100) }}%</td>
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="5" class="text-center text-muted">No monthly data.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- AUTO -->
    <div class="tab-pane fade" id="auto" role="tabpanel">
        <div class="table-card text-center p-5">
            <i class="bi bi-gear-wide-connected text-primary display-5 mb-3"></i>
            <h5 class="fw-semibold">Auto Attendance System Active</h5>
            <p class="text-muted mb-2">Employee attendance is automatically recorded through GPS and biometric integration.</p>
            <button class="btn btn-primary btn-sm" onclick="fetchAuto()"><i class="bi bi-arrow-repeat me-1"></i>Refresh View</button>
        </div>
    </div>

</div>

<!-- MANUAL OVERRIDE MODAL -->
<div class="modal fade" id="manualModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content p-3">
            <form id="attendanceForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square text-primary me-2"></i>Manual Override</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="edit_id" value="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Employee</label>
                            <input type="text" name="employee_name" id="employee_name" class="form-control" placeholder="Enter name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <input type="date" name="date" id="att_date" class="form-control" value="{{ $today ?? now()->toDateString() }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Time In</label>
                            <div class="input-group">
                                <input type="text" name="time_in" id="time_in" class="form-control" placeholder="07:30">
                                <select id="time_in_ampm" class="form-select"><option>AM</option><option>PM</option></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Time Out</label>
                            <div class="input-group">
                                <input type="text" name="time_out" id="time_out" class="form-control" placeholder="05:30">
                                <select id="time_out_ampm" class="form-select"><option>AM</option><option>PM</option></select>
                            </div>
                        </div>

                        <!-- IMAGE UPLOAD -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><i class="bi bi-camera me-1 text-primary"></i>Check-In Photo</label>
                            <div class="img-preview-box" id="previewInBox" onclick="document.getElementById('image_in').click()">
                                <img id="previewInImg" src="" alt="" style="display:none;">
                                <div class="img-placeholder" id="previewInPlaceholder">
                                    <i class="bi bi-camera"></i>
                                    <span>Tap to take live photo</span>
                                </div>
                            </div>
                            <input type="file" id="image_in" name="image_in" accept="image/*" capture="environment" class="d-none" onchange="previewImage(this,'previewInImg','previewInPlaceholder')">
                            <button type="button" class="btn btn-link btn-sm text-danger p-0 mt-1" onclick="clearImage('image_in','previewInImg','previewInPlaceholder')">
                                <i class="bi bi-x-circle me-1"></i>Clear photo
                            </button>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><i class="bi bi-camera me-1 text-danger"></i>Check-Out Photo</label>
                            <div class="img-preview-box" id="previewOutBox" onclick="document.getElementById('image_out').click()">
                                <img id="previewOutImg" src="" alt="" style="display:none;">
                                <div class="img-placeholder" id="previewOutPlaceholder">
                                    <i class="bi bi-camera"></i>
                                    <span>Tap to take live photo</span>
                                </div>
                            </div>
                            <input type="file" id="image_out" name="image_out" accept="image/*" capture="environment" class="d-none" onchange="previewImage(this,'previewOutImg','previewOutPlaceholder')">
                            <button type="button" class="btn btn-link btn-sm text-danger p-0 mt-1" onclick="clearImage('image_out','previewOutImg','previewOutPlaceholder')">
                                <i class="bi bi-x-circle me-1"></i>Clear photo
                            </button>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Status</label>
                            <select name="status" id="att_status" class="form-select">
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                                <option value="Late">Late</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Override</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CHECK-IN / CHECK-OUT QUICK MODAL -->
<div class="modal fade" id="checkModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content p-3">
            <form id="checkForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="check_type" name="check_type" value="in">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" id="checkModalTitle">
                        <i class="bi bi-box-arrow-in-right text-success me-2"></i>Check In
                    </h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Employee Name</label>
                        <input type="text" name="employee_name" id="check_emp" class="form-control" placeholder="Your name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" id="checkImgLabel"><i class="bi bi-camera me-1 text-primary"></i>Check-In Photo</label>
                        <div class="img-preview-box" id="checkImgBox" style="height:100px;" onclick="openAdmCamera()">
                            <img id="checkImgPreview" src="" alt="" style="display:none;">
                            <div class="img-placeholder" id="checkImgPlaceholder">
                                <i class="bi bi-camera fs-4"></i>
                                <span>Tap to take live photo</span>
                            </div>
                        </div>
                        <input type="hidden" id="check_image_blob" name="image_blob_ready" value="0">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light w-100 mb-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success w-100" id="checkSubmitBtn">
                        <i class="bi bi-check-circle me-1"></i>Confirm Check In
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══ LIVE CAMERA FULLSCREEN OVERLAY ═══ -->
<div class="adm-cam-overlay" id="admCameraOverlay">
    <div class="adm-cam-header">
        <div class="adm-cam-title">
            <i class="bi bi-camera-video-fill"></i>
            <span>Take Live Photo</span>
            <span class="adm-cam-badge in" id="admCamBadge">CHECK IN</span>
        </div>
        <button class="adm-cam-close" onclick="closeAdmCamera()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="adm-cam-video-wrap">
        <video id="admCamVideo" autoplay playsinline muted></video>
        <canvas id="admCamCanvas"></canvas>
        <div class="adm-cam-error" id="admCamError" style="display:none;">
            <i class="bi bi-camera-video-off"></i>
            <p>Camera access denied or not available.<br>Please allow camera permission and try again.</p>
            <button onclick="closeAdmCamera()">Go Back</button>
        </div>
    </div>
    <div class="adm-cam-controls" id="admCamControls">
        <button class="adm-cam-capture" onclick="admCapturePhoto()"></button>
        <button class="adm-cam-switch" onclick="admSwitchCamera()" title="Switch Camera">
            <i class="bi bi-arrow-repeat"></i>
        </button>
    </div>
</div>

<div id="attToast" class="position-fixed bottom-0 end-0 p-3" style="z-index:1080;"></div>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;
let admCameraStream = null;
let admFacingMode = 'user';
let admCapturedBlob = null;

// Live clock
function updateClock(){
    const now = new Date();
    let h = now.getHours(), m = now.getMinutes(), s = now.getSeconds();
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    document.getElementById('liveClock').textContent =
        String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0') + ' ' + ampm;
}
updateClock();
setInterval(updateClock, 1000);

// Image preview helper
function previewImage(input, imgId, placeholderId){
    if(input.files && input.files[0]){
        const reader = new FileReader();
        reader.onload = function(e){
            const img = document.getElementById(imgId);
            const ph  = document.getElementById(placeholderId);
            img.src = e.target.result;
            img.style.display = 'block';
            ph.style.display  = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function clearImage(inputId, imgId, placeholderId){
    document.getElementById(inputId).value = '';
    document.getElementById(imgId).src = '';
    document.getElementById(imgId).style.display = 'none';
    document.getElementById(placeholderId).style.display = 'flex';
}

// ═══ LIVE CAMERA FUNCTIONS FOR ADMIN ═══
async function openAdmCamera() {
    const overlay = document.getElementById('admCameraOverlay');
    const badge = document.getElementById('admCamBadge');
    const error = document.getElementById('admCamError');
    const controls = document.getElementById('admCamControls');
    const video = document.getElementById('admCamVideo');
    const type = document.getElementById('check_type').value;

    badge.textContent = type === 'in' ? 'CHECK IN' : 'CHECK OUT';
    badge.className = type === 'in' ? 'adm-cam-badge in' : 'adm-cam-badge out';

    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
    error.style.display = 'none';
    controls.style.display = 'flex';
    video.style.display = 'block';

    try {
        await startAdmCamera();
    } catch (err) {
        console.error('Camera error:', err);
        video.style.display = 'none';
        controls.style.display = 'none';
        error.style.display = 'block';
    }
}

async function startAdmCamera() {
    stopAdmCamera();
    const constraints = {
        video: { facingMode: admFacingMode, width: { ideal: 1280 }, height: { ideal: 720 } },
        audio: false
    };
    admCameraStream = await navigator.mediaDevices.getUserMedia(constraints);
    const video = document.getElementById('admCamVideo');
    video.srcObject = admCameraStream;
    await video.play();
}

function stopAdmCamera() {
    if (admCameraStream) {
        admCameraStream.getTracks().forEach(t => t.stop());
        admCameraStream = null;
    }
    document.getElementById('admCamVideo').srcObject = null;
}

function closeAdmCamera() {
    stopAdmCamera();
    document.getElementById('admCameraOverlay').classList.remove('active');
    document.body.style.overflow = '';
}

async function admSwitchCamera() {
    admFacingMode = admFacingMode === 'user' ? 'environment' : 'user';
    try { await startAdmCamera(); } catch(e) {
        admFacingMode = admFacingMode === 'user' ? 'environment' : 'user';
        await startAdmCamera();
    }
}

function admCapturePhoto() {
    const video = document.getElementById('admCamVideo');
    const canvas = document.getElementById('admCamCanvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    if (admFacingMode === 'user') { ctx.translate(canvas.width, 0); ctx.scale(-1, 1); }
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    canvas.toBlob(function(blob) {
        if (!blob) return;
        admCapturedBlob = blob;

        // Show preview in modal
        const preview = document.getElementById('checkImgPreview');
        preview.src = URL.createObjectURL(blob);
        preview.style.display = 'block';
        document.getElementById('checkImgPlaceholder').style.display = 'none';
        document.getElementById('checkImgBox').classList.add('has-photo');
        document.getElementById('check_image_blob').value = '1';

        closeAdmCamera();
    }, 'image/jpeg', 0.85);
}

// Open check-in / check-out quick modal
function openCheckModal(type){
    const isIn = type === 'in';
    document.getElementById('check_type').value = type;
    document.getElementById('checkModalTitle').innerHTML = isIn
        ? '<i class="bi bi-box-arrow-in-right text-success me-2"></i>Check In'
        : '<i class="bi bi-box-arrow-right text-danger me-2"></i>Check Out';
    document.getElementById('checkImgLabel').innerHTML = isIn
        ? '<i class="bi bi-camera me-1 text-success"></i>Check-In Photo (Live)'
        : '<i class="bi bi-camera me-1 text-danger"></i>Check-Out Photo (Live)';
    const btn = document.getElementById('checkSubmitBtn');
    btn.className = isIn ? 'btn btn-success w-100' : 'btn btn-danger w-100';
    btn.innerHTML = isIn
        ? '<i class="bi bi-check-circle me-1"></i>Confirm Check In'
        : '<i class="bi bi-sign-stop me-1"></i>Confirm Check Out';
    // reset preview
    admCapturedBlob = null;
    document.getElementById('checkImgPreview').src = '';
    document.getElementById('checkImgPreview').style.display = 'none';
    document.getElementById('checkImgPlaceholder').style.display = 'flex';
    document.getElementById('checkImgBox').classList.remove('has-photo');
    document.getElementById('check_image_blob').value = '0';
    document.getElementById('check_emp').value = '';
    
    const modalEl = document.getElementById('checkModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
    
    // Auto-open live camera after modal is fully visible
    modalEl.addEventListener('shown.bs.modal', function autoCamera() {
        modalEl.removeEventListener('shown.bs.modal', autoCamera);
        openAdmCamera();
    });
}

// Quick check-in / check-out form submit
document.getElementById('checkForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const type = document.getElementById('check_type').value;
    const empName = document.getElementById('check_emp').value.trim();
    if(!empName){ showToast('Please enter employee name', false); return; }

    const now = new Date();
    const h = String(now.getHours()).padStart(2,'0');
    const m = String(now.getMinutes()).padStart(2,'0');
    const timeNow = h + ':' + m;
    const dateNow = now.toISOString().split('T')[0];

    const fd = new FormData();
    fd.append('_token', csrf);
    fd.append('employee_name', empName);

    // Create file from live camera blob
    let capturedFile = null;
    if (admCapturedBlob) {
        const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
        capturedFile = new File([admCapturedBlob], 'attendance_' + timestamp + '.jpg', { type: 'image/jpeg' });
    }

    let url;
    if(type === 'in'){
        url = '/attendance/store';
        fd.append('date', dateNow);
        fd.append('time_in', timeNow);
        fd.append('status', 'Present');
        fd.append('notes', '');
        if(capturedFile) fd.append('image_in', capturedFile);
    } else {
        url = '/attendance/checkout';
        if(capturedFile) fd.append('image_out', capturedFile);
    }

    const submitBtn = document.getElementById('checkSubmitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

    const res = await fetch(url, {
        method:'POST',
        headers:{'X-CSRF-TOKEN': csrf, 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest'},
        body: fd
    });
    const data = await res.json();
    submitBtn.disabled = false;
    submitBtn.innerHTML = type === 'in'
        ? '<i class="bi bi-check-circle me-1"></i>Confirm Check In'
        : '<i class="bi bi-sign-stop me-1"></i>Confirm Check Out';

    if(data && data.ok){
        bootstrap.Modal.getInstance(document.getElementById('checkModal'))?.hide();
        const row = document.querySelector('#attendanceTbody tr[data-id="' + data.data.id + '"]');
        if(row) row.outerHTML = buildRow(data.data);
        else document.getElementById('attendanceTbody').insertAdjacentHTML('afterbegin', buildRow(data.data));
        showToast(type === 'in' ? '✅ Checked In at ' + formatTime(timeNow) : '🚪 Checked Out at ' + formatTime(timeNow));
    } else {
        const err = data?.errors ? Object.values(data.errors)[0][0] : 'Failed';
        showToast(err, false);
    }
});

function formatTime(hhmm){
    const [h, m] = hhmm.split(':').map(Number);
    const ampm = h >= 12 ? 'PM' : 'AM';
    const hr12 = h % 12 || 12;
    return String(hr12).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ' ' + ampm;
}

function showToast(msg, ok=true){
    const t = document.createElement('div');
    t.innerHTML = `<div class="toast align-items-center text-bg-${ok?'success':'danger'} border-0 show" role="alert"><div class="d-flex"><div class="toast-body">${msg}</div><button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`;
    document.getElementById('attToast').appendChild(t);
    setTimeout(()=>t.remove(), 3500);
}

function time24To12(v){
    if(!v) return {hour:null,minute:null,ampm:'',display:'--'};
    const parts = v.split(':'); let h = parseInt(parts[0],10); const m = parts[1]||'00';
    const ampm = h>=12?'PM':'AM'; let hr12 = h%12; if(hr12===0) hr12=12;
    return {hour:hr12, minute:m, ampm, display:`${String(hr12).padStart(2,'0')}:${m} ${ampm}`};
}

function time12To24(time12, ampm){
    if(!time12) return '';
    const parts = time12.split(':'); let h = parseInt(parts[0].trim(),10); let m = (parts[1]||'00').trim();
    if(isNaN(h)) return '';
    if(ampm==='AM'){ if(h===12) h=0; } else { if(h<12) h=h+12; }
    return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
}

function buildRow(a){
    const tin = time24To12(a.time_in); const tout = time24To12(a.time_out);
    const imgCell = a.image_in
        ? `<img src="/storage/${a.image_in}" style="width:36px;height:36px;object-fit:cover;border-radius:6px;cursor:pointer;" onclick="window.open(this.src)">`
        : '<span class="text-muted" style="font-size:12px;">—</span>';
    return `<tr data-id="${a.id}">
        <td class="emp-name">${a.employee_name}</td>
        <td class="time-in">${tin.display}</td>
        <td class="time-out">${tout.display}</td>
        <td class="img-cell">${imgCell}</td>
        <td class="status-cell"><span class="status-dot ${a.status=='Present'?'status-present':(a.status=='Late'?'status-late':'status-absent')}"></span>${a.status}</td>
        <td class="text-end">
            <button class="btn btn-light btn-sm edit-att" data-id="${a.id}"><i class="bi bi-pencil text-primary"></i></button>
            <button class="btn btn-light btn-sm delete-att" data-id="${a.id}"><i class="bi bi-trash text-danger"></i></button>
        </td>
    </tr>`;
}

// Manual override form
document.getElementById('attendanceForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const fd = new FormData(this);
    const tin = document.getElementById('time_in').value.trim();
    const tinAmp = document.getElementById('time_in_ampm').value;
    const tout = document.getElementById('time_out').value.trim();
    const toutAmp = document.getElementById('time_out_ampm').value;
    fd.set('time_in', tin ? time12To24(tin, tinAmp) : '');
    fd.set('time_out', tout ? time12To24(tout, toutAmp) : '');
    const editId = document.getElementById('edit_id').value;
    const url = editId ? '/attendance/update/'+editId : '/attendance/store';
    const res = await fetch(url, { method:'POST', headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}, body:fd });
    const data = await res.json();
    if(data && data.ok){
        if(editId){ const tr = document.querySelector('tr[data-id="'+editId+'"]'); if(tr) tr.outerHTML = buildRow(data.data); showToast('Updated'); }
        else { document.getElementById('attendanceTbody').insertAdjacentHTML('afterbegin', buildRow(data.data)); showToast('Added'); }
        this.reset(); document.getElementById('edit_id').value = '';
        clearImage('image_in','previewInImg','previewInPlaceholder');
        clearImage('image_out','previewOutImg','previewOutPlaceholder');
        bootstrap.Modal.getInstance(document.getElementById('manualModal'))?.hide();
    } else {
        const err = data?.errors ? Object.values(data.errors)[0][0] : 'Save failed';
        showToast(err, false);
    }
});

document.addEventListener('click', async function(e){
    const ed = e.target.closest('.edit-att');
    const del = e.target.closest('.delete-att');
    if(ed){
        const id = ed.getAttribute('data-id');
        const resp = await fetch('/attendance/edit/'+id, {headers:{'Accept':'application/json'}});
        const rec = await resp.json();
        document.getElementById('edit_id').value = rec.id;
        document.getElementById('employee_name').value = rec.employee_name||'';
        document.getElementById('att_date').value = rec.date||'';
        const tin = time24To12(rec.time_in); const tout = time24To12(rec.time_out);
        document.getElementById('time_in').value = tin.hour ? `${String(tin.hour).padStart(2,'0')}:${tin.minute}` : '';
        document.getElementById('time_in_ampm').value = tin.ampm||'AM';
        document.getElementById('time_out').value = tout.hour ? `${String(tout.hour).padStart(2,'0')}:${tout.minute}` : '';
        document.getElementById('time_out_ampm').value = tout.ampm||'AM';
        document.getElementById('att_status').value = rec.status||'Present';
        document.getElementById('notes').value = rec.notes||'';
        // show existing images if any
        if(rec.image_in){
            document.getElementById('previewInImg').src = '/storage/'+rec.image_in;
            document.getElementById('previewInImg').style.display = 'block';
            document.getElementById('previewInPlaceholder').style.display = 'none';
        } else {
            clearImage('image_in','previewInImg','previewInPlaceholder');
        }
        if(rec.image_out){
            document.getElementById('previewOutImg').src = '/storage/'+rec.image_out;
            document.getElementById('previewOutImg').style.display = 'block';
            document.getElementById('previewOutPlaceholder').style.display = 'none';
        } else {
            clearImage('image_out','previewOutImg','previewOutPlaceholder');
        }
        new bootstrap.Modal(document.getElementById('manualModal')).show();
    }
    if(del){
        const id = del.getAttribute('data-id');
        if(!confirm('Delete attendance?')) return;
        const resp = await fetch('/attendance/delete/'+id, {method:'DELETE', headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
        const data = await resp.json();
        if(data?.ok){ document.querySelector('tr[data-id="'+id+'"]')?.remove(); showToast('Deleted'); }
        else showToast('Delete failed', false);
    }
});

async function fetchMonthly(){
    const res = await fetch('/attendance/monthly-data', {headers:{'Accept':'application/json'}});
    const j = await res.json();
    if(j?.ok){
        const tbody = document.querySelector('#monthly table tbody');
        tbody.innerHTML = j.data.length
            ? j.data.map(m=>`<tr><td>${m.employee_name}</td><td>${m.present}</td><td>${m.absent}</td><td>${m.late}</td><td>${m.attendance_percent}%</td></tr>`).join('')
            : '<tr><td colspan="5" class="text-center text-muted">No monthly data.</td></tr>';
    }
}

async function fetchAuto(){
    const res = await fetch('/attendance/auto-data', {headers:{'Accept':'application/json'}});
    const j = await res.json();
    if(j?.ok){
        const container = document.querySelector('#auto .table-card');
        container.innerHTML = j.data.length
            ? `<div class="table-responsive"><table class="table align-middle"><thead class="table-light"><tr><th>Date</th><th>Employee</th><th>Time In</th><th>Time Out</th><th>Status</th></tr></thead><tbody>${j.data.map(a=>`<tr><td>${a.date}</td><td>${a.employee_name}</td><td>${a.time_in||'--'}</td><td>${a.time_out||'--'}</td><td>${a.status}</td></tr>`).join('')}</tbody></table></div>`
            : '<p class="text-center text-muted">No auto records.</p>';
    }
}

document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(a => {
    a.addEventListener('shown.bs.tab', function(e){
        const target = e.target.getAttribute('href');
        if(target==='#monthly') fetchMonthly();
        if(target==='#auto') fetchAuto();
    });
});
</script>

@endsection
