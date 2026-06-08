@extends('layouts.app')
@section('title', 'Konsultasi Online')

@push('styles')
<style>
    .chat-container { 
        max-width: 1200px; margin: 0 auto; padding: 40px 24px; 
        display: grid; grid-template-columns: 320px 1fr; gap: 24px; 
        height: calc(100vh - 148px); 
    }
    
    /* Doctor List Sidebar */
    .doctor-list { 
        background: #fff; border-radius: 16px; border: 1px solid var(--gray-200); 
        overflow: hidden; display: flex; flex-direction: column;
    }
    .doctor-list-header { 
        padding: 20px; border-bottom: 1px solid var(--gray-200); 
        font-size: 16px; font-weight: 800; color: var(--gray-800);
        display: flex; align-items: center; gap: 8px;
    }
    .doctor-list-body { flex: 1; overflow-y: auto; }
    
    .doctor-item { 
        padding: 16px 20px; border-bottom: 1px solid var(--gray-100); 
        cursor: pointer; transition: all .2s; display: flex; gap: 12px; align-items: start;
    }
    .doctor-item:hover { background: var(--teal-pale); }
    .doctor-item.active { background: var(--teal-pale); border-left: 3px solid var(--teal); }
    
    .doctor-avatar {
        width: 48px; height: 48px; border-radius: 50%; background: var(--teal-pale);
        display: flex; align-items: center; justify-content: center; color: var(--teal);
        font-size: 20px; flex-shrink: 0;
    }
    .doctor-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    
    .doctor-info { flex: 1; min-width: 0; }
    .doctor-name { 
        font-size: 14px; font-weight: 700; color: var(--gray-800); 
        margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .doctor-spec { font-size: 12px; color: var(--gray-500); margin-bottom: 4px; }
    .doctor-status {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 10px;
    }
    .doctor-status.online { background: #d1fae5; color: #10b981; }
    .doctor-status.offline { background: var(--gray-200); color: var(--gray-600); }
    .doctor-status .dot {
        width: 6px; height: 6px; border-radius: 50%; background: currentColor;
    }
    
    .empty-state {
        padding: 40px 20px; text-align: center; color: var(--gray-400);
    }
    .empty-state i { font-size: 32px; margin-bottom: 8px; display: block; }
    .empty-state p { font-size: 13px; }
    
    /* Chat Window */
    .chat-window { 
        background: #fff; border-radius: 16px; border: 1px solid var(--gray-200); 
        display: flex; flex-direction: column; 
    }
    .chat-header { 
        padding: 20px 24px; border-bottom: 1px solid var(--gray-200); 
        display: flex; align-items: center; gap: 12px;
    }
    .chat-header-avatar {
        width: 40px; height: 40px; border-radius: 50%; background: var(--teal-pale);
        display: flex; align-items: center; justify-content: center; color: var(--teal);
        font-size: 18px;
    }
    .chat-header-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    .chat-header-info { flex: 1; }
    .chat-header-name { font-size: 16px; font-weight: 800; color: var(--gray-800); }
    .chat-header-status { font-size: 12px; color: var(--gray-500); }
    
    .chat-messages { 
        flex: 1; padding: 24px; overflow-y: auto; 
        display: flex; flex-direction: column; gap: 12px;
        background: var(--gray-50);
    }
    .message { 
        max-width: 70%; padding: 12px 16px; border-radius: 12px; 
        font-size: 14px; line-height: 1.5; 
    }
    .message.sent { 
        align-self: flex-end; background: var(--teal); color: #fff; 
        border-bottom-right-radius: 4px; 
    }
    .message.received { 
        align-self: flex-start; background: #fff; color: var(--gray-800); 
        border-bottom-left-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,.05);
    }
    .message-time {
        font-size: 11px; margin-top: 4px; opacity: 0.7;
    }
    
    .chat-input { 
        padding: 20px 24px; border-top: 1px solid var(--gray-200); 
        display: flex; gap: 12px; background: #fff;
    }
    .chat-input input { 
        flex: 1; padding: 12px 16px; border: 1.5px solid var(--gray-200); 
        border-radius: 12px; font-size: 14px; font-family: inherit;
    }
    .chat-input input:focus { 
        outline: none; border-color: var(--teal); 
        box-shadow: 0 0 0 3px rgba(13,148,136,.1);
    }
    .chat-input button { 
        padding: 12px 24px; background: var(--teal); color: #fff; 
        border: none; border-radius: 12px; font-weight: 700; cursor: pointer;
        transition: all .2s; display: flex; align-items: center; gap: 6px;
    }
    .chat-input button:hover { background: var(--teal-dark); }
    .chat-input button:disabled { 
        background: var(--gray-300); cursor: not-allowed; 
    }
    
    .welcome-message {
        text-align: center; padding: 60px 24px; color: var(--gray-400);
    }
    .welcome-message i { font-size: 48px; margin-bottom: 12px; display: block; }
    .welcome-message h3 { font-size: 18px; color: var(--gray-700); margin-bottom: 8px; }
    .welcome-message p { font-size: 14px; }
    
    @media (max-width: 768px) {
        .chat-container { grid-template-columns: 1fr; height: auto; }
        .doctor-list { max-height: 300px; }
    }
</style>
@endpush

@section('content')
<div style="padding-top:68px;background:var(--gray-50);min-height:100vh;">
    <div class="chat-container">
        <!-- Doctor List Sidebar -->
        <div class="doctor-list">
            <div class="doctor-list-header">
                <i class="fa-solid fa-user-doctor"></i>
                Pilih Dokter
            </div>
            <div class="doctor-list-body" id="doctorList">
                <div class="empty-state">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    <p>Memuat daftar dokter...</p>
                </div>
            </div>
        </div>

        <!-- Chat Window -->
        <div class="chat-window">
            <div class="chat-header" id="chatHeader" style="display:none;">
                <div class="chat-header-avatar" id="doctorAvatar">
                    <i class="fa-solid fa-user-doctor"></i>
                </div>
                <div class="chat-header-info">
                    <div class="chat-header-name" id="doctorName">Dr. Dokter</div>
                    <div class="chat-header-status" id="doctorStatus">Offline</div>
                </div>
            </div>
            
            <div class="chat-messages" id="chatMessages">
                <div class="welcome-message">
                    <i class="fa-solid fa-comments"></i>
                    <h3>Konsultasi Online dengan Dokter Hewan</h3>
                    <p>Pilih dokter dari daftar di sebelah kiri untuk memulai konsultasi</p>
                </div>
            </div>
            
            <div class="chat-input">
                <input type="text" id="messageInput" placeholder="Ketik pesan..." disabled>
                <button id="sendBtn" disabled>
                    <i class="fa-solid fa-paper-plane"></i> Kirim
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const token = localStorage.getItem('vetra_token');
    const user = JSON.parse(localStorage.getItem('vetra_user'));

    if (!token || !user) {
        window.location.href = '/login';
    }

    let doctors = [];
    let selectedDoctor = null;
    let currentChat = null;
    let messages = [];

    // Load doctors
    async function loadDoctors() {
        try {
            const res = await fetch('/api/doctors', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.status === 401) {
                // Token expired
                console.error('Token expired, redirecting to login...');
                alert('Sesi Anda telah berakhir. Silakan login kembali.');
                localStorage.removeItem('vetra_token');
                localStorage.removeItem('vetra_user');
                window.location.href = '/login';
                return;
            }

            if (res.ok) {
                const data = await res.json();
                doctors = data.data || data;
                console.log('Doctors loaded:', doctors);
                renderDoctors();
            } else {
                showError('Gagal memuat daftar dokter');
            }
        } catch (error) {
            console.error('Error loading doctors:', error);
            showError('Gagal terhubung ke server');
        }
    }

    // Render doctors list
    function renderDoctors() {
        const container = document.getElementById('doctorList');
        
        if (!doctors || doctors.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-user-doctor-slash"></i>
                    <p>Tidak ada dokter tersedia</p>
                </div>`;
            return;
        }

        container.innerHTML = doctors.map(doctor => {
            const isOnline = doctor.doctorProfile?.is_online || false;
            const spec = doctor.doctorProfile?.spesialis || 'Dokter Hewan Umum';
            
            return `
                <div class="doctor-item" onclick="selectDoctor(${doctor.id})">
                    <div class="doctor-avatar">
                        ${doctor.profile_pic 
                            ? `<img src="${doctor.profile_pic}" alt="${doctor.name}">` 
                            : '<i class="fa-solid fa-user-doctor"></i>'}
                    </div>
                    <div class="doctor-info">
                        <div class="doctor-name">${doctor.name}</div>
                        <div class="doctor-spec">${spec}</div>
                        <div class="doctor-status ${isOnline ? 'online' : 'offline'}">
                            <span class="dot"></span>
                            ${isOnline ? 'Online' : 'Offline'}
                        </div>
                    </div>
                </div>`;
        }).join('');
    }

    // Select doctor to chat with
    async function selectDoctor(doctorId) {
        console.log('Selecting doctor:', doctorId);
        const doctor = doctors.find(d => d.id === doctorId);
        if (!doctor) {
            console.log('Doctor not found');
            return;
        }

        selectedDoctor = doctor;
        console.log('Selected doctor:', selectedDoctor);
        
        // Update active state
        document.querySelectorAll('.doctor-item').forEach(item => {
            item.classList.remove('active');
        });
        event.currentTarget.classList.add('active');

        // Show chat header
        const header = document.getElementById('chatHeader');
        const avatar = document.getElementById('doctorAvatar');
        const name = document.getElementById('doctorName');
        const status = document.getElementById('doctorStatus');
        
        header.style.display = 'flex';
        
        if (doctor.profile_pic) {
            avatar.innerHTML = `<img src="${doctor.profile_pic}" alt="${doctor.name}">`;
        } else {
            avatar.innerHTML = '<i class="fa-solid fa-user-doctor"></i>';
        }
        
        name.textContent = doctor.name;
        const isOnline = doctor.doctorProfile?.is_online || false;
        status.textContent = isOnline ? 'Online' : 'Offline';

        // Enable chat input
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');
        
        messageInput.disabled = false;
        sendBtn.disabled = false;
        
        console.log('Chat input enabled');

        // Load or create chat
        await loadChat(doctorId);
    }

    // Load existing chat or create new one
    async function loadChat(doctorId) {
        try {
            console.log('Loading chat for doctor:', doctorId);
            const res = await fetch(`/api/chats/${doctorId}`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            console.log('Chat API response status:', res.status);

            if (res.status === 401) {
                // Token expired
                console.error('Token expired, redirecting to login...');
                alert('Sesi Anda telah berakhir. Silakan login kembali.');
                localStorage.removeItem('vetra_token');
                localStorage.removeItem('vetra_user');
                window.location.href = '/login';
                return;
            }

            if (res.ok) {
                const data = await res.json();
                currentChat = data.chat;
                console.log('Chat loaded successfully:', currentChat);
                
                // Load messages
                await loadMessages(currentChat.id);
            } else {
                const errorText = await res.text();
                console.error('Failed to load chat:', res.status, errorText);
                
                // Show error to user
                alert('Gagal memuat chat dengan dokter. Silakan refresh halaman dan coba lagi.');
            }
        } catch (error) {
            console.error('Error loading chat:', error);
            alert('Gagal terhubung ke server. Silakan periksa koneksi internet Anda.');
        }
    }

    // Load messages for current chat
    async function loadMessages(chatId) {
        try {
            const res = await fetch(`/api/chats/${chatId}/messages`, {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                const data = await res.json();
                messages = data.messages || [];
                renderMessages();
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }

    // Render messages
    function renderMessages() {
        const container = document.getElementById('chatMessages');
        
        if (!messages || messages.length === 0) {
            container.innerHTML = `
                <div class="welcome-message">
                    <i class="fa-solid fa-comments"></i>
                    <h3>Mulai Konsultasi</h3>
                    <p>Kirim pesan pertama Anda ke ${selectedDoctor.name}</p>
                </div>`;
            return;
        }

        container.innerHTML = messages.map(msg => {
            const isSent = msg.sender_id === user.id;
            const time = new Date(msg.created_at).toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
            
            // Use content or message field, whichever exists
            const messageText = msg.content || msg.message || '[Pesan tidak dapat ditampilkan]';
            
            return `
                <div class="message ${isSent ? 'sent' : 'received'}">
                    <div>${messageText}</div>
                    <div class="message-time">${time}</div>
                </div>`;
        }).join('');

        // Scroll to bottom
        container.scrollTop = container.scrollHeight;
    }

    // Send message function
    async function sendMessage() {
        console.log('sendMessage called');
        const input = document.getElementById('messageInput');
        const message = input.value.trim();
        
        console.log('Message:', message);
        console.log('Current chat:', currentChat);
        console.log('Selected doctor:', selectedDoctor);
        
        if (!message) {
            console.log('Message is empty');
            return;
        }
        
        if (!selectedDoctor) {
            console.log('No doctor selected');
            alert('Silakan pilih dokter terlebih dahulu');
            return;
        }
        
        // If no chat exists, try to create it first
        if (!currentChat) {
            console.log('No chat exists, creating chat first...');
            await loadChat(selectedDoctor.id);
            
            // Check again after creating
            if (!currentChat) {
                console.error('Failed to create chat');
                alert('Gagal membuat chat dengan dokter. Silakan coba lagi.');
                return;
            }
        }

        // Disable button while sending
        const sendBtn = document.getElementById('sendBtn');
        sendBtn.disabled = true;
        input.disabled = true;

        // Optimistically add message to UI
        const tempMessage = {
            sender_id: user.id,
            content: message,
            created_at: new Date().toISOString()
        };
        messages.push(tempMessage);
        renderMessages();
        input.value = '';

        try {
            console.log('Sending to API:', `/api/chats/${currentChat.id}/messages`);
            
            const res = await fetch(`/api/chats/${currentChat.id}/messages`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ content: message })
            });

            console.log('Response status:', res.status);

            if (res.status === 401) {
                // Token expired
                console.error('Token expired, redirecting to login...');
                alert('Sesi Anda telah berakhir. Silakan login kembali.');
                localStorage.removeItem('vetra_token');
                localStorage.removeItem('vetra_user');
                window.location.href = '/login';
                return;
            }

            if (res.ok) {
                const data = await res.json();
                console.log('Response data:', data);
                // Replace temp message with actual message from server
                messages[messages.length - 1] = data.data;
                renderMessages();
            } else {
                const errorText = await res.text();
                console.error('Error response:', errorText);
                // Remove temp message if failed
                messages.pop();
                renderMessages();
                
                try {
                    const errorData = JSON.parse(errorText);
                    alert('Gagal mengirim pesan: ' + (errorData.message || 'Unknown error'));
                } catch (e) {
                    alert('Gagal mengirim pesan. Response: ' + errorText);
                }
            }
        } catch (error) {
            console.error('Error sending message:', error);
            // Remove temp message if error
            messages.pop();
            renderMessages();
            alert('Gagal terhubung ke server: ' + error.message);
        } finally {
            // Re-enable button
            sendBtn.disabled = false;
            input.disabled = false;
            input.focus();
        }
    }
    
    // Attach event listeners after DOM is ready
    document.getElementById('sendBtn').addEventListener('click', function(e) {
        console.log('Send button clicked');
        e.preventDefault();
        sendMessage();
    });
    
    document.getElementById('messageInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            console.log('Enter key pressed');
            e.preventDefault();
            sendMessage();
        }
    });

    function showError(message) {
        document.getElementById('doctorList').innerHTML = `
            <div class="empty-state">
                <i class="fa-solid fa-exclamation-triangle"></i>
                <p>${message}</p>
            </div>`;
    }

    // Load doctors on page load
    loadDoctors();

    // Refresh messages every 5 seconds if chat is active
    setInterval(() => {
        if (currentChat) {
            loadMessages(currentChat.id);
        }
    }, 5000);
</script>
@endsection
