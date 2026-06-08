@extends('layouts.app')
@section('title', 'Dokter Kami')

@push('styles')
<style>
    .doctors-container { max-width: 1200px; margin: 0 auto; padding: 40px 24px; }
    
    .page-header { 
        display: flex; justify-content: space-between; align-items: center; 
        margin-bottom: 32px; flex-wrap: wrap; gap: 16px;
    }
    .page-header-left { flex: 1; }
    .page-header h1 { font-size: 32px; font-weight: 800; color: var(--gray-800); margin-bottom: 8px; }
    .page-header p { font-size: 15px; color: var(--gray-500); }
    
    .btn-add-doctor {
        display: flex; align-items: center; gap: 8px; padding: 12px 24px;
        background: var(--teal); color: #fff; border: none; border-radius: 12px;
        font-weight: 700; font-size: 14px; cursor: pointer; transition: all .2s;
    }
    .btn-add-doctor:hover { background: var(--teal-dark); transform: translateY(-1px); }
    
    .doctors-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; }
    .doctor-card {
        background: #fff; border-radius: 16px; border: 1px solid var(--gray-200);
        overflow: hidden; transition: all .3s; box-shadow: var(--shadow); position: relative;
    }
    .doctor-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--teal); }
    
    .doctor-header {
        background: linear-gradient(135deg, var(--teal), var(--teal-dark));
        padding: 32px 24px 60px; text-align: center; position: relative;
    }
    .doctor-avatar {
        width: 100px; height: 100px; border-radius: 50%; background: #fff;
        display: flex; align-items: center; justify-content: center; font-size: 40px;
        color: var(--teal); margin: 0 auto 16px; box-shadow: 0 4px 12px rgba(0,0,0,.15);
        border: 4px solid #fff;
    }
    .doctor-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    .doctor-name { font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 6px; }
    .doctor-speciality { 
        font-size: 14px; color: rgba(255,255,255,.9); font-weight: 600;
        background: rgba(255,255,255,.2); padding: 6px 14px; border-radius: 20px;
        display: inline-block;
    }
    
    .online-badge {
        position: absolute; top: 16px; right: 16px; 
        padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;
        display: flex; align-items: center; gap: 6px;
    }
    .online-badge.online { background: #d1fae5; color: #10b981; }
    .online-badge.offline { background: var(--gray-200); color: var(--gray-600); }
    .online-badge .dot {
        width: 8px; height: 8px; border-radius: 50%; background: currentColor;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: .5; }
    }
    
    .doctor-body { padding: 24px; }
    .info-item {
        display: flex; align-items: center; gap: 12px; margin-bottom: 12px;
        font-size: 14px; color: var(--gray-700);
    }
    .info-icon {
        width: 36px; height: 36px; border-radius: 8px; background: var(--teal-pale);
        display: flex; align-items: center; justify-content: center; color: var(--teal);
        font-size: 16px; flex-shrink: 0;
    }
    .info-text { flex: 1; }
    .info-label { font-size: 12px; color: var(--gray-500); margin-bottom: 2px; }
    .info-value { font-weight: 600; color: var(--gray-800); }
    
    .doctor-bio {
        margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--gray-100);
        font-size: 14px; color: var(--gray-600); line-height: 1.6;
    }
    
    .doctor-actions {
        display: flex; gap: 8px; margin-top: 16px; padding-top: 16px;
        border-top: 1px solid var(--gray-100);
    }
    .btn-edit, .btn-delete {
        flex: 1; padding: 10px; border-radius: 10px; font-size: 13px; font-weight: 600;
        border: none; cursor: pointer; transition: all .2s; display: flex;
        align-items: center; justify-content: center; gap: 6px;
    }
    .btn-edit { background: var(--teal-pale); color: var(--teal-dark); }
    .btn-edit:hover { background: var(--teal); color: #fff; }
    .btn-delete { background: #fee2e2; color: #dc2626; }
    .btn-delete:hover { background: #dc2626; color: #fff; }
    
    .empty-state {
        text-align: center; padding: 80px 24px; background: #fff;
        border-radius: 16px; border: 1px solid var(--gray-200); grid-column: 1/-1;
    }
    .empty-state i { font-size: 64px; color: var(--gray-300); margin-bottom: 16px; }
    .empty-state p { font-size: 16px; color: var(--gray-500); margin-bottom: 24px; }
    .empty-state .btn-action {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 12px 24px; background: var(--teal); color: #fff; border-radius: 12px;
        font-weight: 700; text-decoration: none; transition: all .2s;
    }
    .empty-state .btn-action:hover { background: var(--teal-dark); transform: translateY(-1px); }
    
    .loading-spinner { 
        text-align: center; padding: 60px; grid-column: 1/-1;
        background: #fff; border-radius: 16px; border: 1px solid var(--gray-200);
    }
    .loading-spinner i { font-size: 48px; color: var(--teal); }
    
    .stats-bar {
        background: #fff; border-radius: 12px; padding: 20px 24px; margin-bottom: 24px;
        border: 1px solid var(--gray-200); display: flex; gap: 32px; align-items: center;
        flex-wrap: wrap;
    }
    .stat-item { display: flex; align-items: center; gap: 12px; }
    .stat-icon {
        width: 48px; height: 48px; border-radius: 12px; background: var(--teal-pale);
        display: flex; align-items: center; justify-content: center; color: var(--teal);
        font-size: 24px;
    }
    .stat-content { }
    .stat-value { font-size: 24px; font-weight: 800; color: var(--gray-800); }
    .stat-label { font-size: 13px; color: var(--gray-500); }
    
    /* Modal */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,.5); backdrop-filter: blur(4px);
        display: none; align-items: center; justify-content: center; z-index: 1000; padding: 20px;
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
        background: #fff; border-radius: 20px; padding: 32px; width: 100%; max-width: 550px;
        box-shadow: 0 20px 60px rgba(0,0,0,.3); max-height: 90vh; overflow-y: auto;
    }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .modal-header h3 { font-size: 20px; font-weight: 800; color: var(--gray-800); }
    .modal-close {
        width: 32px; height: 32px; border-radius: 50%; background: var(--gray-100);
        border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
        color: var(--gray-600); transition: all .2s;
    }
    .modal-close:hover { background: var(--gray-200); }
    
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--gray-700); margin-bottom: 8px; }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%; padding: 11px 14px; border: 1.5px solid var(--gray-200);
        border-radius: 10px; font-size: 14px; font-family: inherit; transition: all .2s;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
        outline: none; border-color: var(--teal); box-shadow: 0 0 0 3px rgba(13,148,136,.1);
    }
    .form-group textarea { resize: vertical; min-height: 80px; }
    .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    
    .modal-actions { display: flex; gap: 10px; margin-top: 24px; }
    .btn-cancel {
        flex: 1; padding: 12px; border-radius: 10px; font-size: 14px; font-weight: 600;
        background: var(--gray-100); color: var(--gray-700); border: none; cursor: pointer;
    }
    .btn-submit {
        flex: 1; padding: 12px; border-radius: 10px; font-size: 14px; font-weight: 700;
        background: var(--teal); color: #fff; border: none; cursor: pointer;
    }
    .btn-submit:hover { background: var(--teal-dark); }
    
    .alert {
        padding: 14px 18px; border-radius: 12px; margin-bottom: 20px;
        display: none; align-items: center; gap: 10px;
    }
    .alert.success { background: #f0fdf4; border: 1px solid #86efac; color: #16a34a; }
    .alert.error { background: #fef2f2; border: 1px solid #fca5a5; color: #dc2626; }
</style>
@endpush

@section('content')
<div style="padding-top:68px;background:var(--gray-50);min-height:100vh;">
    <div class="doctors-container">
        <div class="alert success" id="successAlert">
            <i class="fa-solid fa-circle-check"></i>
            <span id="successMessage"></span>
        </div>

        <div class="page-header">
            <div class="page-header-left">
                <h1>👨‍⚕️ Dokter Kami</h1>
                <p>Tim dokter hewan profesional di klinik kami</p>
            </div>
            <button class="btn-add-doctor" onclick="openAddModal()">
                <i class="fa-solid fa-plus"></i>
                Tambah Dokter
            </button>
        </div>

        <div class="stats-bar" id="statsBar" style="display:none;">
            <div class="stat-item">
                <div class="stat-icon"><i class="fa-solid fa-user-doctor"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="totalDoctors">0</div>
                    <div class="stat-label">Total Dokter</div>
                </div>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
                <div class="stat-content">
                    <div class="stat-value" id="onlineDoctors">0</div>
                    <div class="stat-label">Dokter Online</div>
                </div>
            </div>
        </div>

        <div class="doctors-grid" id="doctorsGrid">
            <div class="loading-spinner">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <p style="margin-top:16px;color:var(--gray-500);">Memuat data dokter...</p>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Doctor Modal -->
<div class="modal-overlay" id="doctorModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah Dokter Baru</h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="doctorForm">
            <input type="hidden" id="doctorId">
            <div class="form-group">
                <label for="name">Nama Lengkap *</label>
                <input type="text" id="name" required placeholder="Dr. Nama Dokter">
            </div>
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" required placeholder="email@example.com">
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label for="phone">No. Telepon</label>
                    <input type="tel" id="phone" placeholder="08123456789">
                </div>
                <div class="form-group" id="passwordGroup">
                    <label for="password">Password *</label>
                    <input type="password" id="password" placeholder="Min. 6 karakter">
                </div>
            </div>
            <div class="form-group">
                <label for="spesialis">Spesialisasi</label>
                <input type="text" id="spesialis" placeholder="Bedah, Umum, dll">
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label for="experience_years">Pengalaman (tahun)</label>
                    <input type="number" id="experience_years" min="0" placeholder="0">
                </div>
                <div class="form-group">
                    <label for="license_number">No. Lisensi</label>
                    <input type="text" id="license_number" placeholder="DRH123456">
                </div>
            </div>
            <div class="form-group">
                <label for="bio">Bio/Deskripsi</label>
                <textarea id="bio" placeholder="Deskripsi singkat tentang dokter..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fa-solid fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const token = localStorage.getItem('vetra_token');
    const user = JSON.parse(localStorage.getItem('vetra_user'));

    if (!token || !user || user.role !== 'clinic') {
        window.location.href = '/login';
    }

    let doctors = [];

    // Load doctors
    async function loadDoctors() {
        try {
            const res = await fetch('/api/clinic/doctors', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                const data = await res.json();
                console.log('Doctors data:', data);
                doctors = data.doctors || [];
                renderDoctors();
                updateStats();
            } else {
                console.error('Failed to load doctors:', res.status);
                showError();
            }
        } catch (error) {
            console.error('Error loading doctors:', error);
            showError();
        }
    }

    // Render doctors
    function renderDoctors() {
        const container = document.getElementById('doctorsGrid');
        
        if (!doctors || doctors.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-user-doctor-slash"></i>
                    <p>Belum ada dokter terdaftar di klinik ini</p>
                    <a href="mailto:admin@vetra.id" class="btn-action">
                        <i class="fa-solid fa-envelope"></i>
                        Hubungi Admin untuk Menambahkan
                    </a>
                </div>`;
            return;
        }

        container.innerHTML = doctors.map(doctor => {
            const isOnline = doctor.is_online;
            const experienceYears = doctor.experience_years || 0;
            
            return `
                <div class="doctor-card">
                    <div class="doctor-header">
                        <div class="online-badge ${isOnline ? 'online' : 'offline'}">
                            <span class="dot"></span>
                            ${isOnline ? 'Online' : 'Offline'}
                        </div>
                        <div class="doctor-avatar">
                            ${doctor.user.profile_pic 
                                ? `<img src="${doctor.user.profile_pic}" alt="${doctor.user.name}">` 
                                : '<i class="fa-solid fa-user-doctor"></i>'}
                        </div>
                        <div class="doctor-name">${doctor.user.name}</div>
                        <div class="doctor-speciality">${doctor.spesialis || 'Dokter Hewan Umum'}</div>
                    </div>
                    
                    <div class="doctor-body">
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
                            <div class="info-text">
                                <div class="info-label">Email</div>
                                <div class="info-value">${doctor.user.email}</div>
                            </div>
                        </div>
                        
                        ${doctor.user.phone ? `
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                            <div class="info-text">
                                <div class="info-label">Telepon</div>
                                <div class="info-value">${doctor.user.phone}</div>
                            </div>
                        </div>` : ''}
                        
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                            <div class="info-text">
                                <div class="info-label">Pengalaman</div>
                                <div class="info-value">${experienceYears} Tahun</div>
                            </div>
                        </div>
                        
                        ${doctor.license_number ? `
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-id-card"></i></div>
                            <div class="info-text">
                                <div class="info-label">No. Lisensi</div>
                                <div class="info-value">${doctor.license_number}</div>
                            </div>
                        </div>` : ''}
                        
                        ${doctor.bio ? `
                        <div class="doctor-bio">
                            ${doctor.bio}
                        </div>` : ''}
                    </div>
                    
                    <div class="doctor-actions">
                        <button class="btn-edit" onclick="editDoctor(${doctor.id})">
                            <i class="fa-solid fa-pen"></i> Edit
                        </button>
                        <button class="btn-delete" onclick="deleteDoctor(${doctor.id}, '${doctor.user.name.replace(/'/g, "\\'")}')">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>`;
        }).join('');
    }

    // Update stats
    function updateStats() {
        const total = doctors.length;
        const online = doctors.filter(d => d.is_online).length;
        
        document.getElementById('totalDoctors').textContent = total;
        document.getElementById('onlineDoctors').textContent = online;
        
        if (total > 0) {
            document.getElementById('statsBar').style.display = 'flex';
        }
    }

    function showError() {
        document.getElementById('doctorsGrid').innerHTML = `
            <div class="empty-state">
                <i class="fa-solid fa-exclamation-triangle"></i>
                <p>Gagal memuat data. Silakan refresh halaman.</p>
            </div>`;
    }

    let editingId = null;

    // Open add modal
    function openAddModal() {
        editingId = null;
        document.getElementById('modalTitle').textContent = 'Tambah Dokter Baru';
        document.getElementById('doctorForm').reset();
        document.getElementById('doctorId').value = '';
        document.getElementById('passwordGroup').style.display = 'block';
        document.getElementById('password').required = true;
        document.getElementById('doctorModal').classList.add('open');
    }

    // Edit doctor
    function editDoctor(id) {
        const doctor = doctors.find(d => d.id === id);
        if (!doctor) return;

        editingId = id;
        document.getElementById('modalTitle').textContent = 'Edit Data Dokter';
        document.getElementById('doctorId').value = doctor.id;
        document.getElementById('name').value = doctor.user.name;
        document.getElementById('email').value = doctor.user.email;
        document.getElementById('phone').value = doctor.user.phone || '';
        document.getElementById('spesialis').value = doctor.spesialis || '';
        document.getElementById('experience_years').value = doctor.experience_years || '';
        document.getElementById('license_number').value = doctor.license_number || '';
        document.getElementById('bio').value = doctor.bio || '';
        
        // Hide password field for edit
        document.getElementById('passwordGroup').style.display = 'none';
        document.getElementById('password').required = false;
        document.getElementById('password').value = '';
        
        document.getElementById('doctorModal').classList.add('open');
    }

    // Close modal
    function closeModal() {
        document.getElementById('doctorModal').classList.remove('open');
    }

    // Save doctor
    document.getElementById('doctorForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
        btn.disabled = true;

        const data = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value || null,
            spesialis: document.getElementById('spesialis').value || null,
            experience_years: document.getElementById('experience_years').value || null,
            license_number: document.getElementById('license_number').value || null,
            bio: document.getElementById('bio').value || null,
        };

        // Add password for new doctor
        if (!editingId) {
            data.password = document.getElementById('password').value;
        }

        try {
            const url = editingId ? `/api/clinic/doctors/${editingId}` : '/api/clinic/doctors';
            const method = editingId ? 'PUT' : 'POST';

            const res = await fetch(url, {
                method: method,
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (res.ok) {
                const result = await res.json();
                showSuccess(result.message || 'Data dokter berhasil disimpan!');
                closeModal();
                loadDoctors();
            } else {
                const error = await res.json();
                alert('Gagal menyimpan: ' + (error.message || error.error || 'Error'));
            }
        } catch (error) {
            alert('Gagal terhubung ke server');
        } finally {
            btn.innerHTML = '<i class="fa-solid fa-save"></i> Simpan';
            btn.disabled = false;
        }
    });

    // Delete doctor
    async function deleteDoctor(id, name) {
        if (!confirm(`Yakin ingin menghapus dokter "${name}"?\n\nDokter yang dihapus tidak dapat dipulihkan dan akunnya akan dinonaktifkan.`)) {
            return;
        }

        try {
            const res = await fetch(`/api/clinic/doctors/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                const result = await res.json();
                showSuccess(result.message || 'Dokter berhasil dihapus');
                loadDoctors();
            } else {
                const error = await res.json();
                alert('Gagal menghapus: ' + (error.message || error.error || 'Error'));
            }
        } catch (error) {
            alert('Gagal terhubung ke server');
        }
    }

    function showSuccess(message) {
        const alert = document.getElementById('successAlert');
        document.getElementById('successMessage').textContent = message;
        alert.style.display = 'flex';
        setTimeout(() => alert.style.display = 'none', 5000);
    }

    // Load doctors on page load
    loadDoctors();
</script>
@endsection
