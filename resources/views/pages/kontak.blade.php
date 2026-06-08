@extends('layouts.app')
@section('title', 'Kontak')
@section('meta_desc', 'Hubungi tim Vetra. Kami siap membantu Anda 24/7 melalui WhatsApp, email, telepon, atau form pesan.')

@push('styles')
<style>
    .contact-section { background:var(--gray-50); padding:64px 24px; }
    .contact-grid { display:grid; grid-template-columns:1fr 1.5fr; gap:56px; align-items:start; }
    /* INFO SIDE */
    .contact-info-card {
        background:#fff; border-radius:var(--radius-lg); padding:36px;
        border:1px solid var(--gray-200); box-shadow:var(--shadow);
    }
    .contact-info-card h3 { font-size:20px; font-weight:800; color:var(--gray-800); margin-bottom:8px; }
    .contact-info-card > p { font-size:14px; color:var(--gray-500); line-height:1.7; margin-bottom:28px; }
    .contact-item { display:flex; align-items:flex-start; gap:14px; margin-bottom:22px; }
    .contact-item:last-of-type { margin-bottom:0; }
    .contact-item-icon {
        width:46px; height:46px; border-radius:13px; background:var(--teal-pale);
        display:flex; align-items:center; justify-content:center;
        color:var(--teal); font-size:18px; flex-shrink:0;
    }
    .contact-item-icon.coral { background:#fff7ed; color:var(--coral); }
    .contact-item-icon.blue { background:var(--blue-soft); color:var(--blue); }
    .contact-item-icon.purple { background:#f5f3ff; color:#7c3aed; }
    .contact-item-text strong { display:block; font-size:14px; font-weight:700; color:var(--gray-800); margin-bottom:3px; }
    .contact-item-text span { font-size:13px; color:var(--gray-500); line-height:1.5; }
    .contact-item-text a { font-size:13px; color:var(--teal); font-weight:600; }
    .contact-item-text a:hover { text-decoration:underline; }
    /* SOCIAL */
    .social-section { margin-top:28px; padding-top:24px; border-top:1px solid var(--gray-100); }
    .social-section h4 { font-size:13px; font-weight:700; color:var(--gray-500); text-transform:uppercase; letter-spacing:1px; margin-bottom:14px; }
    .social-links { display:flex; gap:10px; flex-wrap:wrap; }
    .social-btn {
        display:flex; align-items:center; gap:8px; padding:9px 16px;
        border-radius:11px; background:var(--gray-50); border:1.5px solid var(--gray-200);
        color:var(--gray-600); font-size:13px; font-weight:600; transition:all .2s;
    }
    .social-btn:hover { background:var(--teal); color:#fff; border-color:var(--teal); transform:translateY(-2px); }
    .social-btn i { font-size:15px; }
    /* FORM */
    .contact-form-card {
        background:#fff; border-radius:var(--radius-lg); padding:40px;
        border:1px solid var(--gray-200); box-shadow:var(--shadow);
    }
    .contact-form-card h3 { font-size:20px; font-weight:800; color:var(--gray-800); margin-bottom:6px; }
    .contact-form-card > p { font-size:14px; color:var(--gray-500); margin-bottom:28px; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .form-group { margin-bottom:20px; }
    .form-group label { display:block; font-size:13px; font-weight:600; color:var(--gray-700); margin-bottom:7px; }
    .form-group input, .form-group textarea, .form-group select {
        width:100%; padding:12px 16px; border-radius:12px;
        border:1.5px solid var(--gray-200); font-size:14px; font-family:inherit;
        color:var(--gray-800); background:var(--gray-50); transition:all .2s; outline:none;
    }
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
        border-color:var(--teal); background:#fff; box-shadow:0 0 0 3px rgba(13,148,136,.1);
    }
    .form-group textarea { resize:vertical; min-height:130px; }
    .form-group .error { color:var(--coral); font-size:12px; margin-top:5px; display:block; }
    .btn-submit {
        width:100%; padding:15px; background:var(--teal); color:#fff;
        border:none; border-radius:14px; font-size:16px; font-weight:700;
        cursor:pointer; transition:all .25s; display:flex; align-items:center; justify-content:center; gap:9px;
        font-family:inherit;
    }
    .btn-submit:hover { background:var(--teal-dark); transform:translateY(-1px); box-shadow:0 6px 20px rgba(13,148,136,.35); }
    .alert-success {
        background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d;
        padding:14px 18px; border-radius:12px; font-size:14px; font-weight:500;
        margin-bottom:24px; display:flex; align-items:center; gap:9px;
    }
    /* MAP PLACEHOLDER */
    .map-section { background:var(--white); padding:0 24px 64px; }
    .map-card {
        max-width:1200px; margin:0 auto; border-radius:var(--radius-lg);
        overflow:hidden; border:1px solid var(--gray-200); box-shadow:var(--shadow);
    }
    .map-placeholder {
        height:300px; background:linear-gradient(135deg,var(--teal-50),#e0f2fe);
        display:flex; flex-direction:column; align-items:center; justify-content:center;
        color:var(--teal); gap:12px;
    }
    .map-placeholder i { font-size:48px; opacity:.5; }
    .map-placeholder p { font-size:15px; color:var(--gray-500); font-weight:500; }
    /* FAQ */
    .faq-section { background:var(--gray-50); padding:64px 24px; }
    .faq-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    .faq-item {
        background:#fff; border-radius:var(--radius); padding:22px 24px;
        border:1px solid var(--gray-200); cursor:pointer; transition:all .2s;
    }
    .faq-item:hover { border-color:var(--teal-light); box-shadow:var(--shadow); }
    .faq-q { font-size:15px; font-weight:700; color:var(--gray-800); display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .faq-q i { color:var(--teal); flex-shrink:0; transition:transform .3s; }
    .faq-a { font-size:13px; color:var(--gray-500); line-height:1.7; margin-top:12px; display:none; }
    .faq-item.open .faq-a { display:block; }
    .faq-item.open .faq-q i { transform:rotate(45deg); }
    @media(max-width:900px) {
        .contact-grid { grid-template-columns:1fr; }
        .form-row { grid-template-columns:1fr; }
        .faq-grid { grid-template-columns:1fr; }
    }
</style>
@endpush

@section('content')

<!-- PAGE HEADER -->
<div class="page-header">
    <div class="page-header-inner">
        <div class="page-header-tag"><i class="fa-solid fa-envelope"></i> Hubungi Kami</div>
        <h1>Ada Pertanyaan? Kami Siap Membantu</h1>
        <p>Hubungi tim Vetra melalui berbagai saluran komunikasi. Kami siap membantu Anda 24/7.</p>
    </div>
</div>

<!-- CONTACT MAIN -->
<section class="contact-section">
    <div class="container">
        <div class="contact-grid">

            <!-- INFO -->
            <div class="contact-info-card">
                <h3>Informasi Kontak</h3>
                <p>Tim kami siap membantu Anda kapan saja. Pilih saluran komunikasi yang paling nyaman untuk Anda.</p>

                <div class="contact-item">
                    <div class="contact-item-icon" style="background:#f0fdf4;color:#16a34a;"><i class="fa-brands fa-whatsapp"></i></div>
                    <div class="contact-item-text">
                        <strong>WhatsApp</strong>
                        <a href="https://wa.me/6281234567890" target="_blank">+62 812-3456-7890</a>
                        <span>Respons cepat, tersedia 24/7</span>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon coral"><i class="fa-solid fa-phone"></i></div>
                    <div class="contact-item-text">
                        <strong>Telepon</strong>
                        <a href="tel:02112345678">021-1234-5678</a>
                        <span>Senin–Jumat, 08.00–17.00 WIB</span>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon blue"><i class="fa-solid fa-envelope"></i></div>
                    <div class="contact-item-text">
                        <strong>Email</strong>
                        <a href="mailto:halo@vetra.id">halo@vetra.id</a>
                        <span>Dibalas dalam 1×24 jam kerja</span>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-item-icon purple"><i class="fa-solid fa-location-dot"></i></div>
                    <div class="contact-item-text">
                        <strong>Alamat Kantor</strong>
                        <span>Jl. Kesehatan Hewan No. 1<br>Jakarta Selatan, DKI Jakarta 12345</span>
                    </div>
                </div>

                <div class="social-section">
                    <h4>Ikuti Kami</h4>
                    <div class="social-links">
                        <a href="#" class="social-btn"><i class="fa-brands fa-instagram"></i> Instagram</a>
                        <a href="#" class="social-btn"><i class="fa-brands fa-facebook-f"></i> Facebook</a>
                        <a href="#" class="social-btn"><i class="fa-brands fa-x-twitter"></i> Twitter/X</a>
                        <a href="#" class="social-btn"><i class="fa-brands fa-tiktok"></i> TikTok</a>
                        <a href="#" class="social-btn"><i class="fa-brands fa-youtube"></i> YouTube</a>
                    </div>
                </div>
            </div>

            <!-- FORM -->
            <div class="contact-form-card">
                <h3>Kirim Pesan</h3>
                <p>Isi form di bawah ini dan tim kami akan segera menghubungi Anda.</p>

                @if(session('success'))
                <div class="alert-success">
                    <i class="fa-solid fa-circle-check" style="font-size:18px;"></i>
                    {{ session('success') }}
                </div>
                @endif

<form action="{{ route('kontak') }}" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nama">Nama Lengkap <span style="color:var(--coral)">*</span></label>
                            <input type="text" id="nama" name="nama" placeholder="Nama Anda" required value="{{ old('nama') }}">
                            @error('nama')<span class="error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Email <span style="color:var(--coral)">*</span></label>
                            <input type="email" id="email" name="email" placeholder="email@anda.com" required value="{{ old('email') }}">
                            @error('email')<span class="error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Nomor Telepon</label>
                            <input type="tel" id="phone" name="phone" placeholder="08xx xxxx xxxx" value="{{ old('phone') }}">
                            @error('phone')<span class="error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="subjek">Subjek <span style="color:var(--coral)">*</span></label>
                            <input type="text" id="subjek" name="subjek" placeholder="Topik pesan Anda" required value="{{ old('subjek') }}">
                            @error('subjek')<span class="error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pesan">Pesan <span style="color:var(--coral)">*</span></label>
                        <textarea id="pesan" name="pesan" placeholder="Tuliskan pesan Anda di sini..." required>{{ old('pesan') }}</textarea>
                        @error('pesan')<span class="error">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit" class="btn-submit">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Pesan
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>

<!-- MAP -->
<div class="map-section">
    <div class="map-card">
        <div class="map-placeholder">
            <i class="fa-solid fa-map-location-dot"></i>
            <p>Jl. Kesehatan Hewan No. 1, Jakarta Selatan</p>
        </div>
    </div>
</div>

<!-- FAQ -->
<section class="faq-section">
    <div class="container">
        <div style="text-align:center;margin-bottom:40px;">
            <div class="section-tag">FAQ</div>
            <h2 class="section-title">Pertanyaan yang Sering Diajukan</h2>
        </div>
        <div class="faq-grid">
            <div class="faq-item" onclick="toggleFaq(this)">
                <div class="faq-q">Bagaimana cara mendaftar di Vetra? <i class="fa-solid fa-plus"></i></div>
                <div class="faq-a">Klik tombol "Daftar Gratis" di navbar, isi data diri Anda, dan akun langsung aktif. Proses pendaftaran hanya membutuhkan waktu kurang dari 1 menit.</div>
            </div>
            <div class="faq-item" onclick="toggleFaq(this)">
                <div class="faq-q">Apakah konsultasi di Vetra berbayar? <i class="fa-solid fa-plus"></i></div>
                <div class="faq-a">Pendaftaran akun gratis. Biaya konsultasi bervariasi tergantung dokter dan jenis layanan yang dipilih. Anda bisa melihat tarif sebelum memulai konsultasi.</div>
            </div>
            <div class="faq-item" onclick="toggleFaq(this)">
                <div class="faq-q">Bagaimana cara mendaftarkan klinik saya? <i class="fa-solid fa-plus"></i></div>
                <div class="faq-a">Daftar akun dengan role "Klinik", lengkapi profil klinik Anda, dan tim Vetra akan memverifikasi dalam 1-3 hari kerja. Setelah terverifikasi, klinik Anda akan tampil di platform.</div>
            </div>
            <div class="faq-item" onclick="toggleFaq(this)">
                <div class="faq-q">Apakah dokter di Vetra sudah berlisensi? <i class="fa-solid fa-plus"></i></div>
                <div class="faq-a">Ya, semua dokter hewan di Vetra telah melalui proses verifikasi ketat termasuk pengecekan lisensi praktik dari Persatuan Dokter Hewan Indonesia (PDHI).</div>
            </div>
            <div class="faq-item" onclick="toggleFaq(this)">
                <div class="faq-q">Berapa lama waktu respons dokter? <i class="fa-solid fa-plus"></i></div>
                <div class="faq-a">Rata-rata waktu respons dokter adalah 3-10 menit untuk konsultasi online. Untuk kondisi darurat, tersedia dokter siaga yang merespons dalam hitungan menit.</div>
            </div>
            <div class="faq-item" onclick="toggleFaq(this)">
                <div class="faq-q">Hewan apa saja yang bisa dikonsultasikan? <i class="fa-solid fa-plus"></i></div>
                <div class="faq-a">Vetra melayani konsultasi untuk berbagai jenis hewan peliharaan termasuk kucing, anjing, kelinci, burung, hamster, reptil, dan hewan eksotik lainnya.</div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
function toggleFaq(el) {
    el.classList.toggle('open');
}
</script>
@endpush
