<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar — Vetra</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --teal:#0bbfb0; --teal-dark:#0d9488; --teal-deeper:#0f766e;
            --teal-pale:#ccfbf1; --teal-50:#f0fdfa;
            --coral:#f97316; --coral-dark:#ea580c;
            --gray-50:#f8fafc; --gray-100:#f1f5f9; --gray-200:#e2e8f0;
            --gray-300:#cbd5e1; --gray-400:#94a3b8; --gray-500:#64748b;
            --gray-600:#475569; --gray-700:#334155; --gray-800:#1e293b;
            --red:#ef4444; --red-pale:#fef2f2; --green:#16a34a;
        }
        html, body { height:100%; font-family:'Plus Jakarta Sans',sans-serif; background:#dde3ec; }
        body { min-height:100vh; display:flex; align-items:center; justify-content:center; padding:40px 24px; }

        /* CARD */
        .auth-wrap {
            display:grid; grid-template-columns:1fr 1fr;
            width:100%; max-width:960px;
            border-radius:20px; overflow:hidden;
            box-shadow:0 20px 60px rgba(0,0,0,.18);
        }

        /* LEFT */
        .left-panel {
            position:relative;
            background:linear-gradient(160deg, var(--teal) 0%, var(--teal-dark) 100%);
            display:flex; flex-direction:column;
            padding:40px 44px 0; overflow:hidden; min-height:560px;
        }
        .left-panel::before {
            content:'\f1b0'; font-family:'Font Awesome 6 Free'; font-weight:900;
            position:absolute; top:10px; right:20px;
            font-size:150px; color:rgba(255,255,255,.08);
            transform:rotate(15deg); pointer-events:none; z-index:0;
        }

        .lp-brand {
            display:flex; align-items:center; gap:10px;
            font-size:22px; font-weight:800; color:#fff;
            text-decoration:none; position:relative; z-index:1; margin-bottom:28px;
        }
        .lp-brand i { font-size:22px; }

        .lp-text { position:relative; z-index:1; }
        .lp-text h2 { font-size:30px; font-weight:800; color:#fff; line-height:1.2; margin-bottom:10px; }
        .lp-text p  { font-size:14px; color:rgba(255,255,255,.85); line-height:1.7; margin-bottom:22px; max-width:260px; }

        .feature-list { display:flex; flex-direction:column; gap:11px; position:relative; z-index:1; }
        .feature-item { display:flex; align-items:center; gap:12px; font-size:14px; color:rgba(255,255,255,.88); font-weight:500; }
        .fi-dot { width:30px; height:30px; border-radius:8px; background:rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0; }

        /* image — di belakang teks */
        .lp-img-wrap {
            position:absolute; bottom:0; left:0; right:0;
            display:flex; align-items:flex-end; justify-content:center;
            z-index:0; pointer-events:none;
        }
        .lp-img-wrap img {
            width:100%; max-height:300px;
            object-fit:contain; object-position:bottom center; display:block;
            opacity:.55;
        }

        /* wave */
        .lp-wave { position:absolute; bottom:0; left:0; right:0; height:64px; overflow:hidden; z-index:1; }
        .lp-wave svg { width:100%; height:100%; }

        /* RIGHT */
        .right-panel {
            background:#fff; padding:36px 48px;
            display:flex; flex-direction:column; justify-content:center; overflow-y:auto;
        }

        .back-link {
            display:inline-flex; align-items:center; gap:6px;
            font-size:13px; color:var(--gray-400); text-decoration:none;
            margin-bottom:16px; transition:color .2s;
        }
        .back-link:hover { color:var(--teal-dark); }

        .form-title { font-size:30px; font-weight:800; color:var(--gray-800); margin-bottom:3px; }
        .form-sub   { font-size:14px; color:var(--gray-500); margin-bottom:20px; }
        .form-sub a { color:var(--teal-dark); font-weight:700; text-decoration:none; }
        .form-sub a:hover { text-decoration:underline; }

        /* alerts */
        .alert-error, .alert-success {
            display:none; border-radius:10px; padding:11px 14px;
            font-size:13px; line-height:1.6; margin-bottom:14px;
            align-items:flex-start; gap:8px;
        }
        .alert-error   { background:var(--red-pale); border:1.5px solid rgba(239,68,68,.25); color:var(--red); }
        .alert-success { background:#f0fdf4; border:1.5px solid rgba(34,197,94,.25); color:var(--green); }
        .alert-error i, .alert-success i { flex-shrink:0; margin-top:1px; }

        /* form */
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .form-group { margin-bottom:14px; }
        .form-group label { display:block; font-size:14px; font-weight:700; color:var(--gray-700); margin-bottom:6px; }
        .input-wrap { position:relative; }
        .input-icon { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--gray-400); font-size:15px; pointer-events:none; }
        .input-wrap input {
            width:100%; padding:13px 16px 13px 42px;
            border:1.5px solid var(--gray-200); border-radius:10px;
            font-size:15px; font-family:inherit; color:var(--gray-800);
            background:#fff; outline:none; transition:all .2s;
        }
        .input-wrap input:focus { border-color:var(--teal-dark); box-shadow:0 0 0 3px rgba(13,148,136,.1); }
        .input-wrap input::placeholder { color:var(--gray-400); }
        .pw-toggle { position:absolute; right:13px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--gray-400); font-size:16px; padding:4px; transition:color .2s; }
        .pw-toggle:hover { color:var(--teal-dark); }
        .input-wrap.has-toggle input { padding-right:44px; }
        .field-error { font-size:12px; color:var(--red); margin-top:4px; display:none; }

        /* submit */
        .btn-submit {
            width:100%; padding:14px;
            background:var(--coral); color:#fff; border:none; border-radius:10px;
            font-size:17px; font-weight:700; font-family:inherit; cursor:pointer;
            transition:all .25s; display:flex; align-items:center; justify-content:center; gap:8px;
            box-shadow:0 4px 16px rgba(249,115,22,.35);
        }
        .btn-submit:hover:not(:disabled) { background:var(--coral-dark); transform:translateY(-1px); box-shadow:0 8px 24px rgba(249,115,22,.4); }
        .btn-submit:disabled { opacity:.65; cursor:not-allowed; transform:none; }

        .auth-footer { text-align:center; margin-top:14px; font-size:14px; color:var(--gray-500); }
        .auth-footer a { color:var(--teal-dark); font-weight:700; text-decoration:none; }
        .auth-footer a:hover { text-decoration:underline; }

        @media (max-width:700px) {
            body { padding:0; align-items:stretch; }
            .auth-wrap { grid-template-columns:1fr; border-radius:0; box-shadow:none; }
            .left-panel { min-height:200px; padding:28px 28px 0; }
            .lp-text h2 { font-size:22px; } .feature-list { display:none; }
            .right-panel { padding:28px 22px 44px; }
            .form-row { grid-template-columns:1fr; }
            .form-title { font-size:24px; }
        }
    </style>
</head>
<body>
<div class="auth-wrap">

    <!-- LEFT -->
    <div class="left-panel">
        <a href="/" class="lp-brand"><i class="fa-solid fa-paw"></i> Vetra</a>
        <div class="lp-text">
            <h2>Buat Akun Baru</h2>
            <p>Gabung dengan Vetra dan kelola kesehatan hewan peliharaan Anda dengan mudah.</p>
        </div>
        <div class="feature-list">
            <div class="feature-item"><div class="fi-dot"><i class="fa-solid fa-calendar-check"></i></div> Booking janji temu dengan dokter hewan</div>
            <div class="feature-item"><div class="fi-dot"><i class="fa-solid fa-notes-medical"></i></div> Simpan riwayat kesehatan hewan</div>
            <div class="feature-item"><div class="fi-dot"><i class="fa-solid fa-bell"></i></div> Dapatkan pengingat vaksin &amp; jadwal</div>
            <div class="feature-item"><div class="fi-dot"><i class="fa-solid fa-comments"></i></div> Konsultasi online kapan saja</div>
        </div>
        <div class="lp-img-wrap">
            <img src="{{ asset('images/gambar daftar.png') }}" alt="Vetra Register">
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
        <div class="form-title">Daftar</div>
        <div class="form-sub">Buat akun Vetra baru</div>

        <div class="alert-error" id="error-msg" style="display:none;">
            <i class="fa-solid fa-circle-exclamation"></i><span id="error-text"></span>
        </div>
        <div class="alert-success" id="success-msg" style="display:none;">
            <i class="fa-solid fa-circle-check"></i><span id="success-text"></span>
        </div>

        <form id="register-form" autocomplete="on">
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <div class="input-wrap">
                    <i class="input-icon fa-regular fa-user"></i>
                    <input type="text" id="name" name="name" required placeholder="Masukkan nama lengkap Anda" autocomplete="name">
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrap">
                    <i class="input-icon fa-regular fa-envelope"></i>
                    <input type="email" id="email" name="email" required placeholder="Enter your email" autocomplete="email">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap has-toggle">
                        <i class="input-icon fa-solid fa-lock"></i>
                        <input type="password" id="password" name="password" required placeholder="Minimal 6 karakter" minlength="6" autocomplete="new-password">
                        <button type="button" class="pw-toggle" onclick="togglePw('password',this)" tabindex="-1"><i class="fa-regular fa-eye-slash"></i></button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password_confirm">Konfirmasi Password</label>
                    <div class="input-wrap has-toggle">
                        <i class="input-icon fa-solid fa-lock"></i>
                        <input type="password" id="password_confirm" name="password_confirm" required placeholder="Ulangi password Anda" autocomplete="new-password">
                        <button type="button" class="pw-toggle" onclick="togglePw('password_confirm',this)" tabindex="-1"><i class="fa-regular fa-eye-slash"></i></button>
                    </div>
                    <span class="field-error" id="pw-match-err">Password tidak cocok.</span>
                </div>
            </div>
            <button type="submit" class="btn-submit" id="register-btn">Daftar</button>
        </form>

        <div class="auth-footer">Sudah punya akun? <a href="/login">Login</a></div>
    </div>
</div>

<script>
    if (localStorage.getItem('vetra_token')) {
        const user = JSON.parse(localStorage.getItem('vetra_user'));
        const role = user?.role;
        
        if (role === 'doctor') {
            window.location.href = '/doctor/dashboard';
        } else if (role === 'clinic') {
            window.location.href = '/clinic/dashboard';
        } else if (role === 'admin') {
            window.location.href = '/admin/dashboard';
        } else {
            window.location.href = '/';
        }
    }

    function togglePw(id, btn) {
        const input = document.getElementById(id);
        const icon  = btn.querySelector('i');
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'text' ? 'fa-regular fa-eye' : 'fa-regular fa-eye-slash';
    }

    /* live password match */
    document.getElementById('password_confirm').addEventListener('input', function() {
        const err = document.getElementById('pw-match-err');
        err.style.display = (this.value && this.value !== document.getElementById('password').value) ? 'block' : 'none';
    });

    document.getElementById('register-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const pw  = document.getElementById('password').value;
        const pw2 = document.getElementById('password_confirm').value;
        const btn    = document.getElementById('register-btn');
        const errBox = document.getElementById('error-msg');
        const errTxt = document.getElementById('error-text');
        const sucBox = document.getElementById('success-msg');
        const sucTxt = document.getElementById('success-text');

        if (pw !== pw2) {
            errTxt.textContent = 'Konfirmasi password tidak cocok.';
            errBox.style.display = 'flex'; return;
        }

        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
        btn.disabled  = true;
        errBox.style.display = sucBox.style.display = 'none';

        try {
            const res  = await fetch('/api/auth/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    name:     document.getElementById('name').value,
                    email:    document.getElementById('email').value,
                    password: pw,
                    role:     'user'
                })
            });
            const data = await res.json();
            if (res.ok) {
                localStorage.setItem('vetra_token', data.access_token);
                localStorage.setItem('vetra_user', JSON.stringify(data.data));
                sucTxt.textContent = 'Akun berhasil dibuat! Mengalihkan...';
                sucBox.style.display = 'flex';
                btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Berhasil!';
                btn.style.background = '#16a34a';
                
                const role = data.data.role;
                let redirectUrl = '/';
                if (role === 'doctor') redirectUrl = '/doctor/dashboard';
                else if (role === 'clinic') redirectUrl = '/clinic/dashboard';
                else if (role === 'admin') redirectUrl = '/admin/dashboard';
                
                setTimeout(() => { window.location.href = redirectUrl; }, 900);
            } else {
                errTxt.textContent = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : (data.message || 'Registrasi gagal. Periksa kembali data Anda.');
                errBox.style.display = 'flex';
                btn.innerHTML = 'Daftar';
                btn.disabled  = false;
            }
        } catch {
            errTxt.textContent = 'Gagal terhubung ke server.';
            errBox.style.display = 'flex';
            btn.innerHTML = 'Daftar';
            btn.disabled  = false;
        }
    });
</script>
</body>
</html>
