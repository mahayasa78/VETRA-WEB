@extends('layouts.app')
@section('title', 'Booking Jadwal')

@push('styles')
<style>
    .booking-container { max-width: 800px; margin: 0 auto; padding: 40px 24px; }
    .booking-card {
        background: #fff; border-radius: 16px; border: 1px solid var(--gray-200);
        padding: 32px; margin-bottom: 24px; box-shadow: var(--shadow);
    }
    .step-indicator {
        display: flex; justify-content: space-between; margin-bottom: 32px;
        position: relative;
    }
    .step-indicator::before {
        content: ''; position: absolute; top: 20px; left: 0; right: 0; height: 2px;
        background: var(--gray-200); z-index: 0;
    }
    .step {
        flex: 1; text-align: center; position: relative; z-index: 1;
    }
    .step-circle {
        width: 40px; height: 40px; border-radius: 50%; background: var(--gray-200);
        display: inline-flex; align-items: center; justify-content: center;
        font-weight: 700; color: var(--gray-500); margin-bottom: 8px;
    }
    .step.active .step-circle { background: var(--teal); color: #fff; }
    .step.completed .step-circle { background: #10b981; color: #fff; }
    .step-label { font-size: 13px; font-weight: 600; color: var(--gray-500); }
    .step.active .step-label { color: var(--teal); }
    
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 14px; font-weight: 700; color: var(--gray-700); margin-bottom: 8px; }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%; padding: 12px 16px; border: 1.5px solid var(--gray-200);
        border-radius: 10px; font-size: 15px; font-family: inherit; transition: all .2s;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
        outline: none; border-color: var(--teal); box-shadow: 0 0 0 3px rgba(13,148,136,.1);
    }
    
    .pet-list { display: grid; gap: 12px; }
    .pet-item {
        padding: 16px; border: 2px solid var(--gray-200); border-radius: 12px;
        cursor: pointer; transition: all .2s; display: flex; align-items: center; gap: 12px;
    }
    .pet-item:hover { border-color: var(--teal-light); background: var(--teal-pale); }
    .pet-item.selected { border-color: var(--teal); background: var(--teal-pale); }
    .pet-icon {
        width: 48px; height: 48px; border-radius: 12px; background: var(--teal-pale);
        display: flex; align-items: center; justify-content: center; font-size: 24px; color: var(--teal);
    }
    .pet-info h4 { font-size: 16px; font-weight: 700; color: var(--gray-800); margin-bottom: 2px; }
    .pet-info p { font-size: 13px; color: var(--gray-500); }
    
    .doctor-list { display: grid; gap: 12px; }
    .doctor-item {
        padding: 16px; border: 2px solid var(--gray-200); border-radius: 12px;
        cursor: pointer; transition: all .2s; display: flex; align-items: center; gap: 12px;
    }
    .doctor-item:hover { border-color: var(--teal-light); background: var(--teal-pale); }
    .doctor-item.selected { border-color: var(--teal); background: var(--teal-pale); }
    .doctor-avatar {
        width: 56px; height: 56px; border-radius: 50%; background: var(--teal-pale);
        display: flex; align-items: center; justify-content: center; font-size: 24px; color: var(--teal);
    }
    .doctor-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    .doctor-info h4 { font-size: 16px; font-weight: 700; color: var(--gray-800); margin-bottom: 2px; }
    .doctor-info p { font-size: 13px; color: var(--gray-500); }
    .doctor-clinic { font-size: 12px; color: var(--teal); font-weight: 600; margin-top: 4px; }
    
    .btn-group { display: flex; gap: 12px; margin-top: 24px; }
    .btn-primary {
        background: var(--teal); color: #fff; padding: 13px 28px; border-radius: 12px;
        font-weight: 700; font-size: 15px; border: none; cursor: pointer; transition: all .2s;
        flex: 1;
    }
    .btn-primary:hover { background: var(--teal-dark); transform: translateY(-1px); }
    .btn-primary:disabled { background: var(--gray-300); cursor: not-allowed; transform: none; }
    .btn-secondary {
        background: var(--gray-100); color: var(--gray-700); padding: 13px 28px;
        border-radius: 12px; font-weight: 600; font-size: 15px; border: none; cursor: pointer;
    }
    
    .alert {
        padding: 14px 18px; border-radius: 12px; margin-bottom: 20px;
        display: none; align-items: center; gap: 10px;
    }
    .alert.success { background: #f0fdf4; border: 1px solid #86efac; color: #16a34a; }
    .alert.error { background: #fef2f2; border: 1px solid #fca5a5; color: #dc2626; }
    
    .empty-state { text-align: center; padding: 40px 20px; color: var(--gray-400); }
    .empty-state i { font-size: 48px; margin-bottom: 12px; display: block; }
    .empty-state p { margin-bottom: 16px; }
    
    .step-content { display: none; }
    .step-content.active { display: block; }
    
    .summary-row {
        display: flex; justify-content: space-between; padding: 12px 0;
        border-bottom: 1px solid var(--gray-100);
    }
    .summary-row:last-child { border-bottom: none; }
    .summary-label { font-size: 14px; color: var(--gray-600); }
    .summary-value { font-size: 14px; font-weight: 700; color: var(--gray-800); }
</style>
@endpush

@section('content')
<div style="padding-top:68px;background:var(--gray-50);min-height:100vh;">
    <div class="booking-container">
        <div style="margin-bottom:24px;">
            <h1 style="font-size:32px;font-weight:800;color:var(--gray-800);margin-bottom:8px;">📅 Booking Jadwal</h1>
            <p style="font-size:15px;color:var(--gray-500);">Buat janji temu dengan dokter hewan pilihan Anda</p>
        </div>

        <div class="alert success" id="successAlert">
            <i class="fa-solid fa-circle-check"></i>
            <span id="successMessage"></span>
        </div>

        <div class="alert error" id="errorAlert">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span id="errorMessage"></span>
        </div>

        <div class="booking-card">
            <div class="step-indicator">
                <div class="step active" id="step1Indicator">
                    <div class="step-circle">1</div>
                    <div class="step-label">Pilih Klinik</div>
                </div>
                <div class="step" id="step2Indicator">
                    <div class="step-circle">2</div>
                    <div class="step-label">Pilih Hewan</div>
                </div>
                <div class="step" id="step3Indicator">
                    <div class="step-circle">3</div>
                    <div class="step-label">Pilih Dokter</div>
                </div>
                <div class="step" id="step4Indicator">
                    <div class="step-circle">4</div>
                    <div class="step-label">Jadwal</div>
                </div>
                <div class="step" id="step5Indicator">
                    <div class="step-circle">5</div>
                    <div class="step-label">Konfirmasi</div>
                </div>
            </div>

            <!-- Step 1: Pilih Klinik -->
            <div class="step-content active" id="step1">
                <h3 style="font-size:18px;font-weight:800;margin-bottom:16px;">Pilih Klinik</h3>
                <div class="doctor-list" id="clinicList">
                    <!-- Populated by JS -->
                </div>
                <div class="btn-group">
                    <button class="btn-primary" onclick="nextStep(1)" id="step1Next" disabled>
                        Lanjut <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 2: Pilih Hewan -->
            <div class="step-content" id="step2">
                <h3 style="font-size:18px;font-weight:800;margin-bottom:16px;">Pilih Hewan Peliharaan (Opsional)</h3>
                <p style="font-size:13px;color:var(--gray-500);margin-bottom:16px;">Anda bisa melewati langkah ini jika belum memiliki data hewan</p>
                <div class="pet-list" id="petList">
                    <!-- Populated by JS -->
                </div>
                <div class="btn-group">
                    <button class="btn-secondary" onclick="prevStep(2)">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </button>
                    <button class="btn-primary" onclick="nextStep(2)" id="step2Next">
                        Lanjut <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 3: Pilih Dokter -->
            <div class="step-content" id="step3">
                <h3 style="font-size:18px;font-weight:800;margin-bottom:16px;">Pilih Dokter (Opsional)</h3>
                <p style="font-size:13px;color:var(--gray-500);margin-bottom:16px;">Anda bisa melewati langkah ini, klinik akan menentukan dokter yang tersedia</p>
                <div class="doctor-list" id="doctorList">
                    <!-- Populated by JS -->
                </div>
                <div class="btn-group">
                    <button class="btn-secondary" onclick="prevStep(3)">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </button>
                    <button class="btn-primary" onclick="nextStep(3)" id="step3Next">
                        Lanjut <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 4: Pilih Jadwal -->
            <div class="step-content" id="step4">
                <h3 style="font-size:18px;font-weight:800;margin-bottom:16px;">Pilih Tanggal & Waktu</h3>
                <div class="form-group">
                    <label for="bookingDate">Tanggal</label>
                    <input type="date" id="bookingDate" required>
                </div>
                <div class="form-group">
                    <label for="bookingTime">Waktu</label>
                    <input type="time" id="bookingTime" required>
                </div>
                <div class="form-group">
                    <label for="notes">Catatan (opsional)</label>
                    <textarea id="notes" rows="3" placeholder="Jelaskan keluhan atau kondisi hewan Anda..."></textarea>
                </div>
                <div class="btn-group">
                    <button class="btn-secondary" onclick="prevStep(4)">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </button>
                    <button class="btn-primary" onclick="nextStep(4)" id="step4Next">
                        Lanjut <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <!-- Step 5: Konfirmasi -->
            <div class="step-content" id="step5">
                <h3 style="font-size:18px;font-weight:800;margin-bottom:16px;">Konfirmasi Booking</h3>
                <div id="bookingSummary">
                    <!-- Populated by JS -->
                </div>
                <div class="btn-group">
                    <button class="btn-secondary" onclick="prevStep(5)">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </button>
                    <button class="btn-primary" onclick="submitBooking()" id="submitBtn">
                        <i class="fa-solid fa-check"></i> Konfirmasi Booking
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Debug mode
    const DEBUG = true;
    function debug(...args) {
        if (DEBUG) console.log('[BOOKING DEBUG]', ...args);
    }

    const token = localStorage.getItem('vetra_token');
    const user = JSON.parse(localStorage.getItem('vetra_user') || 'null');

    debug('Token:', token ? 'exists' : 'missing');
    debug('User:', user);

    if (!token || !user) {
        debug('No auth, redirecting to login');
        window.location.href = '/login';
    }

    let currentStep = 1;
    let selectedClinic = null;
    let selectedPet = null;
    let selectedDoctor = null;
    let clinics = [];
    let pets = [];
    let doctors = [];

    // Get URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const preselectedClinicId = urlParams.get('clinic_id');
    const preselectedDoctorId = urlParams.get('doctor_id');
    debug('URL Params:', { preselectedClinicId, preselectedDoctorId });

    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('bookingDate').setAttribute('min', today);

    // Load clinics
    async function loadClinics() {
        debug('Loading clinics...');
        try {
            const res = await fetch('/api/clinics', {
                headers: { 'Accept': 'application/json' }
            });

            debug('Clinics response status:', res.status);

            if (res.ok) {
                const data = await res.json();
                debug('Clinics data:', data);
                
                // Laravel pagination returns { data: [...], current_page, ... }
                clinics = data.data || data || [];
                debug('Clinics array length:', clinics.length);
                
                renderClinics();
                
                // Auto-select clinic if preselected
                if (preselectedClinicId) {
                    const preselected = clinics.find(c => c.id == preselectedClinicId);
                    if (preselected) {
                        debug('Auto-selecting clinic:', preselected);
                        selectedClinic = preselected;
                        // Auto-enable next button and mark as selected
                        setTimeout(() => {
                            const clinicCard = document.querySelector(`[data-clinic-id="${preselectedClinicId}"]`);
                            if (clinicCard) {
                                clinicCard.classList.add('selected');
                                document.getElementById('step1Next').disabled = false;
                            }
                        }, 100);
                    }
                }
            } else {
                const errorText = await res.text();
                debug('Failed to load clinics:', res.status, errorText);
                showError('Gagal memuat daftar klinik');
            }
        } catch (error) {
            debug('Error loading clinics:', error);
            showError('Gagal terhubung ke server');
        }
    }

    // Render clinics
    function renderClinics() {
        const container = document.getElementById('clinicList');
        
        if (clinics.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-hospital"></i>
                    <p>Tidak ada klinik tersedia</p>
                </div>`;
            return;
        }

        container.innerHTML = clinics.map(clinic => {
            const profile = clinic.clinic_profile || {};
            const avatarContent = clinic.profile_pic 
                ? `<img src="${clinic.profile_pic.startsWith('http') ? clinic.profile_pic : '/storage/' + clinic.profile_pic}" alt="${clinic.name}">`
                : '<i class="fa-solid fa-hospital"></i>';
            
            return `
                <div class="doctor-item" data-clinic-id="${clinic.id}" onclick="selectClinic(${clinic.id})">
                    <div class="doctor-avatar">${avatarContent}</div>
                    <div class="doctor-info">
                        <h4>${clinic.name}</h4>
                        <p>${profile.address || '-'}</p>
                        ${profile.phone ? `<div class="doctor-clinic"><i class="fa-solid fa-phone"></i> ${profile.phone}</div>` : ''}
                    </div>
                </div>`;
        }).join('');
    }

    // Select clinic
    function selectClinic(clinicId) {
        selectedClinic = clinics.find(c => c.id === clinicId);
        document.querySelectorAll('.doctor-item').forEach(el => el.classList.remove('selected'));
        event.target.closest('.doctor-item').classList.add('selected');
        document.getElementById('step1Next').disabled = false;
    }

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
                pets = data.pets || [];
                renderPets();
            }
        } catch (error) {
            console.error('Error loading pets:', error);
        }
    }

    // Render pets
    function renderPets() {
        const container = document.getElementById('petList');
        
        if (pets.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-paw"></i>
                    <p>Anda belum memiliki data hewan peliharaan</p>
                    <p style="font-size:12px;margin-top:8px;">Anda bisa melewati langkah ini</p>
                </div>`;
            return;
        }

        container.innerHTML = pets.map(pet => `
            <div class="pet-item" onclick="selectPet(${pet.id})">
                <div class="pet-icon">
                    <i class="fa-solid fa-${pet.species === 'dog' ? 'dog' : pet.species === 'cat' ? 'cat' : 'paw'}"></i>
                </div>
                <div class="pet-info">
                    <h4>${pet.name}</h4>
                    <p>${pet.species} • ${pet.breed || 'Mix'} • ${pet.age || 0} tahun</p>
                </div>
            </div>`).join('');
    }

    // Select pet (optional)
    function selectPet(petId) {
        if (selectedPet && selectedPet.id === petId) {
            // Deselect
            selectedPet = null;
            document.querySelectorAll('.pet-item').forEach(el => el.classList.remove('selected'));
        } else {
            selectedPet = pets.find(p => p.id === petId);
            document.querySelectorAll('.pet-item').forEach(el => el.classList.remove('selected'));
            event.target.closest('.pet-item').classList.add('selected');
        }
    }

    // Load doctors from selected clinic
    async function loadDoctors() {
        if (!selectedClinic) {
            document.getElementById('doctorList').innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    <p>Pilih klinik terlebih dahulu</p>
                </div>`;
            return;
        }

        try {
            const res = await fetch(`/api/clinics/${selectedClinic.id}/doctors`, {
                headers: { 'Accept': 'application/json' }
            });

            if (res.ok) {
                const data = await res.json();
                doctors = data.doctors || [];
                renderDoctors();
                
                // Auto-select doctor if preselected and belongs to selected clinic
                if (preselectedDoctorId) {
                    const preselected = doctors.find(d => d.user_id == preselectedDoctorId);
                    if (preselected) {
                        selectedDoctor = preselected;
                        setTimeout(() => {
                            const doctorCard = document.querySelector(`[data-doctor-id="${preselectedDoctorId}"]`);
                            if (doctorCard) {
                                doctorCard.classList.add('selected');
                            }
                        }, 100);
                    }
                }
            }
        } catch (error) {
            console.error('Error loading doctors:', error);
        }
    }

    // Render doctors
    function renderDoctors() {
        const container = document.getElementById('doctorList');
        
        if (doctors.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-user-doctor"></i>
                    <p>Belum ada dokter di klinik ini</p>
                    <p style="font-size:12px;margin-top:8px;">Anda bisa melewati langkah ini</p>
                </div>`;
            return;
        }

        container.innerHTML = doctors.map(doctor => {
            const user = doctor.user || {};
            const avatarContent = user.profile_pic 
                ? `<img src="${user.profile_pic.startsWith('http') ? user.profile_pic : '/storage/' + user.profile_pic}" alt="${user.name}">`
                : '<i class="fa-solid fa-user-doctor"></i>';
            
            return `
                <div class="doctor-item" data-doctor-id="${doctor.user_id}" onclick="selectDoctor(${doctor.id})">
                    <div class="doctor-avatar">${avatarContent}</div>
                    <div class="doctor-info">
                        <h4>drh. ${user.name}</h4>
                        <p>${doctor.spesialis || 'Dokter Hewan Umum'}</p>
                    </div>
                </div>`;
        }).join('');
    }

    // Select doctor (optional)
    function selectDoctor(doctorId) {
        if (selectedDoctor && selectedDoctor.id === doctorId) {
            // Deselect
            selectedDoctor = null;
            document.querySelectorAll('.doctor-item').forEach(el => el.classList.remove('selected'));
        } else {
            selectedDoctor = doctors.find(d => d.id === doctorId);
            document.querySelectorAll('.doctor-item').forEach(el => el.classList.remove('selected'));
            event.target.closest('.doctor-item').classList.add('selected');
        }
    }

    // Navigation
    function nextStep(current) {
        if (current === 1) {
            // Step 1: Load pets after clinic selected
            if (pets.length === 0) {
                loadPets();
            }
        } else if (current === 2) {
            // Step 2: Load doctors after pet step (optional)
            // Always load doctors from the selected clinic
            loadDoctors();
        } else if (current === 3) {
            // Step 3: Doctor selection done (optional)
            // Continue to schedule step
        } else if (current === 4) {
            // Step 4: Validate date and time
            const date = document.getElementById('bookingDate').value;
            const time = document.getElementById('bookingTime').value;
            if (!date || !time) {
                showError('Tanggal dan waktu harus diisi');
                return;
            }
            renderSummary();
        }

        document.getElementById(`step${current}`).classList.remove('active');
        document.getElementById(`step${current}Indicator`).classList.add('completed');
        
        currentStep = current + 1;
        document.getElementById(`step${currentStep}`).classList.add('active');
        document.getElementById(`step${currentStep}Indicator`).classList.add('active');
    }

    function prevStep(current) {
        document.getElementById(`step${current}`).classList.remove('active');
        document.getElementById(`step${current}Indicator`).classList.remove('active');
        
        currentStep = current - 1;
        document.getElementById(`step${currentStep}`).classList.add('active');
        document.getElementById(`step${currentStep}Indicator`).classList.remove('completed');
    }

    // Render summary
    function renderSummary() {
        const date = document.getElementById('bookingDate').value;
        const time = document.getElementById('bookingTime').value;
        const notes = document.getElementById('notes').value;

        const dateFormatted = new Date(date).toLocaleDateString('id-ID', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });

        let summaryHTML = `
            <div class="summary-row">
                <span class="summary-label">Klinik</span>
                <span class="summary-value">${selectedClinic.name}</span>
            </div>`;

        if (selectedPet) {
            summaryHTML += `
            <div class="summary-row">
                <span class="summary-label">Hewan</span>
                <span class="summary-value">${selectedPet.name} (${selectedPet.species})</span>
            </div>`;
        }

        if (selectedDoctor) {
            summaryHTML += `
            <div class="summary-row">
                <span class="summary-label">Dokter</span>
                <span class="summary-value">drh. ${selectedDoctor.user.name}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Spesialisasi</span>
                <span class="summary-value">${selectedDoctor.spesialis || 'Umum'}</span>
            </div>`;
        } else {
            summaryHTML += `
            <div class="summary-row">
                <span class="summary-label">Dokter</span>
                <span class="summary-value">Akan ditentukan oleh klinik</span>
            </div>`;
        }

        summaryHTML += `
            <div class="summary-row">
                <span class="summary-label">Tanggal</span>
                <span class="summary-value">${dateFormatted}</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Waktu</span>
                <span class="summary-value">${time}</span>
            </div>`;

        if (notes) {
            summaryHTML += `
            <div class="summary-row">
                <span class="summary-label">Catatan</span>
                <span class="summary-value">${notes}</span>
            </div>`;
        }

        document.getElementById('bookingSummary').innerHTML = summaryHTML;
    }

    // Submit booking
    async function submitBooking() {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
        btn.disabled = true;

        const date = document.getElementById('bookingDate').value;
        const time = document.getElementById('bookingTime').value;
        const notes = document.getElementById('notes').value;

        const payload = {
            clinic_id: selectedClinic.id,
            scheduled_at: `${date} ${time}`,
            notes: notes
        };

        // Add optional fields
        if (selectedPet) {
            payload.pet_id = selectedPet.id;
        }
        
        if (selectedDoctor) {
            payload.doctor_id = selectedDoctor.user_id;
        }

        try {
            const res = await fetch('/api/bookings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (res.ok) {
                showSuccess('Booking berhasil dibuat! Mengarahkan ke halaman booking...');
                setTimeout(() => {
                    window.location.href = '/my-bookings';
                }, 2000);
            } else {
                showError(data.message || 'Gagal membuat booking');
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Konfirmasi Booking';
                btn.disabled = false;
            }
        } catch (error) {
            console.error('Error:', error);
            showError('Gagal terhubung ke server');
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Konfirmasi Booking';
            btn.disabled = false;
        }
    }

    function showSuccess(message) {
        const alert = document.getElementById('successAlert');
        document.getElementById('successMessage').textContent = message;
        alert.style.display = 'flex';
        window.scrollTo(0, 0);
    }

    function showError(message) {
        const alert = document.getElementById('errorAlert');
        document.getElementById('errorMessage').textContent = message;
        alert.style.display = 'flex';
        setTimeout(() => alert.style.display = 'none', 5000);
        window.scrollTo(0, 0);
    }

    // Load clinics on page load
    loadClinics();
</script>
@endsection
