<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login · FMCG Platform</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Inter Font -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
/* =============================
   ROOT (NO CONFLICT)
============================= */
.admin-ui {
  --primary: #6366f1;
  --bg-dark: #020617;
  --bg-light: #f8fafc;
  --glass: rgba(255,255,255,0.94);
  --text-dark: #0f172a;
  --text-muted: #64748b;

  font-family: 'Inter', system-ui, sans-serif;
  min-height: 100vh;
}

/* =============================
   LAYOUT
============================= */
.admin-auth {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 1.1fr 1fr;
}

/* =============================
   LEFT BRAND
============================= */
.admin-auth__visual {
  padding: 90px;
  color: #fff;
  background:
    radial-gradient(circle at top left, #6366f1, transparent 45%),
    radial-gradient(circle at bottom right, #22c55e, transparent 45%),
    var(--bg-dark);
}

.admin-auth__logo {
  width: 64px;
  height: 64px;
  border-radius: 18px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 26px;
  margin-bottom: 28px;
}

.admin-auth__visual h1 {
  font-size: 36px;
  font-weight: 700;
}

.admin-auth__visual p {
  font-size: 15px;
  color: rgba(255,255,255,0.75);
}

/* =============================
   RIGHT FORM
============================= */
.admin-auth__form-wrap {
  background: var(--bg-light);
  display: flex;
  align-items: center;
  justify-content: center;
}

.admin-login {
  background: var(--glass);
  backdrop-filter: blur(24px);
  border-radius: 30px;
  padding: 56px;
  width: 100%;
  max-width: 440px;
  box-shadow: 0 40px 90px rgba(2,6,23,0.25);
}

/* =============================
   FORM HEADER
============================= */
.admin-login__badge {
  font-size: 11px;
  font-weight: 600;
  padding: 6px 12px;
  border-radius: 999px;
  background: rgba(99,102,241,0.12);
  color: var(--primary);
}

.admin-login h2 {
  font-size: 26px;
  font-weight: 700;
  margin-top: 14px;
}

.admin-login p {
  font-size: 14px;
  color: var(--text-muted);
}

/* =============================
   INPUTS
============================= */
.admin-login__field {
  position: relative;
}

.admin-login__field i {
  position: absolute;
  left: 18px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
}

.admin-login__field input {
  height: 54px;
  padding-left: 48px;
  padding-right: 48px;
  border-radius: 16px;
  border: 1px solid #e5e7eb;
}

.admin-login__field input:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 4px rgba(99,102,241,0.25);
}

/* Eye icon */
.admin-login__eye {
  position: absolute;
  right: 18px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  color: #94a3b8;
}

.admin-login__eye:hover {
  color: var(--primary);
}

/* =============================
   BUTTON
============================= */
.admin-login__btn {
  height: 54px;
  border-radius: 16px;
  font-weight: 600;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff;
  border: none;
}

.admin-login__btn:hover {
  box-shadow: 0 18px 40px rgba(99,102,241,0.5);
}

/* =============================
   FOOTER
============================= */
.admin-login__footer {
  margin-top: 28px;
  text-align: center;
  font-size: 12px;
  color: #94a3b8;
}

/* =============================
   RESPONSIVE
============================= */
@media (max-width: 991px) {
  .admin-auth {
    grid-template-columns: 1fr;
  }
  .admin-auth__visual {
    display: none;
  }
  .admin-login {
    padding: 36px 24px;
    border-radius: 22px;
  }
}
</style>
</head>

<body>

<div class="admin-ui">
  <div class="admin-auth">

    <!-- LEFT -->
    <div class="admin-auth__visual">
      <div class="admin-auth__logo">
        <i class="bi bi-box-seam"></i>
      </div>
      <h1>FMCG Admin Platform</h1>
      <p>
        Manage products, inventory, sales, delivery,
        attendance and performance from one place.
      </p>
    </div>

    <!-- RIGHT -->
    <div class="admin-auth__form-wrap">
      <div class="admin-login">

        <span class="admin-login__badge">
          <i class="bi bi-shield-lock me-1"></i> Admin Access
        </span>

        <h2>Welcome Back</h2>
        <p class="mb-4">Sign in to continue to dashboard</p>

      <form method="POST" action="/login">
            @csrf
  

          <!-- Username -->
          <div class="admin-login__field mb-3">
            <i class="bi bi-person"></i>
            <input 
  type="email" 
  name="email" 
  class="form-control" 
  placeholder="Email Address" 
  required
>

          </div>

          <!-- Password -->
          <div class="admin-login__field mb-4">
            <i class="bi bi-lock"></i>
           <input 
  type="password" 
  name="password"
  id="password" 
  class="form-control" 
  placeholder="Password" 
  required
>

            <span class="admin-login__eye" id="togglePassword">              
            </span>
          </div>

          <button type="submit" class="btn admin-login__btn w-100">
            Login to Dashboard <i class="bi bi-arrow-right-short ms-1"></i>
          </button>
        </form>

        <div class="admin-login__footer">
          Secure Internal System • © 2026 FMCG Corp
        </div>

      </div>
    </div>

  </div>
</div>


</body>
</html>
