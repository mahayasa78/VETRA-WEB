@extends('layouts.app')
@section('title', 'Dashboard Klinik')

@push('styles')
<style>
    .clinic-dashboard { max-width: 1200px; margin: 0 auto; padding: 40px 24px; }
    
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
    .stat-card.total .stat-icon { background: #dbeafe; color: #3b82f6; }
    .stat-card.pending .stat-icon { background: #fef3c7; color: #f59e0b; }
    .stat-card.confirmed .stat-icon { background: #d1fae5; color: #10b981; }
    .stat-card.doctors .stat-icon { background: #e9d5ff; color: #a855f7; }
    
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
    
    .bookings-table { width: 100%; border-collapse: collapse; }
    .bookings-table th {
        text-align: left; padding: 12px 16px; background: var(--gray-50);
        font-size: 13px; font-weight: 700; color: var(--gray-600);
        border-bottom: 2px solid var(--gray-200);
    }
    .bookings-table td {
        padding: 14px 16px; border-bottom: 1px solid var(--gray-100);
        font-size: 14px; color: var(--gray-700);
    }
    .bookings-table tr:hover { background: var(--gray-50); }
    
    .patient-cell { display: flex; align-items: center; gap: 12px; }
    .patient-avatar {
        width: 40px; height: 40px; border-radius: 10px; background: var(--teal-pale);
        display: flex; align-items: center; justify-content: center; font-size: 18px;
        color: var(--teal); flex-shrink: 0;
    }
    .patient-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 10px; }
    .patient-info { flex: 1; }
    .patient-name { font-weight: 600; color: var(--gray-800); }
    .patient-pet { font-size: 12px; color: var(--gray-500); }
    
    .status-badge {
        padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;
        white-space: nowrap; display: inline-block;
    }
    .status-badge.pending { background: #fef3c7; color: #f59e0b; }
    .status-badge.confirmed { background: #dbeafe; color: #3b82f6; }
    .status-badge.rejected { background: #fee2e2; color: #dc2626; }
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

    @media (max-width: 768px) {
        .bookings-table { display: block; overflow-x: auto; }
    }
</style>
@endpush

@section('content')
<div style="padding-top:68px;background:var(--gray-50);min-height:100vh;">
    <div class="clinic-dashboard">
        <div class="page-header">
            <h1>🏥 Dashboard Klinik</h1>
            <p>Selamat datang kembali, <span id="clinicName">Klinik</span></p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid" id="statsGrid">
            <div class="stat-card total">
                <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
                <div class="stat-value" id="statTotal">-</div>
                <div class="stat-label">Total Booking</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
                <div class="stat-value" id="statPending">-</div>
                <div class="stat-label">Menunggu Konfirmasi</div>
            </div>
            <div class="stat-card confirmed">
                <div class="stat-icon"><i class="fa-solid fa-check-circle"></i></div>
                <div class="stat-value" id="statConfirmed">-</div>
                <div class="stat-label">Terkonfirmasi</div>
            </div>
            <div class="stat-card doctors">
                <div class="stat-icon"><i class="fa-solid fa-user-doctor"></i></div>
                <div class="stat-value" id="statDoctors">-</div>
                <div class="stat-label">Dokter Kami</div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="section-card">
            <div class="section-header">
                <h2>📋 Booking Terbaru</h2>
                <a href="/clinic/bookings" class="btn-link">
                    Lihat Semua <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            <div id="recentBookings">
                <div class="loading-spinner">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    <p style="margin-top:12px;color:var(--gray-500);">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const token = localStorage.getItem('vetra_token');
    const user = JSON.parse(localStorage.getItem('vetra_user'));

    // Check authentication and role
    if (!token || !user || user.role !== 'clinic') {
        window.location.href = '/login';
    }

    // Display clinic name
    document.getElementById('clinicName').textContent = user.name;

    // Load dashboard data
    async function loadDashboard() {
        try {
            console.log('Loading dashboard data...');
            
            const res = await fetch('/api/clinic/dashboard', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            console.log('Response status:', res.status);

            if (res.ok) {
                const data = await res.json();
                console.log('Dashboard data:', data);
                
                // Update stats
                if (data.stats) {
                    document.getElementById('statTotal').textContent = data.stats.total_bookings || 0;
                    document.getElementById('statPending').textContent = data.stats.pending_bookings || 0;
                    document.getElementById('statConfirmed').textContent = data.stats.confirmed_bookings || 0;
                    document.getElementById('statDoctors').textContent = data.stats.total_doctors || 0;
                }
                
                // Render recent bookings
                console.log('Recent bookings count:', data.recent_bookings?.length || 0);
                renderRecentBookings(data.recent_bookings || []);
            } else {
                const errorData = await res.json().catch(() => ({}));
                console.error('Failed to load dashboard:', res.status, errorData);
                showError('Gagal memuat data. Status: ' + res.status);
            }
        } catch (error) {
            console.error('Error loading dashboard:', error);
            showError('Gagal terhubung ke server: ' + error.message);
        }
    }

    function renderRecentBookings(bookings) {
        const container = document.getElementById('recentBookings');
        
        if (!bookings || bookings.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-calendar-xmark"></i>
                    <p>Tidak ada booking terbaru</p>
                </div>`;
            return;
        }

        const statusMap = {
            pending: 'Menunggu',
            confirmed: 'Terkonfirmasi',
            rejected: 'Ditolak',
            done: 'Selesai'
        };

        container.innerHTML = `
            <div style="overflow-x:auto;">
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Pasien</th>
                            <th>Dokter</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${bookings.map(booking => {
                            // Safe data access with null checks
                            const userName = booking.user?.name || 'User tidak ditemukan';
                            const userProfilePic = booking.user?.profile_pic || '';
                            const petName = booking.pet?.name || 'Hewan peliharaan';
                            const doctorName = booking.doctor?.name || '-';
                            
                            // Handle date
                            let dateStr = 'Belum ditentukan';
                            let timeStr = '-';
                            
                            if (booking.scheduled_at) {
                                const date = new Date(booking.scheduled_at);
                                dateStr = date.toLocaleDateString('id-ID', { 
                                    day: 'numeric', 
                                    month: 'short',
                                    year: 'numeric'
                                });
                                timeStr = date.toLocaleTimeString('id-ID', { 
                                    hour: '2-digit', 
                                    minute: '2-digit' 
                                });
                            } else if (booking.booking_date) {
                                const date = new Date(booking.booking_date);
                                dateStr = date.toLocaleDateString('id-ID', { 
                                    day: 'numeric', 
                                    month: 'short',
                                    year: 'numeric'
                                });
                                timeStr = booking.booking_time || '-';
                            }
                            
                            return `
                                <tr>
                                    <td>
                                        <div class="patient-cell">
                                            <div class="patient-avatar">
                                                ${userProfilePic 
                                                    ? `<img src="${userProfilePic.startsWith('http') ? userProfilePic : '/storage/' + userProfilePic}" alt="${userName}">` 
                                                    : '<i class="fa-solid fa-user"></i>'}
                                            </div>
                                            <div class="patient-info">
                                                <div class="patient-name">${userName}</div>
                                                <div class="patient-pet">🐾 ${petName}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>${doctorName}</td>
                                    <td>${dateStr}<br><small style="color:var(--gray-500);">${timeStr}</small></td>
                                    <td><span class="status-badge ${booking.status}">${statusMap[booking.status] || booking.status}</span></td>
                                </tr>`;
                        }).join('')}
                    </tbody>
                </table>
            </div>`;
    }

    function showError(message = 'Gagal memuat data. Silakan refresh halaman.') {
        document.getElementById('recentBookings').innerHTML = `
            <div class="empty-state">
                <i class="fa-solid fa-exclamation-triangle"></i>
                <p>${message}</p>
                <button onclick="location.reload()" style="margin-top:16px;padding:10px 20px;background:var(--teal);color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;">
                    <i class="fa-solid fa-rotate-right"></i> Refresh Halaman
                </button>
            </div>`;
    }

    // Load dashboard on page load
    loadDashboard();
</script>
@endsection
