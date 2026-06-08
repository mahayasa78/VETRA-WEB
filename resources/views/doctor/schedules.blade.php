@extends('layouts.app')
@section('title', 'Jadwal Pasien')

@push('styles')
<style>
    .schedules-container { max-width: 1200px; margin: 0 auto; padding: 40px 24px; }
    
    .page-header { 
        display: flex; justify-content: space-between; align-items: center; 
        margin-bottom: 32px; flex-wrap: wrap; gap: 16px;
    }
    .page-header h1 { font-size: 32px; font-weight: 800; color: var(--gray-800); }
    
    .filter-tabs {
        display: flex; gap: 8px; background: #fff; padding: 6px; border-radius: 12px;
        border: 1px solid var(--gray-200);
    }
    .filter-tab {
        padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600;
        border: none; background: transparent; color: var(--gray-600); cursor: pointer;
        transition: all .2s;
    }
    .filter-tab:hover { background: var(--gray-100); color: var(--gray-800); }
    .filter-tab.active { background: var(--teal); color: #fff; }
    
    .bookings-list { display: flex; flex-direction: column; gap: 16px; }
    .booking-card {
        background: #fff; border-radius: 16px; border: 1px solid var(--gray-200);
        box-shadow: var(--shadow); overflow: hidden; transition: all .3s;
    }
    .booking-card:hover { box-shadow: var(--shadow-lg); border-color: var(--teal); }
    
    .booking-header {
        padding: 20px 24px; display: flex; justify-content: space-between; 
        align-items: start; gap: 16px; border-bottom: 1px solid var(--gray-100);
    }
    .booking-patient-info { display: flex; gap: 16px; align-items: start; flex: 1; }
    .patient-avatar {
        width: 60px; height: 60px; border-radius: 12px; background: var(--teal-pale);
        display: flex; align-items: center; justify-content: center; font-size: 28px;
        color: var(--teal); flex-shrink: 0;
    }
    .patient-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 12px; }
    
    .patient-details { flex: 1; }
    .patient-name { font-size: 18px; font-weight: 700; color: var(--gray-800); margin-bottom: 4px; }
    .patient-contact {
        font-size: 13px; color: var(--gray-500); display: flex; align-items: center; 
        gap: 12px; flex-wrap: wrap; margin-bottom: 8px;
    }
    .pet-badge {
        display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px;
        background: var(--teal-pale); color: var(--teal-dark); border-radius: 8px;
        font-size: 13px; font-weight: 600;
    }
    
    .status-badge {
        padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600;
        white-space: nowrap;
    }
    .status-badge.pending { background: #fef3c7; color: #f59e0b; }
    .status-badge.confirmed { background: #dbeafe; color: #3b82f6; }
    .status-badge.rejected { background: #fee2e2; color: #dc2626; }
    .status-badge.done { background: #d1fae5; color: #10b981; }
    
    .booking-body { padding: 20px 24px; }
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px; }
    .info-item { display: flex; align-items: center; gap: 10px; }
    .info-icon {
        width: 36px; height: 36px; border-radius: 8px; display: flex;
        align-items: center; justify-content: center; background: var(--gray-100);
        color: var(--teal); font-size: 16px; flex-shrink: 0;
    }
    .info-content { flex: 1; }
    .info-label { font-size: 12px; color: var(--gray-500); margin-bottom: 2px; }
    .info-value { font-size: 14px; font-weight: 600; color: var(--gray-800); }
    
    .complaint-box {
        background: var(--gray-50); border-radius: 12px; padding: 16px; margin-top: 12px;
        border-left: 3px solid var(--teal);
    }
    .complaint-label { font-size: 12px; font-weight: 700; color: var(--gray-600); margin-bottom: 6px; }
    .complaint-text { font-size: 14px; color: var(--gray-700); line-height: 1.5; }
    
    .notes-box {
        background: #fef3c7; border-radius: 12px; padding: 16px; margin-top: 12px;
        border-left: 3px solid #f59e0b;
    }
    .notes-label { font-size: 12px; font-weight: 700; color: #92400e; margin-bottom: 6px; }
    .notes-text { font-size: 14px; color: #78350f; line-height: 1.5; }
    
    .booking-actions {
        display: flex; gap: 10px; padding: 16px 24px; background: var(--gray-50);
        border-top: 1px solid var(--gray-100);
    }
    .btn-action {
        flex: 1; padding: 11px; border-radius: 10px; font-size: 14px; font-weight: 600;
        border: none; cursor: pointer; transition: all .2s; display: flex;
        align-items: center; justify-content: center; gap: 6px;
    }
    .btn-confirm { background: var(--teal); color: #fff; }
    .btn-confirm:hover { background: var(--teal-dark); transform: translateY(-1px); }
    .btn-reject { background: #fee2e2; color: #dc2626; }
    .btn-reject:hover { background: #dc2626; color: #fff; transform: translateY(-1px); }
    .btn-done { background: #d1fae5; color: #10b981; }
    .btn-done:hover { background: #10b981; color: #fff; transform: translateY(-1px); }
    .btn-notes { background: #e9d5ff; color: #a855f7; }
    .btn-notes:hover { background: #a855f7; color: #fff; transform: translateY(-1px); }
    
    .empty-state {
        text-align: center; padding: 80px 24px; background: #fff;
        border-radius: 16px; border: 1px solid var(--gray-200);
    }
    .empty-state i { font-size: 64px; color: var(--gray-300); margin-bottom: 16px; }
    .empty-state p { font-size: 16px; color: var(--gray-500); }
    
    .loading-spinner { text-align: center; padding: 60px; }
    .loading-spinner i { font-size: 48px; color: var(--teal); }
    
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
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-header h3 { font-size: 20px; font-weight: 800; color: var(--gray-800); }
    .modal-close {
        width: 32px; height: 32px; border-radius: 50%; background: var(--gray-100);
        border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
        color: var(--gray-600); transition: all .2s;
    }
    .modal-close:hover { background: var(--gray-200); }
    
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--gray-700); margin-bottom: 8px; }
    .form-group textarea {
        width: 100%; padding: 11px 14px; border: 1.5px solid var(--gray-200);
        border-radius: 10px; font-size: 14px; font-family: inherit; transition: all .2s;
        resize: vertical; min-height: 100px;
    }
    .form-group textarea:focus {
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
    <div class="schedules-container">
        <div class="alert success" id="successAlert">
            <i class="fa-solid fa-circle-check"></i>
            <span id="successMessage"></span>
        </div>

        <div class="page-header">
            <h1>📅 Jadwal Pasien</h1>
            <div class="filter-tabs">
                <button class="filter-tab active" onclick="filterBookings('all')">Semua</button>
                <button class="filter-tab" onclick="filterBookings('pending')">Menunggu</button>
                <button class="filter-tab" onclick="filterBookings('confirmed')">Terkonfirmasi</button>
                <button class="filter-tab" onclick="filterBookings('done')">Selesai</button>
            </div>
        </div>

        <div id="bookingsList">
            <div class="loading-spinner">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <p style="margin-top:16px;color:var(--gray-500);">Memuat jadwal pasien...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Notes -->
<div class="modal-overlay" id="notesModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>📝 Catatan Dokter</h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="notesForm">
            <input type="hidden" id="bookingId">
            <input type="hidden" id="actionStatus">
            <div class="form-group">
                <label for="doctorNotes">Catatan (Opsional)</label>
                <textarea id="doctorNotes" placeholder="Masukkan catatan untuk pasien..."></textarea>
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

    if (!token || !user || user.role !== 'doctor') {
        window.location.href = '/login';
    }

    let bookings = [];
    let currentFilter = 'all';

    // Load bookings
    async function loadBookings(status = null) {
        try {
            const url = status ? `/api/doctor/bookings?status=${status}` : '/api/doctor/bookings';
            const res = await fetch(url, {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                const data = await res.json();
                console.log('Bookings data:', data);
                bookings = data.data || data;
                renderBookings();
            } else {
                console.error('Failed to load bookings:', res.status);
                showError();
            }
        } catch (error) {
            console.error('Error loading bookings:', error);
            showError();
        }
    }

    // Render bookings
    function renderBookings() {
        const container = document.getElementById('bookingsList');
        
        if (!bookings || bookings.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-calendar-xmark"></i>
                    <p>Tidak ada jadwal untuk filter ini</p>
                </div>`;
            return;
        }

        try {
            container.innerHTML = `
                <div class="bookings-list">
                    ${bookings.map(booking => {
                        try {
                            return renderBookingCard(booking);
                        } catch (error) {
                            console.error('Error rendering booking card:', booking, error);
                            return `<div class="booking-card" style="background:#fee2e2;padding:20px;border:1px solid #dc2626;">
                                <p style="color:#dc2626;">Error menampilkan booking ID: ${booking.id}</p>
                            </div>`;
                        }
                    }).join('')}
                </div>`;
        } catch (error) {
            console.error('Error in renderBookings:', error);
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    <p>Terjadi kesalahan saat menampilkan data</p>
                </div>`;
        }
    }

    function renderBookingCard(booking) {
        const statusMap = {
            pending: 'Menunggu',
            confirmed: 'Terkonfirmasi',
            rejected: 'Ditolak',
            done: 'Selesai'
        };
        
        const date = new Date(booking.scheduled_at || booking.booking_date);
        const dateStr = date.toLocaleDateString('id-ID', { 
            weekday: 'long',
            day: 'numeric', 
            month: 'long', 
            year: 'numeric' 
        });
        const timeStr = booking.booking_time || date.toLocaleTimeString('id-ID', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        
        // Handle missing relationships with fallback
        const user = booking.user || { name: 'Pasien', phone: null, profile_pic: null };
        const pet = booking.pet || { name: 'Hewan Peliharaan', species: '-' };
        const clinic = booking.clinic || { name: 'Klinik' };
        
        let actions = '';
        if (booking.status === 'pending') {
            actions = `
                <div class="booking-actions">
                    <button class="btn-action btn-confirm" onclick="confirmBooking(${booking.id})">
                        <i class="fa-solid fa-check"></i> Terima
                    </button>
                    <button class="btn-action btn-reject" onclick="rejectBooking(${booking.id})">
                        <i class="fa-solid fa-times"></i> Tolak
                    </button>
                </div>`;
        } else if (booking.status === 'confirmed') {
            actions = `
                <div class="booking-actions">
                    <button class="btn-action btn-done" onclick="markAsDone(${booking.id})">
                        <i class="fa-solid fa-check-double"></i> Tandai Selesai
                    </button>
                    <button class="btn-action btn-notes" onclick="addNotes(${booking.id})">
                        <i class="fa-solid fa-note-medical"></i> Tambah Catatan
                    </button>
                </div>`;
        }

        return `
            <div class="booking-card">
                <div class="booking-header">
                    <div class="booking-patient-info">
                        <div class="patient-avatar">
                            ${user.profile_pic 
                                ? `<img src="${user.profile_pic}" alt="${user.name}">` 
                                : '<i class="fa-solid fa-user"></i>'}
                        </div>
                        <div class="patient-details">
                            <div class="patient-name">${user.name}</div>
                            <div class="patient-contact">
                                ${user.phone ? `<span><i class="fa-solid fa-phone"></i> ${user.phone}</span>` : '<span><i class="fa-solid fa-phone"></i> -</span>'}
                            </div>
                            <div class="pet-badge">
                                <i class="fa-solid fa-paw"></i>
                                ${pet.name} ${pet.species !== '-' ? `(${pet.species})` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="status-badge ${booking.status}">
                        ${statusMap[booking.status]}
                    </div>
                </div>
                
                <div class="booking-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-regular fa-calendar"></i></div>
                            <div class="info-content">
                                <div class="info-label">Tanggal</div>
                                <div class="info-value">${dateStr}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-regular fa-clock"></i></div>
                            <div class="info-content">
                                <div class="info-label">Waktu</div>
                                <div class="info-value">${timeStr}</div>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><i class="fa-solid fa-hospital"></i></div>
                            <div class="info-content">
                                <div class="info-label">Klinik</div>
                                <div class="info-value">${clinic.name}</div>
                            </div>
                        </div>
                    </div>
                    
                    ${booking.complaint ? `
                    <div class="complaint-box">
                        <div class="complaint-label">💬 KELUHAN PASIEN</div>
                        <div class="complaint-text">${booking.complaint}</div>
                    </div>` : ''}
                    
                    ${booking.doctor_notes ? `
                    <div class="notes-box">
                        <div class="notes-label">📝 CATATAN DOKTER</div>
                        <div class="notes-text">${booking.doctor_notes}</div>
                    </div>` : ''}
                </div>
                
                ${actions}
            </div>`;
    }

    // Filter bookings
    function filterBookings(status) {
        currentFilter = status;
        
        // Update active tab
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.classList.remove('active');
        });
        event.target.classList.add('active');
        
        // Load bookings with filter
        loadBookings(status === 'all' ? null : status);
    }

    // Confirm booking
    function confirmBooking(id) {
        openNotesModal(id, 'confirmed', 'Terima Booking');
    }

    // Reject booking
    function rejectBooking(id) {
        if (!confirm('Yakin ingin menolak booking ini?')) return;
        openNotesModal(id, 'rejected', 'Tolak Booking');
    }

    // Mark as done
    function markAsDone(id) {
        openNotesModal(id, 'done', 'Selesaikan Konsultasi');
    }

    // Add notes
    function addNotes(id) {
        openNotesModal(id, 'confirmed', 'Tambah Catatan');
    }

    // Open notes modal
    function openNotesModal(id, status, title) {
        document.getElementById('bookingId').value = id;
        document.getElementById('actionStatus').value = status;
        document.getElementById('notesModal').querySelector('h3').textContent = title;
        document.getElementById('doctorNotes').value = '';
        document.getElementById('notesModal').classList.add('open');
    }

    // Close modal
    function closeModal() {
        document.getElementById('notesModal').classList.remove('open');
    }

    // Submit notes form
    document.getElementById('notesForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const bookingId = document.getElementById('bookingId').value;
        const status = document.getElementById('actionStatus').value;
        const notes = document.getElementById('doctorNotes').value;
        const btn = document.getElementById('submitBtn');
        
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
        btn.disabled = true;

        try {
            const res = await fetch(`/api/doctor/bookings/${bookingId}/status`, {
                method: 'PATCH',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    status: status,
                    doctor_notes: notes || null
                })
            });

            if (res.ok) {
                const data = await res.json();
                showSuccess(data.message || 'Status booking berhasil diperbarui');
                closeModal();
                loadBookings(currentFilter === 'all' ? null : currentFilter);
            } else {
                const error = await res.json();
                alert('Gagal: ' + (error.message || 'Error'));
            }
        } catch (error) {
            alert('Gagal terhubung ke server');
        } finally {
            btn.innerHTML = 'Simpan';
            btn.disabled = false;
        }
    });

    function showSuccess(message) {
        const alert = document.getElementById('successAlert');
        document.getElementById('successMessage').textContent = message;
        alert.style.display = 'flex';
        setTimeout(() => alert.style.display = 'none', 5000);
    }

    function showError() {
        document.getElementById('bookingsList').innerHTML = `
            <div class="empty-state">
                <i class="fa-solid fa-exclamation-triangle"></i>
                <p>Gagal memuat data. Silakan refresh halaman.</p>
            </div>`;
    }

    // Load bookings on page load
    loadBookings();
</script>
@endsection
