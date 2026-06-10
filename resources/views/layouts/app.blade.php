<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Vetra') — Platform Kesehatan Hewan Peliharaan</title>
    <meta name="description" content="@yield('meta_desc', 'Vetra: konsultasi dokter hewan online, booking klinik, artikel kesehatan hewan.')">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --teal:       #0d9488;
            --teal-light: #14b8a6;
            --teal-dark:  #0f766e;
            --teal-pale:  #ccfbf1;
            --teal-50:    #f0fdfa;
            --blue:       #3b82f6;
            --blue-soft:  #eff6ff;
            --coral:      #f97316;
            --coral-dark: #ea580c;
            --white:      #ffffff;
            --gray-50:    #f8fafc;
            --gray-100:   #f1f5f9;
            --gray-200:   #e2e8f0;
            --gray-300:   #cbd5e1;
            --gray-400:   #94a3b8;
            --gray-500:   #64748b;
            --gray-600:   #475569;
            --gray-700:   #334155;
            --gray-800:   #1e293b;
            --gray-900:   #0f172a;
            --radius-sm:  10px;
            --radius:     16px;
            --radius-lg:  24px;
            --shadow:     0 4px 24px rgba(13,148,136,.10);
            --shadow-lg:  0 8px 40px rgba(13,148,136,.15);
            --shadow-xl:  0 20px 60px rgba(13,148,136,.18);
        }
        html { scroll-behavior: smooth; }
        body { font-family:'Plus Jakarta Sans',sans-serif; background:var(--white); color:var(--gray-900); overflow-x:hidden; }
        a { text-decoration:none; }
        img { max-width:100%; display:block; }
        ul { list-style:none; }

        /* ===== NAVBAR ===== */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 999;
            background: rgba(255,255,255,.96); backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--gray-200); transition: box-shadow .3s;
        }
        .navbar.scrolled { box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .nav-inner {
            max-width: 1200px; margin: 0 auto; padding: 0 24px;
            height: 68px; display: flex; align-items: center; justify-content: space-between;
        }
        .logo { display: flex; align-items: center; gap: 10px; font-size: 22px; font-weight: 800; color: var(--teal); }
        .logo-icon {
            width: 38px; height: 38px; background: var(--teal); border-radius: 11px;
            display: flex; align-items: center; justify-content: center; color: #fff; font-size: 18px;
        }
        .nav-menu { display: flex; align-items: center; gap: 2px; }
        .nav-menu a {
            color: var(--gray-600); font-weight: 500; font-size: 14px;
            padding: 8px 16px; border-radius: var(--radius-sm); transition: all .2s; position: relative;
        }
        .nav-menu a:hover { background: var(--teal-pale); color: var(--teal); }
        .nav-menu a.active {
            color: var(--teal); font-weight: 700; background: var(--teal-pale);
        }
        .nav-actions { display: flex; align-items: center; gap: 8px; }
        .btn-login {
            padding: 9px 20px; border-radius: 12px; font-weight: 600; font-size: 14px;
            border: 2px solid var(--teal); color: var(--teal); transition: all .2s;
        }
        .btn-login:hover { background: var(--teal); color: #fff; }
        .btn-register {
            padding: 9px 20px; border-radius: 12px; font-weight: 600; font-size: 14px;
            background: var(--teal); color: #fff; transition: all .2s;
        }
        .btn-register:hover { background: var(--teal-dark); transform: translateY(-1px); }
        .hamburger { display: none; background: none; border: none; cursor: pointer; padding: 8px; }
        .hamburger span { display: block; width: 22px; height: 2px; background: var(--gray-700); margin: 5px 0; border-radius: 2px; transition: all .3s; }
        .hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .hamburger.open span:nth-child(2) { opacity: 0; }
        .hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* Mobile Menu */
        .mobile-menu {
            display: none; position: fixed; top: 68px; left: 0; right: 0; z-index: 998;
            background: #fff; border-bottom: 1px solid var(--gray-200);
            padding: 12px 24px 20px; box-shadow: 0 8px 24px rgba(0,0,0,.08);
        }
        .mobile-menu.open { display: block; }
        .mobile-menu a {
            display: block; padding: 13px 0; font-size: 15px; font-weight: 500;
            color: var(--gray-700); border-bottom: 1px solid var(--gray-100);
        }
        .mobile-menu a.active { color: var(--teal); font-weight: 700; }
        .mobile-menu a:last-of-type { border-bottom: none; }
        .mobile-actions { display: flex; gap: 10px; margin-top: 16px; }
        .mobile-actions a {
            flex: 1; text-align: center; padding: 12px; border-radius: 12px;
            font-weight: 600; font-size: 14px; border: none;
        }
        .mobile-actions .m-login { border: 2px solid var(--teal); color: var(--teal); }
        .mobile-actions .m-register { background: var(--teal); color: #fff; }

        /* ===== PAGE WRAPPER ===== */
        .page-content { padding-top: 68px; min-height: calc(100vh - 68px); }

        /* ===== PAGE HEADER ===== */
        .page-header {
            background: linear-gradient(135deg, var(--teal-50) 0%, #e0f2fe 60%, var(--gray-50) 100%);
            padding: 56px 24px 52px; position: relative; overflow: hidden;
        }
        .page-header::before {
            content: ''; position: absolute; top: -80px; right: -80px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(13,148,136,.08) 0%, transparent 70%);
            border-radius: 50%; pointer-events: none;
        }
        .page-header-inner { max-width: 1200px; margin: 0 auto; position: relative; z-index: 1; }
        .page-header-tag {
            display: inline-flex; align-items: center; gap: 7px;
            background: var(--teal-pale); color: var(--teal-dark);
            padding: 6px 14px; border-radius: 100px; font-size: 12px; font-weight: 700;
            letter-spacing: .5px; text-transform: uppercase; margin-bottom: 14px;
            border: 1px solid rgba(13,148,136,.2);
        }
        .page-header h1 { font-size: 40px; font-weight: 800; color: var(--gray-800); margin-bottom: 10px; line-height: 1.2; }
        .page-header p { font-size: 16px; color: var(--gray-500); max-width: 560px; line-height: 1.7; }

        /* ===== SECTION COMMONS ===== */
        .container { max-width: 1200px; margin: 0 auto; }
        section { padding: 64px 24px; }
        .section-tag {
            display: inline-block; color: var(--teal); font-weight: 700; font-size: 12px;
            letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 10px;
        }
        .section-title { font-size: 32px; font-weight: 800; color: var(--gray-800); margin-bottom: 12px; line-height: 1.25; }
        .section-sub { font-size: 15px; color: var(--gray-500); line-height: 1.7; }

        /* ===== FOOTER ===== */
        footer { background: var(--gray-800); color: var(--gray-400); padding: 56px 24px 32px; }
        .footer-inner { max-width: 1200px; margin: 0 auto; }
        .footer-top { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 48px; margin-bottom: 48px; }
        .footer-brand .logo { color: #fff; margin-bottom: 14px; }
        .footer-brand p { font-size: 14px; line-height: 1.7; max-width: 260px; }
        .footer-col h4 { font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 16px; }
        .footer-col ul li { margin-bottom: 10px; }
        .footer-col ul li a { font-size: 14px; color: var(--gray-400); transition: color .2s; }
        .footer-col ul li a:hover { color: var(--teal-light); }
        .footer-bottom {
            border-top: 1px solid var(--gray-700); padding-top: 28px;
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;
        }
        .footer-bottom p { font-size: 13px; }
        .footer-social { display: flex; gap: 10px; }
        .footer-social a {
            width: 36px; height: 36px; border-radius: 10px; background: var(--gray-700);
            display: flex; align-items: center; justify-content: center;
            color: var(--gray-400); font-size: 14px; transition: all .2s;
        }
        .footer-social a:hover { background: var(--teal); color: #fff; }

        /* ===== BUTTONS ===== */
        .btn-primary {
            background: var(--teal); color: #fff; padding: 13px 26px; border-radius: 12px;
            font-weight: 700; font-size: 15px; display: inline-flex; align-items: center; gap: 8px;
            box-shadow: 0 4px 16px rgba(13,148,136,.3); transition: all .25s; border: none; cursor: pointer;
        }
        .btn-primary:hover { background: var(--teal-dark); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(13,148,136,.4); }
        .btn-ghost {
            background: #fff; color: var(--teal); padding: 13px 26px; border-radius: 12px;
            font-weight: 600; font-size: 15px; border: 2px solid var(--teal); transition: all .25s; display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-ghost:hover { background: var(--teal); color: #fff; transform: translateY(-2px); }

        /* ===== EMPTY STATE ===== */
        .empty-state { text-align: center; padding: 72px 24px; color: var(--gray-400); }
        .empty-state i { font-size: 52px; margin-bottom: 18px; opacity: .35; display: block; }
        .empty-state p { font-size: 16px; margin-bottom: 20px; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .footer-top { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .nav-menu, .nav-actions { display: none; }
            .hamburger { display: block; }
            .page-header h1 { font-size: 28px; }
            .section-title { font-size: 24px; }
            .footer-top { grid-template-columns: 1fr; gap: 28px; }
            .footer-bottom { flex-direction: column; text-align: center; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar" id="navbar">
    <div class="nav-inner">
        <a href="{{ route('home') }}" class="logo">
            <div class="logo-icon"><i class="fa-solid fa-paw"></i></div>
            Vetra
        </a>
        <div class="nav-menu" id="navMenu">
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}" id="dashboardLink">
                <i class="fa-solid fa-house" style="margin-right:5px;font-size:12px;"></i>Dashboard
            </a>
            <a href="{{ route('klinik') }}" class="{{ request()->routeIs('klinik') ? 'active' : '' }}">
                <i class="fa-solid fa-hospital" style="margin-right:5px;font-size:12px;"></i>Klinik & Dokter
            </a>
            <a href="{{ route('artikel') }}" class="{{ request()->routeIs('artikel') ? 'active' : '' }}">
                <i class="fa-solid fa-newspaper" style="margin-right:5px;font-size:12px;"></i>Artikel
            </a>
            <a href="{{ route('kontak') }}" class="{{ request()->routeIs('kontak') ? 'active' : '' }}">
                <i class="fa-solid fa-envelope" style="margin-right:5px;font-size:12px;"></i>Kontak
            </a>
        </div>
        <div class="nav-actions" id="navActions">
            <a href="{{ route('login') }}" class="btn-login">Masuk</a>
            <a href="{{ route('register') }}" class="btn-register">Daftar Gratis</a>
        </div>
        <button class="hamburger" id="hamburger" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}" onclick="closeMobile()" id="mobileDashboardLink">Dashboard</a>
    <a href="{{ route('klinik') }}" class="{{ request()->routeIs('klinik') ? 'active' : '' }}" onclick="closeMobile()">Klinik & Dokter</a>
    <a href="{{ route('artikel') }}" class="{{ request()->routeIs('artikel') ? 'active' : '' }}" onclick="closeMobile()">Artikel</a>
    <a href="{{ route('kontak') }}" class="{{ request()->routeIs('kontak') ? 'active' : '' }}" onclick="closeMobile()">Kontak</a>
    <div class="mobile-actions">
        <a href="{{ route('login') }}" class="m-login">Masuk</a>
        <a href="{{ route('register') }}" class="m-register">Daftar</a>
    </div>
</div>

<div class="page-content">
    @yield('content')
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-inner">
        <div class="footer-top">
            <div class="footer-brand">
                <a href="{{ route('home') }}" class="logo" style="color:#fff;">
                    <div class="logo-icon" style="background:rgba(255,255,255,.15);"><i class="fa-solid fa-paw"></i></div>
                    Vetra
                </a>
                <p>Platform kesehatan hewan peliharaan terpercaya di Indonesia. Menghubungkan pemilik hewan dengan dokter dan klinik terbaik.</p>
            </div>
            <div class="footer-col">
                <h4>Navigasi</h4>
                <ul>
                    <li><a href="{{ route('home') }}">Dashboard</a></li>
                    <li><a href="{{ route('klinik') }}">Klinik & Dokter</a></li>
                    <li><a href="{{ route('artikel') }}">Artikel Kesehatan</a></li>
                    <li><a href="{{ route('kontak') }}">Kontak</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Akun</h4>
                <ul>
                    <li><a href="{{ route('login') }}">Masuk</a></li>
                    <li><a href="{{ route('register') }}">Daftar Gratis</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Bantuan</h4>
                <ul>
                    <li><a href="{{ route('kontak') }}">Hubungi Kami</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Kebijakan Privasi</a></li>
                    <li><a href="#">Syarat & Ketentuan</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© {{ date('Y') }} Vetra. Hak cipta dilindungi undang-undang.</p>
            <div class="footer-social">
                <a href="#" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#" title="Twitter/X"><i class="fa-brands fa-x-twitter"></i></a>
                <a href="#" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
            </div>
        </div>
    </div>
</footer>

<script>
    // Navbar scroll
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => navbar.classList.toggle('scrolled', window.scrollY > 20));

    // Hamburger
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('open');
        mobileMenu.classList.toggle('open');
    });
    function closeMobile() {
        hamburger.classList.remove('open');
        mobileMenu.classList.remove('open');
    }

    // Auth state & Dynamic Menu - IMPROVED VERSION
    document.addEventListener('DOMContentLoaded', function() {
        const token = localStorage.getItem('vetra_token');
        const userStr = localStorage.getItem('vetra_user');
        
        if (token && userStr) {
            try {
                const user = JSON.parse(userStr);
                const navMenu = document.querySelector('.nav-menu');
                const navActions = document.getElementById('navActions');
                const mobileMenu = document.getElementById('mobileMenu');
                
                // Update Dashboard link based on role
                const dashboardLink = document.getElementById('dashboardLink');
                const mobileDashboardLink = document.getElementById('mobileDashboardLink');
                
                let dashboardUrl = '/';
                let dashboardText = 'Dashboard';
                
                if (user.role === 'user') {
                    dashboardUrl = '/';
                    dashboardText = 'Dashboard';
                } else if (user.role === 'doctor') {
                    dashboardUrl = '/doctor/dashboard';
                    dashboardText = 'Dashboard';
                } else if (user.role === 'clinic') {
                    dashboardUrl = '/clinic/dashboard';
                    dashboardText = 'Dashboard';
                } else if (user.role === 'admin') {
                    dashboardUrl = '/admin/dashboard';
                    dashboardText = 'Dashboard';
                }
                
                if (dashboardLink) {
                    dashboardLink.href = dashboardUrl;
                }
                if (mobileDashboardLink) {
                    mobileDashboardLink.href = dashboardUrl;
                }
                
                // Hide "Klinik & Dokter" menu for doctor, clinic, and admin roles
                if (user.role === 'doctor' || user.role === 'clinic' || user.role === 'admin') {
                    // Find and hide Klinik & Dokter link in desktop menu
                    const klinikLinks = document.querySelectorAll('a[href*="/klinik"]');
                    klinikLinks.forEach(link => {
                        if (link.textContent.includes('Klinik')) {
                            link.style.display = 'none';
                        }
                    });
                }
                
                // Hide "Artikel" and "Kontak" for admin (will be replaced)
                if (user.role === 'admin') {
                    const artikelLinks = document.querySelectorAll('a[href*="/artikel"]');
                    artikelLinks.forEach(link => link.style.display = 'none');
                    
                    const kontakLinks = document.querySelectorAll('a[href*="/kontak"]');
                    kontakLinks.forEach(link => link.style.display = 'none');
                }
                
                // Build menu berdasarkan role
                let additionalMenuItems = '';
                let mobileAdditionalItems = '';
                
                if (user.role === 'user') {
                    // Menu untuk user biasa
                    additionalMenuItems = `
                        <a href="/my-pets" class="${window.location.pathname === '/my-pets' ? 'active' : ''}">
                            <i class="fa-solid fa-dog" style="margin-right:5px;font-size:12px;"></i>Hewan Saya
                        </a>
                        <a href="/my-bookings" class="${window.location.pathname === '/my-bookings' ? 'active' : ''}">
                            <i class="fa-solid fa-calendar-check" style="margin-right:5px;font-size:12px;"></i>Booking Saya
                        </a>
                        <a href="/chatbot" class="${window.location.pathname === '/chatbot' ? 'active' : ''}">
                            <i class="fa-solid fa-robot" style="margin-right:5px;font-size:12px;"></i>AI Chatbot
                        </a>
                        <a href="/chat" class="${window.location.pathname === '/chat' ? 'active' : ''}">
                            <i class="fa-solid fa-comments" style="margin-right:5px;font-size:12px;"></i>Konsultasi
                        </a>
                    `;
                    mobileAdditionalItems = `
                        <a href="/my-pets" class="${window.location.pathname === '/my-pets' ? 'active' : ''}" onclick="closeMobile()">Hewan Saya</a>
                        <a href="/my-bookings" class="${window.location.pathname === '/my-bookings' ? 'active' : ''}" onclick="closeMobile()">Booking Saya</a>
                        <a href="/chatbot" class="${window.location.pathname === '/chatbot' ? 'active' : ''}" onclick="closeMobile()">AI Chatbot</a>
                        <a href="/chat" class="${window.location.pathname === '/chat' ? 'active' : ''}" onclick="closeMobile()">Konsultasi Online</a>
                    `;
                } else if (user.role === 'doctor') {
                    // Menu untuk dokter
                    additionalMenuItems = `
                        <a href="/doctor/schedules">
                            <i class="fa-solid fa-calendar-alt" style="margin-right:5px;font-size:12px;"></i>Jadwal Pasien
                        </a>
                        <a href="/doctor/chat">
                            <i class="fa-solid fa-comments" style="margin-right:5px;font-size:12px;"></i>Chat Pasien
                        </a>
                    `;
                    mobileAdditionalItems = `
                        <a href="/doctor/schedules" onclick="closeMobile()">Jadwal Pasien</a>
                        <a href="/doctor/chat" onclick="closeMobile()">Chat Pasien</a>
                    `;
                } else if (user.role === 'clinic') {
                    // Menu untuk klinik
                    additionalMenuItems = `
                        <a href="/clinic/bookings">
                            <i class="fa-solid fa-calendar-days" style="margin-right:5px;font-size:12px;"></i>Booking
                        </a>
                        <a href="/clinic/doctors">
                            <i class="fa-solid fa-user-doctor" style="margin-right:5px;font-size:12px;"></i>Dokter Kami
                        </a>
                    `;
                    mobileAdditionalItems = `
                        <a href="/clinic/bookings" onclick="closeMobile()">Booking</a>
                        <a href="/clinic/doctors" onclick="closeMobile()">Dokter Kami</a>
                    `;
                } else if (user.role === 'admin') {
                    // Menu untuk admin
                    additionalMenuItems = `
                        <a href="/admin/users">
                            <i class="fa-solid fa-users" style="margin-right:5px;font-size:12px;"></i>Kelola User
                        </a>
                        <a href="/admin/articles">
                            <i class="fa-solid fa-newspaper" style="margin-right:5px;font-size:12px;"></i>Kelola Artikel
                        </a>
                        <a href="/admin/messages">
                            <i class="fa-solid fa-envelope" style="margin-right:5px;font-size:12px;"></i>Pesan Masuk
                        </a>
                    `;
                    mobileAdditionalItems = `
                        <a href="/admin/users" onclick="closeMobile()">Kelola User</a>
                        <a href="/admin/articles" onclick="closeMobile()">Kelola Artikel</a>
                        <a href="/admin/messages" onclick="closeMobile()">Pesan Masuk</a>
                    `;
                }
                
                // Insert additional menu items ke nav-menu
                if (navMenu && additionalMenuItems) {
                    navMenu.insertAdjacentHTML('beforeend', additionalMenuItems);
                }
                
                // Update nav actions dengan profile & logout
                if (navActions) {
                    const profilePic = user.profile_pic 
                        ? `<img src="${user.profile_pic.startsWith('http') ? user.profile_pic : '/storage/' + user.profile_pic}" alt="${user.name}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid var(--teal);">`
                        : '<i class="fa-solid fa-user-circle" style="font-size:28px;"></i>';
                    
                    // Admin tidak perlu link profil
                    if (user.role === 'admin') {
                        navActions.innerHTML = `
                            <div style="position:relative;display:flex;align-items:center;gap:12px;">
                                <div style="padding:9px 16px;border-radius:10px;font-weight:600;font-size:14px;color:var(--gray-700);display:flex;align-items:center;gap:7px;">
                                    ${profilePic} ${user.name}
                                </div>
                                <button onclick="logout()" style="padding:9px 16px;border-radius:10px;font-weight:600;font-size:14px;background:var(--coral);color:#fff;border:none;cursor:pointer;display:flex;align-items:center;gap:7px;transition:all .2s;font-family:inherit;"
                                        onmouseover="this.style.background='var(--coral-dark)';"
                                        onmouseout="this.style.background='var(--coral)';">
                                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                                </button>
                            </div>`;
                    } else {
                        navActions.innerHTML = `
                            <div style="position:relative;display:flex;align-items:center;gap:12px;">
                                <a href="/profile" style="padding:9px 16px;border-radius:10px;font-weight:600;font-size:14px;color:var(--gray-700);display:flex;align-items:center;gap:7px;transition:all .2s;" 
                                   onmouseover="this.style.background='var(--teal-pale)';this.style.color='var(--teal)';"
                                   onmouseout="this.style.background='transparent';this.style.color='var(--gray-700)';">
                                    ${profilePic} ${user.name}
                                </a>
                                <button onclick="logout()" style="padding:9px 16px;border-radius:10px;font-weight:600;font-size:14px;background:var(--coral);color:#fff;border:none;cursor:pointer;display:flex;align-items:center;gap:7px;transition:all .2s;font-family:inherit;"
                                        onmouseover="this.style.background='var(--coral-dark)';"
                                        onmouseout="this.style.background='var(--coral)';">
                                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                                </button>
                            </div>`;
                    }
                }
                
                // Update mobile menu
                if (mobileMenu && mobileAdditionalItems) {
                    const kontakLink = mobileMenu.querySelector('a[href*="kontak"]');
                    if (kontakLink) {
                        kontakLink.insertAdjacentHTML('afterend', mobileAdditionalItems);
                    }
                    
                    // Hide "Klinik & Dokter" in mobile menu for doctor, clinic, and admin
                    if (user.role === 'doctor' || user.role === 'clinic' || user.role === 'admin') {
                        const mobileKlinikLinks = mobileMenu.querySelectorAll('a[href*="/klinik"]');
                        mobileKlinikLinks.forEach(link => {
                            if (link.textContent.includes('Klinik')) {
                                link.style.display = 'none';
                            }
                        });
                    }
                    
                    // Hide "Artikel" and "Kontak" in mobile menu for admin
                    if (user.role === 'admin') {
                        const mobileArtikelLinks = mobileMenu.querySelectorAll('a[href*="/artikel"]');
                        mobileArtikelLinks.forEach(link => link.style.display = 'none');
                        
                        const mobileKontakLinks = mobileMenu.querySelectorAll('a[href*="/kontak"]');
                        mobileKontakLinks.forEach(link => link.style.display = 'none');
                    }
                    
                    // Update mobile actions
                    const mobileActions = mobileMenu.querySelector('.mobile-actions');
                    if (mobileActions) {
                        const profilePicMobile = user.profile_pic 
                            ? `<img src="${user.profile_pic.startsWith('http') ? user.profile_pic : '/storage/' + user.profile_pic}" alt="${user.name}" style="width:20px;height:20px;border-radius:50%;object-fit:cover;">`
                            : '<i class="fa-solid fa-user-circle"></i>';
                        
                        // Admin tidak perlu link profil
                        if (user.role === 'admin') {
                            mobileActions.innerHTML = `
                                <div style="padding:12px 20px;background:var(--gray-100);border-radius:10px;display:flex;align-items:center;justify-content:center;gap:6px;color:var(--gray-700);font-weight:600;">
                                    ${profilePicMobile} ${user.name}
                                </div>
                                <button onclick="logout()" class="m-register" style="border:none;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:6px;">
                                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                                </button>`;
                        } else {
                            mobileActions.innerHTML = `
                                <a href="/profile" class="m-login" style="display:flex;align-items:center;justify-content:center;gap:6px;">
                                    ${profilePicMobile} Profil
                                </a>
                                <button onclick="logout()" class="m-register" style="border:none;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:6px;">
                                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                                </button>`;
                        }
                    }
                }
            } catch (error) {
                console.error('Error parsing user data:', error);
            }
        }
    });
    
    // Logout function
    // ── Global auth-aware fetch ──────────────────────────────────────────────
    // Wraps fetch() so that a 401 (token expired) is handled transparently:
    //   1. Try to refresh the token via POST /api/auth/refresh
    //   2. If refresh succeeds → update localStorage & retry original request
    //   3. If refresh fails   → clear session & redirect to /login
    let _refreshing = null; // deduplicate concurrent refresh calls

    async function authFetch(url, options = {}) {
        let token = localStorage.getItem('vetra_token');

        // Merge auth header
        const buildOpts = (t) => ({
            ...options,
            headers: {
                'Accept': 'application/json',
                ...(options.headers || {}),
                'Authorization': 'Bearer ' + t,
            },
        });

        let res = await fetch(url, buildOpts(token));

        if (res.status !== 401) return res;

        // ── 401: attempt token refresh ──────────────────────────────────────
        if (!_refreshing) {
            _refreshing = fetch('/api/auth/refresh', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token,
                },
            }).then(async (r) => {
                if (r.ok) {
                    const data = await r.json();
                    const newToken = data.access_token || data.token;
                    if (newToken) {
                        localStorage.setItem('vetra_token', newToken);
                        return newToken;
                    }
                }
                // Refresh failed – force logout
                localStorage.removeItem('vetra_token');
                localStorage.removeItem('vetra_user');
                window.location.href = '/login';
                return null;
            }).finally(() => { _refreshing = null; });
        }

        const newToken = await _refreshing;
        if (!newToken) return new Response(null, { status: 401 });

        // Retry original request with new token
        return fetch(url, buildOpts(newToken));
    }
    // ── End authFetch ────────────────────────────────────────────────────────

    function logout() {
        if (confirm('Yakin ingin keluar?')) {
            const token = localStorage.getItem('vetra_token');
            if (token) {
                fetch('/api/auth/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                }).finally(() => {
                    localStorage.removeItem('vetra_token');
                    localStorage.removeItem('vetra_user');
                    window.location.href = '/';
                });
            } else {
                localStorage.removeItem('vetra_token');
                localStorage.removeItem('vetra_user');
                window.location.href = '/';
            }
        }
    }
</script>
@stack('scripts')
</body>
</html>
