<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Register — Delivery Partner</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root {
  --bg-deep: #0f172a;
  --card: #ffffff;
  --stroke: #dbe3ee;
  --text: #0f172a;
  --muted: #617085;
  --primary: #0ea5a6;
  --primary-strong: #0c7f80;
  --focus: rgba(14, 165, 166, 0.24);
}
*, *::before, *::after { box-sizing: border-box; }
body {
  margin: 0;
  min-height: 100vh;
  font-family: 'Manrope', sans-serif;
  color: var(--text);
  display: grid;
  grid-template-columns: 1.08fr 1fr;
  background: radial-gradient(circle at 22% 22%, #1f2937 0%, var(--bg-deep) 45%, #020617 100%);
}
.auth-visual {
  position: relative;
  overflow: hidden;
  padding: 76px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  color: #e6f4ff;
}
.auth-visual::before, .auth-visual::after {
  content: "";
  position: absolute;
  border-radius: 999px;
  filter: blur(8px);
  opacity: .45;
}
.auth-visual::before { width:340px; height:340px; background:linear-gradient(135deg,#22d3ee,#14b8a6); top:-110px; left:-90px; }
.auth-visual::after  { width:280px; height:280px; background:linear-gradient(135deg,#0ea5e9,#34d399); bottom:-100px; right:40px; }
.visual-content { position:relative; z-index:1; max-width:520px; }
.auth-visual-logo {
  width:60px; height:60px; border-radius:18px;
  background:rgba(255,255,255,.14); border:1px solid rgba(255,255,255,.24);
  backdrop-filter:blur(8px); display:flex; align-items:center; justify-content:center;
  font-size:27px; margin-bottom:26px;
}
.auth-visual h1 { margin:0 0 12px; font-family:'Sora',sans-serif; font-size:clamp(28px,3.1vw,42px); line-height:1.15; letter-spacing:-.02em; }
.auth-visual p  { margin:0 0 30px; font-size:15px; color:rgba(230,244,255,.86); line-height:1.65; }
.visual-list { display:grid; gap:10px; padding:0; margin:0; list-style:none; }
.visual-list li { display:flex; align-items:center; gap:10px; font-size:14px; }
.visual-list i { color:#2dd4bf; }

.auth-form-wrap {
  background:linear-gradient(180deg,#f4f7fb 0%,#ecf2f9 100%);
  display:flex; align-items:center; justify-content:center; padding:36px 22px;
}
.auth-card {
  width:100%; max-width:430px; border-radius:20px;
  background:rgba(255,255,255,.9); border:1px solid rgba(255,255,255,.95);
  box-shadow:0 22px 55px rgba(15,23,42,.18); backdrop-filter:blur(6px);
  padding:36px 34px 28px; animation:cardIn .42s ease-out;
}
@keyframes cardIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
.auth-badge {
  display:inline-flex; align-items:center; gap:8px; font-size:12px; font-weight:700;
  letter-spacing:.02em; padding:7px 14px; border-radius:999px;
  background:#dff8f5; color:#0f766e; margin-bottom:16px;
}
.auth-card h2 { font-family:'Sora',sans-serif; font-size:26px; margin:0 0 4px; letter-spacing:-.02em; }
.subtitle { margin:0 0 20px; color:var(--muted); font-size:14px; }
.form-label { font-size:12.5px; color:var(--muted); font-weight:700; margin-bottom:8px; }
.input-wrap { position:relative; }
.input-wrap > i { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#8da0b6; font-size:15px; }
.input-wrap input {
  width:100%; height:46px; border:1px solid var(--stroke); border-radius:12px;
  background:#fff; font-size:14px; padding:0 14px 0 41px;
  transition:border-color .18s,box-shadow .18s;
}
.input-wrap input:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 4px var(--focus); }
.btn-login {
  width:100%; border:0; height:48px; border-radius:12px; color:#fff;
  font-weight:700; font-size:14px;
  background:linear-gradient(135deg,var(--primary),#14b8a6);
  box-shadow:0 12px 26px rgba(12,127,128,.26);
  transition:transform .15s,box-shadow .15s,filter .15s;
}
.btn-login:hover { transform:translateY(-1px); filter:brightness(1.02); }
.alert { border-radius:11px; font-size:13.5px; border:none; padding:12px 14px; margin-bottom:14px; }
.alert-danger { background:#fee2e2; color:#b91c1c; }
.security-note { margin-top:16px; text-align:center; font-size:12.5px; color:#7b8ba0; }

@media(max-width:991.98px) {
  body { grid-template-columns:1fr; background:linear-gradient(180deg,#f4f7fb 0%,#ecf2f9 100%); }
  .auth-visual { display:none; }
}
@media(max-width:575.98px) {
  .auth-card { padding:28px 18px 22px; border-radius:16px; }
  .auth-card h2 { font-size:22px; }
}
</style>
</head>
<body>

<div class="auth-visual">
  <div class="visual-content">
    <div class="auth-visual-logo"><i class="bi bi-truck-front"></i></div>
    <h1>Join as a Delivery Partner</h1>
    <p>Create your account to start managing deliveries, tracking orders, and earning with our platform.</p>
    <ul class="visual-list">
      <li><i class="bi bi-check-circle-fill"></i> View assigned orders instantly</li>
      <li><i class="bi bi-check-circle-fill"></i> Update delivery status on the go</li>
      <li><i class="bi bi-check-circle-fill"></i> Track earnings & attendance</li>
    </ul>
  </div>
</div>

<div class="auth-form-wrap">
  <div class="auth-card">
    <span class="auth-badge"><i class="bi bi-person-plus"></i> Delivery Partner</span>
    <h2>Create Account</h2>
    <p class="subtitle">Register to access the delivery panel.</p>

    @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('register.submit') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <div class="input-wrap">
          <i class="bi bi-person"></i>
          <input type="text" name="name" value="{{ old('name') }}" placeholder="Your full name" required>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <div class="input-wrap">
          <i class="bi bi-envelope"></i>
          <input type="email" name="email" value="{{ old('email') }}" placeholder="your@email.com" required>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Mobile Number</label>
        <div class="input-wrap">
          <i class="bi bi-phone"></i>
          <input type="text" name="phone" value="{{ old('phone') }}" placeholder="10-digit mobile number" maxlength="10" pattern="[0-9]{10}">
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <div class="input-wrap">
          <i class="bi bi-lock"></i>
          <input type="password" id="password" name="password" placeholder="Min 6 characters" required>
          <button type="button" class="eye-btn" onclick="toggleField('password','eyeReg1')"><i class="bi bi-eye" id="eyeReg1"></i></button>
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label">Confirm Password</label>
        <div class="input-wrap">
          <i class="bi bi-lock-fill"></i>
          <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat password" required>
          <button type="button" class="eye-btn" onclick="toggleField('password_confirmation','eyeReg2')"><i class="bi bi-eye" id="eyeReg2"></i></button>
        </div>
      </div>
      <button type="submit" class="btn-login">Create Account <i class="bi bi-arrow-right ms-1"></i></button>
    </form>

    <p class="security-note">Already have an account? <a href="{{ route('login') }}" style="color:var(--primary);font-weight:700;">Sign in</a></p>
  </div>
</div>

<script>
function toggleField(id, iconId) {
  const i = document.getElementById(id), icon = document.getElementById(iconId);
  i.type = i.type === 'password' ? 'text' : 'password';
  icon.className = i.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
</body>
</html>
