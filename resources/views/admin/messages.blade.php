@extends('layouts.app')
@section('title', 'Pesan Masuk')

@push('styles')
<style>
    .admin-messages { max-width: 1400px; margin: 0 auto; padding: 40px 24px; }
    .page-header { margin-bottom: 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
    .page-header h1 { font-size: 32px; font-weight: 800; color: var(--gray-800); }
    
    .stats-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px; margin-bottom: 32px;
    }
    .stat-card {
        background: #fff; padding: 20px; border-radius: 12px;
        border: 1px solid var(--gray-200); box-shadow: var(--shadow);
    }
    .stat-value { font-size: 32px; font-weight: 800; color: var(--teal); margin-bottom: 4px; }
    .stat-label { font-size: 13px; font-weight: 600; color: var(--gray-600); text-transform: uppercase; }
    
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
    
    .messages-list {
        background: #fff; border-radius: 16px; border: 1px solid var(--gray-200);
        box-shadow: var(--shadow);
    }
    
    .message-item {
        padding: 20px; border-bottom: 1px solid var(--gray-100);
        cursor: pointer; transition: all .2s;
    }
    .message-item:hover { background: var(--gray-50); }
    .message-item:last-child { border-bottom: none; }
    .message-item.unread { background: var(--teal-50); border-left: 4px solid var(--teal); }
    
    .message-header {
        display: flex; justify-content: space-between; align-items: flex-start;
        margin-bottom: 8px;
    }
    .message-sender { font-size: 16px; font-weight: 700; color: var(--gray-800); }
    .message-date { font-size: 13px; color: var(--gray-500); }
    
    .message-subject {
        font-size: 14px; font-weight: 600; color: var(--gray-700);
        margin-bottom: 6px;
    }
    .message-preview {
        font-size: 14px; color: var(--gray-600); line-height: 1.5;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .message-meta {
        display: flex; gap: 12px; margin-top: 12px; flex-wrap: wrap;
    }
    .status-badge {
        display: inline-block; padding: 4px 12px; border-radius: 8px;
        font-size: 12px; font-weight: 600;
    }
    .status-badge.unread { background: #fef3c7; color: #f59e0b; }
    .status-badge.read { background: #dbeafe; color: #3b82f6; }
    .status-badge.replied { background: #d1fae5; color: #10b981; }
    
    .empty-state {
        text-align: center; padding: 60px 24px; color: var(--gray-400);
    }
    .empty-state i { font-size: 48px; margin-bottom: 16px; }
    
    /* Modal */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,.5); backdrop-filter: blur(4px);
        display: none; align-items: center; justify-content: center; z-index: 1000; padding: 20px;
        overflow-y: auto;
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
        background: #fff; border-radius: 20px; padding: 32px; width: 100%; max-width: 800px;
        box-shadow: 0 20px 60px rgba(0,0,0,.3); margin: 20px; max-height: 90vh;
        overflow-y: auto;
    }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .modal-header h3 { font-size: 20px; font-weight: 800; color: var(--gray-800); }
    .modal-close {
        width: 32px; height: 32px; border-radius: 50%; background: var(--gray-100);
        border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
        color: var(--gray-600); transition: all .2s;
    }
    .modal-close:hover { background: var(--gray-200); }
    
    .message-detail { margin-bottom: 24px; }
    .detail-row { margin-bottom: 12px; }
    .detail-label { font-size: 13px; font-weight: 700; color: var(--gray-600); margin-bottom: 4px; }
    .detail-value { font-size: 14px; color: var(--gray-800); }
    .message-content {
        background: var(--gray-50); padding: 16px; border-radius: 12px;
        font-size: 14px; color: var(--gray-700); line-height: 1.7;
        white-space: pre-wrap; word-wrap: break-word;
    }
    
    .reply-section {
        border-top: 2px solid var(--gray-200); padding-top: 24px; margin-top: 24px;
    }
    .reply-section h4 { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
    .reply-section textarea {
        width: 100%; padding: 12px; border: 1.5px solid var(--gray-200);
        border-radius: 10px; font-size: 14px; font-family: inherit;
        min-height: 120px; resize: vertical;
    }
    .reply-section textarea:focus {
        outline: none; border-color: var(--teal); box-shadow: 0 0 0 3px rgba(13,148,136,.1);
    }
    
    .modal-actions {
        display: flex; gap: 10px; margin-top: 16px;
    }
    .btn-secondary {
        flex: 1; padding: 12px; border-radius: 10px; font-size: 14px; font-weight: 600;
        background: var(--gray-100); color: var(--gray-700); border: none; cursor: pointer;
    }
    .btn-primary {
        flex: 1; padding: 12px; border-radius: 10px; font-size: 14px; font-weight: 700;
        background: var(--teal); color: #fff; border: none; cursor: pointer;
    }
    .btn-primary:hover { background: var(--teal-dark); }
    
    .btn-delete {
        padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600;
        background: #fee2e2; color: #dc2626; border: none; cursor: pointer;
    }
    .btn-delete:hover { background: #dc2626; color: #fff; }
    
    .alert {
        padding: 14px 18px; border-radius: 12px; margin-bottom: 20px;
        display: none; align-items: center; gap: 10px;
    }
    .alert.success { background: #f0fdf4; border: 1px solid #86efac; color: #16a34a; }
    .alert.error { background: #fef2f2; border: 1px solid #fca5a5; color: #dc2626; }
    
    .replied-info {
        background: var(--teal-50); padding: 16px; border-radius: 12px; margin-top: 16px;
        border-left: 4px solid var(--teal);
    }
    .replied-info strong { display: block; margin-bottom: 8px; color: var(--teal-dark); }
    .replied-info p { margin: 0; color: var(--gray-700); line-height: 1.6; }
</style>
@endpush

@section('content')
<div style="padding-top:68px;background:var(--gray-50);min-height:100vh;">
    <div class="admin-messages">
        <div class="alert success" id="successAlert">
            <i class="fa-solid fa-circle-check"></i>
            <span id="successMessage"></span>
        </div>
        
        <div class="page-header">
            <h1>📬 Pesan Masuk</h1>
        </div>

        <div class="stats-grid" id="statsGrid">
            <div class="stat-card">
                <div class="stat-value" id="totalCount">-</div>
                <div class="stat-label">Total Pesan</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="unreadCount">-</div>
                <div class="stat-label">Belum Dibaca</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="readCount">-</div>
                <div class="stat-label">Sudah Dibaca</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="repliedCount">-</div>
                <div class="stat-label">Sudah Dibalas</div>
            </div>
        </div>

        <div class="filter-tabs">
            <button class="filter-tab active" onclick="filterMessages('all')">Semua</button>
            <button class="filter-tab" onclick="filterMessages('unread')">Belum Dibaca</button>
            <button class="filter-tab" onclick="filterMessages('read')">Sudah Dibaca</button>
            <button class="filter-tab" onclick="filterMessages('replied')">Sudah Dibalas</button>
        </div>

        <div class="messages-list" id="messagesList">
            <div class="empty-state">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <p>Memuat pesan...</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Message -->
<div class="modal-overlay" id="messageModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Detail Pesan</h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <div class="message-detail" id="messageDetail">
            <!-- Will be populated dynamically -->
        </div>
    </div>
</div>

<script>
    const token = localStorage.getItem('vetra_token');
    const user = JSON.parse(localStorage.getItem('vetra_user'));

    if (!token || !user || user.role !== 'admin') {
        window.location.href = '/login';
    }

    let messages = [];
    let currentFilter = 'all';
    let isLoading = false;

    async function loadStats() {
        try {
            const res = await fetch('/api/admin/messages/stats', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                const stats = await res.json();
                document.getElementById('totalCount').textContent = stats.total || 0;
                document.getElementById('unreadCount').textContent = stats.unread || 0;
                document.getElementById('readCount').textContent = stats.read || 0;
                document.getElementById('repliedCount').textContent = stats.replied || 0;
            }
        } catch (error) {
            console.error('Error loading stats:', error);
        }
    }

    async function loadMessages() {
        if (isLoading) return;
        
        isLoading = true;
        const container = document.getElementById('messagesList');
        container.innerHTML = `
            <div class="empty-state">
                <i class="fa-solid fa-spinner fa-spin"></i>
                <p>Memuat pesan...</p>
            </div>`;
        
        try {
            const url = '/api/admin/messages' + (currentFilter !== 'all' ? `?status=${currentFilter}` : '');
            console.log('Loading messages from:', url);
            
            const res = await fetch(url, {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            console.log('Response status:', res.status);

            if (res.ok) {
                const data = await res.json();
                console.log('Messages loaded:', data);
                
                messages = data.data || data;
                renderMessages();
                loadStats(); // Refresh stats
            } else if (res.status === 401) {
                localStorage.clear();
                window.location.href = '/login';
            } else {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fa-solid fa-exclamation-triangle"></i>
                        <p>Gagal memuat pesan. Status: ${res.status}</p>
                    </div>`;
            }
        } catch (error) {
            console.error('Error loading messages:', error);
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    <p>Gagal terhubung ke server: ${error.message}</p>
                </div>`;
        } finally {
            isLoading = false;
        }
    }

    function renderMessages() {
        const container = document.getElementById('messagesList');

        if (messages.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-inbox"></i>
                    <p>Belum ada pesan untuk filter ini</p>
                </div>`;
            return;
        }

        container.innerHTML = messages.map(msg => `
            <div class="message-item ${msg.status}" onclick="viewMessage(${msg.id})">
                <div class="message-header">
                    <div class="message-sender">${msg.name}</div>
                    <div class="message-date">${new Date(msg.created_at).toLocaleString('id-ID')}</div>
                </div>
                <div class="message-subject">${msg.subject}</div>
                <div class="message-preview">${msg.message}</div>
                <div class="message-meta">
                    <span class="status-badge ${msg.status}">${getStatusLabel(msg.status)}</span>
                    ${msg.email ? `<span style="font-size:13px;color:var(--gray-500);"><i class="fa-solid fa-envelope" style="margin-right:4px;"></i>${msg.email}</span>` : ''}
                    ${msg.phone ? `<span style="font-size:13px;color:var(--gray-500);"><i class="fa-solid fa-phone" style="margin-right:4px;"></i>${msg.phone}</span>` : ''}
                </div>
            </div>
        `).join('');
    }

    function getStatusLabel(status) {
        const labels = {
            'unread': 'Belum Dibaca',
            'read': 'Sudah Dibaca',
            'replied': 'Sudah Dibalas'
        };
        return labels[status] || status;
    }

    function filterMessages(status) {
        if (isLoading) return;
        
        currentFilter = status;
        document.querySelectorAll('.filter-tab').forEach(tab => tab.classList.remove('active'));
        event.target.classList.add('active');
        loadMessages();
    }

    async function viewMessage(id) {
        try {
            const res = await fetch(`/api/admin/messages/${id}`, {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                const data = await res.json();
                const msg = data.message;
                
                const repliedInfo = msg.status === 'replied' && msg.admin_reply ? `
                    <div class="replied-info">
                        <strong>Balasan Anda (${new Date(msg.replied_at).toLocaleString('id-ID')})</strong>
                        <p>${msg.admin_reply}</p>
                    </div>
                ` : '';
                
                const replySection = msg.status !== 'replied' ? `
                    <div class="reply-section">
                        <h4>Balas Pesan</h4>
                        <textarea id="replyText" placeholder="Tulis balasan Anda di sini..."></textarea>
                        <div class="modal-actions">
                            <button class="btn-secondary" onclick="closeModal()">Batal</button>
                            <button class="btn-primary" onclick="sendReply(${msg.id})">Kirim Balasan</button>
                        </div>
                    </div>
                ` : '';
                
                document.getElementById('messageDetail').innerHTML = `
                    <div class="detail-row">
                        <div class="detail-label">Dari</div>
                        <div class="detail-value">${msg.name}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Email</div>
                        <div class="detail-value">${msg.email}</div>
                    </div>
                    ${msg.phone ? `
                    <div class="detail-row">
                        <div class="detail-label">Telepon</div>
                        <div class="detail-value">${msg.phone}</div>
                    </div>
                    ` : ''}
                    <div class="detail-row">
                        <div class="detail-label">Subjek</div>
                        <div class="detail-value">${msg.subject}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Tanggal</div>
                        <div class="detail-value">${new Date(msg.created_at).toLocaleString('id-ID')}</div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Status</div>
                        <div class="detail-value"><span class="status-badge ${msg.status}">${getStatusLabel(msg.status)}</span></div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-label">Pesan</div>
                        <div class="message-content">${msg.message}</div>
                    </div>
                    ${repliedInfo}
                    ${replySection}
                    <div style="margin-top:16px;">
                        <button class="btn-delete" onclick="deleteMessage(${msg.id})">
                            <i class="fa-solid fa-trash"></i> Hapus Pesan
                        </button>
                    </div>
                `;
                
                document.getElementById('messageModal').classList.add('open');
                
                // Refresh list to update read status
                loadMessages();
            }
        } catch (error) {
            console.error('Error loading message:', error);
            alert('Gagal memuat detail pesan');
        }
    }

    async function sendReply(id) {
        const replyText = document.getElementById('replyText').value.trim();
        
        if (!replyText) {
            alert('Silakan tulis balasan terlebih dahulu');
            return;
        }
        
        try {
            const res = await fetch(`/api/admin/messages/${id}/reply`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reply: replyText })
            });

            if (res.ok) {
                showSuccess('Balasan berhasil dikirim!');
                closeModal();
                loadMessages();
            } else {
                const error = await res.json();
                alert('Error: ' + (error.message || 'Gagal mengirim balasan'));
            }
        } catch (error) {
            alert('Gagal terhubung ke server');
        }
    }

    async function deleteMessage(id) {
        if (!confirm('Yakin ingin menghapus pesan ini?')) return;

        try {
            const res = await fetch(`/api/admin/messages/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                showSuccess('Pesan berhasil dihapus!');
                closeModal();
                loadMessages();
            } else {
                alert('Gagal menghapus pesan');
            }
        } catch (error) {
            alert('Gagal terhubung ke server');
        }
    }

    function closeModal() {
        document.getElementById('messageModal').classList.remove('open');
    }

    function showSuccess(message) {
        const alert = document.getElementById('successAlert');
        document.getElementById('successMessage').textContent = message;
        alert.style.display = 'flex';
        setTimeout(() => alert.style.display = 'none', 5000);
    }

    // Load initial data
    loadStats();
    loadMessages();
</script>
@endsection
