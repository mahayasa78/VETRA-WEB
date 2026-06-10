@extends('layouts.app')
@section('title', 'Dashboard')
@section('meta_desc', 'Vetra — Platform kesehatan hewan peliharaan. Konsultasi dokter hewan online, booking klinik, dan edukasi kesehatan hewan.')

@push('styles')
<style>
/* ═══════════════════════════════════════════════════
   HERO SECTION
═══════════════════════════════════════════════════ */
.hero {
    min-height: calc(100vh - 68px);
    background: linear-gradient(135deg, #0a1628 0%, #0f2744 40%, #0d4a3e 100%);
    display: flex; align-items: center; position: relative; overflow: hidden;
}
/* Animated particle dots */
.hero::before {
    content:''; position:absolute; inset:0;
    background-image:
        radial-gradient(circle at 20% 20%, rgba(13,148,136,.18) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(59,130,246,.12) 0%, transparent 50%),
        radial-gradient(circle at 50% 50%, rgba(20,184,166,.08) 0%, transparent 60%);
    pointer-events:none;
}
/* Subtle grid pattern */
.hero::after {
    content:''; position:absolute; inset:0;
    background-image: linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                      linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
    background-size: 60px 60px;
    pointer-events:none;
}

.hero-inner {
    max-width: 1200px; margin: 0 auto; padding: 80px 24px;
    display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: center;
    position: relative; z-index: 1; width: 100%;
}
.hero-left {}
.hero-badge {
    display:inline-flex; align-items:center; gap:8px;
    background:rgba(13,148,136,.2); color:#5eead4;
    padding:7px 16px; border-radius:100px; font-size:13px; font-weight:600;
    margin-bottom:28px; border:1px solid rgba(13,148,136,.3);
    backdrop-filter: blur(8px);
}
.badge-dot { width:8px; height:8px; background:#14b8a6; border-radius:50%; animation:pulse-dot 2s infinite; }
@keyframes pulse-dot { 0%,100%{transform:scale(1);opacity:1;box-shadow:0 0 0 0 rgba(20,184,166,.4);} 50%{transform:scale(1.2);opacity:.8;box-shadow:0 0 0 6px rgba(20,184,166,0);} }

.hero h1 {
    font-size: 54px; font-weight: 800; line-height: 1.1;
    color: #fff; margin-bottom: 20px; letter-spacing: -1px;
}
.hero h1 em { font-style:normal; color: #14b8a6; }
.hero h1 .line2 { color: #93c5fd; }

.hero-desc { font-size:17px; color:rgba(255,255,255,.75); line-height:1.8; margin-bottom:36px; }

.hero-btns { display:flex; gap:14px; flex-wrap:wrap; margin-bottom:48px; }
.btn-hero-primary {
    background: linear-gradient(135deg, #0d9488, #14b8a6);
    color: #fff; padding: 15px 32px; border-radius: 14px;
    font-weight: 700; font-size: 15px; display:inline-flex; align-items:center; gap:9px;
    box-shadow: 0 8px 28px rgba(13,148,136,.4); transition: all .25s; border:none;
    text-decoration:none;
}
.btn-hero-primary:hover { transform:translateY(-3px); box-shadow:0 14px 36px rgba(13,148,136,.5); }
.btn-hero-ghost {
    background: rgba(255,255,255,.08); color: #fff; padding: 15px 32px; border-radius: 14px;
    font-weight: 600; font-size: 15px; border: 1.5px solid rgba(255,255,255,.25);
    display:inline-flex; align-items:center; gap:9px; transition: all .25s; backdrop-filter:blur(8px);
    text-decoration:none;
}
.btn-hero-ghost:hover { background:rgba(255,255,255,.15); border-color:rgba(255,255,255,.5); transform:translateY(-2px); }

.hero-stats { display:flex; gap:28px; flex-wrap:wrap; }
.hero-stat-item {
    display:flex; flex-direction:column; align-items:flex-start;
    padding: 16px 22px; background: rgba(255,255,255,.06);
    border-radius: 14px; border: 1px solid rgba(255,255,255,.1);
    backdrop-filter: blur(8px);
}
.hero-stat-item .num { font-size:28px; font-weight:800; color:#14b8a6; line-height:1; }
.hero-stat-item .lbl { font-size:12px; color:rgba(255,255,255,.6); margin-top:4px; }

/* Hero right — image */
.hero-image-wrap {
    position:relative; display:flex; align-items:center; justify-content:center;
}
.hero-img {
    width: 100%; max-width: 560px; border-radius: 28px;
    box-shadow: 0 32px 80px rgba(0,0,0,.5);
    border: 2px solid rgba(255,255,255,.1);
    animation: float 6s ease-in-out infinite;
}
@keyframes float { 0%,100%{transform:translateY(0);} 50%{transform:translateY(-14px);} }

/* Floating badge on image */
.hero-float-badge {
    position: absolute; bottom: -20px; left: -20px;
    background: #fff; border-radius: 16px; padding: 14px 20px;
    box-shadow: 0 12px 40px rgba(0,0,0,.25);
    display:flex; align-items:center; gap:12px; min-width:180px;
}
.hero-float-badge .icon { width:42px; height:42px; background:var(--teal-pale); border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:20px; }
.hero-float-badge .text strong { display:block; font-size:15px; font-weight:800; color:var(--gray-800); }
.hero-float-badge .text span { font-size:12px; color:var(--gray-500); }

.hero-float-badge2 {
    position: absolute; top: 20px; right: -24px;
    background: linear-gradient(135deg, #0d9488, #14b8a6); border-radius: 14px;
    padding: 12px 18px; box-shadow: 0 8px 28px rgba(13,148,136,.4);
    color:#fff; display:flex; align-items:center; gap:10px;
}
.hero-float-badge2 .num { font-size:24px; font-weight:800; }
.hero-float-badge2 .lbl { font-size:12px; opacity:.85; }

/* Hero pills */
.hero-pills { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:32px; }
.hero-pill {
    display:inline-flex; align-items:center; gap:7px;
    background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.15);
    border-radius:100px; padding:8px 16px; font-size:13px; font-weight:600;
    color:rgba(255,255,255,.85); backdrop-filter:blur(8px); transition:all .2s;
}
.hero-pill:hover { background:rgba(13,148,136,.25); border-color:rgba(20,184,166,.4); color:#5eead4; }
.hero-pill i { font-size:13px; }

/* ═══════════════════════════════════════════════════
   ABOUT / STATS SECTION
═══════════════════════════════════════════════════ */
.about-section {
    background: #fff;
    background-image: url('/images/vet_pets_bg.png');
    background-size: cover;
    background-position: center;
    background-blend-mode: overlay;
    position: relative;
}
.about-section::before {
    content:''; position:absolute; inset:0;
    background: rgba(255,255,255,.92);
}
.about-section .container { position:relative; z-index:1; }
.about-grid { display:grid; grid-template-columns:1fr 1fr; gap:72px; align-items:center; }
.about-img-wrap { position:relative; }
.about-img {
    width:100%; border-radius:24px;
    box-shadow: 0 20px 60px rgba(13,148,136,.15);
}
.about-img-badge {
    position:absolute; top:-16px; right:-16px;
    background:linear-gradient(135deg,#0d9488,#0891b2);
    color:#fff; border-radius:16px; padding:16px 22px;
    box-shadow:0 8px 28px rgba(13,148,136,.35);
    text-align:center;
}
.about-img-badge .num { font-size:30px; font-weight:800; line-height:1; }
.about-img-badge .lbl { font-size:11px; opacity:.85; }
.about-text p { font-size:16px; color:var(--gray-600); line-height:1.8; margin-bottom:16px; }
.stats-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:28px; }
.stat-card {
    background: linear-gradient(135deg, var(--teal-50), #eff6ff);
    border-radius:var(--radius); padding:24px;
    border:1px solid rgba(13,148,136,.15); transition:all .3s;
}
.stat-card:hover { border-color:var(--teal-light); box-shadow:var(--shadow); transform:translateY(-3px); }
.stat-card .num { font-size:36px; font-weight:800; color:var(--teal); line-height:1; }
.stat-card .lbl { font-size:13px; color:var(--gray-500); margin-top:6px; font-weight:500; }
.stat-card .icon { font-size:28px; margin-bottom:12px; }

/* ═══════════════════════════════════════════════════
   FEATURES
═══════════════════════════════════════════════════ */
.features-section {
    background: linear-gradient(135deg, #0a1628 0%, #0f2744 50%, #0d4a3e 100%);
    position:relative; overflow:hidden;
}
.features-section::before {
    content:''; position:absolute; inset:0;
    background-image: radial-gradient(circle at 20% 50%, rgba(13,148,136,.15) 0%, transparent 50%),
                      radial-gradient(circle at 80% 20%, rgba(59,130,246,.1) 0%, transparent 50%);
}
.features-section .container { position:relative; z-index:1; }
.features-section .section-tag { color:#5eead4; }
.features-section .section-title { color:#fff; }
.features-section .section-sub { color:rgba(255,255,255,.65); }
.features-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:20px; }
.feat-card {
    background: rgba(255,255,255,.06); border-radius:var(--radius-lg); padding:30px;
    border:1px solid rgba(255,255,255,.1); transition:all .3s; backdrop-filter:blur(8px);
}
.feat-card:hover { transform:translateY(-6px); background:rgba(255,255,255,.1); border-color:rgba(20,184,166,.4); box-shadow:0 20px 48px rgba(0,0,0,.3); }
.feat-icon {
    width:54px; height:54px; border-radius:14px;
    display:flex; align-items:center; justify-content:center; font-size:24px; margin-bottom:18px;
}
.feat-icon.teal   { background:rgba(13,148,136,.25); color:#5eead4; }
.feat-icon.blue   { background:rgba(59,130,246,.2); color:#93c5fd; }
.feat-icon.coral  { background:rgba(249,115,22,.2); color:#fca5a5; }
.feat-icon.purple { background:rgba(124,58,237,.2); color:#c4b5fd; }
.feat-icon.green  { background:rgba(22,163,74,.2); color:#86efac; }
.feat-icon.pink   { background:rgba(219,39,119,.2); color:#f9a8d4; }
.feat-card h3 { font-size:18px; font-weight:700; color:#fff; margin-bottom:10px; }
.feat-card p { font-size:14px; color:rgba(255,255,255,.6); line-height:1.75; }

/* ═══════════════════════════════════════════════════
   HOW IT WORKS
═══════════════════════════════════════════════════ */
.how-section { background:var(--gray-50); }
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
.how-panel { display:none; }
.how-panel.active { display:block; }
.how-flow { display:grid; grid-template-columns:repeat(3,1fr); gap:0; position:relative; }
.how-flow::before {
    content:''; position:absolute; top:52px; left:calc(16.66% + 16px); right:calc(16.66% + 16px);
    height:2px; background:linear-gradient(90deg,var(--teal-pale),var(--teal),var(--teal-pale)); z-index:0;
}
.how-step { display:flex; flex-direction:column; align-items:center; text-align:center; padding:0 20px 0; position:relative; z-index:1; }
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
.how-flow.four-steps { grid-template-columns:repeat(4,1fr); }
.how-flow.four-steps::before { left:calc(12.5% + 16px); right:calc(12.5% + 16px); top:52px; }
.feature-badge { display:inline-flex; align-items:center; gap:8px; padding:8px 18px; border-radius:100px; font-size:13px; font-weight:700; margin-bottom:32px; }
.feature-badge.konsultasi { background:var(--teal-pale); color:var(--teal-dark); border:1px solid rgba(13,148,136,.2); }
.feature-badge.booking { background:#eff6ff; color:#1d4ed8; border:1px solid rgba(59,130,246,.2); }
.how-cta { text-align:center; margin-top:44px; }
.how-cta p { font-size:14px; color:var(--gray-500); margin-bottom:16px; }

/* ═══════════════════════════════════════════════════
   PREVIEW / EXPLORE SECTION
═══════════════════════════════════════════════════ */
.preview-section {
    background: #fff;
    position: relative;
}
.preview-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; }
.preview-card {
    background:#fff; border-radius:var(--radius-lg); padding:32px;
    border:1.5px solid var(--gray-200); transition:all .3s;
    box-shadow: 0 4px 16px rgba(0,0,0,.04);
    position: relative; overflow: hidden;
}
.preview-card::before {
    content:''; position:absolute; top:0; left:0; right:0; height:4px;
    background:linear-gradient(90deg, var(--teal), #0891b2);
    transform: scaleX(0); transform-origin:left; transition:transform .3s;
}
.preview-card:hover::before { transform:scaleX(1); }
.preview-card:hover { transform:translateY(-6px); box-shadow:var(--shadow-xl); border-color:var(--teal-light); }
.preview-card-icon { width:56px; height:56px; border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:26px; margin-bottom:16px; }
.preview-card h4 { font-size:18px; font-weight:700; color:var(--gray-800); margin-bottom:8px; }
.preview-card p { font-size:14px; color:var(--gray-500); line-height:1.7; margin-bottom:20px; }
.preview-link { font-size:13px; font-weight:700; color:var(--teal); display:inline-flex; align-items:center; gap:6px; transition:gap .2s; }
.preview-link:hover { gap:10px; }

/* ═══════════════════════════════════════════════════
   CTA SECTION
═══════════════════════════════════════════════════ */
.cta-section {
    background:linear-gradient(135deg, #0a1628 0%, #0d4a3e 60%, #0a1628 100%);
    padding:100px 24px; text-align:center; color:#fff; position:relative; overflow:hidden;
}
.cta-section::before {
    content:''; position:absolute; top:-100px; left:50%; transform:translateX(-50%);
    width:800px; height:800px;
    background:radial-gradient(circle, rgba(13,148,136,.15) 0%, transparent 65%);
    border-radius:50%; pointer-events:none;
}
/* Paw prints decoration */
.cta-paws { position:absolute; inset:0; pointer-events:none; overflow:hidden; }
.cta-paw { position:absolute; opacity:.05; font-size:60px; animation: drift 20s linear infinite; }
.cta-paw:nth-child(1) { top:10%; left:5%; animation-delay:0s; }
.cta-paw:nth-child(2) { top:60%; left:15%; animation-delay:-5s; }
.cta-paw:nth-child(3) { top:20%; right:10%; animation-delay:-10s; }
.cta-paw:nth-child(4) { top:70%; right:5%; animation-delay:-15s; }
@keyframes drift { 0%{transform:translateY(0) rotate(0deg);} 100%{transform:translateY(-20px) rotate(10deg);} }
.cta-badge {
    display:inline-flex; align-items:center; gap:8px;
    background:rgba(20,184,166,.15); color:#5eead4;
    padding:7px 16px; border-radius:100px; font-size:13px; font-weight:600;
    margin-bottom:24px; border:1px solid rgba(20,184,166,.25);
}
.cta-section h2 { font-size:44px; font-weight:800; margin-bottom:16px; position:relative; letter-spacing:-1px; }
.cta-section p { font-size:17px; opacity:.8; margin-bottom:44px; max-width:520px; margin-left:auto; margin-right:auto; position:relative; line-height:1.7; }
.cta-btns { display:flex; gap:16px; justify-content:center; flex-wrap:wrap; position:relative; }
.btn-white { background:#fff; color:var(--teal); padding:16px 36px; border-radius:14px; font-weight:700; font-size:16px; transition:all .25s; box-shadow:0 4px 20px rgba(0,0,0,.15); display:inline-flex; align-items:center; gap:8px; }
.btn-white:hover { transform:translateY(-3px); box-shadow:0 10px 32px rgba(0,0,0,.2); }
.btn-outline-white { background:rgba(255,255,255,.08); color:#fff; border:2px solid rgba(255,255,255,.35); padding:14px 36px; border-radius:14px; font-weight:600; font-size:16px; transition:all .25s; backdrop-filter:blur(8px); display:inline-flex; align-items:center; gap:8px; }
.btn-outline-white:hover { background:rgba(255,255,255,.18); border-color:#fff; transform:translateY(-2px); }

/* Testimonial chips */
.cta-trust { display:flex; gap:20px; justify-content:center; margin-top:44px; flex-wrap:wrap; }
.trust-chip {
    display:flex; align-items:center; gap:8px; padding:10px 18px;
    background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12);
    border-radius:100px; font-size:13px; color:rgba(255,255,255,.8);
    backdrop-filter:blur(8px);
}
.trust-chip i { color:#5eead4; }

/* ═══════════════════════════════════════════════════
   RESPONSIVE
═══════════════════════════════════════════════════ */
@media(max-width:1024px) {
    .hero-inner { grid-template-columns:1fr; gap:48px; text-align:center; }
    .hero-left { display:flex; flex-direction:column; align-items:center; }
    .hero h1 { font-size:40px; }
    .hero-image-wrap { order:-1; }
    .hero-float-badge { left:0; bottom:-12px; }
    .hero-float-badge2 { right:-8px; }
    .about-grid { grid-template-columns:1fr; }
    .features-grid, .preview-grid { grid-template-columns:1fr 1fr; }
    .how-flow.four-steps { grid-template-columns:1fr 1fr; gap:32px; }
    .how-flow.four-steps::before, .how-flow::before { display:none; }
}
@media(max-width:640px) {
    .hero h1 { font-size:30px; }
    .hero-stats { gap:12px; }
    .hero-stat-item { padding:12px 16px; }
    .features-grid, .preview-grid { grid-template-columns:1fr; }
    .how-flow, .how-flow.four-steps { grid-template-columns:1fr; }
    .cta-section h2 { font-size:30px; }
    .stats-grid { grid-template-columns:1fr 1fr; }
}
</style>
@endpush

@section('content')

<!-- ═══ HERO ═══ -->
<section class="hero">
    <div class="hero-inner">
        <!-- LEFT TEXT -->
        <div class="hero-left">
            <div class="hero-badge">
                <span class="badge-dot"></span>
                Platform Kesehatan Hewan Terpercaya
            </div>

            <h1>
                Kesehatan Hewan<br>
                Peliharaanmu<br>
                <em>Prioritas Kami</em>
            </h1>

            <p class="hero-desc">
                Vetra menghubungkan pemilik hewan dengan dokter hewan berlisensi dan klinik terpercaya.
                Konsultasi online, booking digital, <strong style="color:#5eead4;">chatbot AI</strong>, dan artikel kesehatan — semua dalam satu platform.
            </p>

            <div class="hero-pills">
                <span class="hero-pill"><i class="fa-solid fa-comments" style="color:#5eead4;"></i> Konsultasi Online</span>
                <span class="hero-pill"><i class="fa-solid fa-calendar-check" style="color:#93c5fd;"></i> Booking Klinik</span>
                <span class="hero-pill"><i class="fa-solid fa-robot" style="color:#c4b5fd;"></i> AI Chatbot</span>
                <span class="hero-pill"><i class="fa-solid fa-notes-medical" style="color:#86efac;"></i> Rekam Medis</span>
            </div>

            <div class="hero-btns">
                <a href="{{ route('register') }}" class="btn-hero-primary">
                    <i class="fa-solid fa-paw"></i> Mulai Gratis Sekarang
                </a>
                <a href="{{ route('klinik') }}" class="btn-hero-ghost">
                    Lihat Klinik & Dokter
                </a>
            </div>

            <div class="hero-stats">
                <div class="hero-stat-item">
                    <span class="num">{{ $doctorCount }}+</span>
                    <span class="lbl">Dokter Aktif</span>
                </div>
                <div class="hero-stat-item">
                    <span class="num">{{ $clinicCount }}+</span>
                    <span class="lbl">Klinik Mitra</span>
                </div>
                <div class="hero-stat-item">
                    <span class="num">4.9<small style="font-size:16px;">/5</small></span>
                    <span class="lbl">Rating Pengguna</span>
                </div>
            </div>
        </div>

        <!-- RIGHT IMAGE -->
        <div class="hero-image-wrap">
            <img src="/images/vet_hero.png" alt="Dokter hewan merawat hewan peliharaan" class="hero-img">

            <!-- Floating badge 1 -->
            <div class="hero-float-badge">
                <div class="icon">🏥</div>
                <div class="text">
                    <strong>{{ $clinicCount }}+ Klinik</strong>
                    <span>Mitra Terdaftar</span>
                </div>
            </div>

            <!-- Floating badge 2 -->
            <div class="hero-float-badge2">
                <div>
                    <div class="num">⭐ 4.9</div>
                    <div class="lbl">Rating Kepuasan</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ ABOUT ═══ -->
<section class="about-section" style="padding:96px 24px;">
    <div class="container">
        <div class="about-grid">
            <!-- Left: Image -->
            <div class="about-img-wrap">
                <img src="/images/vet_about.png" alt="Tim dokter hewan Vetra" class="about-img">
                <div class="about-img-badge">
                    <div class="num">{{ $articleCount > 0 ? $articleCount : '100' }}+</div>
                    <div class="lbl">Artikel Edukasi</div>
                </div>
            </div>

            <!-- Right: Text + Stats -->
            <div class="about-text">
                <div class="section-tag">Tentang Vetra</div>
                <h2 class="section-title">Platform Kesehatan Hewan Peliharaan Terlengkap</h2>
                <p>Vetra adalah platform digital yang menghubungkan pemilik hewan peliharaan dengan dokter hewan berlisensi dan klinik terpercaya di seluruh Indonesia.</p>
                <p>Dengan teknologi terkini, Vetra memungkinkan konsultasi online real-time, booking klinik digital, pemantauan kesehatan hewan, dan akses ke ratusan artikel edukasi dari dokter hewan berpengalaman.</p>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="icon">🏥</div>
                        <div class="num">{{ $clinicCount > 0 ? $clinicCount : '200+' }}</div>
                        <div class="lbl">Klinik Mitra Terdaftar</div>
                    </div>
                    <div class="stat-card">
                        <div class="icon">👨‍⚕️</div>
                        <div class="num">{{ $doctorCount > 0 ? $doctorCount : '500+' }}</div>
                        <div class="lbl">Dokter Hewan Aktif</div>
                    </div>
                    <div class="stat-card">
                        <div class="icon">📰</div>
                        <div class="num">{{ $articleCount > 0 ? $articleCount : '100+' }}</div>
                        <div class="lbl">Artikel Kesehatan</div>
                    </div>
                    <div class="stat-card">
                        <div class="icon">⭐</div>
                        <div class="num">4.9/5</div>
                        <div class="lbl">Rating Kepuasan Pengguna</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ═══ FEATURES ═══ -->
<section class="features-section" style="padding:96px 24px;">
    <div class="container">
        <div style="text-align:center;margin-bottom:56px;">
            <div class="section-tag">Fitur Unggulan</div>
            <h2 class="section-title">Semua yang Kamu Butuhkan</h2>
            <p class="section-sub" style="max-width:540px;margin:12px auto 0;">Platform lengkap untuk menjaga kesehatan dan kebahagiaan hewan peliharaanmu</p>
        </div>
        <div class="features-grid">
            <div class="feat-card">
                <div class="feat-icon teal"><i class="fa-solid fa-comments"></i></div>
                <h3>Konsultasi Online Real-time</h3>
                <p>Chat langsung dengan dokter hewan berlisensi kapan saja. Kirim foto, video, dan deskripsi gejala untuk diagnosis yang akurat.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon coral"><i class="fa-solid fa-truck-medical"></i></div>
                <h3>Layanan Darurat 24/7</h3>
                <p>Dokter hewan siaga sepanjang waktu untuk kondisi darurat. Respons cepat dalam hitungan menit, tidak perlu menunggu hingga pagi.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon blue"><i class="fa-solid fa-calendar-days"></i></div>
                <h3>Booking Klinik Digital</h3>
                <p>Jadwalkan kunjungan ke klinik mitra terdekat langsung dari aplikasi. Antrian digital, pilih dokter, dan konfirmasi otomatis.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon green"><i class="fa-solid fa-book-open-reader"></i></div>
                <h3>Artikel & Edukasi Kesehatan</h3>
                <p>Ratusan artikel kesehatan hewan dari dokter berlisensi. Pahami kondisi, nutrisi, dan perawatan hewan kesayanganmu lebih baik.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon purple"><i class="fa-solid fa-brain"></i></div>
                <h3>AI Symptom Checker</h3>
                <p>Cek gejala hewan peliharaanmu dengan kecerdasan buatan sebelum berkonsultasi. Dapatkan saran awal yang akurat dan cepat.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon pink"><i class="fa-solid fa-notes-medical"></i></div>
                <h3>Rekam Medis Digital</h3>
                <p>Simpan riwayat kesehatan, vaksinasi, dan resep hewan peliharaanmu secara digital. Akses kapan saja, di mana saja.</p>
            </div>
        </div>
    </div>
</section>

<!-- ═══ HOW IT WORKS ═══ -->
<section class="how-section" style="padding:96px 24px;">
    <div class="container">
        <div style="text-align:center;margin-bottom:44px;">
            <div class="section-tag">Cara Kerja</div>
            <h2 class="section-title">Mudah Digunakan, Cepat Terhubung</h2>
            <p class="section-sub" style="max-width:520px;margin:12px auto 0;">Dua fitur utama Vetra dirancang sesederhana mungkin agar kamu bisa fokus pada yang penting — kesehatan hewan peliharaanmu.</p>
        </div>

        <div class="how-tabs">
            <button class="how-tab active" id="tab-konsultasi" onclick="switchTab('konsultasi')">
                <i class="fa-solid fa-comments"></i> Konsultasi Online
            </button>
            <button class="how-tab" id="tab-booking" onclick="switchTab('booking')">
                <i class="fa-solid fa-calendar-check"></i> Booking Jadwal
            </button>
        </div>

        <!-- PANEL: KONSULTASI -->
        <div class="how-panel active" id="panel-konsultasi">
            <div style="text-align:center;margin-bottom:40px;">
                <span class="feature-badge konsultasi"><i class="fa-solid fa-comments"></i> Konsultasi Dokter Online</span>
                <p style="font-size:15px;color:var(--gray-500);max-width:480px;margin:0 auto;">Hubungi dokter hewan berlisensi kapan saja, di mana saja — tanpa perlu keluar rumah.</p>
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
                <a href="{{ route('register') }}" class="btn-primary"><i class="fa-solid fa-comments"></i> Mulai Konsultasi Sekarang</a>
            </div>
        </div>

        <!-- PANEL: BOOKING -->
        <div class="how-panel" id="panel-booking">
            <div style="text-align:center;margin-bottom:40px;">
                <span class="feature-badge booking"><i class="fa-solid fa-calendar-check"></i> Booking Jadwal Klinik</span>
                <p style="font-size:15px;color:var(--gray-500);max-width:480px;margin:0 auto;">Jadwalkan kunjungan ke klinik mitra Vetra dengan mudah — tanpa antri panjang di tempat.</p>
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

<!-- ═══ PREVIEW / EXPLORE ═══ -->
<section class="preview-section" style="padding:96px 24px; background:var(--gray-50);">
    <div class="container">
        <div style="text-align:center;margin-bottom:56px;">
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

<!-- ═══ CTA ═══ -->
<section class="cta-section">
    <div class="cta-paws">
        <span class="cta-paw">🐾</span>
        <span class="cta-paw">🐾</span>
        <span class="cta-paw">🐾</span>
        <span class="cta-paw">🐾</span>
    </div>
    <div style="position:relative;z-index:1;">
        <div class="cta-badge"><i class="fa-solid fa-shield-heart"></i> Terpercaya & Berlisensi</div>
        <h2>Jaga Kesehatan Hewan<br>Peliharaanmu <i class="fa-solid fa-paw" style="color:#5eead4;"></i></h2>
        <p>Daftar gratis sekarang dan dapatkan akses ke ratusan dokter hewan berlisensi serta klinik terpercaya di seluruh Indonesia.</p>
        <div class="cta-btns">
            <a href="{{ route('register') }}" class="btn-white"><i class="fa-solid fa-paw"></i> Daftar Gratis Sekarang</a>
            <a href="{{ route('login') }}" class="btn-outline-white"><i class="fa-solid fa-arrow-right-to-bracket"></i> Sudah Punya Akun? Masuk</a>
        </div>
        <div class="cta-trust">
            <div class="trust-chip"><i class="fa-solid fa-certificate"></i> Dokter Berlisensi</div>
            <div class="trust-chip"><i class="fa-solid fa-lock"></i> Data Aman & Terlindungi</div>
            <div class="trust-chip"><i class="fa-solid fa-clock"></i> Layanan 24/7</div>
            <div class="trust-chip"><i class="fa-solid fa-star"></i> Rating 4.9/5</div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
function switchTab(tab) {
    document.querySelectorAll('.how-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    document.querySelectorAll('.how-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel-' + tab).classList.add('active');
}
</script>
@endpush
