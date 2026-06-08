@extends('layouts.app')
@section('title', 'Booking Klinik')

@push('styles')
<style>
    .bookings-container { max-width: 1200px; margin: 0 auto; padding: 40px 24px; }
    
    .page-header { 
        display: flex; justify-content: space-between; align-items: center; 
        margin-bottom: 32px; flex-wrap: wrap; gap: 16px;
    }
    .page-header h1 { font-size: 32px; font-weight: 800; color: var(--gray-800); }
    
    .filter-tabs {
        display: flex; gap: 8px; background: #fff; padding: 6px; border-radius: 12px;
        border: 1px solid var(--gray-200);
    }
    .filter-tab {
        padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600;
        border: none; background: transparent; color: var(--gray-600); cursor: pointer;
        transition: all .2s;
    }
    .filter-tab:hover { background: var(--gray-100); color: var(--gray-800); }
    .filter-tab.active { background: var(--teal); color: #fff; }
    
    .bookings-list { display: flex; flex-direction: column; gap: 16px; }
    .booking-card {
        background: #fff; border-radius: 16px; border: 1px solid var(--gray-200);
        box-shadow: var(--shadow); overflow: hidden; transition: all .3s;
    }
    .booking-card:hover { box-shadow: var(--shadow-lg); border-color: var(--teal); }
    
    .booking-header {
        padding: 20px 24px; display: flex; justify-content: space-between; 
        align-items: start; gap: 16px; border-bottom: 1px solid var(--gray-100);
    }
    .booking-patient-info { display: flex; gap: 16px; align-items: start; flex: 1; }
    .patient-avatar {
        width: 60px; height: 60px; border-radius: 12px; background: var(--teal-pale);
        display: flex; align-items: center; justify-content: center; font-size: 28px;
        color: var(--teal); flex-shrink: 0;
    }
    .patient-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 12px; }
    
    .patient-details { flex: 1; }
    .patient-name { font-size: 18px; font-weight: 700; color: var(--gray-800); margin-bottom: 4px; }
    .patient-contact {
        font-size: 13px; color: var(--gray-500); display: flex; align-items: center; 
        gap: 12px; flex-wrap: wrap; margin-bottom: 8px;
    }
    .pet-badge {
        display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px;
        background: var(--teal-pale); color: var(--teal-dark); border-radius: 8px;
        font-size: 13px; font-weight: 600;
    }
    
    .status-badge {
        padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600;
        white-space: nowrap;
    }
    .status-badge.pending { background: #fef3c7; color: #f59e0b; }
    .status-badge.confirmed { background: #dbeafe; color: #3b82f6; }
    .status-badge.rejected { background: #fee2e2; color: #dc2626; }
    .status-badge.done { background: #d1fae5; color: #10b981; }
    
    .booking-body { padding: 20px 24px; }
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px; }
    .info-item { display: flex; align-items: center; gap: 10px; }
    .info-icon {
        width: 36px; height: 36px; border-radius: 8px; display: flex;
        align-items: center; justify-content: center; background: var(--gray-100);
        color: var(--teal); font-size: 16px; flex-shrink: 0;
    }
    .info-content { flex: 1; }
    .info-label { font-size: 12px; color: var(--gray-500); margin-bottom: 2px; }
    .info-value { font-size: 14px; font-weight: 600; color: var(--gray-800); }
    
    .complaint-box {
        background: var(--gray-50); border-radius: 12px; padding: 16px; margin-top: 12px;
        border-left: 3px solid var(--teal);
    }
    .complaint-label { font-size: 12px; font-weight: 700; color: var(--gray-600); margin-bottom: 6px; }
    .complaint-text { font-size: 14px; color: var(--gray-700); line-height: 1.5; }
    
    .notes-box {
        background: #fef3c7; border-radius: 12px; padding: 16px; margin-top: 12px;
        border-left: 3px solid #f59e0b;
    }
    .notes-label { font-size: 12px; font-weight: 700; color: #92400e; margin-bottom: 6px; }
    .notes-text { font-size: 14px; color: #78350f; line-height: 1.5; }
    
    .empty-state {
        text-align: center; padding: 80px 24px; background: #fff;
        border-radius: 16px; border: 1px solid var(--gray-200);
    }
    .empty-state i { font-size: 64px; color: var(--gray-300); margin-bottom: 16px; }
    .empty-state p { font-size: 16px; color: var(--gray-500); }
    
    .loading-spinner { text-align: center; padding: 60px; }
    .loading-spinner i { font-size: 48px; color: var(--teal); }
</style>
@endpush

@section('content')
<div style="padding-top:68px;background:var(--gray-50);min-height:100vh;">
    <div class="bookings-container">
        <div class="page-header">
            <h1>📋 Booking Klinik</h1>
            <div class="filter-tabs">
                <button class="filter-tab active" onclick="filterBookings('all')">Semua</button>
                <button class="filter-tab" onclick="filterBookings('pending')">Menunggu</button>
                <button class="filter-tab" onclick="filterBookings('confirmed')">Terkonfirmasi</button>
                <button class="filter-tab" onclick="filterBookings('done')">Selesai</button>
            </div>
        </div>

        <div id="bookingsList">
            <div class="loading-spinner">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <p style="margin-top:16px;color:var(--gray-500);">Memuat data booking...</p>
            </div>
        </div>
    </div>
</div>

<script>
    const token = localStorage.getItem('vetra_token');
    const user = JSON.parse(localStorage.getItem('vetra_user'));

    if (!token || !user || user.role !== 'clinic') {
        window.location.href = '/login';
    }

    let bookings = [];
    let currentFilter = 'all';

    // Load bookings
    async function loadBookings(status = null) {
        const container = document.getElementById('bookingsList');
        container.innerHTML = `
            <div class="loading-spinner">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <p style="margin-top:16px;color:var(--gray-500);">Memuat data booking...</p>
            </div>`;
        
        try {
            const url = status ? `/api/clinic/bookings?status=${status}` : '/api/clinic/bookings';
            console.log('Loading bookings from:', url);
            
            const res = await fetch(url, {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            console.log('Response status:', res.status);
            
            if (res.ok) {
                const data = await res.json();
                console.log('Bookings data received:', data);
                
                // Handle different response formats
                if (data.data) {
                    bookings = data.data;
                } else if (Array.isArray(data)) {
                    bookings = data;
                } else {
                    bookings = [];
                }
                
                console.log('Total bookings:', bookings.length);
                renderBookings();
            } else {
                const errorData = await res.json().catch(() => ({}));
                console.error('Failed to load bookings:', res.status, errorData);
                showError('Gagal memuat data booking. Status: ' + res.status);
            }
        } catch (error) {
            console.error('Error loading bookings:', error);
            showError('Gagal terhubung ke server: ' + error.message);
        }
    }

    // Render bookings
    function renderBookings() {
        const container = document.getElementById('bookingsList');
        
        if (!bookings || bookings.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-calendar-xmark"></i>
                    <p>Tidak ada booking untuk filter ini</p>
                </div>`;
            return;
        }

        container.innerHTML = `
            <div class="bookings-list">
                ${bookings.map(booking => renderBookingCard(booking)).join('')}
            </div>`;
    }

    function renderBookingCard(booking) {
        const statusMap = {
            pending: 'Menunggu',
            confirmed: 'Terkonfirmasi',
            rejected: 'Ditolak',
            done: 'Selesai'
        };
        
        // Handle date
        let dateStr = 'Belum ditentukan';
        let timeStr = 'Belum ditentukan';
        
        if (booking.scheduled_at) {
            const date = new Date(booking.scheduled_at);
            dateStr = date.toLocaleDateString('id-ID', { 
                weekday: 'long',
                day: 'numeric', 
                month: 'long', 
                year: 'numeric' 
            });
            timeStr = date.toLocaleTimeString('id-ID', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        } else if (booking.booking_date) {
            const date = new Date(booking.booking_date);
            dateStr = date.toLocaleDateString('id-ID', { 
                weekday: 'long',
                day: 'numeric', 
                month: 'long', 
                year: 'numeric' 
            });
            if (booking.booking_time) {
                timeStr = booking.booking_time;
            }
        }

        // Handle user data
        const userName = booking.user?.name || 'User tidak ditemukan';
        const userPhone = booking.user?.phone || '';
        const userProfilePic = booking.user?.profile_pic || '';
        
        // Handle pet data
        const petName = booking.pet?.name || 'Hewan peliharaan';
        const petSpecies = booking.pet?.species || 'Unknown';
        
        // Handle doctor data
        const doctorName = booking.doctor?.name || 'Belum ditentukan';

        return `
            <div class="booking-card">
                <div class="booking-header">
                    <div class="booking-patient-info">
                        <div class="patient-avatar">
                            ${userProfilePic 
                                ? `<img src="${userProfilePic.startsWith('http') ? userProfilePic : '/storage/' + userProfilePic}" alt="${userName}">` 
                                : '<i class="fa-solid fa-user"></i>'}
                        </div>
                        <div class="patient-details">
                            <div class="patient-name">${userName}</div>
                            <div class="patient-contact">
                                ${userPhone ? `<span><i class="fa-solid fa-phone"></i> ${userPhone}</span>` : ''}
                                ${booking.user?.email ? `<span><i class="fa-solid fa-envelope"></i> ${booking.user.email}</span>` : ''}
                            </div>
                            <div class="pet-badge">
                                <i class="fa-solid fa-paw"></i>
                                ${petName} (${petSpecies})
                            </div>
                        </div>
                    </div>
                    <div class="status-badge ${booking.status}">
                        ${statusMap[booking.status] || booking.status}
                    </div>
                </div>
                
                <div class="booking-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-regular fa-calendar"></i></div>
                            <div class="info-content">
                                <div class="info-label">Tanggal</div>
                                <div class="info-value">${dateStr}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-regular fa-clock"></i></div>
                            <div class="info-content">
                                <div class="info-label">Waktu</div>
                                <div class="info-value">${timeStr}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-user-doctor"></i></div>
                            <div class="info-content">
                                <div class="info-label">Dokter</div>
                                <div class="info-value">${doctorName}</div>
                            </div>
                        </div>
                    </div>
                    
                    ${booking.complaint ? `
                    <div class="complaint-box">
                        <div class="complaint-label">💬 KELUHAN PASIEN</div>
                        <div class="complaint-text">${booking.complaint}</div>
                    </div>` : ''}
                    
                    ${booking.doctor_notes ? `
                    <div class="notes-box">
                        <div class="notes-label">📝 CATATAN DOKTER</div>
                        <div class="notes-text">${booking.doctor_notes}</div>
                    </div>` : ''}
                </div>
            </div>`;
    }

    // Filter bookings
    function filterBookings(status) {
        currentFilter = status;
        
        // Update active tab
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        event.target.classList.add('active');
        
        // Load bookings with filter
        loadBookings(status === 'all' ? null : status);
    }

    function showError(message = 'Gagal memuat data. Silakan refresh halaman.') {
        document.getElementById('bookingsList').innerHTML = `
            <div class="empty-state">
                <i class="fa-solid fa-exclamation-triangle"></i>
                <p>${message}</p>
                <button onclick="location.reload()" style="margin-top:16px;padding:10px 20px;background:var(--teal);color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;">
                    <i class="fa-solid fa-rotate-right"></i> Refresh Halaman
                </button>
            </div>`;
    }

    // Load bookings on page load
    loadBookings();
</script>
@endsection
