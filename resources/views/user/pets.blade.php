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
    .state-box p  { font-size: 16px; color: var(--gray-500); margin-bottom: 12px; }
    .retry-btn {
        display: inline-flex; align-items: center; gap: 6px;
        margin-top: 4px; padding: 10px 20px; background: var(--teal); color: #fff;
        border-radius: 10px; border: none; cursor: pointer; font-weight: 600; font-size: 14px; font-family: inherit;
    }

    /* Alert bar */
    .alert-bar { padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; display: none; align-items: center; gap: 10px; }
    .alert-bar.success { background: #f0fdf4; border: 1px solid #86efac; color: #16a34a; display: flex; }
    .alert-bar.error   { background: #fef2f2; border: 1px solid #fca5a5; color: #dc2626; display: flex; }

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
    .btn-cancel { flex: 1; padding: 12px; border-radius: 10px; font-size: 14px; font-weight: 600; background: var(--gray-100); color: var(--gray-700); border: none; cursor: pointer; }
    .btn-save   { flex: 1; padding: 12px; border-radius: 10px; font-size: 14px; font-weight: 700; background: var(--teal); color: #fff; border: none; cursor: pointer; }
    .btn-save:hover { background: var(--teal-dark); }
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

<!-- Modal Tambah/Edit -->
<div class="modal-overlay" id="petModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalTitle">Tambah Hewan Baru</h3>
            <button class="modal-close" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="petForm">
            <input type="hidden" id="petId">
            <div class="form-group">
                <label>Nama Hewan</label>
                <input type="text" id="petName" required placeholder="Contoh: Milo">
            </div>
            <div class="form-group">
                <label>Jenis Hewan</label>
                <select id="petSpecies" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option>Anjing</option><option>Kucing</option>
                    <option>Kelinci</option><option>Hamster</option>
                    <option>Burung</option><option>Lainnya</option>
                </select>
            </div>
            <div class="form-group">
                <label>Ras</label>
                <input type="text" id="petBreed" placeholder="Contoh: Golden Retriever">
            </div>
            <div class="form-group">
                <label>Usia (tahun)</label>
                <input type="number" id="petAge" min="0" step="0.1" placeholder="Contoh: 2.5">
            </div>
            <div class="form-group">
                <label>Berat (kg)</label>
                <input type="number" id="petWeight" min="0" step="0.1" placeholder="Contoh: 15.5">
            </div>
            <div class="form-group">
                <label>Catatan Kesehatan</label>
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

@push('scripts')
<script>
// ── Inline apiFetch: JWT auto-refresh ──────────────────────────────────────
async function apiFetch(url, opts) {
    opts = opts || {};
    var token = localStorage.getItem('vetra_token');
    var headers = Object.assign(
        { 'Accept': 'application/json', 'Authorization': 'Bearer ' + token },
        opts.headers || {}
    );
    var res = await fetch(url, Object.assign({}, opts, { headers: headers }));
    if (res.status !== 401) return res;

    // 401: try to refresh
    var rr = await fetch('/api/auth/refresh', {
        method: 'POST',
        headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
    });
    if (rr.ok) {
        var d = await rr.json();
        var newTok = d.access_token || d.token;
        if (newTok) {
            localStorage.setItem('vetra_token', newTok);
            headers['Authorization'] = 'Bearer ' + newTok;
            return fetch(url, Object.assign({}, opts, { headers: headers }));
        }
    }
    // Refresh failed
    localStorage.removeItem('vetra_token');
    localStorage.removeItem('vetra_user');
    window.location.href = '/login';
    return res;
}
// ── End apiFetch ──────────────────────────────────────────────────────────

var petsData   = [];
var editingId  = null;

var grid     = document.getElementById('petsGrid');
var alertBar = document.getElementById('alertBar');
var alertMsg = document.getElementById('alertMsg');

function showAlert(msg, type) {
    type = type || 'success';
    alertBar.className = 'alert-bar ' + type;
    alertMsg.textContent = msg;
    setTimeout(function() { alertBar.className = 'alert-bar'; }, 5000);
}

function setGrid(html) { grid.innerHTML = '<div class="state-box">' + html + '</div>'; }

function renderPets() {
    if (!petsData || petsData.length === 0) {
        grid.innerHTML = '<div class="state-box"><i class="fa-solid fa-paw" style="color:var(--gray-300);"></i><p>Belum ada hewan yang terdaftar.</p>'
            + '<button class="retry-btn" onclick="openAddModal()"><i class="fa-solid fa-plus"></i> Tambah Hewan Pertama</button></div>';
        return;
    }
    grid.innerHTML = petsData.map(function(pet) {
        var icon = pet.species === 'Anjing' ? 'fa-dog'
                 : pet.species === 'Kucing'  ? 'fa-cat'
                 : pet.species === 'Burung'  ? 'fa-dove'
                 : pet.species === 'Kelinci' ? 'fa-rabbit'
                 : 'fa-paw';
        var safeName = (pet.name || '').replace(/'/g, "\\'");
        return '<div class="pet-card">'
            + '<div class="pet-image">'
            +   (pet.photo ? '<img src="' + pet.photo + '" alt="' + pet.name + '">' : '<i class="fa-solid ' + icon + '"></i>')
            + '</div>'
            + '<div class="pet-body">'
            +   '<div class="pet-name">' + pet.name + '</div>'
            +   '<div class="pet-info"><i class="fa-solid fa-tag"></i><span>' + pet.species + (pet.breed ? ' · ' + pet.breed : '') + '</span></div>'
            +   (pet.age    ? '<div class="pet-info"><i class="fa-solid fa-calendar"></i><span>' + pet.age + ' tahun</span></div>' : '')
            +   (pet.weight ? '<div class="pet-info"><i class="fa-solid fa-weight-scale"></i><span>' + pet.weight + ' kg</span></div>' : '')
            +   (pet.notes  ? '<div class="pet-info"><i class="fa-solid fa-notes-medical"></i><span>' + pet.notes.substring(0,60) + (pet.notes.length > 60 ? '…' : '') + '</span></div>' : '')
            +   '<div class="pet-actions">'
            +     '<button class="btn-edit" onclick="editPet(' + pet.id + ')"><i class="fa-solid fa-pen"></i> Edit</button>'
            +     '<button class="btn-delete" onclick="deletePet(' + pet.id + ',\'' + safeName + '\')"><i class="fa-solid fa-trash"></i> Hapus</button>'
            +   '</div>'
            + '</div></div>';
    }).join('');
}

async function loadPets() {
    setGrid('<i class="fa-solid fa-spinner fa-spin" style="color:var(--teal);"></i><p>Memuat data hewan…</p>');
    try {
        var res = await apiFetch('/api/user/pets');
        if (res.ok) {
            var data = await res.json();
            petsData = data.pets || data || [];
            renderPets();
        } else {
            var err = {};
            try { err = await res.json(); } catch(e) {}
            setGrid('<i class="fa-solid fa-circle-exclamation" style="color:#dc2626;"></i><p>Gagal memuat: ' + (err.message || 'Error ' + res.status) + '</p>'
                + '<button class="retry-btn" onclick="loadPets()"><i class="fa-solid fa-rotate-right"></i> Coba Lagi</button>');
        }
    } catch(e) {
        console.error('loadPets error:', e);
        setGrid('<i class="fa-solid fa-wifi" style="color:#dc2626;"></i><p>Tidak dapat terhubung ke server.</p>'
            + '<button class="retry-btn" onclick="loadPets()"><i class="fa-solid fa-rotate-right"></i> Coba Lagi</button>');
    }
}

function openAddModal() {
    editingId = null;
    document.getElementById('modalTitle').textContent = 'Tambah Hewan Baru';
    document.getElementById('petForm').reset();
    document.getElementById('petId').value = '';
    document.getElementById('petModal').classList.add('open');
}

function editPet(id) {
    var pet = petsData.find(function(p) { return p.id === id; });
    if (!pet) return;
    editingId = id;
    document.getElementById('modalTitle').textContent  = 'Edit Data Hewan';
    document.getElementById('petId').value      = pet.id;
    document.getElementById('petName').value    = pet.name    || '';
    document.getElementById('petSpecies').value = pet.species || '';
    document.getElementById('petBreed').value   = pet.breed   || '';
    document.getElementById('petAge').value     = pet.age     || '';
    document.getElementById('petWeight').value  = pet.weight  || '';
    document.getElementById('petNotes').value   = pet.notes   || '';
    document.getElementById('petModal').classList.add('open');
}

function closeModal() {
    document.getElementById('petModal').classList.remove('open');
}

document.getElementById('petForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    var btn = document.getElementById('saveBtn');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan…';
    btn.disabled  = true;

    var payload = {
        name:    document.getElementById('petName').value,
        species: document.getElementById('petSpecies').value,
        breed:   document.getElementById('petBreed').value   || null,
        age:     document.getElementById('petAge').value     || null,
        weight:  document.getElementById('petWeight').value  || null,
        notes:   document.getElementById('petNotes').value   || null
    };

    try {
        var url    = editingId ? '/api/user/pets/' + editingId : '/api/user/pets';
        var method = editingId ? 'PUT' : 'POST';
        var res = await apiFetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        if (res.ok) {
            showAlert(editingId ? 'Data hewan berhasil diperbarui!' : 'Hewan baru berhasil ditambahkan!', 'success');
            closeModal();
            loadPets();
        } else {
            var err = {};
            try { err = await res.json(); } catch(x) {}
            showAlert('Gagal menyimpan: ' + (err.message || 'Error tidak diketahui'), 'error');
        }
    } catch(ex) {
        showAlert('Gagal terhubung ke server.', 'error');
    } finally {
        btn.innerHTML = '<i class="fa-solid fa-save"></i> Simpan';
        btn.disabled  = false;
    }
});

async function deletePet(id, name) {
    if (!confirm('Yakin ingin menghapus data "' + name + '"?')) return;
    try {
        var res = await apiFetch('/api/user/pets/' + id, { method: 'DELETE' });
        if (res.ok) { showAlert('Data hewan berhasil dihapus', 'success'); loadPets(); }
        else { showAlert('Gagal menghapus data hewan.', 'error'); }
    } catch(e) { showAlert('Gagal terhubung ke server.', 'error'); }
}

// Init — cek login lalu muat data
var _vetraToken = localStorage.getItem('vetra_token');
var _vetraUser  = localStorage.getItem('vetra_user');
if (!_vetraToken || !_vetraUser) {
    window.location.href = '/login';
} else {
    loadPets();
}
</script>
@endpush
@endsection
