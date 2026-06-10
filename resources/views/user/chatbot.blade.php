@extends('layouts.app')
@section('title', 'AI Chatbot Konsultasi')

@push('styles')
<style>
    .chatbot-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 40px 24px;
        min-height: calc(100vh - 68px);
        display: flex;
        flex-direction: column;
    }
    
    .chatbot-header {
        text-align: center;
        margin-bottom: 24px;
        position: relative;
    }
    
    .chatbot-header h1 {
        font-size: 32px;
        font-weight: 800;
        color: var(--gray-800);
        margin-bottom: 8px;
    }
    
    .chatbot-header p {
        font-size: 15px;
        color: var(--gray-500);
    }
    
    .clear-history-btn {
        position: absolute;
        top: 0;
        right: 0;
        padding: 8px 16px;
        background: var(--gray-100);
        color: var(--gray-600);
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .clear-history-btn:hover {
        background: #fee;
        color: #dc2626;
        border-color: #fca5a5;
    }
    
    .chat-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        flex: 1;
        overflow: hidden;
    }
    
    .chat-messages {
        flex: 1;
        padding: 24px;
        overflow-y: auto;
        max-height: 500px;
        min-height: 400px;
        background: #f8fafc;
    }
    
    .message {
        display: flex;
        margin-bottom: 20px;
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .message.user {
        justify-content: flex-end;
    }
    
    .message.bot {
        justify-content: flex-start;
    }
    
    .message-bubble {
        max-width: 70%;
        padding: 14px 18px;
        border-radius: 16px;
        position: relative;
    }
    
    .message.user .message-bubble {
        background: var(--teal);
        color: white;
        border-bottom-right-radius: 4px;
    }
    
    .message.bot .message-bubble {
        background: white;
        color: var(--gray-800);
        border: 1px solid var(--gray-200);
        border-bottom-left-radius: 4px;
    }
    
    .message-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
        margin: 0 12px;
    }
    
    .message.user .message-avatar {
        background: var(--teal-pale);
        color: var(--teal);
        order: 2;
    }
    
    .message.bot .message-avatar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .message-text {
        font-size: 14px;
        line-height: 1.6;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    
    .message-time {
        font-size: 11px;
        opacity: 0.7;
        margin-top: 4px;
    }
    
    .typing-indicator {
        display: none;
        align-items: center;
        gap: 4px;
        padding: 12px 16px;
    }
    
    .typing-indicator.active {
        display: flex;
    }
    
    .typing-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--gray-400);
        animation: typing 1.4s infinite;
    }
    
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    
    @keyframes typing {
        0%, 60%, 100% { transform: translateY(0); }
        30% { transform: translateY(-10px); }
    }
    
    .chat-input-container {
        padding: 20px 24px;
        background: white;
        border-top: 1px solid var(--gray-200);
    }
    
    .chat-input-wrapper {
        display: flex;
        gap: 12px;
        align-items: flex-end;
    }
    
    .chat-input {
        flex: 1;
        padding: 14px 18px;
        border: 2px solid var(--gray-200);
        border-radius: 12px;
        font-size: 15px;
        font-family: inherit;
        resize: none;
        min-height: 52px;
        max-height: 120px;
        transition: all 0.2s;
    }
    
    .chat-input:focus {
        outline: none;
        border-color: var(--teal);
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
    }
    
    .send-button {
        width: 52px;
        height: 52px;
        background: var(--teal);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 20px;
        cursor: pointer;
        transition: all 0.2s;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .send-button:hover {
        background: var(--teal-dark);
        transform: translateY(-2px);
    }
    
    .send-button:active {
        transform: translateY(0);
    }
    
    .send-button:disabled {
        background: var(--gray-300);
        cursor: not-allowed;
        transform: none;
    }
    
    .quick-questions {
        padding: 16px 24px;
        background: white;
        border-top: 1px solid var(--gray-100);
    }
    
    .quick-questions-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--gray-400);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 12px;
    }
    
    .quick-questions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 8px;
    }
    
    .quick-question-btn {
        padding: 10px 16px;
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        font-size: 13px;
        color: var(--gray-700);
        cursor: pointer;
        transition: all 0.2s;
        text-align: left;
        font-family: inherit;
    }
    
    .quick-question-btn:hover {
        background: var(--teal-pale);
        border-color: var(--teal);
        color: var(--teal-dark);
    }
    
    .welcome-message {
        text-align: center;
        padding: 40px 20px;
        color: var(--gray-400);
    }
    
    .welcome-message .icon {
        font-size: 64px;
        margin-bottom: 16px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .welcome-message h3 {
        font-size: 20px;
        font-weight: 700;
        color: var(--gray-700);
        margin-bottom: 8px;
    }
    
    .welcome-message p {
        font-size: 14px;
    }
    
    @media (max-width: 768px) {
        .chatbot-container {
            padding: 20px 16px;
        }
        
        .message-bubble {
            max-width: 85%;
        }
        
        .quick-questions-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div style="padding-top:68px;background:var(--gray-50);min-height:100vh;">
    <div class="chatbot-container">
        <div class="chatbot-header">
            <h1>🤖 AI Chatbot Konsultasi</h1>
            <p>Tanya apa saja seputar kesehatan hewan peliharaan Anda</p>
            <button class="clear-history-btn" onclick="clearChatHistory()" title="Hapus Riwayat Chat">
                <i class="fa-solid fa-trash"></i>
                Hapus Riwayat
            </button>
        </div>

        <div class="chat-card">
            <div class="chat-messages" id="chatMessages">
                <div class="welcome-message">
                    <div class="icon">🤖</div>
                    <h3>Halo! Saya Asisten Virtual Vetra</h3>
                    <p>Saya siap membantu menjawab pertanyaan Anda tentang kesehatan hewan peliharaan</p>
                </div>
            </div>

            <div class="quick-questions">
                <div class="quick-questions-title">Pertanyaan Cepat</div>
                <div class="quick-questions-grid">
                    <button class="quick-question-btn" onclick="askQuestion('Bagaimana cara merawat kucing yang baru lahir?')">
                        Cara merawat kucing baru lahir
                    </button>
                    <button class="quick-question-btn" onclick="askQuestion('Apa makanan yang cocok untuk anjing?')">
                        Makanan untuk anjing
                    </button>
                    <button class="quick-question-btn" onclick="askQuestion('Kapan harus vaksinasi hewan peliharaan?')">
                        Jadwal vaksinasi
                    </button>
                    <button class="quick-question-btn" onclick="askQuestion('Bagaimana mengatasi kucing yang tidak mau makan?')">
                        Kucing tidak mau makan
                    </button>
                </div>
            </div>

            <div class="chat-input-container">
                <div class="chat-input-wrapper">
                    <textarea 
                        id="chatInput" 
                        class="chat-input" 
                        placeholder="Ketik pertanyaan Anda di sini..."
                        rows="1"
                        onkeypress="handleKeyPress(event)"
                    ></textarea>
                    <button class="send-button" id="sendButton" onclick="sendMessage()">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const token = localStorage.getItem('vetra_token');
    const user = JSON.parse(localStorage.getItem('vetra_user') || 'null');

    // Optional: Check if user is logged in (but chatbot works for everyone)
    // We keep this for potential future features that need user context

    const chatMessages = document.getElementById('chatMessages');
    const chatInput = document.getElementById('chatInput');
    const sendButton = document.getElementById('sendButton');

    // Load chat history from localStorage
    const STORAGE_KEY = 'vetra_chatbot_history_' + (user?.id || 'guest');
    let chatHistory = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');

    // Load existing messages
    function loadChatHistory() {
        if (chatHistory.length > 0) {
            // Remove welcome message
            const welcomeMsg = chatMessages.querySelector('.welcome-message');
            if (welcomeMsg) {
                welcomeMsg.remove();
            }
            
            // Display all messages from history
            chatHistory.forEach(msg => {
                addMessageToUI(msg.text, msg.isUser, msg.time);
            });
        }
    }

    // Save chat history to localStorage
    function saveChatHistory() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(chatHistory));
    }

    // Auto-resize textarea
    chatInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    function handleKeyPress(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendMessage();
        }
    }

    function addMessage(text, isUser = false) {
        const time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        
        // Save to history
        chatHistory.push({
            text: text,
            isUser: isUser,
            time: time
        });
        saveChatHistory();
        
        // Display in UI
        addMessageToUI(text, isUser, time);
    }
    
    function addMessageToUI(text, isUser, time) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isUser ? 'user' : 'bot'}`;
        
        messageDiv.innerHTML = `
            <div class="message-avatar">
                ${isUser ? '<i class="fa-solid fa-user"></i>' : '🤖'}
            </div>
            <div class="message-bubble">
                <div class="message-text">${text}</div>
                <div class="message-time">${time}</div>
            </div>
        `;
        
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message bot';
        typingDiv.id = 'typingIndicator';
        typingDiv.innerHTML = `
            <div class="message-avatar">🤖</div>
            <div class="message-bubble">
                <div class="typing-indicator active">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>
        `;
        chatMessages.appendChild(typingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function hideTypingIndicator() {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    async function sendMessage() {
        const question = chatInput.value.trim();
        
        if (!question) return;
        
        // Clear welcome message
        const welcomeMsg = chatMessages.querySelector('.welcome-message');
        if (welcomeMsg) {
            welcomeMsg.remove();
        }
        
        // Add user message
        addMessage(question, true);
        chatInput.value = '';
        chatInput.style.height = 'auto';
        
        // Disable input
        sendButton.disabled = true;
        chatInput.disabled = true;
        
        // Show typing indicator
        showTypingIndicator();
        
        try {
            const response = await fetch('/api/chatbot/ask', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ question: question })
            });
            
            hideTypingIndicator();
            
            if (response.ok) {
                const data = await response.json();
                addMessage(data.answer || 'Maaf, saya tidak dapat menjawab pertanyaan Anda saat ini.', false);
            } else {
                console.error('API Error:', response.status);
                if (response.status === 500) {
                    addMessage('Maaf, layanan chatbot sedang tidak tersedia. Silakan coba lagi nanti.', false);
                } else {
                    addMessage('Maaf, terjadi kesalahan. Silakan coba lagi nanti.', false);
                }
            }
        } catch (error) {
            hideTypingIndicator();
            console.error('Error:', error);
            addMessage('Maaf, tidak dapat terhubung ke server. Silakan cek koneksi internet Anda.', false);
        } finally {
            sendButton.disabled = false;
            chatInput.disabled = false;
            chatInput.focus();
        }
    }

    function askQuestion(question) {
        chatInput.value = question;
        sendMessage();
    }
    
    // Clear chat history
    function clearChatHistory() {
        if (confirm('Apakah Anda yakin ingin menghapus semua riwayat chat?')) {
            chatHistory = [];
            saveChatHistory();
            chatMessages.innerHTML = `
                <div class="welcome-message">
                    <div class="icon">🤖</div>
                    <h3>Halo! Saya Asisten Virtual Vetra</h3>
                    <p>Saya siap membantu menjawab pertanyaan Anda tentang kesehatan hewan peliharaan</p>
                </div>
            `;
        }
    }

    // Load chat history on page load
    loadChatHistory();

    // Focus on input when page loads
    chatInput.focus();
</script>
@endsection
