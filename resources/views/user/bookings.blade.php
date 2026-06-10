@extends('layouts.app')
@section('title', 'Booking Saya')

@push('styles')
<style>
    .bookings-container { max-width: 1100px; margin: 0 auto; padding: 40px 24px; }
    .bookings-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
    .btn-book {
        background: var(--teal); color: #fff; padding: 12px 24px; border-radius: 12px;
        font-weight: 700; font-size: 14px; border: none; cursor: pointer;
        display: flex; align-items: center; gap: 8px; transition: background .2s;
        text-decoration: none;
    }
    .btn-book:hover { background: var(--teal-dark); }
    .bookings-list { display: flex; flex-direction: column; gap: 16px; }
    .booking-card {
        background: #fff; border-radius: 16px; border: 1px solid var(--gray-200);
        padding: 24px; box-shadow: var(--shadow); transition: box-shadow .2s;
    }
    .booking-card:hover { box-shadow: var(--shadow-lg); }
    .booking-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
    .booking-date { font-size: 17px; font-weight: 800; color: var(--gray-800); }
    .status-badge { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; white-space: nowrap; }
    .status-pending   { background: #fef3c7; color: #92400e; }
    .status-confirmed { background: #dbeafe; color: #1e40af; }
    .status-completed { background: #dcfce7; color: #166534; }
    .status-done      { background: #dcfce7; color: #166534; }
    .status-rejected  { background: #fee2e2; color: #991b1b; }
    .booking-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
    .info-item { font-size: 14px; color: var(--gray-600); }
    .info-item strong { display: block; font-size: 12px; color: var(--gray-400); margin-bottom: 4px; }
    .cancel-btn {
        margin-top: 16px; padding: 8px 18px; border-radius: 10px; font-size: 13px; font-weight: 600;
        background: #fee2e2; color: #dc2626; border: none; cursor: pointer; transition: background .2s;
    }
    .cancel-btn:hover { background: #dc2626; color: #fff; }
    .state-box {
        text-align: center; padding: 60px 24px; background: #fff;
        border-radius: 16px; border: 1px solid var(--gray-200);
    }
    .state-box i { font-size: 48px; margin-bottom: 16px; display: block; }
    .state-box p { font-size: 16px; color: var(--gray-500); }
    .state-box a {
        display: inline-block; margin-top: 16px; padding: 10px 20px;
        background: var(--teal); color: #fff; border-radius: 10px;
        text-decoration: none; font-weight: 600;
    }
    .retry-btn {
        display: inline-block; margin-top: 16px; padding: 10px 20px;
        background: var(--teal); color: #fff; border-radius: 10px;
        border: none; cursor: pointer; font-weight: 600; font-size: 14px;
        font-family: inherit;
    }
</style>
@endpush

@section('content')
<div style="padding-top:68px;background:var(--gray-50);min-height:100vh;">
    <div class="bookings-container">
        <div class="bookings-header">
            <div>
                <h1>📅 Booking Saya</h1>
                <p style="font-size:15px;color:var(--gray-500);margin-top:4px;">Lihat dan kelola jadwal konsultasi Anda</p>
            </div>
            <a href="/booking" class="btn-book">
                <i class="fa-solid fa-plus"></i> Buat Booking Baru
            </a>
        </div>

        <div id="bookingsList" class="bookings-list">
            <div class="state-box">
                <i class="fa-solid fa-spinner fa-spin" style="color:var(--teal);"></i>
                <p>Memuat data booking…</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ── Inline authFetch: handles JWT auto-refresh ─────────────────────────────
var _isRefreshing = false;
var _refreshQueue = [];

async function _doRefresh(oldToken) {
    var r = await fetch('/api/auth/refresh', {
        method: 'POST',
        headers: { 'Authorization': 'Bearer ' + oldToken, 'Accept': 'application/json' }
    });
    if (r.ok) {
        var d = await r.json();
        var newTok = d.access_token || d.token;
        if (newTok) {
            localStorage.setItem('vetra_token', newTok);
            return newTok;
        }
    }
    return null;
}

async function apiFetch(url, opts) {
    opts = opts || {};
    var token = localStorage.getItem('vetra_token');
    var headers = Object.assign({ 'Accept': 'application/json', 'Authorization': 'Bearer ' + token }, opts.headers || {});
    var res = await fetch(url, Object.assign({}, opts, { headers: headers }));

    if (res.status !== 401) return res;

    // 401 → try refresh
    var newToken = await _doRefresh(token);
    if (!newToken) {
        localStorage.removeItem('vetra_token');
        localStorage.removeItem('vetra_user');
        window.location.href = '/login';
        return res;
    }
    // Retry with new token
    headers['Authorization'] = 'Bearer ' + newToken;
    return fetch(url, Object.assign({}, opts, { headers: headers }));
}
// ── End inline authFetch ───────────────────────────────────────────────────

(function() {
    var vetraToken = localStorage.getItem('vetra_token');
    if (!vetraToken) { window.location.href = '/login'; return; }

    var container = document.getElementById('bookingsList');

    var statusLabels = {
        pending:   'Menunggu Konfirmasi',
        confirmed: 'Dikonfirmasi',
        done:      'Selesai',
        completed: 'Selesai',
        rejected:  'Dibatalkan'
    };

    function showLoading() {
        container.innerHTML = '<div class="state-box"><i class="fa-solid fa-spinner fa-spin" style="color:var(--teal);"></i><p>Memuat data booking…</p></div>';
    }
    function showEmpty() {
        container.innerHTML = '<div class="state-box"><i class="fa-solid fa-calendar-xmark" style="color:var(--gray-300);"></i><p>Belum ada booking.</p><a href="/booking">Buat Booking Baru</a></div>';
    }
    function showError(msg) {
        container.innerHTML = '<div class="state-box"><i class="fa-solid fa-circle-exclamation" style="color:#dc2626;"></i><p>' + (msg || 'Gagal memuat data.') + '</p><button class="retry-btn" onclick="loadBookings()"><i class="fa-solid fa-rotate-right"></i> Coba Lagi</button></div>';
    }

    function renderBookings(bookings) {
        container.innerHTML = bookings.map(function(b) {
            var dateStr = b.scheduled_at
                ? new Date(b.scheduled_at).toLocaleDateString('id-ID', {weekday:'long', day:'numeric', month:'long', year:'numeric'})
                : '-';
            var timeStr = b.booking_time ? ' • ' + b.booking_time : '';
            var statusClass = 'status-' + (b.status || 'pending');
            var statusText  = statusLabels[b.status] || b.status || '-';
            var clinicName  = (b.clinic && b.clinic.name) ? b.clinic.name : '-';
            var doctorName  = (b.doctor && b.doctor.name) ? 'drh. ' + b.doctor.name : 'Belum ditentukan';
            var petName     = (b.pet && b.pet.name) ? b.pet.name : '-';
            var complaint   = b.complaint || b.notes || '-';
            var cancelBtn   = b.status === 'pending'
                ? '<button class="cancel-btn" onclick="cancelBooking(' + b.id + ')"><i class="fa-solid fa-xmark"></i> Batalkan</button>'
                : '';
            return '<div class="booking-card" id="booking-' + b.id + '">'
                + '<div class="booking-header">'
                +   '<div class="booking-date"><i class="fa-solid fa-calendar-days" style="color:var(--teal);margin-right:6px;"></i>' + dateStr + timeStr + '</div>'
                +   '<span class="status-badge ' + statusClass + '">' + statusText + '</span>'
                + '</div>'
                + '<div class="booking-info">'
                +   '<div class="info-item"><strong>Klinik</strong>' + clinicName + '</div>'
                +   '<div class="info-item"><strong>Dokter</strong>' + doctorName + '</div>'
                +   '<div class="info-item"><strong>Hewan</strong>' + petName + '</div>'
                +   '<div class="info-item"><strong>Keluhan</strong>' + complaint + '</div>'
                + '</div>'
                + cancelBtn
                + '</div>';
        }).join('');
    }

    window.loadBookings = async function() {
        showLoading();
        try {
            var res = await apiFetch('/api/bookings');
            if (res.ok) {
                var data = await res.json();
                var bookings = data.data || [];
                if (bookings.length === 0) { showEmpty(); } else { renderBookings(bookings); }
            } else {
                var err = {};
                try { err = await res.json(); } catch(e) {}
                showError('Error ' + res.status + ': ' + (err.message || 'Gagal memuat data booking.'));
            }
        } catch(e) {
            console.error('loadBookings error:', e);
            showError('Tidak dapat terhubung ke server.');
        }
    };

    window.cancelBooking = async function(id) {
        if (!confirm('Yakin ingin membatalkan booking ini?')) return;
        try {
            var res = await apiFetch('/api/bookings/' + id, { method: 'DELETE' });
            if (res.ok) {
                var el = document.getElementById('booking-' + id);
                if (el) el.remove();
                if (!container.querySelector('.booking-card')) showEmpty();
            } else {
                alert('Gagal membatalkan booking.');
            }
        } catch(e) { alert('Terjadi kesalahan jaringan.'); }
    };

    loadBookings();
})();
</script>
@endpush
@endsection
