@extends('layouts.app')
@section('title', 'Kelola User')

@push('styles')
<style>
    .admin-users { max-width: 1400px; margin: 0 auto; padding: 40px 24px; }
    .page-header { margin-bottom: 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
    .page-header h1 { font-size: 32px; font-weight: 800; color: var(--gray-800); }
    
    .btn-primary {
        padding: 12px 24px; background: var(--teal); color: #fff;
        border: none; border-radius: 12px; font-size: 14px; font-weight: 700;
        cursor: pointer; transition: all .2s; display: flex; align-items: center; gap: 8px;
    }
    .btn-primary:hover { background: var(--teal-dark); transform: translateY(-1px); }
    
    .filter-tabs {
        display: flex; gap: 8px; background: #fff; padding: 6px; border-radius: 12px;
        border: 1px solid var(--gray-200); margin-bottom: 24px; flex-wrap: wrap;
    }
    .filter-tab {
        padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600;
        border: none; background: transparent; color: var(--gray-600); cursor: pointer;
        transition: all .2s;
    }
    .filter-tab:hover { background: var(--gray-100); color: var(--gray-800); }
    .filter-tab.active { background: var(--teal); color: #fff; }
    
    .users-table {
        background: #fff; border-radius: 16px; border: 1px solid var(--gray-200);
        box-shadow: var(--shadow); overflow: auto;
    }
    
    table {
        width: 100%; border-collapse: collapse; min-width: 800px;
    }
    
    thead {
        background: var(--gray-50);
    }
    
    th {
        padding: 16px 20px; text-align: left; font-size: 13px; font-weight: 700;
        color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.5px;
    }
    
    td {
        padding: 16px 20px; border-top: 1px solid var(--gray-100);
        font-size: 14px; color: var(--gray-700);
    }
    
    tbody tr:hover {
        background: var(--gray-50);
    }
    
    .role-badge {
        display: inline-block; padding: 4px 12px; border-radius: 8px;
        font-size: 12px; font-weight: 600;
    }
    .role-badge.user { background: #dbeafe; color: #3b82f6; }
    .role-badge.doctor { background: #d1fae5; color: #10b981; }
    .role-badge.clinic { background: #e9d5ff; color: #a855f7; }
    
    .action-btns {
        display: flex; gap: 8px;
    }
    
    .btn-edit, .btn-delete {
        padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;
        border: none; cursor: pointer; transition: all .2s;
    }
    
    .btn-edit {
        background: #dbeafe; color: #3b82f6;
    }
    .btn-edit:hover {
        background: #3b82f6; color: #fff;
    }
    
    .btn-delete {
        background: #fee2e2; color: #dc2626;
    }
    .btn-delete:hover {
        background: #dc2626; color: #fff;
    }
    
    .empty-state {
        text-align: center; padding: 60px 24px; color: var(--gray-400);
    }
    .empty-state i { font-size: 48px; margin-bottom: 16px; }
    
    /* Modal */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,.5); backdrop-filter: blur(4px);
        display: none; align-items: center; justify-content: center; z-index: 1000; padding: 20px;
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
        background: #fff; border-radius: 20px; padding: 32px; width: 100%; max-width: 500px;
        box-shadow: 0 20px 60px rgba(0,0,0,.3);
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
    .form-group input, .form-group select {
        width: 100%; padding: 11px 14px; border: 1.5px solid var(--gray-200);
        border-radius: 10px; font-size: 14px; font-family: inherit; transition: all .2s;
    }
    .form-group input:focus, .form-group select:focus {
        outline: none; border-color: var(--teal); box-shadow: 0 0 0 3px rgba(13,148,136,.1);
    }
    
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
    <div class="admin-users">
        <div class="alert success" id="successAlert">
            <i class="fa-solid fa-circle-check"></i>
            <span id="successMessage"></span>
        </div>
        
        <div class="page-header">
            <h1>👥 Kelola User</h1>
            <button class="btn-primary" onclick="openCreateModal()">
                <i class="fa-solid fa-plus"></i> Tambah User Baru
            </button>
        </div>

        <div class="filter-tabs">
            <button class="filter-tab active" onclick="filterUsers('all')">Semua</button>
            <button class="filter-tab" onclick="filterUsers('user')">Pengguna</button>
            <button class="filter-tab" onclick="filterUsers('doctor')">Dokter</button>
            <button class="filter-tab" onclick="filterUsers('clinic')">Klinik</button>
        </div>

        <div class="users-table" id="usersTable">
            <div class="empty-state">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <p>Memuat data user...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Create/Edit -->
<div class="modal-overlay" id="userModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah User Baru</h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="userForm">
            <input type="hidden" id="userId">
            <div class="form-group">
                <label for="userName">Nama Lengkap</label>
                <input type="text" id="userName" required>
            </div>
            <div class="form-group">
                <label for="userEmail">Email</label>
                <input type="email" id="userEmail" required>
            </div>
            <div class="form-group">
                <label for="userRole">Role</label>
                <select id="userRole" required>
                    <option value="">Pilih Role</option>
                    <option value="user">Pengguna</option>
                    <option value="doctor">Dokter</option>
                    <option value="clinic">Klinik</option>
                </select>
            </div>
            <div class="form-group" id="passwordGroup">
                <label for="userPassword">Password</label>
                <input type="password" id="userPassword" placeholder="Minimal 6 karakter">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-submit" id="submitBtn">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const token = localStorage.getItem('vetra_token');
    const user = JSON.parse(localStorage.getItem('vetra_user'));

    if (!token || !user || user.role !== 'admin') {
        window.location.href = '/login';
    }

    let allUsers = []; // Store all users
    let currentFilter = 'all';
    let editMode = false;
    let isLoading = false; // Prevent multiple simultaneous loads

    async function loadUsers() {
        if (isLoading) {
            console.log('Already loading users, skipping...');
            return;
        }
        
        isLoading = true;
        const container = document.getElementById('usersTable');
        container.innerHTML = `
            <div class="empty-state">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <p>Memuat data user...</p>
            </div>`;
        
        try {
            console.log('Loading users from: /api/admin/users');
            
            const res = await fetch('/api/admin/users', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            console.log('Response status:', res.status);

            if (res.ok) {
                const data = await res.json();
                console.log('Users loaded:', data);
                
                // Store all users (admin already filtered by backend)
                allUsers = data.data || data.users || data;
                console.log('Total users loaded:', allUsers.length);
                console.log('Users by role:', {
                    user: allUsers.filter(u => u.role === 'user').length,
                    doctor: allUsers.filter(u => u.role === 'doctor').length,
                    clinic: allUsers.filter(u => u.role === 'clinic').length
                });
                renderUsers();
            } else if (res.status === 401) {
                localStorage.clear();
                window.location.href = '/login';
            } else {
                console.error('Error response:', res.status);
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fa-solid fa-exclamation-triangle"></i>
                        <p>Gagal memuat user. Status: ${res.status}</p>
                        <button class="btn-primary" onclick="location.reload()">Refresh</button>
                    </div>`;
            }
        } catch (error) {
            console.error('Error loading users:', error);
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    <p>Gagal terhubung ke server: ${error.message}</p>
                    <button class="btn-primary" onclick="location.reload()">Refresh</button>
                </div>`;
        } finally {
            isLoading = false;
        }
    }

    function renderUsers() {
        const container = document.getElementById('usersTable');
        const filtered = currentFilter === 'all' ? allUsers : allUsers.filter(u => u.role === currentFilter);

        console.log('Rendering users, filter:', currentFilter, 'count:', filtered.length);

        if (filtered.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-users-slash"></i>
                    <p>Tidak ada user untuk filter ini</p>
                </div>`;
            return;
        }

        container.innerHTML = `
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    ${filtered.map(u => `
                        <tr>
                            <td>${u.id}</td>
                            <td>${u.name}</td>
                            <td>${u.email}</td>
                            <td><span class="role-badge ${u.role}">${u.role}</span></td>
                            <td>${new Date(u.created_at).toLocaleDateString('id-ID')}</td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-edit" onclick="openEditModal(${u.id})">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </button>
                                    <button class="btn-delete" onclick="deleteUser(${u.id})">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>`;
    }

    function filterUsers(role) {
        if (isLoading) {
            console.log('Currently loading, please wait...');
            return;
        }
        
        currentFilter = role;
        document.querySelectorAll('.filter-tab').forEach(tab => tab.classList.remove('active'));
        event.target.classList.add('active');
        renderUsers();
    }

    function openCreateModal() {
        editMode = false;
        document.getElementById('modalTitle').textContent = 'Tambah User Baru';
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('userPassword').required = true;
        document.getElementById('passwordGroup').querySelector('label').textContent = 'Password';
        document.getElementById('userModal').classList.add('open');
    }

    function openEditModal(id) {
        editMode = true;
        const u = allUsers.find(user => user.id === id);
        if (!u) return;

        document.getElementById('modalTitle').textContent = 'Edit User';
        document.getElementById('userId').value = u.id;
        document.getElementById('userName').value = u.name;
        document.getElementById('userEmail').value = u.email;
        document.getElementById('userRole').value = u.role;
        document.getElementById('userPassword').value = '';
        document.getElementById('userPassword').required = false;
        document.getElementById('passwordGroup').querySelector('label').textContent = 'Password (kosongkan jika tidak diubah)';
        document.getElementById('userModal').classList.add('open');
    }

    function closeModal() {
        document.getElementById('userModal').classList.remove('open');
    }

    document.getElementById('userForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const id = document.getElementById('userId').value;
        const name = document.getElementById('userName').value;
        const email = document.getElementById('userEmail').value;
        const role = document.getElementById('userRole').value;
        const password = document.getElementById('userPassword').value;

        const data = { name, email, role };
        if (password) data.password = password;

        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
        btn.disabled = true;

        try {
            const url = editMode ? `/api/admin/users/${id}` : '/api/admin/users';
            const method = editMode ? 'PUT' : 'POST';

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
                showSuccess(editMode ? 'User berhasil diupdate!' : 'User berhasil ditambahkan!');
                closeModal();
                loadUsers();
            } else {
                const error = await res.json();
                alert('Error: ' + (error.message || 'Gagal menyimpan user'));
            }
        } catch (error) {
            alert('Gagal terhubung ke server');
        } finally {
            btn.innerHTML = 'Simpan';
            btn.disabled = false;
        }
    });

    async function deleteUser(id) {
        const u = allUsers.find(user => user.id === id);
        if (!confirm(`Yakin ingin menghapus user "${u.name}"?`)) return;

        try {
            const res = await fetch(`/api/admin/users/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                showSuccess('User berhasil dihapus!');
                loadUsers();
            } else {
                alert('Gagal menghapus user');
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

    loadUsers();
</script>
@endsection
