<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Vetra</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --teal:#0bbfb0; --teal-dark:#0d9488; --teal-deeper:#0f766e;
            --coral:#f97316; --coral-dark:#ea580c;
            --gray-50:#f8fafc; --gray-100:#f1f5f9; --gray-200:#e2e8f0;
            --gray-300:#cbd5e1; --gray-400:#94a3b8; --gray-500:#64748b;
            --gray-600:#475569; --gray-700:#334155; --gray-800:#1e293b;
            --red:#ef4444; --red-pale:#fef2f2;
        }
        html, body { height:100%; font-family:'Plus Jakarta Sans',sans-serif; background:#dde3ec; }
        body { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:40px 24px; }

        /* ── CARD ── */
        .auth-wrap {
            display:grid; grid-template-columns:1fr 1fr;
            width:100%; max-width:960px;
            border-radius:20px; overflow:hidden;
            box-shadow:0 20px 60px rgba(0,0,0,.18);
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            position:relative;
            background:linear-gradient(160deg, var(--teal) 0%, var(--teal-dark) 100%);
            display:flex; flex-direction:column;
            padding:40px 44px 0; overflow:hidden; min-height:480px;
        }
        .left-panel::before {
            content:'\f1b0'; font-family:'Font Awesome 6 Free'; font-weight:900;
            position:absolute; top:10px; right:24px;
            font-size:160px; color:rgba(255,255,255,.08);
            transform:rotate(15deg); pointer-events:none; z-index:0;
        }

        .lp-brand {
            display:flex; align-items:center; gap:10px;
            font-size:22px; font-weight:800; color:#fff;
            text-decoration:none; position:relative; z-index:1; margin-bottom:32px;
        }
        .lp-brand i { font-size:22px; }

        .lp-text { position:relative; z-index:1; }
        .lp-text h2 { font-size:32px; font-weight:800; color:#fff; line-height:1.2; margin-bottom:10px; }
        .lp-text p  { font-size:15px; color:rgba(255,255,255,.85); line-height:1.7; max-width:260px; }

        /* floating icons — dihapus */

        /* image */
        .lp-img-wrap {
            position:absolute; bottom:0; left:0; right:0;
            display:flex; align-items:flex-end; justify-content:center;
            z-index:2; pointer-events:none;
        }
        .lp-img-wrap img { width:100%; max-height:300px; object-fit:contain; object-position:bottom center; display:block; }

        /* wave */
        .lp-wave { position:absolute; bottom:0; left:0; right:0; height:64px; overflow:hidden; z-index:1; }
        .lp-wave svg { width:100%; height:100%; }

        /* ── RIGHT PANEL ── */
        .right-panel {
            background:#fff; padding:44px 52px;
            display:flex; flex-direction:column; justify-content:center;
        }

        .back-link {
            display:inline-flex; align-items:center; gap:6px;
            font-size:13px; color:var(--gray-400); text-decoration:none;
            margin-bottom:22px; transition:color .2s;
        }
        .back-link:hover { color:var(--teal-dark); }

        .form-title { font-size:32px; font-weight:800; color:var(--gray-800); margin-bottom:4px; }
        .form-sub   { font-size:15px; color:var(--gray-500); margin-bottom:28px; }

        /* alert */
        .alert-error {
            display:none; background:var(--red-pale);
            border:1.5px solid rgba(239,68,68,.25); color:var(--red);
            border-radius:10px; padding:12px 15px; font-size:14px;
            line-height:1.6; margin-bottom:18px; align-items:flex-start; gap:8px;
        }
        .alert-error i { flex-shrink:0; margin-top:1px; }

        /* form */
        .form-group { margin-bottom:18px; }
        .form-group label { display:block; font-size:14px; font-weight:700; color:var(--gray-700); margin-bottom:7px; }
        .input-wrap { position:relative; }
        .input-icon { position:absolute; left:15px; top:50%; transform:translateY(-50%); color:var(--gray-400); font-size:15px; pointer-events:none; }
        .input-wrap input {
            width:100%; padding:14px 18px 14px 44px;
            border:1.5px solid var(--gray-200); border-radius:10px;
            font-size:15px; font-family:inherit; color:var(--gray-800);
            background:#fff; outline:none; transition:all .2s;
        }
        .input-wrap input:focus { border-color:var(--teal-dark); box-shadow:0 0 0 3px rgba(13,148,136,.1); }
        .input-wrap input::placeholder { color:var(--gray-400); }
        .pw-toggle {
            position:absolute; right:14px; top:50%; transform:translateY(-50%);
            background:none; border:none; cursor:pointer;
            color:var(--gray-400); font-size:16px; padding:4px; transition:color .2s;
        }
        .pw-toggle:hover { color:var(--teal-dark); }
        .input-wrap.has-toggle input { padding-right:46px; }

        /* remember + forgot */
        .form-row-2 { display:flex; align-items:center; justify-content:space-between; margin-bottom:22px; }
        .remember-row { display:flex; align-items:center; gap:8px; }
        .remember-row input[type="checkbox"] { width:17px; height:17px; accent-color:var(--teal-dark); cursor:pointer; }
        .remember-row label { font-size:14px; color:var(--gray-600); cursor:pointer; }
        .forgot { font-size:14px; color:var(--teal-dark); font-weight:600; text-decoration:none; }
        .forgot:hover { text-decoration:underline; }

        /* submit */
        .btn-submit {
            width:100%; padding:15px;
            background:var(--coral); color:#fff; border:none; border-radius:10px;
            font-size:17px; font-weight:700; font-family:inherit; cursor:pointer;
            transition:all .25s; display:flex; align-items:center; justify-content:center; gap:8px;
            box-shadow:0 4px 16px rgba(249,115,22,.35);
        }
        .btn-submit:hover:not(:disabled) { background:var(--coral-dark); transform:translateY(-1px); box-shadow:0 8px 24px rgba(249,115,22,.4); }
        .btn-submit:disabled { opacity:.65; cursor:not-allowed; transform:none; }

        .auth-footer { text-align:center; margin-top:16px; font-size:15px; color:var(--gray-500); }
        .auth-footer a { color:var(--teal-dark); font-weight:700; text-decoration:none; }
        .auth-footer a:hover { text-decoration:underline; }

        /* ── MODAL LUPA PASSWORD ── */
        .modal-overlay {
            display:none; position:fixed; inset:0; z-index:999;
            background:rgba(15,23,42,.5); backdrop-filter:blur(4px);
            align-items:center; justify-content:center; padding:20px;
        }
        .modal-overlay.open { display:flex; }
        .modal-box {
            background:#fff; border-radius:18px; padding:36px 40px;
            width:100%; max-width:420px;
            box-shadow:0 20px 60px rgba(0,0,0,.2); position:relative;
        }
        .modal-close {
            position:absolute; top:14px; right:16px;
            background:none; border:none; font-size:20px;
            color:var(--gray-400); cursor:pointer; transition:color .2s;
        }
        .modal-close:hover { color:var(--gray-800); }
        .modal-box h3 { font-size:22px; font-weight:800; color:var(--gray-800); margin-bottom:6px; }
        .modal-box .modal-sub { font-size:14px; color:var(--gray-500); margin-bottom:24px; line-height:1.6; }
        .modal-alert {
            display:none; border-radius:10px; padding:11px 14px;
            font-size:13px; margin-bottom:16px; align-items:flex-start; gap:8px;
        }
        .modal-alert.success { background:#f0fdf4; border:1.5px solid rgba(34,197,94,.25); color:#16a34a; }
        .modal-alert.error   { background:var(--red-pale); border:1.5px solid rgba(239,68,68,.25); color:var(--red); }

        /* responsive */
        @media (max-width:700px) {
            body { padding:0; align-items:stretch; }
            .auth-wrap { grid-template-columns:1fr; border-radius:0; box-shadow:none; min-height:100vh; }
            .left-panel { min-height:220px; padding:28px 28px 0; }
            .lp-text h2 { font-size:22px; }
            .fi-plus, .fi-heart { display:none; }
            .right-panel { padding:32px 24px 44px; }
            .form-title { font-size:26px; }
        }
    </style>
</head>
<body>

<div class="auth-wrap">
    <!-- LEFT -->
    <div class="left-panel">
        <a href="/" class="lp-brand"><i class="fa-solid fa-paw"></i> Vetra</a>
        <div class="lp-text">
            <h2>Selamat Datang di Vetra</h2>
            <p>Aplikasi Veterinary &amp; Pet Care untuk hewan peliharaan yang lebih sehat dan bahagia.</p>
        </div>
        <div class="lp-img-wrap">
            <img src="{{ asset('images/gambar login.png') }}" alt="Vetra Pet">
        </div>
        <div class="lp-wave">
            <svg viewBox="0 0 500 64" preserveAspectRatio="none">
                <path d="M0,32 C80,64 180,0 280,32 C380,64 440,16 500,32 L500,64 L0,64 Z" fill="rgba(255,255,255,.09)"/>
                <path d="M0,44 C100,24 200,60 320,42 C420,26 460,52 500,44 L500,64 L0,64 Z" fill="rgba(255,255,255,.06)"/>
            </svg>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="right-panel">
        <a href="/" class="back-link"><i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda</a>
        <div class="form-title">Login</div>
        <div class="form-sub">Masuk ke akun Vetra Anda</div>

        <div class="alert-error" id="error-msg" style="display:none;">
            <i class="fa-solid fa-circle-exclamation"></i><span id="error-text"></span>
        </div>

        <form id="login-form" autocomplete="on">
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrap">
                    <i class="input-icon fa-regular fa-envelope"></i>
                    <input type="email" id="email" name="email" required placeholder="Enter your email" autocomplete="email">
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap has-toggle">
                    <i class="input-icon fa-solid fa-lock"></i>
                    <input type="password" id="password" name="password" required placeholder="Enter your password" autocomplete="current-password">
                    <button type="button" class="pw-toggle" onclick="togglePw('password',this)" tabindex="-1">
                        <i class="fa-regular fa-eye-slash"></i>
                    </button>
                </div>
            </div>
            <div class="form-row-2">
                <div class="remember-row">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember Me</label>
                </div>
                <a href="#" class="forgot" onclick="openForgot(event)">Lupa Password?</a>
            </div>
            <button type="submit" class="btn-submit" id="login-btn">Login</button>
        </form>

        <div class="auth-footer">Belum punya akun? <a href="/register">Daftar</a></div>
    </div>
</div>

<!-- MODAL LUPA PASSWORD -->
<div class="modal-overlay" id="forgotModal" onclick="closeOnOverlay(event)">
    <div class="modal-box">
        <button class="modal-close" onclick="closeForgot()"><i class="fa-solid fa-xmark"></i></button>
        <h3>Lupa Password?</h3>
        <p class="modal-sub">Masukkan email Anda dan kami akan mengirimkan instruksi untuk mereset password.</p>
        <div class="modal-alert" id="forgot-alert">
            <i id="forgot-alert-icon" class="fa-solid fa-circle-check"></i>
            <span id="forgot-alert-text"></span>
        </div>
        <form id="forgot-form">
            <div class="form-group">
                <label for="forgot-email">Email</label>
                <div class="input-wrap">
                    <i class="input-icon fa-regular fa-envelope"></i>
                    <input type="email" id="forgot-email" required placeholder="Masukkan email Anda">
                </div>
            </div>
            <button type="submit" class="btn-submit" id="forgot-btn" style="margin-top:4px;">
                Kirim Instruksi Reset
            </button>
        </form>
        <div style="text-align:center;margin-top:14px;">
            <a href="#" onclick="closeForgot()" style="font-size:14px;color:var(--gray-400);text-decoration:none;">
                ← Kembali ke Login
            </a>
        </div>
    </div>
</div>

<script>
    // Redirect if already logged in
    const existingToken = localStorage.getItem('vetra_token');
    const existingUser = localStorage.getItem('vetra_user');
    
    if (existingToken && existingUser) {
        try {
            const user = JSON.parse(existingUser);
            const role = user.role;
            
            let redirectUrl = '/';
            if (role === 'doctor') {
                redirectUrl = '/doctor/dashboard';
            } else if (role === 'clinic') {
                redirectUrl = '/clinic/dashboard';
            } else if (role === 'admin') {
                redirectUrl = '/admin/dashboard';
            }
            
            window.location.href = redirectUrl;
        } catch (e) {
            // Invalid data, clear and stay on login
            localStorage.clear();
        }
    }

    function togglePw(id, btn) {
        const input = document.getElementById(id);
        const icon  = btn.querySelector('i');
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'text' ? 'fa-regular fa-eye' : 'fa-regular fa-eye-slash';
    }

    /* ── LOGIN ── */
    document.getElementById('login-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn     = document.getElementById('login-btn');
        const errBox  = document.getElementById('error-msg');
        const errText = document.getElementById('error-text');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
        btn.disabled  = true;
        errBox.style.display = 'none';
        try {
            const res  = await fetch('/api/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    email:    document.getElementById('email').value,
                    password: document.getElementById('password').value
                })
            });
            const data = await res.json();
            if (res.ok) {
                localStorage.setItem('vetra_token', data.access_token);
                localStorage.setItem('vetra_user', JSON.stringify(data.user));
                btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Berhasil!';
                btn.style.background = '#16a34a';
                
                // Redirect based on role
                const role = data.user.role;
                let redirectUrl = '/';
                
                if (role === 'doctor') {
                    redirectUrl = '/doctor/dashboard';
                } else if (role === 'clinic') {
                    redirectUrl = '/clinic/dashboard';
                } else if (role === 'admin') {
                    redirectUrl = '/admin/dashboard';
                } else {
                    redirectUrl = '/'; // user or guest
                }
                
                window.location.href = redirectUrl;
            } else {
                errText.textContent = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : (data.message || 'Email atau password salah.');
                errBox.style.display = 'flex';
                btn.innerHTML = 'Login';
                btn.disabled  = false;
            }
        } catch {
            errText.textContent = 'Gagal terhubung ke server. Periksa koneksi Anda.';
            errBox.style.display = 'flex';
            btn.innerHTML = 'Login';
            btn.disabled  = false;
        }
    });

    /* ── MODAL LUPA PASSWORD ── */
    function openForgot(e) {
        e.preventDefault();
        document.getElementById('forgotModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeForgot() {
        document.getElementById('forgotModal').classList.remove('open');
        document.body.style.overflow = '';
    }
    function closeOnOverlay(e) {
        if (e.target === document.getElementById('forgotModal')) closeForgot();
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeForgot(); });

    document.getElementById('forgot-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn       = document.getElementById('forgot-btn');
        const alertEl   = document.getElementById('forgot-alert');
        const alertText = document.getElementById('forgot-alert-text');
        const alertIcon = document.getElementById('forgot-alert-icon');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mengirim...';
        btn.disabled  = true;
        alertEl.style.display = 'none';
        try {
            const res  = await fetch('/api/auth/forgot-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email: document.getElementById('forgot-email').value })
            });
            const data = await res.json();
            if (res.ok) {
                alertEl.className = 'modal-alert success';
                alertIcon.className = 'fa-solid fa-circle-check';
                alertText.textContent = data.message || 'Instruksi reset password telah dikirim ke email Anda.';
            } else {
                alertEl.className = 'modal-alert error';
                alertIcon.className = 'fa-solid fa-circle-exclamation';
                alertText.textContent = data.message || 'Email tidak ditemukan.';
            }
        } catch {
            alertEl.className = 'modal-alert error';
            alertIcon.className = 'fa-solid fa-circle-exclamation';
            alertText.textContent = 'Gagal terhubung ke server.';
        }
        alertEl.style.display = 'flex';
        btn.innerHTML = 'Kirim Instruksi Reset';
        btn.disabled  = false;
    });
</script>
</body>
</html>
