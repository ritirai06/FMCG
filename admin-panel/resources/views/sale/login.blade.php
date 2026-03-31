<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sales Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html, body { height: 100%; font-family: 'Inter', sans-serif; }
body { display: flex; min-height: 100vh; }

.left {
  flex: 1.1;
  background: linear-gradient(145deg, #0c1445, #1e3a8a, #1d4ed8);
  position: relative; overflow: hidden;
  display: flex; flex-direction: column; justify-content: center;
  padding: 60px 64px; color: #dbeafe;
}
.left::before {
  content: ''; position: absolute;
  width: 400px; height: 400px; border-radius: 50%;
  background: radial-gradient(circle, rgba(96,165,250,.3) 0%, transparent 70%);
  top: -100px; left: -80px;
}
.left::after {
  content: ''; position: absolute;
  width: 300px; height: 300px; border-radius: 50%;
  background: radial-gradient(circle, rgba(59,130,246,.2) 0%, transparent 70%);
  bottom: -60px; right: -40px;
}
.left-content { position: relative; z-index: 1; max-width: 500px; }
.brand-icon {
  width: 56px; height: 56px; border-radius: 16px;
  background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.18);
  display: flex; align-items: center; justify-content: center;
  font-size: 24px; margin-bottom: 32px;
}
.left h1 {
  font-family: 'Sora', sans-serif;
  font-size: clamp(28px, 3vw, 42px);
  line-height: 1.2; letter-spacing: -.02em;
  margin-bottom: 14px; color: #fff;
}
.left p { font-size: 15px; color: rgba(219,234,254,.75); line-height: 1.7; margin-bottom: 32px; }
.features { list-style: none; display: flex; flex-direction: column; gap: 12px; }
.features li { display: flex; align-items: center; gap: 10px; font-size: 14px; color: rgba(219,234,254,.85); }
.features li i { color: #60a5fa; font-size: 16px; flex-shrink: 0; }

.right {
  flex: 1; background: #f0f7ff;
  display: flex; align-items: center; justify-content: center;
  padding: 40px 24px; min-height: 100vh;
}
.card {
  width: 100%; max-width: 420px; background: #fff;
  border-radius: 20px;
  box-shadow: 0 8px 40px rgba(15,23,42,.12), 0 2px 8px rgba(15,23,42,.06);
  padding: 40px 36px 36px;
}
.badge {
  display: inline-flex; align-items: center; gap: 7px;
  background: #dbeafe; color: #1d4ed8;
  border-radius: 999px; padding: 6px 14px;
  font-size: 12px; font-weight: 700; letter-spacing: .02em; margin-bottom: 20px;
}
.card h2 { font-family: 'Sora', sans-serif; font-size: 28px; color: #0f172a; letter-spacing: -.02em; margin-bottom: 6px; }
.card .sub { font-size: 14px; color: #64748b; margin-bottom: 28px; }
.form-group { margin-bottom: 18px; }
label { display: block; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .04em; margin-bottom: 8px; }
.input-box { position: relative; }
.input-box i.icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 15px; pointer-events: none; }
.input-box input {
  width: 100%; height: 48px; border: 1.5px solid #e2e8f0; border-radius: 12px;
  background: #f8fafc; font-size: 14px; font-family: 'Inter', sans-serif;
  color: #0f172a; padding: 0 44px 0 42px; outline: none;
  transition: border-color .18s, box-shadow .18s, background .18s;
}
.input-box input:focus { border-color: #2563eb; background: #fff; box-shadow: 0 0 0 4px rgba(37,99,235,.1); }
.input-box input::placeholder { color: #94a3b8; }
.eye-btn { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); border: 0; background: transparent; color: #94a3b8; cursor: pointer; padding: 4px; border-radius: 6px; font-size: 15px; transition: color .15s; }
.eye-btn:hover { color: #475569; }
.btn-login {
  width: 100%; height: 50px; border: 0; border-radius: 12px;
  background: linear-gradient(135deg, #2563eb, #1d4ed8); color: #fff;
  font-size: 15px; font-weight: 700; font-family: 'Inter', sans-serif;
  cursor: pointer; box-shadow: 0 8px 24px rgba(37,99,235,.28);
  transition: transform .15s, box-shadow .15s, filter .15s;
  margin-top: 8px; display: flex; align-items: center; justify-content: center; gap: 8px;
}
.btn-login:hover { transform: translateY(-1px); box-shadow: 0 12px 28px rgba(37,99,235,.32); filter: brightness(1.04); }
.btn-login:active { transform: translateY(0); }
.alert { border-radius: 10px; font-size: 13px; padding: 12px 14px; margin-bottom: 18px; border: none; display: flex; align-items: center; gap: 8px; }
.alert-danger { background: #fee2e2; color: #b91c1c; }
.alert-success { background: #dcfce7; color: #166534; }
.divider { display: flex; align-items: center; gap: 10px; margin: 22px 0 16px; color: #cbd5e1; font-size: 12px; }
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }
.panel-links { display: flex; gap: 10px; }
.panel-link {
  flex: 1; display: flex; align-items: center; justify-content: center; gap: 6px;
  padding: 10px 12px; border-radius: 10px; border: 1.5px solid #e2e8f0;
  background: #f8fafc; font-size: 13px; font-weight: 600; color: #475569;
  text-decoration: none; transition: all .15s;
}
.panel-link:hover { border-color: #94a3b8; background: #f1f5f9; color: #0f172a; }
.panel-link.admin { color: #0f766e; border-color: #99f6e4; background: #f0fdfa; }
.panel-link.admin:hover { background: #ccfbf1; border-color: #5eead4; }
.panel-link.delivery { color: #b45309; border-color: #fde68a; background: #fffbeb; }
.panel-link.delivery:hover { background: #fef3c7; border-color: #fcd34d; }
.footer-note { text-align: center; font-size: 12px; color: #94a3b8; margin-top: 20px; }

@media (max-width: 991px) {
  body { flex-direction: column; }
  .left { display: none; }
  .right { flex: 1; background: linear-gradient(180deg, #f0f7ff, #dbeafe); min-height: 100vh; }
  .card { max-width: 460px; }
}
@media (max-width: 480px) {
  .card { padding: 30px 22px 26px; border-radius: 16px; }
  .card h2 { font-size: 24px; }
  .panel-links { flex-direction: column; }
}
</style>
</head>
<body>

<div class="left">
  <div class="left-content">
    <div class="brand-icon"><i class="bi bi-person-badge-fill"></i></div>
    <h1>Sales Panel</h1>
    <p>Manage your routes, orders, parties, attendance, and daily sales workflow from one place.</p>
    <ul class="features">
      <li><i class="bi bi-check-circle-fill"></i> Create & track orders</li>
      <li><i class="bi bi-check-circle-fill"></i> Manage parties & stores</li>
      <li><i class="bi bi-check-circle-fill"></i> Record attendance & expenses</li>
    </ul>
  </div>
</div>

<div class="right">
  <div class="card">
    <span class="badge"><i class="bi bi-person-badge-fill"></i> Sales Access</span>
    <h2>Sales Login</h2>
    <p class="sub">Sign in to your sales account.</p>

    @if(session('error'))
      <div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}</div>
    @endif
    @if(session('success'))
      <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger"><i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('sale.login.submit') }}">
      @csrf
      <div class="form-group">
        <label>Email Address</label>
        <div class="input-box">
          <i class="bi bi-envelope icon"></i>
          <input type="email" name="email" value="{{ old('email') }}" placeholder="sales@example.com" required autofocus>
        </div>
      </div>
      <div class="form-group">
        <label>Password</label>
        <div class="input-box">
          <i class="bi bi-lock icon"></i>
          <input type="password" id="pw" name="password" placeholder="Enter your password" required>
          <button type="button" class="eye-btn" onclick="togglePw()">
            <i class="bi bi-eye" id="eyeIcon"></i>
          </button>
        </div>
      </div>
      <button type="submit" class="btn-login">Sign In <i class="bi bi-arrow-right"></i></button>
    </form>

    <div class="divider">Other Panels</div>
    <div class="panel-links">
      <a href="{{ route('login') }}" class="panel-link admin">
        <i class="bi bi-shield-lock-fill"></i> Admin Login
      </a>
      <a href="{{ route('delivery.panel.login') }}" class="panel-link delivery">
        <i class="bi bi-truck-front-fill"></i> Delivery Login
      </a>
    </div>
    <p class="footer-note">Secure Internal System &copy; {{ now()->year }}</p>
  </div>
</div>

<script>
function togglePw() {
  const i = document.getElementById('pw'), icon = document.getElementById('eyeIcon');
  i.type = i.type === 'password' ? 'text' : 'password';
  icon.className = i.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
</body>
</html>
