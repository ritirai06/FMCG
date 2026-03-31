<!DOCTYPE html>
<html lang="en">
<head>

    <title>Delivery OTP Verify - Jobick</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/png" href="{{ asset('sale_assets/images/favicon.png') }}">
    <link href="{{ asset('sale_assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link class="main-css" href="{{ asset('sale_assets/css/style.css') }}" rel="stylesheet">

</head>

<body>
<div class="fix-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-6">
                <div class="card mb-0 h-auto">
                    <div class="card-body">

                        <div class="text-center mb-3 logo-img">
                            <img class="logo-auth light" src="{{ asset('sale_assets/images/logo-full.png') }}" alt="">
                        </div>

                        <h4 class="text-center mb-2">OTP Verification</h4>
                        <p class="text-center text-muted mb-4">
                            Enter OTP sent to your mobile number
                        </p>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif

                        <form method="POST" action="{{ route('delivery.panel.verifyOtp') }}">
                            @csrf

                            <div class="form-group mb-3">
                                <label class="form-label">Mobile Number</label>
                                <input type="text"
                                       class="form-control"
                                       name="phone"
                                       maxlength="10"
                                       pattern="[0-9]{10}"
                                       value="{{ old('phone', $phone) }}"
                                       placeholder="Enter registered mobile number"
                                       required>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label">OTP</label>
                                <input type="text"
                                       class="form-control"
                                       name="otp"
                                       maxlength="6"
                                       pattern="[0-9]{6}"
                                       value="{{ old('otp') }}"
                                       placeholder="Enter 6-digit OTP"
                                       required>
                            </div>

                            <div class="text-center mb-3">
                                <button type="submit" class="btn btn-primary btn-block">Verify OTP</button>
                            </div>
                        </form>

                        <div class="text-center">
                            <a href="{{ route('delivery.panel.login') }}" class="text-primary">Back to Login</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('sale_assets/vendor/global/global.min.js') }}"></script>
<script src="{{ asset('sale_assets/js/custom.min.js') }}"></script>
</body>
</html>
