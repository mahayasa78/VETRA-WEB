@extends('layouts.app')
@section('title', 'Klinik & Dokter')
@section('meta_desc', 'Temukan klinik hewan terpercaya dan dokter spesialis yang bergabung dengan jaringan Vetra.')

@push('styles')
<style>
    /* ===== FILTER BAR ===== */
    .filter-bar {
        background:#fff; border-bottom:1px solid var(--gray-200);
        padding:16px 24px; position:sticky; top:68px; z-index:90;
        box-shadow:0 2px 8px rgba(0,0,0,.04);
    }
    .filter-inner { max-width:1200px; margin:0 auto; display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
    .search-box {
        flex:1; min-width:220px; display:flex; align-items:center; gap:10px;
        background:var(--gray-50); border:1.5px solid var(--gray-200); border-radius:12px;
        padding:10px 16px; transition:all .2s;
    }
    .search-box:focus-within { border-color:var(--teal); background:#fff; box-shadow:0 0 0 3px rgba(13,148,136,.1); }
    .search-box i { color:var(--gray-400); font-size:14px; }
    .search-box input { border:none; background:transparent; outline:none; font-size:14px; font-family:inherit; color:var(--gray-800); width:100%; }
    .filter-tabs { display:flex; gap:6px; }
    .filter-tab {
        padding:9px 18px; border-radius:10px; font-size:13px; font-weight:600;
        border:1.5px solid var(--gray-200); color:var(--gray-600); cursor:pointer;
        background:#fff; transition:all .2s;
    }
    .filter-tab.active, .filter-tab:hover { background:var(--teal); color:#fff; border-color:var(--teal); }

    /* ===== CARDS ===== */
    .clinics-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; margin-bottom:64px; }
    .clinic-card {
        border-radius:var(--radius-lg); border:1px solid var(--gray-200);
        overflow:hidden; transition:all .3s; background:#fff; cursor:pointer;
    }
    .clinic-card:hover { transform:translateY(-5px); box-shadow:var(--shadow-lg); border-color:var(--teal-light); }
    .clinic-header {
        padding:22px 22px 18px; background:linear-gradient(135deg,var(--teal-50),#e0f2fe);
        display:flex; align-items:flex-start; gap:14px;
    }
    .clinic-icon {
        width:52px; height:52px; background:var(--teal); border-radius:14px;
        display:flex; align-items:center; justify-content:center; color:#fff; font-size:22px; flex-shrink:0;
    }
    .clinic-name { font-size:16px; font-weight:700; color:var(--gray-800); margin-bottom:4px; }
    .clinic-jam { font-size:12px; color:var(--teal); font-weight:600; display:flex; align-items:center; gap:4px; }
    .clinic-body { padding:18px 22px 22px; }
    .clinic-info-row { display:flex; align-items:flex-start; gap:9px; margin-bottom:10px; font-size:13px; color:var(--gray-600); }
    .clinic-info-row i { color:var(--teal); margin-top:2px; flex-shrink:0; width:14px; }
    .clinic-doctors { margin-top:16px; padding-top:16px; border-top:1px solid var(--gray-100); }
    .clinic-doctors-title { font-size:11px; font-weight:700; color:var(--gray-400); text-transform:uppercase; letter-spacing:1px; margin-bottom:10px; }
    .doctor-chip {
        display:inline-flex; align-items:center; gap:5px;
        background:var(--teal-pale); color:var(--teal-dark);
        padding:5px 10px; border-radius:20px; font-size:12px; font-weight:600; margin:0 4px 6px 0;
    }
    .card-click-hint {
        display:flex; align-items:center; justify-content:center; gap:6px;
        font-size:12px; color:var(--teal); font-weight:600; padding:10px 22px 14px;
        border-top:1px solid var(--gray-100); background:var(--teal-50);
    }

    /* ===== DOCTORS GRID ===== */
    .doctors-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; }
    .doc-card {
        background:#fff; border-radius:var(--radius-lg); overflow:hidden;
        border:1px solid var(--gray-200); transition:all .3s; cursor:pointer;
    }
    .doc-card:hover { transform:translateY(-5px); box-shadow:var(--shadow-lg); border-color:var(--teal-light); }
    .doc-avatar { height:110px; display:flex; align-items:center; justify-content:center; font-size:48px; }
    .doc-avatar.c1{background:linear-gradient(135deg,#ccfbf1,#a7f3d0);color:#0f766e;}
    .doc-avatar.c2{background:linear-gradient(135deg,#dbeafe,#bfdbfe);color:#1d4ed8;}
    .doc-avatar.c3{background:linear-gradient(135deg,#fce7f3,#fbcfe8);color:#be185d;}
    .doc-avatar.c4{background:linear-gradient(135deg,#fef3c7,#fde68a);color:#b45309;}
    .doc-avatar.c5{background:linear-gradient(135deg,#f5f3ff,#ede9fe);color:#7c3aed;}
    .doc-avatar.c6{background:linear-gradient(135deg,#f0fdf4,#bbf7d0);color:#15803d;}
    .doc-avatar.c7{background:linear-gradient(135deg,#fff7ed,#fed7aa);color:#c2410c;}
    .doc-avatar.c8{background:linear-gradient(135deg,#fdf2f8,#fbcfe8);color:#9d174d;}
    .doc-body { padding:16px; }
    .doc-name { font-size:14px; font-weight:700; color:var(--gray-800); margin-bottom:3px; }
    .doc-spec { font-size:12px; color:var(--teal); font-weight:600; margin-bottom:8px; }
    .doc-clinic-lbl { font-size:11px; color:var(--gray-400); display:flex; align-items:center; gap:4px; }
    .doc-card-hint {
        font-size:11px; color:var(--teal); font-weight:600; padding:8px 16px 12px;
        display:flex; align-items:center; gap:5px;
    }

    /* ===== SECTION DIVIDER ===== */
    .section-divider { display:flex; align-items:center; gap:16px; margin-bottom:28px; }
    .section-divider h2 { font-size:22px; font-weight:800; color:var(--gray-800); white-space:nowrap; }
    .section-divider .line { flex:1; height:1px; background:var(--gray-200); }
    .section-divider .count-badge {
        background:var(--teal-pale); color:var(--teal-dark); padding:4px 12px;
        border-radius:20px; font-size:12px; font-weight:700; white-space:nowrap;
    }
</style>
@endpush

@push('styles')
<style>
    /* ===== MODAL OVERLAY ===== */
    .modal-overlay {
        position:fixed; inset:0; z-index:1000;
        background:rgba(15,23,42,.55); backdrop-filter:blur(4px);
        display:flex; align-items:center; justify-content:center; padding:20px;
        opacity:0; pointer-events:none; transition:opacity .25s;
    }
    .modal-overlay.open { opacity:1; pointer-events:all; }

    /* ===== MODAL BOX ===== */
    .modal-box {
        background:#fff; border-radius:24px; width:100%; max-width:640px;
        max-height:90vh; overflow-y:auto; box-shadow:0 24px 80px rgba(0,0,0,.2);
        transform:translateY(24px) scale(.97); transition:transform .3s, opacity .3s;
        opacity:0; position:relative;
    }
    .modal-overlay.open .modal-box { transform:translateY(0) scale(1); opacity:1; }

    /* scrollbar */
    .modal-box::-webkit-scrollbar { width:5px; }
    .modal-box::-webkit-scrollbar-track { background:transparent; }
    .modal-box::-webkit-scrollbar-thumb { background:var(--gray-200); border-radius:10px; }

    /* close btn */
    .modal-close {
        position:absolute; top:16px; right:16px; z-index:10;
        width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,.9);
        border:1.5px solid var(--gray-200); display:flex; align-items:center; justify-content:center;
        cursor:pointer; font-size:16px; color:var(--gray-600); transition:all .2s;
        box-shadow:0 2px 8px rgba(0,0,0,.08);
    }
    .modal-close:hover { background:var(--gray-100); color:var(--gray-900); }

    /* ===== MODAL — KLINIK ===== */
    .modal-clinic-hero {
        background:linear-gradient(135deg,var(--teal-dark),var(--teal),#0891b2);
        padding:36px 32px 28px; color:#fff; position:relative; overflow:hidden;
    }
    .modal-clinic-hero::before {
        content:''; position:absolute; top:-60px; right:-60px; width:220px; height:220px;
        background:rgba(255,255,255,.07); border-radius:50%;
    }
    .modal-clinic-hero-icon {
        width:64px; height:64px; background:rgba(255,255,255,.2); border-radius:18px;
        display:flex; align-items:center; justify-content:center; font-size:28px;
        margin-bottom:16px; border:2px solid rgba(255,255,255,.3);
    }
    .modal-clinic-hero h2 { font-size:24px; font-weight:800; margin-bottom:6px; }
    .modal-clinic-hero .jam {
        display:inline-flex; align-items:center; gap:6px;
        background:rgba(255,255,255,.2); padding:5px 12px; border-radius:20px;
        font-size:13px; font-weight:600;
    }
    .modal-body { padding:28px 32px 32px; }
    .modal-info-group { margin-bottom:24px; }
    .modal-info-group h4 {
        font-size:11px; font-weight:700; color:var(--gray-400);
        text-transform:uppercase; letter-spacing:1.2px; margin-bottom:14px;
        display:flex; align-items:center; gap:8px;
    }
    .modal-info-group h4::after { content:''; flex:1; height:1px; background:var(--gray-100); }
    .modal-info-row {
        display:flex; align-items:flex-start; gap:12px; margin-bottom:12px;
        font-size:14px; color:var(--gray-700); line-height:1.6;
    }
    .modal-info-row:last-child { margin-bottom:0; }
    .modal-info-icon {
        width:34px; height:34px; border-radius:10px; background:var(--teal-pale);
        display:flex; align-items:center; justify-content:center;
        color:var(--teal); font-size:14px; flex-shrink:0; margin-top:1px;
    }
    .modal-info-text strong { display:block; font-size:12px; color:var(--gray-400); font-weight:600; margin-bottom:2px; }
    .modal-info-text span { font-size:14px; color:var(--gray-800); font-weight:500; }

    /* doctor list inside clinic modal */
    .modal-doc-list { display:flex; flex-direction:column; gap:10px; }
    .modal-doc-item {
        display:flex; align-items:center; gap:14px; padding:14px 16px;
        background:var(--gray-50); border-radius:14px; border:1px solid var(--gray-200);
        transition:all .2s; cursor:pointer;
    }
    .modal-doc-item:hover { background:var(--teal-pale); border-color:var(--teal-light); }
    .modal-doc-ava {
        width:44px; height:44px; border-radius:12px; flex-shrink:0;
        display:flex; align-items:center; justify-content:center; font-size:20px;
    }
    .modal-doc-item-name { font-size:14px; font-weight:700; color:var(--gray-800); }
    .modal-doc-item-spec { font-size:12px; color:var(--teal); font-weight:600; margin-top:2px; }
    .modal-doc-arrow { margin-left:auto; color:var(--gray-300); font-size:13px; }

    /* ===== MODAL — DOKTER ===== */
    .modal-doc-hero {
        padding:36px 32px 28px; position:relative; overflow:hidden;
    }
    .modal-doc-hero::before {
        content:''; position:absolute; inset:0; opacity:.12;
    }
    .modal-doc-hero-inner { position:relative; z-index:1; display:flex; align-items:center; gap:20px; }
    .modal-doc-big-ava {
        width:80px; height:80px; border-radius:20px; flex-shrink:0;
        display:flex; align-items:center; justify-content:center; font-size:36px;
        border:3px solid rgba(255,255,255,.6); box-shadow:0 4px 20px rgba(0,0,0,.1);
    }
    .modal-doc-hero-name { font-size:22px; font-weight:800; color:var(--gray-800); margin-bottom:4px; }
    .modal-doc-hero-spec {
        display:inline-flex; align-items:center; gap:6px;
        background:var(--teal-pale); color:var(--teal-dark);
        padding:5px 12px; border-radius:20px; font-size:13px; font-weight:700;
    }
    .modal-doc-clinic-tag {
        display:inline-flex; align-items:center; gap:6px; margin-top:8px;
        font-size:13px; color:var(--gray-500); font-weight:500;
    }

    /* modal CTA */
    .modal-cta {
        display:flex; gap:10px; padding:0 32px 32px; flex-wrap:wrap;
    }
    .modal-cta a {
        flex:1; min-width:140px; padding:13px 20px; border-radius:12px;
        font-size:14px; font-weight:700; text-align:center;
        display:flex; align-items:center; justify-content:center; gap:8px; transition:all .2s;
    }
    .modal-cta .cta-primary { background:var(--teal); color:#fff; box-shadow:0 4px 14px rgba(13,148,136,.3); }
    .modal-cta .cta-primary:hover { background:var(--teal-dark); transform:translateY(-1px); }
    .modal-cta .cta-secondary { background:var(--gray-50); color:var(--gray-700); border:1.5px solid var(--gray-200); }
    .modal-cta .cta-secondary:hover { background:var(--gray-100); }

    @media(max-width:1024px) { .clinics-grid{grid-template-columns:repeat(2,1fr);} .doctors-grid{grid-template-columns:repeat(2,1fr);} }
    @media(max-width:640px) {
        .clinics-grid,.doctors-grid{grid-template-columns:1fr;}
        .filter-tabs{display:none;}
        .modal-body { padding:20px 20px 24px; }
        .modal-clinic-hero, .modal-doc-hero { padding:28px 20px 22px; }
        .modal-cta { padding:0 20px 24px; }
    }
</style>
@endpush

@section('content')

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="page-header-inner">
        <div class="page-header-tag"><i class="fa-solid fa-hospital"></i> Jaringan Vetra</div>
        <h1>Klinik & Dokter Mitra Vetra</h1>
        <p>Temukan klinik hewan terpercaya dan dokter spesialis yang bergabung dengan jaringan Vetra di seluruh Indonesia.</p>
    </div>
</div>

<!-- FILTER BAR -->
<div class="filter-bar">
    <div class="filter-inner">
        <div class="search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchInput" placeholder="Cari klinik atau dokter..." oninput="filterCards()">
        </div>
        <div class="filter-tabs">
            <button class="filter-tab active" onclick="showTab('all', this)">Semua</button>
            <button class="filter-tab" onclick="showTab('klinik', this)">Klinik</button>
            <button class="filter-tab" onclick="showTab('dokter', this)">Dokter</button>
        </div>
    </div>
</div>

<section style="padding:56px 24px;">
    <div class="container">

        <!-- ===== CLINICS ===== -->
        <div id="section-klinik">
            <div class="section-divider">
                <h2><i class="fa-solid fa-hospital" style="color:var(--teal);margin-right:8px;"></i>Klinik Terdaftar</h2>
                <div class="line"></div>
                <span class="count-badge">{{ $clinics->count() }} Klinik</span>
            </div>

            @if($clinics->count() > 0)
            <div class="clinics-grid" id="clinicsGrid">
                @foreach($clinics as $clinic)
                <div class="clinic-card"
                     data-name="{{ strtolower($clinic->nama_klinik) }} {{ strtolower($clinic->alamat) }}"
                     onclick="openClinicModal({{ $clinic->id }})">
                    <div class="clinic-header">
                        <div class="clinic-icon"><i class="fa-solid fa-hospital"></i></div>
                        <div>
                            <div class="clinic-name">{{ $clinic->nama_klinik }}</div>
                            @if($clinic->jam_operasional)
                            <div class="clinic-jam"><i class="fa-regular fa-clock"></i> {{ $clinic->jam_operasional }}</div>
                            @endif
                        </div>
                    </div>
                    <div class="clinic-body">
                        <div class="clinic-info-row"><i class="fa-solid fa-location-dot"></i><span>{{ $clinic->alamat }}</span></div>
                        @if($clinic->no_hp)
                        <div class="clinic-info-row"><i class="fa-solid fa-phone"></i><span>{{ $clinic->no_hp }}</span></div>
                        @endif
                        @if($clinic->deskripsi)
                        <div class="clinic-info-row"><i class="fa-solid fa-circle-info"></i><span>{{ Str::limit($clinic->deskripsi, 100) }}</span></div>
                        @endif
                        @if($clinic->doctors && $clinic->doctors->count() > 0)
                        <div class="clinic-doctors">
                            <div class="clinic-doctors-title">Dokter di Klinik Ini</div>
                            @foreach($clinic->doctors as $doc)
                            <span class="doctor-chip"><i class="fa-solid fa-user-doctor"></i> {{ $doc->user->name ?? 'Dokter' }} — {{ $doc->spesialis }}</span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <div class="card-click-hint"><i class="fa-solid fa-circle-info"></i> Klik untuk info lengkap</div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <i class="fa-solid fa-hospital"></i>
                <p>Belum ada klinik yang terdaftar.</p>
                <a href="{{ route('register') }}" class="btn-primary">Daftarkan Klinik</a>
            </div>
            @endif
        </div>

        <!-- ===== DOCTORS ===== -->
        <div id="section-dokter" style="margin-top:16px;">
            <div class="section-divider">
                <h2><i class="fa-solid fa-user-doctor" style="color:var(--teal);margin-right:8px;"></i>Dokter Hewan</h2>
                <div class="line"></div>
                <span class="count-badge">{{ $doctors->count() }} Dokter</span>
            </div>

            @if($doctors->count() > 0)
            <div class="doctors-grid" id="doctorsGrid">
                @foreach($doctors as $index => $doctor)
                @php $colors=['c1','c2','c3','c4','c5','c6','c7','c8']; $color=$colors[$index%8]; @endphp
                <div class="doc-card"
                     data-name="{{ strtolower($doctor->user->name ?? '') }} {{ strtolower($doctor->spesialis) }}"
                     onclick="openDoctorModal({{ $doctor->id }})">
                    <div class="doc-avatar {{ $color }}"><i class="fa-solid fa-user-doctor"></i></div>
                    <div class="doc-body">
                        <div class="doc-name">drh. {{ $doctor->user->name ?? 'Dokter Hewan' }}</div>
                        <div class="doc-spec">{{ $doctor->spesialis }}</div>
                        @if($doctor->clinic)
                        <div class="doc-clinic-lbl"><i class="fa-solid fa-hospital"></i> {{ $doctor->clinic->nama_klinik }}</div>
                        @else
                        <div class="doc-clinic-lbl"><i class="fa-solid fa-globe"></i> Konsultasi Online</div>
                        @endif
                    </div>
                    <div class="doc-card-hint"><i class="fa-solid fa-circle-info"></i> Lihat profil lengkap</div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <i class="fa-solid fa-user-doctor"></i>
                <p>Belum ada dokter yang terdaftar.</p>
            </div>
            @endif
        </div>

    </div>
</section>

<!-- ===== MODAL KLINIK ===== -->
<div class="modal-overlay" id="clinicModal" onclick="closeOnOverlay(event, 'clinicModal')">
    <div class="modal-box" id="clinicModalBox">
        <button class="modal-close" onclick="closeModal('clinicModal')"><i class="fa-solid fa-xmark"></i></button>
        <div id="clinicModalContent">
            <!-- diisi JS -->
        </div>
    </div>
</div>

<!-- ===== MODAL DOKTER ===== -->
<div class="modal-overlay" id="doctorModal" onclick="closeOnOverlay(event, 'doctorModal')">
    <div class="modal-box" id="doctorModalBox">
        <button class="modal-close" onclick="closeModal('doctorModal')"><i class="fa-solid fa-xmark"></i></button>
        <div id="doctorModalContent">
            <!-- diisi JS -->
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ===== DATA dari Controller ===== */
const clinicsData = {!! json_encode($clinicsJson) !!};
const doctorsData = {!! json_encode($doctorsJson) !!};

const avatarColors = ['c1','c2','c3','c4','c5','c6','c7','c8'];
const avatarGradients = {
    c1:'linear-gradient(135deg,#ccfbf1,#a7f3d0)', c2:'linear-gradient(135deg,#dbeafe,#bfdbfe)',
    c3:'linear-gradient(135deg,#fce7f3,#fbcfe8)', c4:'linear-gradient(135deg,#fef3c7,#fde68a)',
    c5:'linear-gradient(135deg,#f5f3ff,#ede9fe)', c6:'linear-gradient(135deg,#f0fdf4,#bbf7d0)',
    c7:'linear-gradient(135deg,#fff7ed,#fed7aa)', c8:'linear-gradient(135deg,#fdf2f8,#fbcfe8)',
};
const avatarTextColors = {
    c1:'#0f766e',c2:'#1d4ed8',c3:'#be185d',c4:'#b45309',
    c5:'#7c3aed',c6:'#15803d',c7:'#c2410c',c8:'#9d174d',
};

/* ===== OPEN CLINIC MODAL ===== */
function openClinicModal(id) {
    const c = clinicsData.find(x => x.id === id);
    if (!c) return;

    let doctorsHtml = '';
    if (c.doctors && c.doctors.length > 0) {
        doctorsHtml = `
        <div class="modal-info-group">
            <h4><i class="fa-solid fa-user-doctor" style="color:var(--teal)"></i> Dokter di Klinik Ini</h4>
            <div class="modal-doc-list">
                ${c.doctors.map((d, i) => {
                    const col = avatarColors[i % 8];
                    return `
                    <div class="modal-doc-item" onclick="closeModal('clinicModal'); openDoctorModal(${d.id})">
                        <div class="modal-doc-ava" style="background:${avatarGradients[col]};color:${avatarTextColors[col]};">
                            <i class="fa-solid fa-user-doctor"></i>
                        </div>
                        <div>
                            <div class="modal-doc-item-name">drh. ${d.name}</div>
                            <div class="modal-doc-item-spec">${d.spesialis}</div>
                        </div>
                        <i class="fa-solid fa-chevron-right modal-doc-arrow"></i>
                    </div>`;
                }).join('')}
            </div>
        </div>`;
    } else {
        doctorsHtml = `
        <div class="modal-info-group">
            <h4><i class="fa-solid fa-user-doctor" style="color:var(--teal)"></i> Dokter di Klinik Ini</h4>
            <p style="font-size:13px;color:var(--gray-400);padding:12px 0;">Belum ada dokter terdaftar di klinik ini.</p>
        </div>`;
    }

    document.getElementById('clinicModalContent').innerHTML = `
        <div class="modal-clinic-hero">
            <div class="modal-clinic-hero-icon"><i class="fa-solid fa-hospital"></i></div>
            <h2>${c.nama_klinik}</h2>
            ${c.jam_operasional ? `<span class="jam"><i class="fa-regular fa-clock"></i> ${c.jam_operasional}</span>` : ''}
        </div>
        <div class="modal-body">
            <div class="modal-info-group">
                <h4><i class="fa-solid fa-circle-info" style="color:var(--teal)"></i> Informasi Klinik</h4>
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa-solid fa-location-dot"></i></div>
                    <div class="modal-info-text"><strong>Alamat</strong><span>${c.alamat}</span></div>
                </div>
                ${c.no_hp ? `
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa-solid fa-phone"></i></div>
                    <div class="modal-info-text"><strong>Nomor Telepon</strong><span>${c.no_hp}</span></div>
                </div>` : ''}
                ${c.jam_operasional ? `
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa-regular fa-clock"></i></div>
                    <div class="modal-info-text"><strong>Jam Operasional</strong><span>${c.jam_operasional}</span></div>
                </div>` : ''}
                ${c.deskripsi ? `
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa-solid fa-align-left"></i></div>
                    <div class="modal-info-text"><strong>Deskripsi</strong><span>${c.deskripsi}</span></div>
                </div>` : ''}
            </div>
            ${doctorsHtml}
        </div>
        <div class="modal-cta">
            ${c.no_hp ? `<a href="tel:${c.no_hp}" class="cta-primary"><i class="fa-solid fa-phone"></i> Hubungi Klinik</a>` : ''}
            <a href="/booking?clinic_id=${c.id}" class="cta-primary"><i class="fa-solid fa-calendar-check"></i> Booking Sekarang</a>
        </div>`;

    openModal('clinicModal');
}

/* ===== OPEN DOCTOR MODAL ===== */
function openDoctorModal(id) {
    const d = doctorsData.find(x => x.id === id);
    if (!d) return;
    const idx = doctorsData.indexOf(d);
    const col = avatarColors[idx % 8];

    const clinicHtml = d.clinic ? `
        <div class="modal-info-group">
            <h4><i class="fa-solid fa-hospital" style="color:var(--teal)"></i> Klinik Tempat Praktik</h4>
            <div class="modal-info-row">
                <div class="modal-info-icon"><i class="fa-solid fa-hospital"></i></div>
                <div class="modal-info-text"><strong>Nama Klinik</strong><span>${d.clinic.nama_klinik}</span></div>
            </div>
            <div class="modal-info-row">
                <div class="modal-info-icon"><i class="fa-solid fa-location-dot"></i></div>
                <div class="modal-info-text"><strong>Alamat Klinik</strong><span>${d.clinic.alamat}</span></div>
            </div>
            ${d.clinic.no_hp ? `
            <div class="modal-info-row">
                <div class="modal-info-icon"><i class="fa-solid fa-phone"></i></div>
                <div class="modal-info-text"><strong>Telepon Klinik</strong><span>${d.clinic.no_hp}</span></div>
            </div>` : ''}
            <div style="margin-top:12px;">
                <button onclick="closeModal('doctorModal'); openClinicModal(${d.clinic.id})"
                    style="display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border-radius:10px;
                    background:var(--teal-pale);color:var(--teal-dark);border:none;cursor:pointer;
                    font-size:13px;font-weight:700;font-family:inherit;transition:all .2s;"
                    onmouseover="this.style.background='var(--teal)';this.style.color='#fff'"
                    onmouseout="this.style.background='var(--teal-pale)';this.style.color='var(--teal-dark)'">
                    <i class="fa-solid fa-hospital"></i> Lihat Detail Klinik
                </button>
            </div>
        </div>` : `
        <div class="modal-info-group">
            <h4><i class="fa-solid fa-hospital" style="color:var(--teal)"></i> Praktik</h4>
            <div class="modal-info-row">
                <div class="modal-info-icon"><i class="fa-solid fa-globe"></i></div>
                <div class="modal-info-text"><strong>Mode Praktik</strong><span>Konsultasi Online</span></div>
            </div>
        </div>`;

    document.getElementById('doctorModalContent').innerHTML = `
        <div class="modal-doc-hero" style="background:${avatarGradients[col]};">
            <div class="modal-doc-hero-inner">
                <div class="modal-doc-big-ava" style="background:${avatarGradients[col]};color:${avatarTextColors[col]};">
                    <i class="fa-solid fa-user-doctor"></i>
                </div>
                <div>
                    <div class="modal-doc-hero-name">drh. ${d.name}</div>
                    <div class="modal-doc-hero-spec"><i class="fa-solid fa-stethoscope"></i> ${d.spesialis}</div>
                    ${d.clinic ? `<div class="modal-doc-clinic-tag"><i class="fa-solid fa-hospital"></i> ${d.clinic.nama_klinik}</div>` : '<div class="modal-doc-clinic-tag"><i class="fa-solid fa-globe"></i> Konsultasi Online</div>'}
                </div>
            </div>
        </div>
        <div class="modal-body">
            <div class="modal-info-group">
                <h4><i class="fa-solid fa-id-card" style="color:var(--teal)"></i> Profil Dokter</h4>
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa-solid fa-user-doctor"></i></div>
                    <div class="modal-info-text"><strong>Nama Lengkap</strong><span>drh. ${d.name}</span></div>
                </div>
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa-solid fa-stethoscope"></i></div>
                    <div class="modal-info-text"><strong>Spesialisasi</strong><span>${d.spesialis}</span></div>
                </div>
                ${d.email ? `
                <div class="modal-info-row">
                    <div class="modal-info-icon"><i class="fa-solid fa-envelope"></i></div>
                    <div class="modal-info-text"><strong>Email</strong><span>${d.email}</span></div>
                </div>` : ''}
            </div>
            ${clinicHtml}
        </div>
        <div class="modal-cta">
            <a href="/chat" class="cta-primary"><i class="fa-solid fa-comments"></i> Konsultasi Sekarang</a>
            <a href="/booking?clinic_id=${d.clinic ? d.clinic.id : ''}&doctor_id=${d.user_id}" class="cta-primary" style="background:var(--blue);box-shadow:0 4px 14px rgba(59,130,246,.3);"><i class="fa-solid fa-calendar-check"></i> Booking Jadwal</a>
        </div>`;

    openModal('doctorModal');
}

/* ===== HELPERS ===== */
function openModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}
function closeOnOverlay(e, id) {
    if (e.target === document.getElementById(id)) closeModal(id);
}
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeModal('clinicModal');
        closeModal('doctorModal');
    }
});

/* ===== FILTER & SEARCH ===== */
function showTab(tab, btn) {
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    const sk = document.getElementById('section-klinik');
    const sd = document.getElementById('section-dokter');
    if (tab === 'all')    { sk.style.display=''; sd.style.display=''; }
    else if (tab === 'klinik') { sk.style.display=''; sd.style.display='none'; }
    else                  { sk.style.display='none'; sd.style.display=''; }
}
function filterCards() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#clinicsGrid .clinic-card').forEach(c => {
        c.style.display = c.dataset.name.includes(q) ? '' : 'none';
    });
    document.querySelectorAll('#doctorsGrid .doc-card').forEach(c => {
        c.style.display = c.dataset.name.includes(q) ? '' : 'none';
    });
}
</script>
@endpush
