@extends('layouts.app')
@section('title', 'Dashboard Dokter')

@push('styles')
<style>
    .doctor-dashboard { max-width: 1200px; margin: 0 auto; padding: 40px 24px; }
    
    .page-header { margin-bottom: 32px; }
    .page-header h1 { font-size: 32px; font-weight: 800; color: var(--gray-800); margin-bottom: 8px; }
    .page-header p { font-size: 15px; color: var(--gray-500); }
    
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 32px; }
    .stat-card {
        background: #fff; border-radius: 16px; padding: 24px; border: 1px solid var(--gray-200);
        box-shadow: var(--shadow); transition: all .3s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-lg); }
    .stat-icon {
        width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center;
        justify-content: center; font-size: 24px; margin-bottom: 16px;
    }
    .stat-card.pending .stat-icon { background: #fef3c7; color: #f59e0b; }
    .stat-card.confirmed .stat-icon { background: #dbeafe; color: #3b82f6; }
    .stat-card.done .stat-icon { background: #d1fae5; color: #10b981; }
    .stat-card.month .stat-icon { background: #e9d5ff; color: #a855f7; }
    
    .stat-value { font-size: 32px; font-weight: 800; color: var(--gray-800); margin-bottom: 4px; }
    .stat-label { font-size: 14px; color: var(--gray-500); }
    
    .section-card {
        background: #fff; border-radius: 16px; border: 1px solid var(--gray-200);
        box-shadow: var(--shadow); padding: 28px;
    }
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .section-header h2 { font-size: 20px; font-weight: 800; color: var(--gray-800); }
    .btn-link {
        font-size: 14px; font-weight: 600; color: var(--teal); text-decoration: none;
        display: flex; align-items: center; gap: 6px; transition: color .2s;
    }
    .btn-link:hover { color: var(--teal-dark); }
    
    .booking-list { display: flex; flex-direction: column; gap: 16px; }
    .booking-item {
        padding: 18px; border-radius: 12px; border: 1.5px solid var(--gray-200);
        transition: all .2s; display: flex; gap: 16px; align-items: start;
    }
    .booking-item:hover { border-color: var(--teal); background: var(--teal-pale); }
    
    .booking-avatar {
        width: 56px; height: 56px; border-radius: 12px; background: var(--teal-pale);
        display: flex; align-items: center; justify-content: center; font-size: 24px;
        color: var(--teal); flex-shrink: 0;
    }
    .booking-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 12px; }
    
    .booking-info { flex: 1; }
    .booking-patient { font-size: 16px; font-weight: 700; color: var(--gray-800); margin-bottom: 4px; }
    .booking-pet {
        font-size: 14px; color: var(--gray-600); margin-bottom: 8px;
        display: flex; align-items: center; gap: 6px;
    }
    .booking-date {
        font-size: 13px; color: var(--gray-500); display: flex; align-items: center; gap: 6px;
    }
    
    .status-badge {
        padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;
        white-space: nowrap;
    }
    .status-badge.pending { background: #fef3c7; color: #f59e0b; }
    .status-badge.confirmed { background: #dbeafe; color: #3b82f6; }
    .status-badge.done { background: #d1fae5; color: #10b981; }
    
    .empty-state {
        text-align: center; padding: 60px 24px; color: var(--gray-400);
    }
    .empty-state i { font-size: 48px; margin-bottom: 16px; }
    .empty-state p { font-size: 15px; }
    
    .loading-spinner {
        text-align: center; padding: 40px;
    }
    .loading-spinner i { font-size: 32px; color: var(--teal); }
</style>
@endpush

@section('content')
<div style="padding-top:68px;background:var(--gray-50);min-height:100vh;">
    <div class="doctor-dashboard">
        <div class="page-header">
            <h1>👨‍⚕️ Dashboard Dokter</h1>
            <p>Selamat datang kembali, <span id="doctorName">Dokter</span></p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid" id="statsGrid">
            <div class="stat-card pending">
                <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
                <div class="stat-value" id="statPending">-</div>
                <div class="stat-label">Booking Menunggu</div>
            </div>
            <div class="stat-card confirmed">
                <div class="stat-icon"><i class="fa-solid fa-check-circle"></i></div>
                <div class="stat-value" id="statConfirmed">-</div>
                <div class="stat-label">Booking Terkonfirmasi</div>
            </div>
            <div class="stat-card done">
                <div class="stat-icon"><i class="fa-solid fa-clipboard-check"></i></div>
                <div class="stat-value" id="statDone">-</div>
                <div class="stat-label">Konsultasi Selesai</div>
            </div>
            <div class="stat-card month">
                <div class="stat-icon"><i class="fa-solid fa-calendar-days"></i></div>
                <div class="stat-value" id="statMonth">-</div>
                <div class="stat-label">Booking Bulan Ini</div>
            </div>
        </div>

        <!-- Upcoming Bookings -->
        <div class="section-card">
            <div class="section-header">
                <h2>📋 Jadwal Mendatang</h2>
                <a href="/doctor/schedules" class="btn-link">
                    Lihat Semua <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            <div id="upcomingBookings">
                <div class="loading-spinner">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    <p style="margin-top:12px;color:var(--gray-500);">Memuat jadwal...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const token = localStorage.getItem('vetra_token');
    const user = JSON.parse(localStorage.getItem('vetra_user'));

    // Check authentication and role
    if (!token || !user || user.role !== 'doctor') {
        window.location.href = '/login';
    }

    // Display doctor name
    document.getElementById('doctorName').textContent = user.name;

    // Load dashboard data
    async function loadDashboard() {
        try {
            const res = await fetch('/api/doctor/dashboard', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                const data = await res.json();
                console.log('Dashboard data:', data);
                
                // Update stats
                document.getElementById('statPending').textContent = data.stats.total_pending;
                document.getElementById('statConfirmed').textContent = data.stats.total_confirmed;
                document.getElementById('statDone').textContent = data.stats.total_done;
                document.getElementById('statMonth').textContent = data.stats.total_this_month;
                
                // Render upcoming bookings
                renderUpcomingBookings(data.upcoming_bookings);
            } else {
                console.error('Failed to load dashboard:', res.status);
                showError();
            }
        } catch (error) {
            console.error('Error loading dashboard:', error);
            showError();
        }
    }

    function renderUpcomingBookings(bookings) {
        const container = document.getElementById('upcomingBookings');
        
        if (!bookings || bookings.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-calendar-xmark"></i>
                    <p>Tidak ada jadwal mendatang</p>
                </div>`;
            return;
        }

        try {
            container.innerHTML = `
                <div class="booking-list">
                    ${bookings.map(booking => {
                        try {
                            const statusMap = {
                                pending: 'Menunggu',
                                confirmed: 'Terkonfirmasi',
                                done: 'Selesai'
                            };
                            
                            // Handle null relationships with fallback
                            const user = booking.user || { name: 'Pasien', profile_pic: null };
                            const pet = booking.pet || { name: 'Hewan Peliharaan', species: '-' };
                            
                            const date = new Date(booking.scheduled_at);
                            const dateStr = date.toLocaleDateString('id-ID', { 
                                day: 'numeric', 
                                month: 'short', 
                                year: 'numeric' 
                            });
                            const timeStr = date.toLocaleTimeString('id-ID', { 
                                hour: '2-digit', 
                                minute: '2-digit' 
                            });
                            
                            return `
                                <div class="booking-item">
                                    <div class="booking-avatar">
                                        ${user.profile_pic 
                                            ? `<img src="${user.profile_pic}" alt="${user.name}">` 
                                            : '<i class="fa-solid fa-user"></i>'}
                                    </div>
                                    <div class="booking-info">
                                        <div class="booking-patient">${user.name}</div>
                                        <div class="booking-pet">
                                            <i class="fa-solid fa-paw"></i>
                                            ${pet.name}${pet.species !== '-' ? ` (${pet.species})` : ''}
                                        </div>
                                        <div class="booking-date">
                                            <i class="fa-regular fa-calendar"></i>
                                            ${dateStr} • ${timeStr}
                                        </div>
                                        ${booking.complaint ? `<div style="margin-top:8px;font-size:13px;color:var(--gray-600);">💬 ${booking.complaint}</div>` : ''}
                                    </div>
                                    <div class="status-badge ${booking.status}">
                                        ${statusMap[booking.status]}
                                    </div>
                                </div>`;
                        } catch (error) {
                            console.error('Error rendering booking item:', booking, error);
                            return `<div class="booking-item" style="background:#fee2e2;border-color:#dc2626;">
                                <p style="color:#dc2626;padding:10px;">Error menampilkan booking ID: ${booking.id}</p>
                            </div>`;
                        }
                    }).join('')}
                </div>`;
        } catch (error) {
            console.error('Error in renderUpcomingBookings:', error);
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    <p>Terjadi kesalahan saat menampilkan data</p>
                </div>`;
        }
    }

    function showError() {
        document.getElementById('upcomingBookings').innerHTML = `
            <div class="empty-state">
                <i class="fa-solid fa-exclamation-triangle"></i>
                <p>Gagal memuat data. Silakan refresh halaman.</p>
            </div>`;
    }

    // Load dashboard on page load
    loadDashboard();
</script>
@endsection
