@extends('layouts.app')
@section('title', 'Hewan Saya')

@push('styles')
<style>
    .pets-container { max-width: 1100px; margin: 0 auto; padding: 40px 24px; }
    .pets-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; }
    .pets-header h1 { font-size: 32px; font-weight: 800; color: var(--gray-800); }
    .btn-add-pet {
        background: var(--teal); color: #fff; padding: 12px 24px; border-radius: 12px;
        font-weight: 700; font-size: 14px; border: none; cursor: pointer;
        display: flex; align-items: center; gap: 8px; transition: all .2s;
    }
    .btn-add-pet:hover { background: var(--teal-dark); transform: translateY(-1px); }

    .pets-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; }
    .pet-card {
        background: #fff; border-radius: 16px; border: 1px solid var(--gray-200);
        overflow: hidden; transition: all .3s; box-shadow: var(--shadow);
    }
    .pet-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); border-color: var(--teal); }

    .pet-image {
        height: 180px; background: linear-gradient(135deg, var(--teal-pale), #dbeafe);
        display: flex; align-items: center; justify-content: center;
        font-size: 64px; color: var(--teal); position: relative;
    }
    .pet-image img { width: 100%; height: 100%; object-fit: cover; }

    .pet-body { padding: 20px; }
    .pet-name { font-size: 20px; font-weight: 800; color: var(--gray-800); margin-bottom: 8px; }
    .pet-info { display: flex; align-items: center; gap: 12px; margin-bottom: 6px; font-size: 14px; color: var(--gray-600); }
    .pet-info i { color: var(--teal); width: 18px; }

    .pet-actions { display: flex; gap: 8px; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--gray-100); }
    .btn-edit, .btn-delete {
        flex: 1; padding: 9px; border-radius: 10px; font-size: 13px; font-weight: 600;
        border: none; cursor: pointer; transition: all .2s; display: flex; align-items: center;
        justify-content: center; gap: 6px;
    }
    .btn-edit  { background: var(--teal-pale); color: var(--teal-dark); }
    .btn-edit:hover  { background: var(--teal); color: #fff; }
    .btn-delete { background: #fee2e2; color: #dc2626; }
    .btn-delete:hover { background: #dc2626; color: #fff; }

    .state-box {
        grid-column: 1 / -1; text-align: center; padding: 80px 24px;
        background: #fff; border-radius: 16px; border: 1px solid var(--gray-200);
    }
    .state-box i  { font-size: 64px; margin-bottom: 16px; display: block; }
    .state-box p  { font-size: 16px; color: var(--gray-500); margin-bottom: 24px; }

    /* Modal */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,.5); backdrop-filter: blur(4px);
        display: none; align-items: center; justify-content: center; z-index: 1000; padding: 20px;
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
        background: #fff; border-radius: 20px; padding: 32px; width: 100%; max-width: 500px;
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

    .modal-actions { display: flex; gap: 10px; margin-top: 24px; }
    .btn-cancel {
        flex: 1; padding: 12px; border-radius: 10px; font-size: 14px; font-weight: 600;
        background: var(--gray-100); color: var(--gray-700); border: none; cursor: pointer;
    }
    .btn-save {
        flex: 1; padding: 12px; border-radius: 10px; font-size: 14px; font-weight: 700;
        background: var(--teal); color: #fff; border: none; cursor: pointer;
    }
    .btn-save:hover { background: var(--teal-dark); }

    .alert-bar {
        padding: 14px 18px; border-radius: 12px; margin-bottom: 20px;
        display: none; align-items: center; gap: 10px;
    }
    .alert-bar.success { background: #f0fdf4; border: 1px solid #86efac; color: #16a34a; display: flex; }
    .alert-bar.error   { background: #fef2f2; border: 1px solid #fca5a5; color: #dc2626; display: flex; }
</style>
@endpush

@section('content')
<div style="padding-top:68px;background:var(--gray-50);min-height:100vh;">
    <div class="pets-container">
        <div id="alertBar" class="alert-bar">
            <i class="fa-solid fa-circle-check"></i>
            <span id="alertMsg"></span>
        </div>

        <div class="pets-header">
            <div>
                <h1>🐕 Hewan Saya</h1>
                <p style="font-size:15px;color:var(--gray-500);margin-top:4px;">Kelola data hewan peliharaan Anda</p>
            </div>
            <button class="btn-add-pet" onclick="openAddModal()">
                <i class="fa-solid fa-plus"></i> Tambah Hewan
            </button>
        </div>

        <div id="petsGrid" class="pets-grid">
            <div class="state-box">
                <i class="fa-solid fa-spinner fa-spin" style="color:var(--teal);"></i>
                <p>Memuat data hewan…</p>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal-overlay" id="petModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah Hewan Baru</h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="petForm">
            <input type="hidden" id="petId">
            <div class="form-group">
                <label for="petName">Nama Hewan</label>
                <input type="text" id="petName" required placeholder="Contoh: Milo">
            </div>
            <div class="form-group">
                <label for="petSpecies">Jenis Hewan</label>
                <select id="petSpecies" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="Anjing">Anjing</option>
                    <option value="Kucing">Kucing</option>
                    <option value="Kelinci">Kelinci</option>
                    <option value="Hamster">Hamster</option>
                    <option value="Burung">Burung</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div class="form-group">
                <label for="petBreed">Ras</label>
                <input type="text" id="petBreed" placeholder="Contoh: Golden Retriever">
            </div>
            <div class="form-group">
                <label for="petAge">Usia (tahun)</label>
                <input type="number" id="petAge" min="0" step="0.1" placeholder="Contoh: 2.5">
            </div>
            <div class="form-group">
                <label for="petWeight">Berat (kg)</label>
                <input type="number" id="petWeight" min="0" step="0.1" placeholder="Contoh: 15.5">
            </div>
            <div class="form-group">
                <label for="petNotes">Catatan Kesehatan</label>
                <textarea id="petNotes" rows="3" placeholder="Catatan khusus tentang kesehatan hewan..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-save" id="saveBtn">
                    <i class="fa-solid fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const token = localStorage.getItem('vetra_token');
    const user  = (() => { try { return JSON.parse(localStorage.getItem('vetra_user')); } catch { return null; } })();
    if (!token || !user) { window.location.href = '/login'; return; }

    let pets      = [];
    let editingId = null;

    const grid  = document.getElementById('petsGrid');
    const alert = document.getElementById('alertBar');
    const alertMsg = document.getElementById('alertMsg');

    function showAlert(msg, type = 'success') {
        alert.className = 'alert-bar ' + type;
        alertMsg.textContent = msg;
        setTimeout(() => { alert.className = 'alert-bar'; }, 5000);
    }

    function renderState(iconClass, iconColor, message, extra = '') {
        grid.innerHTML = `
        <div class="state-box">
            <i class="${iconClass}" style="color:${iconColor};"></i>
            <p>${message}</p>
            ${extra}
        </div>`;
    }

    function renderPets() {
        if (!pets || pets.length === 0) {
            renderState(
                'fa-solid fa-paw', 'var(--gray-300)',
                'Belum ada hewan yang terdaftar.',
                `<button class="btn-add-pet" onclick="openAddModal()" style="margin:0 auto;">
                    <i class="fa-solid fa-plus"></i> Tambah Hewan Pertama
                </button>`
            );
            return;
        }

        grid.innerHTML = pets.map(pet => {
            const icon = pet.species === 'Anjing' ? 'fa-dog'
                       : pet.species === 'Kucing'  ? 'fa-cat'
                       : pet.species === 'Burung'  ? 'fa-dove'
                       : pet.species === 'Kelinci' ? 'fa-rabbit'
                       : 'fa-paw';

            const safeName = pet.name.replace(/'/g, "\\'");
            return `
            <div class="pet-card">
                <div class="pet-image">
                    ${pet.photo ? `<img src="${pet.photo}" alt="${pet.name}">` : `<i class="fa-solid ${icon}"></i>`}
                </div>
                <div class="pet-body">
                    <div class="pet-name">${pet.name}</div>
                    <div class="pet-info"><i class="fa-solid fa-tag"></i>
                        <span>${pet.species}${pet.breed ? ' · ' + pet.breed : ''}</span>
                    </div>
                    ${pet.age    ? `<div class="pet-info"><i class="fa-solid fa-calendar"></i><span>${pet.age} tahun</span></div>` : ''}
                    ${pet.weight ? `<div class="pet-info"><i class="fa-solid fa-weight-scale"></i><span>${pet.weight} kg</span></div>` : ''}
                    ${pet.notes  ? `<div class="pet-info"><i class="fa-solid fa-notes-medical"></i><span>${pet.notes.substring(0,60)}${pet.notes.length > 60 ? '…' : ''}</span></div>` : ''}
                    <div class="pet-actions">
                        <button class="btn-edit"   onclick="editPet(${pet.id})"><i class="fa-solid fa-pen"></i> Edit</button>
                        <button class="btn-delete" onclick="deletePet(${pet.id}, '${safeName}')"><i class="fa-solid fa-trash"></i> Hapus</button>
                    </div>
                </div>
            </div>`;
        }).join('');
    }

    async function loadPets() {
        renderState('fa-solid fa-spinner fa-spin', 'var(--teal)', 'Memuat data hewan…');
        try {
            const res = await authFetch('/api/user/pets');

            if (res.ok) {
                const data = await res.json();
                pets = data.pets || data || [];
                renderPets();
            } else {
                const err = await res.json().catch(() => ({}));
                renderState(
                    'fa-solid fa-circle-exclamation', '#dc2626',
                    'Gagal memuat data hewan: ' + (err.message || 'Terjadi kesalahan.'),
                    `<button class="btn-add-pet" onclick="loadPets()" style="margin:0 auto;">
                        <i class="fa-solid fa-rotate-right"></i> Coba Lagi
                    </button>`
                );
            }
        } catch (e) {
            console.error('loadPets error:', e);
            renderState(
                'fa-solid fa-wifi', '#dc2626',
                'Tidak dapat terhubung ke server. Periksa koneksi Anda.',
                `<button class="btn-add-pet" onclick="loadPets()" style="margin:0 auto;">
                    <i class="fa-solid fa-rotate-right"></i> Coba Lagi
                </button>`
            );
        }
    }

    // ── Modal ───────────────────────────────────────────────────────────────
    window.openAddModal = function () {
        editingId = null;
        document.getElementById('modalTitle').textContent  = 'Tambah Hewan Baru';
        document.getElementById('petForm').reset();
        document.getElementById('petId').value = '';
        document.getElementById('petModal').classList.add('open');
    };

    window.editPet = function (id) {
        const pet = pets.find(p => p.id === id);
        if (!pet) return;
        editingId = id;
        document.getElementById('modalTitle').textContent = 'Edit Data Hewan';
        document.getElementById('petId').value      = pet.id;
        document.getElementById('petName').value    = pet.name;
        document.getElementById('petSpecies').value = pet.species;
        document.getElementById('petBreed').value   = pet.breed   || '';
        document.getElementById('petAge').value     = pet.age     || '';
        document.getElementById('petWeight').value  = pet.weight  || '';
        document.getElementById('petNotes').value   = pet.notes   || '';
        document.getElementById('petModal').classList.add('open');
    };

    window.closeModal = function () {
        document.getElementById('petModal').classList.remove('open');
    };

    document.getElementById('petForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('saveBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan…';
        btn.disabled  = true;

        const payload = {
            name:    document.getElementById('petName').value,
            species: document.getElementById('petSpecies').value,
            breed:   document.getElementById('petBreed').value   || null,
            age:     document.getElementById('petAge').value     || null,
            weight:  document.getElementById('petWeight').value  || null,
            notes:   document.getElementById('petNotes').value   || null,
        };

        try {
            const url    = editingId ? `/api/user/pets/${editingId}` : '/api/user/pets';
            const method = editingId ? 'PUT' : 'POST';

            const res = await authFetch(url, {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });

            if (res.ok) {
                showAlert(editingId ? 'Data hewan berhasil diperbarui!' : 'Hewan baru berhasil ditambahkan!');
                window.closeModal();
                loadPets();
            } else {
                const err = await res.json().catch(() => ({}));
                showAlert('Gagal menyimpan: ' + (err.message || 'Error tidak diketahui'), 'error');
            }
        } catch (err) {
            showAlert('Gagal terhubung ke server.', 'error');
        } finally {
            btn.innerHTML = '<i class="fa-solid fa-save"></i> Simpan';
            btn.disabled  = false;
        }
    });

    window.deletePet = async function (id, name) {
        if (!confirm(`Yakin ingin menghapus data "${name}"?`)) return;
        try {
            const res = await authFetch(`/api/user/pets/${id}`, { method: 'DELETE' });
            if (res.ok) {
                showAlert('Data hewan berhasil dihapus');
                loadPets();
            } else {
                showAlert('Gagal menghapus data hewan.', 'error');
            }
        } catch {
            showAlert('Gagal terhubung ke server.', 'error');
        }
    };

    // ── Init ─────────────────────────────────────────────────────────────────
    loadPets();
})();
</script>
@endsection
