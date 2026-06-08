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
    .btn-edit { background: var(--teal-pale); color: var(--teal-dark); }
    .btn-edit:hover { background: var(--teal); color: #fff; }
    .btn-delete { background: #fee2e2; color: #dc2626; }
    .btn-delete:hover { background: #dc2626; color: #fff; }
    
    .empty-state {
        text-align: center; padding: 80px 24px; background: #fff;
        border-radius: 16px; border: 1px solid var(--gray-200);
    }
    .empty-state i { font-size: 64px; color: var(--gray-300); margin-bottom: 16px; }
    .empty-state p { font-size: 16px; color: var(--gray-500); margin-bottom: 24px; }
    
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
    <div class="pets-container">
        <div class="alert success" id="successAlert">
            <i class="fa-solid fa-circle-check"></i>
            <span id="successMessage"></span>
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
            <div style="grid-column:1/-1;text-align:center;padding:60px;">
                <i class="fa-solid fa-spinner fa-spin" style="font-size:48px;color:var(--teal);margin-bottom:16px;"></i>
                <p style="color:var(--gray-500);">Memuat data hewan...</p>
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
                <label for="name">Nama Hewan</label>
                <input type="text" id="name" required placeholder="Contoh: Milo">
            </div>
            <div class="form-group">
                <label for="species">Jenis Hewan</label>
                <select id="species" required>
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
                <label for="breed">Ras</label>
                <input type="text" id="breed" placeholder="Contoh: Golden Retriever">
            </div>
            <div class="form-group">
                <label for="age">Usia (tahun)</label>
                <input type="number" id="age" min="0" step="0.1" placeholder="Contoh: 2.5">
            </div>
            <div class="form-group">
                <label for="weight">Berat (kg)</label>
                <input type="number" id="weight" min="0" step="0.1" placeholder="Contoh: 15.5">
            </div>
            <div class="form-group">
                <label for="notes">Catatan Kesehatan</label>
                <textarea id="notes" rows="3" placeholder="Catatan khusus tentang kesehatan hewan..."></textarea>
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
    const token = localStorage.getItem('vetra_token');
    const user = JSON.parse(localStorage.getItem('vetra_user'));

    if (!token || !user) {
        window.location.href = '/login';
    }

    let pets = [];
    let editingId = null;

    // Load pets
    async function loadPets() {
        try {
            const res = await fetch('/api/user/pets', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                const data = await res.json();
                console.log('API Response:', data); // Debug
                // Controller returns {pets: [...]} not just [...]
                pets = data.pets || data || [];
                console.log('Pets array:', pets); // Debug
                renderPets();
            } else {
                console.error('Failed to load pets:', res.status);
                const errorData = await res.json();
                console.error('Error details:', errorData);
            }
        } catch (error) {
            console.error('Error loading pets:', error);
        }
    }

    // Render pets grid
    function renderPets() {
        const grid = document.getElementById('petsGrid');
        
        console.log('Rendering pets, count:', pets.length); // Debug
        
        if (!pets || pets.length === 0) {
            grid.innerHTML = `
                <div class="empty-state" style="grid-column:1/-1;">
                    <i class="fa-solid fa-paw"></i>
                    <p>Belum ada hewan yang terdaftar</p>
                    <button class="btn-add-pet" onclick="openAddModal()">
                        <i class="fa-solid fa-plus"></i> Tambah Hewan Pertama
                    </button>
                </div>`;
            return;
        }

        grid.innerHTML = pets.map(pet => {
            console.log('Rendering pet:', pet); // Debug
            const icon = pet.species === 'Anjing' ? 'fa-dog' : 
                        pet.species === 'Kucing' ? 'fa-cat' :
                        pet.species === 'Burung' ? 'fa-dove' :
                        pet.species === 'Kelinci' ? 'fa-rabbit' : 'fa-paw';
            
            return `
                <div class="pet-card">
                    <div class="pet-image">
                        <i class="fa-solid ${icon}"></i>
                    </div>
                    <div class="pet-body">
                        <div class="pet-name">${pet.name}</div>
                        <div class="pet-info">
                            <i class="fa-solid fa-tag"></i>
                            <span>${pet.species}${pet.breed ? ' - ' + pet.breed : ''}</span>
                        </div>
                        ${pet.age ? `<div class="pet-info"><i class="fa-solid fa-calendar"></i><span>${pet.age} tahun</span></div>` : ''}
                        ${pet.weight ? `<div class="pet-info"><i class="fa-solid fa-weight-scale"></i><span>${pet.weight} kg</span></div>` : ''}
                        ${pet.notes ? `<div class="pet-info"><i class="fa-solid fa-notes-medical"></i><span>${pet.notes.substring(0, 50)}${pet.notes.length > 50 ? '...' : ''}</span></div>` : ''}
                        <div class="pet-actions">
                            <button class="btn-edit" onclick="editPet(${pet.id})">
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>
                            <button class="btn-delete" onclick="deletePet(${pet.id}, '${pet.name.replace(/'/g, "\\'")}')">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>`;
        }).join('');
    }

    // Open add modal
    function openAddModal() {
        editingId = null;
        document.getElementById('modalTitle').textContent = 'Tambah Hewan Baru';
        document.getElementById('petForm').reset();
        document.getElementById('petId').value = '';
        document.getElementById('petModal').classList.add('open');
    }

    // Edit pet
    function editPet(id) {
        const pet = pets.find(p => p.id === id);
        if (!pet) return;

        editingId = id;
        document.getElementById('modalTitle').textContent = 'Edit Data Hewan';
        document.getElementById('petId').value = pet.id;
        document.getElementById('name').value = pet.name;
        document.getElementById('species').value = pet.species;
        document.getElementById('breed').value = pet.breed || '';
        document.getElementById('age').value = pet.age || '';
        document.getElementById('weight').value = pet.weight || '';
        document.getElementById('notes').value = pet.notes || '';
        document.getElementById('petModal').classList.add('open');
    }

    // Close modal
    function closeModal() {
        document.getElementById('petModal').classList.remove('open');
    }

    // Save pet
    document.getElementById('petForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('saveBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
        btn.disabled = true;

        const data = {
            name: document.getElementById('name').value,
            species: document.getElementById('species').value,
            breed: document.getElementById('breed').value || null,
            age: document.getElementById('age').value || null,
            weight: document.getElementById('weight').value || null,
            notes: document.getElementById('notes').value || null
        };

        try {
            const url = editingId ? `/api/user/pets/${editingId}` : '/api/user/pets';
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
                showSuccess(editingId ? 'Data hewan berhasil diperbarui!' : 'Hewan baru berhasil ditambahkan!');
                closeModal();
                loadPets();
            } else {
                const error = await res.json();
                alert('Gagal menyimpan: ' + (error.message || 'Error'));
            }
        } catch (error) {
            alert('Gagal terhubung ke server');
        } finally {
            btn.innerHTML = '<i class="fa-solid fa-save"></i> Simpan';
            btn.disabled = false;
        }
    });

    // Delete pet
    async function deletePet(id, name) {
        if (!confirm(`Yakin ingin menghapus data "${name}"?`)) return;

        try {
            const res = await fetch(`/api/user/pets/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                showSuccess('Data hewan berhasil dihapus');
                loadPets();
            } else {
                alert('Gagal menghapus data');
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

    // Load pets on page load
    loadPets();
</script>
@endsection
