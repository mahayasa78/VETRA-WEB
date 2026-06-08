@extends('layouts.app')
@section('title', 'Chat dengan Pasien')

@push('styles')
<style>
    .chat-container { 
        max-width: 1200px; margin: 0 auto; padding: 40px 24px; 
        display: grid; grid-template-columns: 320px 1fr; gap: 24px; 
        height: calc(100vh - 148px); 
    }
    
    /* Patient List Sidebar */
    .patient-list { 
        background: #fff; border-radius: 16px; border: 1px solid var(--gray-200); 
        overflow: hidden; display: flex; flex-direction: column;
    }
    .patient-list-header { 
        padding: 20px; border-bottom: 1px solid var(--gray-200); 
        font-size: 16px; font-weight: 800; color: var(--gray-800);
        display: flex; align-items: center; gap: 8px;
    }
    .patient-list-body { flex: 1; overflow-y: auto; }
    
    .patient-item { 
        padding: 16px 20px; border-bottom: 1px solid var(--gray-100); 
        cursor: pointer; transition: all .2s; display: flex; gap: 12px; align-items: start;
    }
    .patient-item:hover { background: var(--teal-pale); }
    .patient-item.active { background: var(--teal-pale); border-left: 3px solid var(--teal); }
    
    .patient-avatar {
        width: 48px; height: 48px; border-radius: 50%; background: var(--teal-pale);
        display: flex; align-items: center; justify-content: center; color: var(--teal);
        font-size: 20px; flex-shrink: 0;
    }
    .patient-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    
    .patient-info { flex: 1; min-width: 0; }
    .patient-name { 
        font-size: 14px; font-weight: 700; color: var(--gray-800); 
        margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .patient-last-message { 
        font-size: 12px; color: var(--gray-500); 
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .patient-time { font-size: 11px; color: var(--gray-400); margin-top: 2px; }
    
    .unread-badge {
        background: var(--teal); color: white; 
        font-size: 11px; font-weight: 700;
        padding: 2px 8px; border-radius: 10px;
        display: inline-block;
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
    .chat-header-email { font-size: 12px; color: var(--gray-500); }
    
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
        .patient-list { max-height: 300px; }
    }
</style>
@endpush

@section('content')
<div style="padding-top:68px;background:var(--gray-50);min-height:100vh;">
    <div class="chat-container">
        <!-- Patient List Sidebar -->
        <div class="patient-list">
            <div class="patient-list-header">
                <i class="fa-solid fa-users"></i>
                Daftar Pasien
            </div>
            <div class="patient-list-body" id="patientList">
                <div class="empty-state">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    <p>Memuat daftar chat...</p>
                </div>
            </div>
        </div>

        <!-- Chat Window -->
        <div class="chat-window">
            <div class="chat-header" id="chatHeader" style="display:none;">
                <div class="chat-header-avatar" id="patientAvatar">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="chat-header-info">
                    <div class="chat-header-name" id="patientName">Pasien</div>
                    <div class="chat-header-email" id="patientEmail">-</div>
                </div>
            </div>
            
            <div class="chat-messages" id="chatMessages">
                <div class="welcome-message">
                    <i class="fa-solid fa-comments"></i>
                    <h3>Chat dengan Pasien</h3>
                    <p>Pilih pasien dari daftar di sebelah kiri untuk memulai chat</p>
                </div>
            </div>
            
            <div class="chat-input">
                <input type="text" id="messageInput" placeholder="Ketik balasan..." disabled>
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

    // Check if doctor role
    if (user.role !== 'doctor') {
        alert('Halaman ini hanya untuk dokter');
        window.location.href = '/';
    }

    let chats = [];
    let selectedChat = null;
    let selectedPatient = null;
    let messages = [];

    // Load chats for this doctor
    async function loadChats() {
        try {
            const res = await fetch('/api/chats', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.status === 401) {
                alert('Sesi Anda telah berakhir. Silakan login kembali.');
                localStorage.removeItem('vetra_token');
                localStorage.removeItem('vetra_user');
                window.location.href = '/login';
                return;
            }

            if (res.ok) {
                const data = await res.json();
                chats = data.chats || [];
                console.log('Chats loaded:', chats);
                renderChats();
            } else {
                showError('Gagal memuat daftar chat');
            }
        } catch (error) {
            console.error('Error loading chats:', error);
            showError('Gagal terhubung ke server');
        }
    }

    // Render chats list
    function renderChats() {
        const container = document.getElementById('patientList');
        
        if (!chats || chats.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fa-solid fa-inbox"></i>
                    <p>Belum ada chat dengan pasien</p>
                </div>`;
            return;
        }

        container.innerHTML = chats.map(chat => {
            const patient = chat.user;
            const lastMessage = chat.last_message || 'Belum ada pesan';
            const unread = chat.unread_doctor || 0;
            const time = chat.last_timestamp ? new Date(chat.last_timestamp).toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            }) : '';
            
            return `
                <div class="patient-item" onclick="selectChat(${chat.id})">
                    <div class="patient-avatar">
                        ${patient.profile_pic 
                            ? `<img src="${patient.profile_pic}" alt="${patient.name}">` 
                            : '<i class="fa-solid fa-user"></i>'}
                    </div>
                    <div class="patient-info">
                        <div class="patient-name">
                            ${patient.name}
                            ${unread > 0 ? `<span class="unread-badge">${unread}</span>` : ''}
                        </div>
                        <div class="patient-last-message">${lastMessage}</div>
                        <div class="patient-time">${time}</div>
                    </div>
                </div>`;
        }).join('');
    }

    // Select chat
    async function selectChat(chatId) {
        console.log('Selecting chat:', chatId);
        const chat = chats.find(c => c.id === chatId);
        if (!chat) {
            console.log('Chat not found');
            return;
        }

        selectedChat = chat;
        selectedPatient = chat.user;
        console.log('Selected chat:', selectedChat);
        console.log('Selected patient:', selectedPatient);
        
        // Update active state
        document.querySelectorAll('.patient-item').forEach(item => {
            item.classList.remove('active');
        });
        event.currentTarget.classList.add('active');

        // Show chat header
        const header = document.getElementById('chatHeader');
        const avatar = document.getElementById('patientAvatar');
        const name = document.getElementById('patientName');
        const email = document.getElementById('patientEmail');
        
        header.style.display = 'flex';
        
        if (selectedPatient.profile_pic) {
            avatar.innerHTML = `<img src="${selectedPatient.profile_pic}" alt="${selectedPatient.name}">`;
        } else {
            avatar.innerHTML = '<i class="fa-solid fa-user"></i>';
        }
        
        name.textContent = selectedPatient.name;
        email.textContent = selectedPatient.email || '-';

        // Enable chat input
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');
        
        messageInput.disabled = false;
        sendBtn.disabled = false;
        
        console.log('Chat input enabled');

        // Load messages
        await loadMessages(chatId);
        
        // Mark as read
        await markAsRead(chatId);
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
                console.log('Messages loaded:', messages);
                renderMessages();
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }

    // Mark chat as read
    async function markAsRead(chatId) {
        try {
            await fetch(`/api/chats/${chatId}/read`, {
                method: 'PATCH',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });
            
            // Update unread count in UI
            const chat = chats.find(c => c.id === chatId);
            if (chat) {
                chat.unread_doctor = 0;
                renderChats();
            }
        } catch (error) {
            console.error('Error marking as read:', error);
        }
    }

    // Render messages
    function renderMessages() {
        const container = document.getElementById('chatMessages');
        
        if (!messages || messages.length === 0) {
            container.innerHTML = `
                <div class="welcome-message">
                    <i class="fa-solid fa-comments"></i>
                    <h3>Mulai Chat</h3>
                    <p>Kirim pesan pertama Anda ke ${selectedPatient.name}</p>
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
        console.log('Selected chat:', selectedChat);
        
        if (!message) {
            console.log('Message is empty');
            return;
        }
        
        if (!selectedChat) {
            console.log('No chat selected');
            alert('Silakan pilih pasien terlebih dahulu');
            return;
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
            console.log('Sending to API:', `/api/chats/${selectedChat.id}/messages`);
            
            const res = await fetch(`/api/chats/${selectedChat.id}/messages`, {
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
                    alert('Gagal mengirim pesan');
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
    
    // Attach event listeners
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
        document.getElementById('patientList').innerHTML = `
            <div class="empty-state">
                <i class="fa-solid fa-exclamation-triangle"></i>
                <p>${message}</p>
            </div>`;
    }

    // Load chats on page load
    loadChats();

    // Refresh messages every 5 seconds if chat is active
    setInterval(() => {
        if (selectedChat) {
            loadMessages(selectedChat.id);
        }
        // Also refresh chat list to update unread counts
        loadChats();
    }, 5000);
</script>
@endsection
