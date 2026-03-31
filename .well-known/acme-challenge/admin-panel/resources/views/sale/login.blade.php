<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $companyName ?? 'FMCG' }} Sales Panel Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('sale_assets/images/favicon.png') }}">
    <link href="{{ asset('sale_assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link class="main-css" href="{{ asset('sale_assets/css/style.css') }}" rel="stylesheet">
    <style>
        .fix-wrapper { min-height: 100vh; display: flex; align-items: center; padding: 24px 0; }
        .logo-wrap { display: flex; flex-direction: column; align-items: center; gap: 8px; margin-bottom: 16px; }
        .logo-wrap img { max-height: 56px; width: auto; object-fit: contain; }
        .company-name { font-weight: 700; font-size: 24px; line-height: 1; margin: 0; }
        .btn-primary, .btn-primary:hover, .btn-primary:focus, .btn-primary:active { background-color: #ff4f00 !important; border-color: #ff4f00 !important; color: #fff !important; }
        .auth-switch { display: flex; gap: 8px; margin-bottom: 16px; }
        .auth-switch .btn { flex: 1; }
        .new-account, .new-account p { text-align: center; margin-bottom: 0; }
    </style>
</head>
<body>
@php
    $logoPath = asset('sale_assets/images/logo-full.png');
    if (!empty($companySettings?->profile_image)) {
        $img = ltrim((string) $companySettings->profile_image, '/');
        $logoPath = str_starts_with($img, 'http') ? $img : (file_exists(public_path('uploads/admin/' . $img)) ? asset('uploads/admin/' . $img) : asset('storage/' . $img));
    }
@endphp

<div class="fix-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-6">
                <div class="card mb-0 h-auto">
                    <div class="card-body">
                        <div class="logo-wrap">
                            <img class="logo-auth light" src="{{ $logoPath }}" alt="Company Logo">
                            <p class="company-name">{{ $companyName ?? 'FMCG' }}</p>
                        </div>

                        <h4 class="text-center mb-4">Sign in your account</h4>

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif

                        <div class="auth-switch">
                            <button type="button" class="btn btn-primary" id="showPasswordBtn">Password Login</button>
                            <button type="button" class="btn btn-light" id="showOtpBtn">Login with OTP</button>
                        </div>

                        <form method="POST" action="{{ route('sale.login.submit') }}" id="passwordLoginForm">
                            @csrf
                            <div class="form-group mb-4">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" class="form-control" placeholder="hello@example.com" id="email" name="email" value="{{ old('email') }}" required>
                            </div>

                            <div class="mb-sm-4 mb-3">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" id="password" class="form-control" name="password" placeholder="Enter password" required>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-block">Login</button>
                            </div>
                        </form>

                        <div id="otpLoginForm" style="display:none;">
                            <div class="form-group mb-4">
                                <label class="form-label" for="otpMobile">Mobile Number</label>
                                <input type="text" class="form-control" id="otpMobile" placeholder="Enter 10 digit mobile number" maxlength="10" pattern="[0-9]{10}">
                            </div>
                            <div class="text-center mb-3">
                                <button type="button" class="btn btn-primary btn-block" id="sendOtpBtn">Send OTP</button>
                            </div>

                            <div id="otpVerifyBox" style="display:none;">
                                <div class="form-group mb-4">
                                    <label class="form-label" for="otpCode">OTP</label>
                                    <input type="text" class="form-control" id="otpCode" placeholder="Enter 6-digit OTP" maxlength="6" pattern="[0-9]{6}">
                                </div>
                                <div class="text-center">
                                    <button type="button" class="btn btn-primary btn-block" id="verifyOtpBtn">Verify OTP</button>
                                </div>
                            </div>
                        </div>

                        <div class="new-account mt-3">
                            <p>New user? <a class="text-primary" href="{{ route('sale.register') }}">Register here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('sale_assets/vendor/global/global.min.js') }}"></script>
<script src="{{ asset('sale_assets/js/custom.min.js') }}"></script>
<script>
(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const passwordForm = document.getElementById('passwordLoginForm');
    const otpForm = document.getElementById('otpLoginForm');
    const otpVerifyBox = document.getElementById('otpVerifyBox');
    const showPasswordBtn = document.getElementById('showPasswordBtn');
    const showOtpBtn = document.getElementById('showOtpBtn');

    showPasswordBtn.addEventListener('click', function () {
        passwordForm.style.display = '';
        otpForm.style.display = 'none';
        showPasswordBtn.classList.replace('btn-light', 'btn-primary');
        showOtpBtn.classList.replace('btn-primary', 'btn-light');
    });

    showOtpBtn.addEventListener('click', function () {
        passwordForm.style.display = 'none';
        otpForm.style.display = '';
        showOtpBtn.classList.replace('btn-light', 'btn-primary');
        showPasswordBtn.classList.replace('btn-primary', 'btn-light');
    });

    document.getElementById('sendOtpBtn').addEventListener('click', async function () {
        const mobile = (document.getElementById('otpMobile').value || '').replace(/\D/g, '');
        if (mobile.length !== 10) {
            alert('Please enter valid 10-digit mobile number.');
            return;
        }

        const response = await fetch("{{ route('sale.sendOtp') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ mobile })
        });
        const data = await response.json();
        if (!response.ok || !data.success) {
            alert(data.message || 'Failed to send OTP');
            return;
        }
        otpVerifyBox.style.display = '';
        alert(data.message || 'OTP sent successfully.');
    });

    document.getElementById('verifyOtpBtn').addEventListener('click', async function () {
        const mobile = (document.getElementById('otpMobile').value || '').replace(/\D/g, '');
        const otp = (document.getElementById('otpCode').value || '').replace(/\D/g, '');

        if (mobile.length !== 10 || otp.length !== 6) {
            alert('Enter valid mobile and 6-digit OTP.');
            return;
        }

        const response = await fetch("{{ route('sale.verifyOtp') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ mobile, otp })
        });
        const data = await response.json();
        if (!response.ok || !data.success) {
            alert(data.message || 'Invalid OTP');
            return;
        }
        window.location.href = data.redirect || "{{ route('sale.dashboard') }}";
    });
})();
</script>
</body>
</html>
