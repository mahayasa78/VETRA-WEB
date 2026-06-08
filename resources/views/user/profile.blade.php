@extends('layouts.app')
@section('title', 'Profil Saya')

@push('styles')
<style>
    .profile-container { max-width: 900px; margin: 0 auto; padding: 40px 24px; }
    .profile-card {
        background: #fff; border-radius: 16px; border: 1px solid var(--gray-200);
        padding: 32px; margin-bottom: 24px; box-shadow: var(--shadow);
    }
    .profile-header { display: flex; align-items: center; gap: 24px; margin-bottom: 32px; position: relative; }
    .profile-avatar-wrapper { position: relative; }
    .profile-avatar {
        width: 100px; height: 100px; border-radius: 50%; background: var(--teal-pale);
        display: flex; align-items: center; justify-content: center;
        font-size: 40px; color: var(--teal); border: 4px solid var(--teal);
        overflow: hidden;
    }
    .profile-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .upload-overlay {
        position: absolute; inset: 0; border-radius: 50%;
        background: rgba(0,0,0,.6); display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: opacity .2s; cursor: pointer;
    }
    .profile-avatar-wrapper:hover .upload-overlay { opacity: 1; }
    .upload-overlay i { color: #fff; font-size: 24px; }
    .photo-actions {
        display: flex; gap: 8px; margin-top: 8px;
    }
    .photo-actions button {
        padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;
        border: none; cursor: pointer; transition: all .2s; font-family: inherit;
    }
    .btn-upload-photo {
        background: var(--teal); color: #fff;
    }
    .btn-upload-photo:hover { background: var(--teal-dark); }
    .btn-delete-photo {
        background: var(--coral); color: #fff;
    }
    .btn-delete-photo:hover { background: var(--coral-dark); }
    .profile-info h2 { font-size: 28px; font-weight: 800; color: var(--gray-800); margin-bottom: 4px; }
    .profile-info .role-badge {
        display: inline-flex; align-items: center; gap: 6px;
        background: var(--teal-pale); color: var(--teal-dark);
        padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;
    }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 14px; font-weight: 700; color: var(--gray-700); margin-bottom: 8px; }
    .form-group input, .form-group textarea {
        width: 100%; padding: 12px 16px; border: 1.5px solid var(--gray-200);
        border-radius: 10px; font-size: 15px; font-family: inherit;
        transition: all .2s;
    }
    .form-group input:focus, .form-group textarea:focus {
        outline: none; border-color: var(--teal); box-shadow: 0 0 0 3px rgba(13,148,136,.1);
    }
    .form-actions { display: flex; gap: 12px; margin-top: 24px; }
    .btn-save {
        background: var(--teal); color: #fff; padding: 13px 28px; border-radius: 12px;
        font-weight: 700; font-size: 15px; border: none; cursor: pointer; transition: all .2s;
    }
    .btn-save:hover { background: var(--teal-dark); transform: translateY(-1px); }
    .btn-cancel {
        background: var(--gray-100); color: var(--gray-700); padding: 13px 28px;
        border-radius: 12px; font-weight: 600; font-size: 15px; border: none; cursor: pointer;
    }
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
    <div class="profile-container">
        <div style="margin-bottom:24px;">
            <h1 style="font-size:32px;font-weight:800;color:var(--gray-800);margin-bottom:8px;">Profil Saya</h1>
            <p style="font-size:15px;color:var(--gray-500);">Kelola informasi profil Anda</p>
        </div>

        <div class="alert success" id="successAlert">
            <i class="fa-solid fa-circle-check"></i>
            <span id="successMessage"></span>
        </div>

        <div class="alert error" id="errorAlert">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span id="errorMessage"></span>
        </div>

        <div class="profile-card">
            <div class="profile-header" id="profileHeader">
                <!-- Will be populated by JS -->
            </div>

            <form id="profileForm">
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Nomor Telepon</label>
                    <input type="tel" id="phone">
                </div>
                <div class="form-group">
                    <label for="address">Alamat</label>
                    <textarea id="address" rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-save" id="saveBtn">
                        <i class="fa-solid fa-save"></i> Simpan Perubahan
                    </button>
                    <button type="button" class="btn-cancel" onclick="window.location.reload()">
                        Batal
                    </button>
                </div>
            </form>
        </div>

        <div class="profile-card">
            <h3 style="font-size:18px;font-weight:800;color:var(--gray-800);margin-bottom:16px;">Ubah Password</h3>
            <form id="passwordForm">
                <div class="form-group">
                    <label for="current_password">Password Saat Ini</label>
                    <input type="password" id="current_password">
                </div>
                <div class="form-group">
                    <label for="new_password">Password Baru</label>
                    <input type="password" id="new_password">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password Baru</label>
                    <input type="password" id="confirm_password">
                </div>
                <button type="submit" class="btn-save">
                    <i class="fa-solid fa-key"></i> Ubah Password
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    const token = localStorage.getItem('vetra_token');
    const user = JSON.parse(localStorage.getItem('vetra_user'));

    if (!token || !user) {
        window.location.href = '/login';
    }

    // Load profile data
    function loadProfile() {
        const roleLabels = {
            user: 'Pengguna',
            doctor: 'Dokter Hewan',
            clinic: 'Klinik',
            admin: 'Administrator'
        };

        const avatarContent = user.profile_pic 
            ? `<img src="${user.profile_pic.startsWith('http') ? user.profile_pic : '/storage/' + user.profile_pic}" alt="${user.name}">`
            : '<i class="fa-solid fa-user"></i>';

        document.getElementById('profileHeader').innerHTML = `
            <div class="profile-avatar-wrapper">
                <div class="profile-avatar" id="profileAvatar">
                    ${avatarContent}
                </div>
                <div class="upload-overlay" onclick="document.getElementById('photoInput').click()">
                    <i class="fa-solid fa-camera"></i>
                </div>
            </div>
            <div class="profile-info">
                <h2>${user.name}</h2>
                <span class="role-badge">
                    <i class="fa-solid fa-id-badge"></i> ${roleLabels[user.role] || 'User'}
                </span>
                <div class="photo-actions">
                    <button class="btn-upload-photo" onclick="document.getElementById('photoInput').click()">
                        <i class="fa-solid fa-camera"></i> Upload Foto
                    </button>
                    ${user.profile_pic ? `
                    <button class="btn-delete-photo" onclick="deletePhoto()">
                        <i class="fa-solid fa-trash"></i> Hapus
                    </button>` : ''}
                </div>
            </div>
            <input type="file" id="photoInput" accept="image/*" style="display:none;" onchange="uploadPhoto(event)">`;

        document.getElementById('name').value = user.name || '';
        document.getElementById('email').value = user.email || '';
        document.getElementById('phone').value = user.phone || '';
        document.getElementById('address').value = user.address || '';
    }

    // Upload photo
    async function uploadPhoto(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            showError('Ukuran file maksimal 2MB');
            return;
        }

        // Validate file type
        if (!file.type.startsWith('image/')) {
            showError('File harus berupa gambar');
            return;
        }

        const formData = new FormData();
        formData.append('profile_pic', file);

        try {
            const res = await fetch('/api/user/profile/picture', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                body: formData
            });

            const data = await res.json();

            if (res.ok) {
                user.profile_pic = data.user.profile_pic;
                localStorage.setItem('vetra_user', JSON.stringify(user));
                showSuccess('Foto profil berhasil diupload!');
                loadProfile();
            } else {
                showError(data.message || 'Gagal upload foto');
            }
        } catch (error) {
            console.error('Upload error:', error);
            showError('Gagal terhubung ke server');
        }
    }

    // Delete photo
    async function deletePhoto() {
        if (!confirm('Yakin ingin menghapus foto profil?')) return;

        try {
            const res = await fetch('/api/user/profile/picture', {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token
                }
            });

            const data = await res.json();

            if (res.ok) {
                user.profile_pic = null;
                localStorage.setItem('vetra_user', JSON.stringify(user));
                showSuccess('Foto profil berhasil dihapus!');
                loadProfile();
            } else {
                showError(data.message || 'Gagal menghapus foto');
            }
        } catch (error) {
            showError('Gagal terhubung ke server');
        }
    }

    loadProfile();

    // Update profile
    document.getElementById('profileForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('saveBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
        btn.disabled = true;

        try {
            const res = await fetch('/api/user/profile', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                body: JSON.stringify({
                    name: document.getElementById('name').value,
                    phone: document.getElementById('phone').value,
                    address: document.getElementById('address').value
                })
            });

            const data = await res.json();

            if (res.ok) {
                // Update localStorage
                user.name = data.user.name;
                user.phone = data.user.phone;
                user.address = data.user.address;
                localStorage.setItem('vetra_user', JSON.stringify(user));
                
                showSuccess('Profil berhasil diperbarui!');
                loadProfile();
            } else {
                showError(data.message || 'Gagal memperbarui profil');
            }
        } catch (error) {
            showError('Gagal terhubung ke server');
        } finally {
            btn.innerHTML = '<i class="fa-solid fa-save"></i> Simpan Perubahan';
            btn.disabled = false;
        }
    });

    function showSuccess(message) {
        const alert = document.getElementById('successAlert');
        document.getElementById('successMessage').textContent = message;
        alert.style.display = 'flex';
        setTimeout(() => alert.style.display = 'none', 5000);
    }

    function showError(message) {
        const alert = document.getElementById('errorAlert');
        document.getElementById('errorMessage').textContent = message;
        alert.style.display = 'flex';
        setTimeout(() => alert.style.display = 'none', 5000);
    }
</script>
@endsection
