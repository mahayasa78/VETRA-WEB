@extends('layouts.app')
@section('title', 'Admin Dashboard')

@push('styles')
<style>
    .admin-dashboard { max-width: 1400px; margin: 0 auto; padding: 40px 24px; }
    
    .page-header { margin-bottom: 32px; }
    .page-header h1 { font-size: 32px; font-weight: 800; color: var(--gray-800); margin-bottom: 8px; }
    .page-header p { font-size: 15px; color: var(--gray-500); }
    
    .stats-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
        gap: 20px; 
        margin-bottom: 32px; 
    }
    
    .stat-card {
        background: #fff; border-radius: 16px; padding: 24px; 
        border: 1px solid var(--gray-200);
        box-shadow: var(--shadow); transition: all .3s;
    }
    .stat-card:hover { 
        transform: translateY(-2px); 
        box-shadow: var(--shadow-lg); 
    }
    
    .stat-icon {
        width: 48px; height: 48px; border-radius: 12px; 
        display: flex; align-items: center;
        justify-content: center; font-size: 24px; margin-bottom: 16px;
    }
    
    .stat-card.users .stat-icon { background: #dbeafe; color: #3b82f6; }
    .stat-card.doctors .stat-icon { background: #d1fae5; color: #10b981; }
    .stat-card.clinics .stat-icon { background: #e9d5ff; color: #a855f7; }
    .stat-card.articles .stat-icon { background: #fee2e2; color: #ef4444; }
    
    .stat-value { font-size: 32px; font-weight: 800; color: var(--gray-800); margin-bottom: 4px; }
    .stat-label { font-size: 14px; color: var(--gray-500); }
    
    .content-grid { 
        display: grid; 
        grid-template-columns: 1fr 1fr; 
        gap: 24px; 
    }
    
    .section-card {
        background: #fff; border-radius: 16px; border: 1px solid var(--gray-200);
        box-shadow: var(--shadow); padding: 28px;
    }
    
    .section-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 20px; 
    }
    
    .section-header h2 { 
        font-size: 20px; 
        font-weight: 800; 
        color: var(--gray-800); 
    }
    
    .btn-link {
        font-size: 14px; font-weight: 600; color: var(--teal); 
        text-decoration: none;
        display: flex; align-items: center; gap: 6px; 
        transition: color .2s;
    }
    .btn-link:hover { color: var(--teal-dark); }
    
    @media (max-width: 768px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div style="padding-top:68px;background:var(--gray-50);min-height:100vh;">
    <div class="admin-dashboard">
        <div class="page-header">
            <h1>🔧 Admin Dashboard</h1>
            <p>Selamat datang di panel admin Vetra</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card users">
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                <div class="stat-value" id="totalUsers">-</div>
                <div class="stat-label">Total Pengguna</div>
            </div>
            <div class="stat-card doctors">
                <div class="stat-icon"><i class="fa-solid fa-user-doctor"></i></div>
                <div class="stat-value" id="totalDoctors">-</div>
                <div class="stat-label">Total Dokter</div>
            </div>
            <div class="stat-card clinics">
                <div class="stat-icon"><i class="fa-solid fa-hospital"></i></div>
                <div class="stat-value" id="totalClinics">-</div>
                <div class="stat-label">Total Klinik</div>
            </div>
            <div class="stat-card articles">
                <div class="stat-icon"><i class="fa-solid fa-newspaper"></i></div>
                <div class="stat-value" id="totalArticles">-</div>
                <div class="stat-label">Total Artikel</div>
            </div>
        </div>

        <!-- System Info -->
        <div class="content-grid">
            <div class="section-card">
                <div class="section-header">
                    <h2>📊 System Info</h2>
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; padding: 12px; background: var(--gray-50); border-radius: 8px;">
                        <span style="color: var(--gray-600); font-size: 14px;">Laravel Version</span>
                        <span style="color: var(--gray-800); font-weight: 600; font-size: 14px;">{{ app()->version() }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 12px; background: var(--gray-50); border-radius: 8px;">
                        <span style="color: var(--gray-600); font-size: 14px;">PHP Version</span>
                        <span style="color: var(--gray-800); font-weight: 600; font-size: 14px;">{{ PHP_VERSION }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 12px; background: var(--gray-50); border-radius: 8px;">
                        <span style="color: var(--gray-600); font-size: 14px;">Environment</span>
                        <span style="color: var(--gray-800); font-weight: 600; font-size: 14px;">{{ app()->environment() }}</span>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <div class="section-header">
                    <h2>🔐 Admin Info</h2>
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; padding: 12px; background: var(--gray-50); border-radius: 8px;">
                        <span style="color: var(--gray-600); font-size: 14px;">Logged in as</span>
                        <span style="color: var(--gray-800); font-weight: 600; font-size: 14px;" id="adminName">-</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 12px; background: var(--gray-50); border-radius: 8px;">
                        <span style="color: var(--gray-600); font-size: 14px;">Email</span>
                        <span style="color: var(--gray-800); font-weight: 600; font-size: 14px;" id="adminEmail">-</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 12px; background: var(--gray-50); border-radius: 8px;">
                        <span style="color: var(--gray-600); font-size: 14px;">Role</span>
                        <span style="color: var(--gray-800); font-weight: 600; font-size: 14px;">Admin</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const token = localStorage.getItem('vetra_token');
    const user = JSON.parse(localStorage.getItem('vetra_user'));

    // Check authentication and role
    if (!token || !user || user.role !== 'admin') {
        alert('Akses ditolak. Halaman ini hanya untuk admin.');
        window.location.href = '/login';
    }

    // Display admin info
    document.getElementById('adminName').textContent = user.name;
    document.getElementById('adminEmail').textContent = user.email;

    // Load dashboard stats
    async function loadStats() {
        try {
            const res = await fetch('/api/admin/stats', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                const data = await res.json();
                console.log('Stats:', data);
                
                document.getElementById('totalUsers').textContent = data.total_users || 0;
                document.getElementById('totalDoctors').textContent = data.total_doctors || 0;
                document.getElementById('totalClinics').textContent = data.total_clinics || 0;
                document.getElementById('totalArticles').textContent = data.total_articles || 0;
            } else {
                console.error('Failed to load stats');
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    // Load stats on page load
    loadStats();
</script>
@endsection
