@extends('layouts.app')
@section('title', 'Booking Saya')

@push('styles')
<style>
    .bookings-container { max-width: 1100px; margin: 0 auto; padding: 40px 24px; }
    .bookings-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
    .btn-book { background: var(--teal); color: #fff; padding: 12px 24px; border-radius: 12px; font-weight: 700; font-size: 14px; border: none; cursor: pointer; display: flex; align-items: center; gap: 8px; }
    .btn-book:hover { background: var(--teal-dark); }
    .bookings-list { display: flex; flex-direction: column; gap: 16px; }
    .booking-card { background: #fff; border-radius: 16px; border: 1px solid var(--gray-200); padding: 24px; box-shadow: var(--shadow); }
    .booking-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px; }
    .booking-date { font-size: 18px; font-weight: 800; color: var(--gray-800); }
    .status-badge { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-confirmed { background: #dbeafe; color: #1e40af; }
    .status-completed { background: #dcfce7; color: #166534; }
    .status-rejected { background: #fee2e2; color: #991b1b; }
    .booking-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
    .info-item { font-size: 14px; color: var(--gray-600); }
    .info-item strong { display: block; font-size: 12px; color: var(--gray-400); margin-bottom: 4px; }
</style>
@endpush

@section('content')
<div style="padding-top:68px;background:var(--gray-50);min-height:100vh;">
    <div class="bookings-container">
        <div class="bookings-header">
            <div><h1>📅 Booking Saya</h1><p style="font-size:15px;color:var(--gray-500);margin-top:4px;">Lihat dan kelola jadwal konsultasi Anda</p></div>
            <button class="btn-book" onclick="window.location.href='/booking'"><i class="fa-solid fa-plus"></i> Buat Booking Baru</button>
        </div>
        <div id="bookingsList" class="bookings-list">
            <div style="text-align:center;padding:60px;color:var(--gray-400);"><i class="fa-solid fa-spinner fa-spin" style="font-size:32px;"></i><p style="margin-top:12px;">Memuat data...</p></div>
        </div>
    </div>
</div>

<script>
    const token = localStorage.getItem('vetra_token');
    if (!token) window.location.href = '/login';

    async function loadBookings() {
        try {
            const res = await fetch('/api/bookings', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            
            if (res.ok) {
                const data = await res.json();
                // Laravel pagination: { current_page, data: [...], ... }
                const bookings = data.data || [];
                const container = document.getElementById('bookingsList');
                
                if (bookings.length === 0) {
                    container.innerHTML = '<div style="text-align:center;padding:60px;background:#fff;border-radius:16px;border:1px solid var(--gray-200);"><i class="fa-solid fa-calendar-xmark" style="font-size:48px;color:var(--gray-300);margin-bottom:16px;"></i><p style="font-size:16px;color:var(--gray-500);">Belum ada booking</p><a href="/booking" style="display:inline-block;margin-top:16px;padding:10px 20px;background:var(--teal);color:#fff;border-radius:10px;text-decoration:none;font-weight:600;">Buat Booking Baru</a></div>';
                } else {
                    container.innerHTML = bookings.map(b => {
                        const statusLabels = {
                            pending: 'Menunggu Konfirmasi',
                            confirmed: 'Dikonfirmasi',
                            completed: 'Selesai',
                            rejected: 'Dibatalkan'
                        };
                        
                        return `
                        <div class="booking-card">
                            <div class="booking-header">
                                <div class="booking-date"><i class="fa-solid fa-calendar"></i> ${new Date(b.scheduled_at).toLocaleDateString('id-ID', {weekday:'long', day:'numeric', month:'long', year:'numeric'})} • ${b.booking_time || ''}</div>
                                <span class="status-badge status-${b.status}">${statusLabels[b.status] || b.status}</span>
                            </div>
                            <div class="booking-info">
                                <div class="info-item"><strong>Klinik</strong>${b.clinic?.name || '-'}</div>
                                <div class="info-item"><strong>Dokter</strong>${b.doctor?.name ? 'drh. ' + b.doctor.name : 'Belum ditentukan'}</div>
                                <div class="info-item"><strong>Hewan</strong>${b.pet?.name || '-'}</div>
                                <div class="info-item"><strong>Keluhan</strong>${b.complaint || b.notes || '-'}</div>
                            </div>
                        </div>`;
                    }).join('');
                }
            } else {
                const container = document.getElementById('bookingsList');
                container.innerHTML = '<div style="text-align:center;padding:60px;background:#fff;border-radius:16px;border:1px solid var(--gray-200);"><i class="fa-solid fa-exclamation-circle" style="font-size:48px;color:var(--red);margin-bottom:16px;"></i><p style="font-size:16px;color:var(--gray-500);">Gagal memuat data booking</p></div>';
            }
        } catch (error) {
            console.error('Error:', error);
            const container = document.getElementById('bookingsList');
            container.innerHTML = '<div style="text-align:center;padding:60px;background:#fff;border-radius:16px;border:1px solid var(--gray-200);"><i class="fa-solid fa-exclamation-circle" style="font-size:48px;color:var(--red);margin-bottom:16px;"></i><p style="font-size:16px;color:var(--gray-500);">Terjadi kesalahan: ' + error.message + '</p></div>';
        }
    }
    loadBookings();
</script>
@endsection
