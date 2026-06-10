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
            <button class="btn-book" onclick="window.location.href='/booking'">
                <i class="fa-solid fa-plus"></i> Buat Booking Baru
            </button>
        </div>

        <div id="bookingsList" class="bookings-list">
            <div class="state-box">
                <i class="fa-solid fa-spinner fa-spin" style="color:var(--teal);"></i>
                <p>Memuat data booking…</p>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const token = localStorage.getItem('vetra_token');
    if (!token) { window.location.href = '/login'; return; }

    const container = document.getElementById('bookingsList');

    const statusLabels = {
        pending:   'Menunggu Konfirmasi',
        confirmed: 'Dikonfirmasi',
        done:      'Selesai',
        completed: 'Selesai',
        rejected:  'Dibatalkan / Ditolak',
    };

    function renderEmpty() {
        container.innerHTML = `
        <div class="state-box">
            <i class="fa-solid fa-calendar-xmark" style="color:var(--gray-300);"></i>
            <p>Belum ada booking.</p>
            <a href="/booking">Buat Booking Baru</a>
        </div>`;
    }

    function renderError(msg) {
        container.innerHTML = `
        <div class="state-box">
            <i class="fa-solid fa-circle-exclamation" style="color:#dc2626;"></i>
            <p>${msg}</p>
        </div>`;
    }

    function renderBookings(bookings) {
        container.innerHTML = bookings.map(b => {
            const dateStr = b.scheduled_at
                ? new Date(b.scheduled_at).toLocaleDateString('id-ID', {
                    weekday:'long', day:'numeric', month:'long', year:'numeric'
                  })
                : '-';
            const timeStr = b.booking_time ? ' • ' + b.booking_time : '';
            const statusClass = 'status-' + (b.status || 'pending');
            const statusText  = statusLabels[b.status] || b.status || '-';

            return `
            <div class="booking-card" id="booking-${b.id}">
                <div class="booking-header">
                    <div class="booking-date">
                        <i class="fa-solid fa-calendar-days" style="color:var(--teal);margin-right:6px;"></i>
                        ${dateStr}${timeStr}
                    </div>
                    <span class="status-badge ${statusClass}">${statusText}</span>
                </div>
                <div class="booking-info">
                    <div class="info-item"><strong>Klinik</strong>${b.clinic?.name || '-'}</div>
                    <div class="info-item"><strong>Dokter</strong>${b.doctor?.name ? 'drh. ' + b.doctor.name : 'Belum ditentukan'}</div>
                    <div class="info-item"><strong>Hewan</strong>${b.pet?.name || '-'}</div>
                    <div class="info-item"><strong>Keluhan</strong>${b.complaint || b.notes || '-'}</div>
                </div>
                ${b.status === 'pending' ? `
                <button class="cancel-btn" onclick="cancelBooking(${b.id})">
                    <i class="fa-solid fa-xmark"></i> Batalkan
                </button>` : ''}
            </div>`;
        }).join('');
    }

    async function loadBookings() {
        try {
            const res = await authFetch('/api/bookings');

            if (res.ok) {
                const data = await res.json();
                const bookings = data.data || [];
                if (bookings.length === 0) { renderEmpty(); } else { renderBookings(bookings); }
            } else {
                const err = await res.json().catch(() => ({}));
                renderError('Gagal memuat data booking: ' + (err.message || 'Terjadi kesalahan'));
            }
        } catch (e) {
            console.error('loadBookings error:', e);
            renderError('Tidak dapat terhubung ke server. Periksa koneksi Anda.');
        }
    }

    async function cancelBooking(id) {
        if (!confirm('Yakin ingin membatalkan booking ini?')) return;
        try {
            const res = await authFetch(`/api/bookings/${id}`, { method: 'DELETE' });
            if (res.ok) {
                document.getElementById('booking-' + id)?.remove();
                if (!document.querySelector('.booking-card')) renderEmpty();
            } else {
                alert('Gagal membatalkan booking.');
            }
        } catch (e) {
            alert('Terjadi kesalahan jaringan.');
        }
    }

    // expose cancelBooking for inline onclick
    window.cancelBooking = cancelBooking;

    loadBookings();
})();
</script>
@endsection
