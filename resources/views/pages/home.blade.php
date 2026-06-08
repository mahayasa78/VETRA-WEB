@extends('layouts.app')
@section('title', 'Dashboard')
@section('meta_desc', 'Vetra — Platform kesehatan hewan peliharaan. Konsultasi dokter hewan online, booking klinik, dan edukasi kesehatan hewan.')

@push('styles')
<style>
    /* HERO */
    .hero {
        min-height: calc(100vh - 68px);
        background: linear-gradient(135deg, var(--teal-50) 0%, #e0f2fe 55%, var(--gray-50) 100%);
        display: flex; align-items: center; position: relative; overflow: hidden;
    }
    .hero::before {
        content:''; position:absolute; top:-120px; right:-120px; width:700px; height:700px;
        background:radial-gradient(circle,rgba(13,148,136,.07) 0%,transparent 70%);
        border-radius:50%; pointer-events:none;
    }
    .hero::after {
        content:''; position:absolute; bottom:-80px; left:-80px; width:400px; height:400px;
        background:radial-gradient(circle,rgba(59,130,246,.05) 0%,transparent 70%);
        border-radius:50%; pointer-events:none;
    }
    .hero-inner {
        max-width: 860px; margin: 0 auto; padding: 100px 24px 90px;
        display: flex; flex-direction: column; align-items: center; text-align: center;
        position: relative; z-index: 1;
    }
    .hero-badge {
        display:inline-flex; align-items:center; gap:8px;
        background:var(--teal-pale); color:var(--teal-dark);
        padding:7px 16px; border-radius:100px; font-size:13px; font-weight:600;
        margin-bottom:24px; border:1px solid rgba(13,148,136,.2);
    }
    .badge-dot { width:8px; height:8px; background:var(--teal); border-radius:50%; animation:pulse-dot 2s infinite; }
    @keyframes pulse-dot { 0%,100%{transform:scale(1);opacity:1;} 50%{transform:scale(1.4);opacity:.6;} }
    .hero h1 { font-size:56px; font-weight:800; line-height:1.1; color:var(--gray-800); margin-bottom:22px; }
    .hero h1 em { font-style:normal; color:var(--teal); }
    .hero-desc { font-size:18px; color:var(--gray-600); line-height:1.8; margin-bottom:38px; max-width:640px; }
    .hero-btns { display:flex; gap:14px; flex-wrap:wrap; justify-content:center; margin-bottom:52px; }
    .hero-stats { display:flex; gap:32px; flex-wrap:wrap; justify-content:center; }
    .hero-stat { display:flex; align-items:center; gap:8px; font-size:13px; color:var(--gray-600); font-weight:500; }
    /* feature pills */
    .hero-pills { display:flex; gap:10px; flex-wrap:wrap; justify-content:center; margin-bottom:40px; }
    .hero-pill {
        display:inline-flex; align-items:center; gap:7px;
        background:#fff; border:1.5px solid var(--gray-200); border-radius:100px;
        padding:8px 16px; font-size:13px; font-weight:600; color:var(--gray-700);
        box-shadow:0 2px 8px rgba(0,0,0,.05); transition:all .2s;
    }
    .hero-pill:hover { border-color:var(--teal); color:var(--teal); transform:translateY(-2px); }
    .hero-pill i { font-size:14px; }
</style>
@endpush

@push('styles')
<style>
    /* ABOUT */
    .about-section { background:var(--white); }
    .about-grid { display:grid; grid-template-columns:1fr 1fr; gap:72px; align-items:center; }
    .about-text p { font-size:16px; color:var(--gray-600); line-height:1.8; margin-bottom:16px; }
    .stats-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .stat-card {
        background:var(--gray-50); border-radius:var(--radius); padding:24px;
        border:1px solid var(--gray-200); transition:all .3s;
    }
    .stat-card:hover { border-color:var(--teal-light); box-shadow:var(--shadow); transform:translateY(-3px); }
    .stat-card .num { font-size:36px; font-weight:800; color:var(--teal); line-height:1; }
    .stat-card .lbl { font-size:13px; color:var(--gray-500); margin-top:6px; font-weight:500; }
    .stat-card .icon { font-size:28px; margin-bottom:12px; }
    /* FEATURES */
    .features-section { background:linear-gradient(135deg,var(--teal-50),#eff6ff); }
    .features-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
    .feat-card {
        background:#fff; border-radius:var(--radius-lg); padding:30px;
        border:1px solid var(--gray-200); transition:all .3s;
    }
    .feat-card:hover { transform:translateY(-6px); box-shadow:var(--shadow-lg); border-color:var(--teal-light); }
    .feat-icon {
        width:54px; height:54px; border-radius:14px;
        display:flex; align-items:center; justify-content:center; font-size:24px; margin-bottom:18px;
    }
    .feat-icon.teal { background:var(--teal-pale); color:var(--teal); }
    .feat-icon.blue { background:var(--blue-soft); color:var(--blue); }
    .feat-icon.coral { background:#fff7ed; color:var(--coral); }
    .feat-icon.purple { background:#f5f3ff; color:#7c3aed; }
    .feat-icon.green { background:#f0fdf4; color:#16a34a; }
    .feat-icon.pink { background:#fdf2f8; color:#db2777; }
    .feat-card h3 { font-size:18px; font-weight:700; color:var(--gray-800); margin-bottom:10px; }
    .feat-card p { font-size:14px; color:var(--gray-500); line-height:1.75; }
    /* HOW IT WORKS */
    .how-section { background:var(--white); }
    /* Tab switcher */
    .how-tabs {
        display:flex; gap:0; background:var(--gray-100); border-radius:16px; padding:5px;
        max-width:480px; margin:0 auto 56px;
    }
    .how-tab {
        flex:1; padding:12px 20px; border-radius:12px; border:none; background:transparent;
        font-size:14px; font-weight:600; color:var(--gray-500); cursor:pointer;
        transition:all .25s; font-family:inherit; display:flex; align-items:center; justify-content:center; gap:8px;
    }
    .how-tab.active { background:#fff; color:var(--teal); box-shadow:0 2px 12px rgba(0,0,0,.08); }
    .how-tab i { font-size:15px; }
    /* Flow panels */
    .how-panel { display:none; }
    .how-panel.active { display:block; }
    .how-flow {
        display:grid; grid-template-columns:repeat(3,1fr); gap:0; position:relative;
    }
    /* connector line */
    .how-flow::before {
        content:''; position:absolute; top:52px; left:calc(16.66% + 16px); right:calc(16.66% + 16px);
        height:2px; background:linear-gradient(90deg,var(--teal-pale),var(--teal),var(--teal-pale));
        z-index:0;
    }
    .how-step {
        display:flex; flex-direction:column; align-items:center; text-align:center;
        padding:0 20px 0; position:relative; z-index:1;
    }
    .how-step-bubble {
        width:104px; height:104px; border-radius:50%; display:flex; align-items:center; justify-content:center;
        font-size:36px; margin-bottom:20px; position:relative; flex-shrink:0;
        border:3px solid #fff; box-shadow:0 4px 20px rgba(13,148,136,.15);
    }
    .how-step-num {
        position:absolute; top:-4px; right:-4px; width:26px; height:26px;
        background:var(--teal); color:#fff; border-radius:50%; font-size:12px; font-weight:800;
        display:flex; align-items:center; justify-content:center; border:2px solid #fff;
    }
    .how-step h3 { font-size:16px; font-weight:700; color:var(--gray-800); margin-bottom:8px; }
    .how-step p { font-size:13px; color:var(--gray-500); line-height:1.65; }
    /* booking has 4 steps — override grid */
    .how-flow.four-steps { grid-template-columns:repeat(4,1fr); }
    .how-flow.four-steps::before { left:calc(12.5% + 16px); right:calc(12.5% + 16px); top:52px; }
    /* feature badge */
    .feature-badge {
        display:inline-flex; align-items:center; gap:8px; padding:8px 18px;
        border-radius:100px; font-size:13px; font-weight:700; margin-bottom:32px;
    }
    .feature-badge.konsultasi { background:var(--teal-pale); color:var(--teal-dark); border:1px solid rgba(13,148,136,.2); }
    .feature-badge.booking { background:#eff6ff; color:#1d4ed8; border:1px solid rgba(59,130,246,.2); }
    /* CTA row */
    .how-cta { text-align:center; margin-top:44px; }
    .how-cta p { font-size:14px; color:var(--gray-500); margin-bottom:16px; }
    /* CTA */
    .cta-section {
        background:linear-gradient(135deg,var(--teal-dark) 0%,var(--teal) 60%,#0891b2 100%);
        padding:88px 24px; text-align:center; color:#fff; position:relative; overflow:hidden;
    }
    .cta-section::before {
        content:''; position:absolute; top:-100px; right:-100px; width:500px; height:500px;
        background:radial-gradient(circle,rgba(255,255,255,.06) 0%,transparent 70%); border-radius:50%;
    }
    .cta-section h2 { font-size:40px; font-weight:800; margin-bottom:16px; position:relative; }
    .cta-section p { font-size:17px; opacity:.9; margin-bottom:40px; max-width:520px; margin-left:auto; margin-right:auto; position:relative; }
    .cta-btns { display:flex; gap:14px; justify-content:center; flex-wrap:wrap; position:relative; }
    .btn-white { background:#fff; color:var(--teal); padding:16px 36px; border-radius:14px; font-weight:700; font-size:16px; transition:all .25s; }
    .btn-white:hover { transform:translateY(-2px); box-shadow:0 8px 28px rgba(0,0,0,.15); }
    .btn-outline-white { background:transparent; color:#fff; border:2px solid rgba(255,255,255,.6); padding:14px 36px; border-radius:14px; font-weight:600; font-size:16px; transition:all .25s; }
    .btn-outline-white:hover { background:rgba(255,255,255,.15); border-color:#fff; }
    /* PREVIEW CARDS */
    .preview-section { background:var(--gray-50); }
    .preview-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
    .preview-card {
        background:#fff; border-radius:var(--radius-lg); padding:24px;
        border:1px solid var(--gray-200); transition:all .3s;
    }
    .preview-card:hover { transform:translateY(-4px); box-shadow:var(--shadow-lg); border-color:var(--teal-light); }
    .preview-card-icon { width:48px; height:48px; border-radius:13px; display:flex; align-items:center; justify-content:center; font-size:22px; margin-bottom:14px; }
    .preview-card h4 { font-size:16px; font-weight:700; color:var(--gray-800); margin-bottom:6px; }
    .preview-card p { font-size:13px; color:var(--gray-500); line-height:1.65; margin-bottom:16px; }
    .preview-link { font-size:13px; font-weight:700; color:var(--teal); display:inline-flex; align-items:center; gap:5px; transition:gap .2s; }
    .preview-link:hover { gap:9px; }
    @media(max-width:900px) {
        .hero h1 { font-size:38px; }
        .hero-desc { font-size:16px; }
        .about-grid { grid-template-columns:1fr; }
        .features-grid, .preview-grid { grid-template-columns:1fr; }
        .how-flow, .how-flow.four-steps { grid-template-columns:1fr 1fr; gap:32px; }
        .how-flow::before, .how-flow.four-steps::before { display:none; }
        .how-tabs { max-width:100%; }
    }
    @media(max-width:480px) {
        .hero h1 { font-size:30px; }
        .hero-pills { gap:8px; }
        .how-flow, .how-flow.four-steps { grid-template-columns:1fr; }
    }
</style>
@endpush

@section('content')

<!-- HERO -->
<section class="hero">
    <div class="hero-inner">
        <div class="hero-badge"><span class="badge-dot"></span> Platform Kesehatan Hewan Terpercaya</div>

        <h1>Kesehatan Hewan<br>Peliharaanmu <em>Prioritas Kami</em></h1>

        <p class="hero-desc">
            Vetra menghubungkan pemilik hewan peliharaan dengan dokter hewan berlisensi dan klinik terpercaya.
            Konsultasi online, booking klinik, edukasi kesehatan, hingga <strong>chatbot AI</strong> untuk cek
            gejala hewan — semua dalam satu platform yang mudah digunakan.
        </p>

        <!-- Feature pills -->
        <div class="hero-pills">
            <span class="hero-pill"><i class="fa-solid fa-comments" style="color:var(--teal)"></i> Konsultasi Online</span>
            <span class="hero-pill"><i class="fa-solid fa-calendar-check" style="color:var(--blue)"></i> Booking Klinik</span>
            <span class="hero-pill"><i class="fa-solid fa-robot" style="color:#7c3aed"></i> Chatbot AI</span>
            <span class="hero-pill"><i class="fa-solid fa-newspaper" style="color:var(--coral)"></i> Artikel Kesehatan</span>
            <span class="hero-pill"><i class="fa-solid fa-notes-medical" style="color:#16a34a"></i> Rekam Medis</span>
        </div>

        <div class="hero-btns">
            <a href="{{ route('register') }}" class="btn-primary"><i class="fa-solid fa-paw"></i> Mulai Sekarang — Gratis</a>
            <a href="{{ route('klinik') }}" class="btn-ghost">Lihat Klinik & Dokter</a>
        </div>

        <div class="hero-stats">
            <div class="hero-stat"><i class="fa-solid fa-user-doctor" style="color:var(--teal)"></i> {{ $doctorCount }}+ Dokter Aktif</div>
            <div class="hero-stat"><i class="fa-solid fa-hospital" style="color:var(--blue)"></i> {{ $clinicCount }}+ Klinik Mitra</div>
            <div class="hero-stat"><i class="fa-solid fa-star" style="color:var(--coral)"></i> Rating 4.9/5</div>
        </div>
    </div>
</section>

<!-- ABOUT -->
<section class="about-section" style="padding:80px 24px;">
    <div class="container">
        <div class="about-grid">
            <div class="about-text">
                <div class="section-tag">Tentang Vetra</div>
                <h2 class="section-title">Platform Kesehatan Hewan Peliharaan Terlengkap</h2>
                <p>Vetra adalah platform digital yang menghubungkan pemilik hewan peliharaan dengan dokter hewan berlisensi dan klinik hewan terpercaya di seluruh Indonesia.</p>
                <p>Dengan teknologi terkini, Vetra memungkinkan konsultasi online real-time, booking klinik digital, pemantauan kesehatan hewan, dan akses ke ratusan artikel edukasi dari dokter hewan berpengalaman.</p>
                <p>Bergabunglah dengan ribuan pemilik hewan peliharaan yang telah mempercayakan kesehatan hewan kesayangan mereka kepada Vetra.</p>
            </div>
            <div class="stats-grid">
                <div class="stat-card"><div class="icon">🏥</div><div class="num">{{ $clinicCount > 0 ? $clinicCount : '200+' }}</div><div class="lbl">Klinik Mitra Terdaftar</div></div>
                <div class="stat-card"><div class="icon">👨‍⚕️</div><div class="num">{{ $doctorCount > 0 ? $doctorCount : '500+' }}</div><div class="lbl">Dokter Hewan Aktif</div></div>
                <div class="stat-card"><div class="icon">📰</div><div class="num">{{ $articleCount > 0 ? $articleCount : '100+' }}</div><div class="lbl">Artikel Kesehatan</div></div>
                <div class="stat-card"><div class="icon">⭐</div><div class="num">4.9/5</div><div class="lbl">Rating Kepuasan Pengguna</div></div>
            </div>
        </div>
    </div>
</section>

<!-- FEATURES -->
<section class="features-section" style="padding:80px 24px;">
    <div class="container">
        <div style="text-align:center;margin-bottom:52px;">
            <div class="section-tag">Fitur Unggulan</div>
            <h2 class="section-title">Semua yang Kamu Butuhkan</h2>
            <p class="section-sub" style="max-width:540px;margin:0 auto;">Platform lengkap untuk menjaga kesehatan dan kebahagiaan hewan peliharaanmu</p>
        </div>
        <div class="features-grid">
            <div class="feat-card"><div class="feat-icon teal"><i class="fa-solid fa-comments"></i></div><h3>Konsultasi Online Real-time</h3><p>Chat langsung dengan dokter hewan berlisensi kapan saja. Kirim foto, video, dan deskripsi gejala untuk diagnosis yang akurat.</p></div>
            <div class="feat-card"><div class="feat-icon coral"><i class="fa-solid fa-truck-medical"></i></div><h3>Layanan Darurat 24/7</h3><p>Dokter hewan siaga sepanjang waktu untuk kondisi darurat. Respons cepat dalam hitungan menit, tidak perlu menunggu hingga pagi.</p></div>
            <div class="feat-card"><div class="feat-icon blue"><i class="fa-solid fa-calendar-days"></i></div><h3>Booking Klinik Digital</h3><p>Jadwalkan kunjungan ke klinik mitra terdekat langsung dari aplikasi. Antrian digital, pilih dokter, dan konfirmasi otomatis.</p></div>
            <div class="feat-card"><div class="feat-icon green"><i class="fa-solid fa-book-open-reader"></i></div><h3>Artikel & Edukasi Kesehatan</h3><p>Ratusan artikel kesehatan hewan dari dokter berlisensi. Pahami kondisi, nutrisi, dan perawatan hewan kesayanganmu lebih baik.</p></div>
            <div class="feat-card"><div class="feat-icon purple"><i class="fa-solid fa-brain"></i></div><h3>AI Symptom Checker</h3><p>Cek gejala hewan peliharaanmu dengan kecerdasan buatan sebelum berkonsultasi. Dapatkan saran awal yang akurat dan cepat.</p></div>
            <div class="feat-card"><div class="feat-icon pink"><i class="fa-solid fa-notes-medical"></i></div><h3>Rekam Medis Digital</h3><p>Simpan riwayat kesehatan, vaksinasi, dan resep hewan peliharaanmu secara digital. Akses kapan saja, di mana saja.</p></div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="how-section" style="padding:80px 24px;">
    <div class="container">
        <div style="text-align:center;margin-bottom:44px;">
            <div class="section-tag">Cara Kerja</div>
            <h2 class="section-title">Mudah Digunakan, Cepat Terhubung</h2>
            <p class="section-sub" style="max-width:520px;margin:12px auto 0;">Dua fitur utama Vetra dirancang sesederhana mungkin agar kamu bisa fokus pada yang penting — kesehatan hewan peliharaanmu.</p>
        </div>

        <!-- TAB SWITCHER -->
        <div class="how-tabs">
            <button class="how-tab active" id="tab-konsultasi" onclick="switchTab('konsultasi')">
                <i class="fa-solid fa-comments"></i> Konsultasi Online
            </button>
            <button class="how-tab" id="tab-booking" onclick="switchTab('booking')">
                <i class="fa-solid fa-calendar-check"></i> Booking Jadwal
            </button>
        </div>

        <!-- PANEL: KONSULTASI ONLINE -->
        <div class="how-panel active" id="panel-konsultasi">
            <div style="text-align:center;margin-bottom:40px;">
                <span class="feature-badge konsultasi">
                    <i class="fa-solid fa-comments"></i> Konsultasi Dokter Online
                </span>
                <p style="font-size:15px;color:var(--gray-500);max-width:480px;margin:0 auto;">
                    Hubungi dokter hewan berlisensi kapan saja, di mana saja — tanpa perlu keluar rumah.
                </p>
            </div>
            <div class="how-flow">
                <div class="how-step">
                    <div class="how-step-bubble" style="background:linear-gradient(135deg,var(--teal-pale),#a7f3d0);">
                        <i class="fa-solid fa-user-plus" style="color:var(--teal);"></i>
                        <div class="how-step-num">1</div>
                    </div>
                    <h3>Daftar atau Masuk</h3>
                    <p>Buat akun gratis atau masuk ke akun Vetra yang sudah ada. Proses cepat, tidak perlu kartu kredit.</p>
                </div>
                <div class="how-step">
                    <div class="how-step-bubble" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);">
                        <i class="fa-solid fa-user-doctor" style="color:var(--blue);"></i>
                        <div class="how-step-num" style="background:var(--blue);">2</div>
                    </div>
                    <h3>Pilih Dokter</h3>
                    <p>Telusuri daftar dokter hewan berlisensi. Filter berdasarkan spesialisasi, rating, atau ketersediaan.</p>
                </div>
                <div class="how-step">
                    <div class="how-step-bubble" style="background:linear-gradient(135deg,#f0fdf4,#bbf7d0);">
                        <i class="fa-solid fa-comments" style="color:#16a34a;"></i>
                        <div class="how-step-num" style="background:#16a34a;">3</div>
                    </div>
                    <h3>Mulai Konsultasi</h3>
                    <p>Chat langsung, kirim foto/video kondisi hewan, atau lakukan video call. Dokter merespons dalam menit.</p>
                </div>
            </div>
            <div class="how-cta">
                <p>Siap berkonsultasi? Daftar gratis dan mulai sekarang.</p>
                <a href="{{ route('register') }}" class="btn-primary">
                    <i class="fa-solid fa-comments"></i> Mulai Konsultasi Sekarang
                </a>
            </div>
        </div>

        <!-- PANEL: BOOKING JADWAL -->
        <div class="how-panel" id="panel-booking">
            <div style="text-align:center;margin-bottom:40px;">
                <span class="feature-badge booking">
                    <i class="fa-solid fa-calendar-check"></i> Booking Jadwal Klinik
                </span>
                <p style="font-size:15px;color:var(--gray-500);max-width:480px;margin:0 auto;">
                    Jadwalkan kunjungan ke klinik mitra Vetra dengan mudah — tanpa antri panjang di tempat.
                </p>
            </div>
            <div class="how-flow four-steps">
                <div class="how-step">
                    <div class="how-step-bubble" style="background:linear-gradient(135deg,var(--teal-pale),#a7f3d0);">
                        <i class="fa-solid fa-user-plus" style="color:var(--teal);"></i>
                        <div class="how-step-num">1</div>
                    </div>
                    <h3>Daftar atau Masuk</h3>
                    <p>Buat akun gratis atau masuk ke akun Vetra yang sudah ada.</p>
                </div>
                <div class="how-step">
                    <div class="how-step-bubble" style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);">
                        <i class="fa-solid fa-hospital" style="color:var(--blue);"></i>
                        <div class="how-step-num" style="background:var(--blue);">2</div>
                    </div>
                    <h3>Pilih Klinik & Dokter</h3>
                    <p>Temukan klinik terdekat dan pilih dokter spesialis yang sesuai dengan kebutuhan hewanmu.</p>
                </div>
                <div class="how-step">
                    <div class="how-step-bubble" style="background:linear-gradient(135deg,#fef3c7,#fde68a);">
                        <i class="fa-solid fa-calendar-days" style="color:#b45309;"></i>
                        <div class="how-step-num" style="background:#b45309;">3</div>
                    </div>
                    <h3>Pilih Tanggal & Waktu</h3>
                    <p>Lihat slot jadwal yang tersedia dan pilih waktu yang paling nyaman untukmu.</p>
                </div>
                <div class="how-step">
                    <div class="how-step-bubble" style="background:linear-gradient(135deg,#f0fdf4,#bbf7d0);">
                        <i class="fa-solid fa-circle-check" style="color:#16a34a;"></i>
                        <div class="how-step-num" style="background:#16a34a;">4</div>
                    </div>
                    <h3>Booking Siap!</h3>
                    <p>Konfirmasi booking dikirim otomatis. Datang ke klinik sesuai jadwal — tanpa antri.</p>
                </div>
            </div>
            <div class="how-cta">
                <p>Ingin booking klinik? Daftar gratis dan jadwalkan sekarang.</p>
                <a href="{{ route('register') }}" class="btn-primary" style="background:var(--blue);box-shadow:0 4px 16px rgba(59,130,246,.3);">
                    <i class="fa-solid fa-calendar-check"></i> Booking Jadwal Sekarang
                </a>
            </div>
        </div>

    </div>
</section>

<!-- PREVIEW SECTIONS -->
<section class="preview-section" style="padding:80px 24px;">
    <div class="container">
        <div style="text-align:center;margin-bottom:52px;">
            <div class="section-tag">Jelajahi Vetra</div>
            <h2 class="section-title">Temukan Semua yang Kamu Butuhkan</h2>
        </div>
        <div class="preview-grid">
            <div class="preview-card">
                <div class="preview-card-icon" style="background:var(--teal-pale);color:var(--teal);"><i class="fa-solid fa-hospital"></i></div>
                <h4>Klinik & Dokter</h4>
                <p>Temukan {{ $clinicCount > 0 ? $clinicCount : 'ratusan' }} klinik mitra dan dokter hewan spesialis yang siap membantu hewan peliharaanmu.</p>
                <a href="{{ route('klinik') }}" class="preview-link">Lihat Semua Klinik <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="preview-card">
                <div class="preview-card-icon" style="background:#fff7ed;color:var(--coral);"><i class="fa-solid fa-newspaper"></i></div>
                <h4>Artikel Kesehatan</h4>
                <p>Baca {{ $articleCount > 0 ? $articleCount : 'ratusan' }} artikel kesehatan hewan dari dokter berlisensi. Tips perawatan, nutrisi, dan pencegahan penyakit.</p>
                <a href="{{ route('artikel') }}" class="preview-link">Baca Artikel <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <div class="preview-card">
                <div class="preview-card-icon" style="background:var(--blue-soft);color:var(--blue);"><i class="fa-solid fa-envelope"></i></div>
                <h4>Hubungi Kami</h4>
                <p>Ada pertanyaan atau butuh bantuan? Tim Vetra siap membantu Anda 24/7 melalui berbagai saluran komunikasi.</p>
                <a href="{{ route('kontak') }}" class="preview-link">Hubungi Kami <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <h2>Jaga Kesehatan Hewan Peliharaanmu <i class="fa-solid fa-paw"></i></h2>
    <p>Daftar gratis sekarang dan dapatkan akses ke ratusan dokter hewan berlisensi serta klinik terpercaya.</p>
    <div class="cta-btns">
        <a href="{{ route('register') }}" class="btn-white">Daftar Gratis Sekarang</a>
        <a href="{{ route('login') }}" class="btn-outline-white">Sudah Punya Akun? Masuk</a>
    </div>
</section>

@endsection

@push('scripts')
<script>
function switchTab(tab) {
    // tabs
    document.querySelectorAll('.how-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    // panels
    document.querySelectorAll('.how-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel-' + tab).classList.add('active');
}
</script>
@endpush
