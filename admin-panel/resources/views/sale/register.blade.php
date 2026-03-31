<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $companyName ?? 'FMCG' }} | Sales Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--line:#dbe4ef;--muted:#64748b;--brand:#0ea5a6;--focus:rgba(14,165,166,.2)}
*{box-sizing:border-box}
body{margin:0;min-height:100vh;font-family:'Manrope',sans-serif;display:grid;place-items:center;padding:22px;background:linear-gradient(135deg,#0f172a,#0b1220)}
.card{width:100%;max-width:560px;background:#fff;border-radius:18px;padding:28px;border:1px solid #fff;box-shadow:0 20px 45px rgba(15,23,42,.32)}
h2{font-family:'Sora',sans-serif;margin:0 0 6px}.sub{color:var(--muted);margin-bottom:16px}
label{font-size:12px;font-weight:700;color:var(--muted);margin-bottom:7px}
input{height:45px;border:1px solid var(--line);border-radius:10px;font-size:14px}
input:focus{outline:none;border-color:var(--brand);box-shadow:0 0 0 4px var(--focus)}
.btn-main{height:46px;border:0;border-radius:10px;color:#fff;font-weight:700;background:linear-gradient(135deg,#0ea5a6,#14b8a6)}
.alert{border:0;border-radius:10px;font-size:13px}
.eye-btn-sale{position:absolute;right:10px;top:50%;transform:translateY(-50%);border:0;background:transparent;color:#94a3b8;cursor:pointer;padding:4px;font-size:15px;}
.eye-btn-sale:hover{color:#475569;}
.input-wrap{position:relative;}
</style>
</head>
<body>
  <div class="card">
    <h2>Create Sales Account</h2>
    <p class="sub">Register to access {{ $companyName ?? 'FMCG' }} sales panel.</p>

    @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('sale.register.submit') }}">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label>Full Name</label>
          <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="col-md-6">
          <label>Phone</label>
          <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" maxlength="10" required>
        </div>
        <div class="col-12">
          <label>Email</label>
          <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="col-md-6">
          <label>Password</label>
          <div class="input-wrap position-relative">
            <input type="password" id="sal_pw1" name="password" class="form-control pe-5" required>
            <button type="button" class="eye-btn-sale" onclick="toggleField('sal_pw1','sal_eye1')"><i class="bi bi-eye" id="sal_eye1"></i></button>
          </div>
        </div>
        <div class="col-md-6">
          <label>Confirm Password</label>
          <div class="input-wrap position-relative">
            <input type="password" id="sal_pw2" name="password_confirmation" class="form-control pe-5" required>
            <button type="button" class="eye-btn-sale" onclick="toggleField('sal_pw2','sal_eye2')"><i class="bi bi-eye" id="sal_eye2"></i></button>
          </div>
        </div>
        <div class="col-12 mt-2">
          <button type="submit" class="btn btn-main w-100">Create Account</button>
        </div>
      </div>
    </form>

    <div class="text-center mt-3" style="font-size:13px;color:#64748b;">Already registered? <a href="{{ route('sale.login') }}">Sign in</a></div>
  </div>
<script>
function toggleField(id,iconId){const i=document.getElementById(id),icon=document.getElementById(iconId);i.type=i.type==='password'?'text':'password';icon.className=i.type==='password'?'bi bi-eye':'bi bi-eye-slash';}
</script>
</body>
</html>
