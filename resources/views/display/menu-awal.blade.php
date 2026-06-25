<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMIPusat-Aset Online</title>
    <link rel="shortcut icon" href="img/logo2024.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            overflow: hidden;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-image: url('img/background-1.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .menu-container {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            transition: all 0.5s ease;
            width: 90%;
            max-width: 420px;
            margin: 0 auto;
            z-index: 10;
        }

        /* Floating Clouds Styling */
        .cloud-left {
            position: absolute;
            left: -8%;
            top: 20%;
            width: 380px;
            max-width: 35vw;
            opacity: 0.9;
            pointer-events: none;
            z-index: 2;
            animation: floatLeft 18s ease-in-out infinite alternate;
            filter: drop-shadow(0 15px 25px rgba(0,0,0,0.1));
        }

        .cloud-right {
            position: absolute;
            right: -8%;
            bottom: 15%;
            width: 420px;
            max-width: 38vw;
            opacity: 0.9;
            pointer-events: none;
            z-index: 2;
            animation: floatRight 22s ease-in-out infinite alternate;
            filter: drop-shadow(0 15px 25px rgba(0,0,0,0.1));
        }

        @keyframes floatLeft {
            0% {
                transform: translate(0, 0) scale(1) rotate(0deg);
            }
            33% {
                transform: translate(25px, -15px) scale(1.03) rotate(0.8deg);
            }
            66% {
                transform: translate(-10px, 20px) scale(0.97) rotate(-0.5deg);
            }
            100% {
                transform: translate(15px, -5px) scale(1.02) rotate(0.3deg);
            }
        }

        @keyframes floatRight {
            0% {
                transform: translate(0, 0) scale(1) rotate(0deg);
            }
            33% {
                transform: translate(-30px, 20px) scale(0.96) rotate(-1deg);
            }
            66% {
                transform: translate(15px, -25px) scale(1.04) rotate(0.6deg);
            }
            100% {
                transform: translate(-15px, 10px) scale(0.98) rotate(-0.4deg);
            }
        }

        /* Responsive design for clouds on mobile */
        @media (max-width: 768px) {
            .cloud-left {
                width: 160px;
                max-width: 45vw;
                left: -12%;
                top: 10%;
                opacity: 0.75;
                animation: floatLeftMobile 12s ease-in-out infinite alternate;
            }
            .cloud-right {
                width: 200px;
                max-width: 50vw;
                right: -12%;
                bottom: 8%;
                opacity: 0.75;
                animation: floatRightMobile 14s ease-in-out infinite alternate;
            }
        }

        @keyframes floatLeftMobile {
            0% {
                transform: translate(0, 0) scale(1);
            }
            100% {
                transform: translate(10px, -10px) scale(1.05);
            }
        }

        @keyframes floatRightMobile {
            0% {
                transform: translate(0, 0) scale(1);
            }
            100% {
                transform: translate(-12px, 12px) scale(0.95);
            }
        }

        /* Laptop/Desktop specific width */
        @media (min-width: 1024px) {
            .menu-container {
                max-width: 420px;
            }
        }



        .primary-color {
            color: #6a0dad;
        }

        .menu-btn {
            background-color: #6a0dad;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .menu-btn:hover {
            background-color: #9c4ade;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(106, 13, 173, 0.3);
        }

        .menu-btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.2) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            transition: all 0.6s;
        }

        .menu-btn:hover:before {
            left: 100%;
        }

        @media (max-width: 640px) {
            .menu-container {
                width: 95%;
                margin: 10px;
            }
        }

        /* AI Chatbot Styles for Menu Awal */
        #ai-chat-window {
            position: fixed;
            bottom: 95px;
            right: 20px;
            width: 350px;
            height: 480px;
            background: rgba(255, 255, 255, 0.98);
            border: 2px solid #6a0dad;
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform-origin: bottom right;
            overflow: hidden;
            z-index: 999;
        }

        #ai-chat-window.chat-window-hidden {
            opacity: 0;
            transform: scale(0) translate(50px, 50px);
            pointer-events: none;
        }

        #ai-chat-header {
            background: linear-gradient(135deg, #6a0dad, #4c0887);
            padding: 12px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
        }

        .ai-avatar-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ai-avatar-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.8);
            overflow: hidden;
        }

        .ai-avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ai-chat-title {
            font-weight: bold;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        .ai-chat-status {
            font-size: 11px;
            color: #a7f3d0;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .ai-chat-status::before {
            content: '';
            display: inline-block;
            width: 6px;
            height: 6px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 6px #10b981;
        }

        .btn-close-chat {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.8);
            font-size: 24px;
            cursor: pointer;
            line-height: 1;
            transition: color 0.2s;
        }

        .btn-close-chat:hover {
            color: #fca5a5;
        }

        #ai-chat-messages {
            flex-grow: 1;
            padding: 15px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background: #f8fafc;
        }

        #ai-chat-messages::-webkit-scrollbar {
            width: 6px;
        }
        #ai-chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }
        #ai-chat-messages::-webkit-scrollbar-thumb {
            background: rgba(106, 13, 173, 0.2);
            border-radius: 3px;
        }
        #ai-chat-messages::-webkit-scrollbar-thumb:hover {
            background: #6a0dad;
        }

        .chat-message {
            display: flex;
            width: 100%;
        }

        .chat-message.user {
            justify-content: flex-end;
        }

        .chat-message.assistant {
            justify-content: flex-start;
        }

        .chat-message .message-bubble {
            max-width: 80%;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 13px;
            line-height: 1.5;
            word-break: break-word;
        }

        .chat-message.user .message-bubble {
            background: #6a0dad;
            color: white;
            border-bottom-right-radius: 2px;
            font-weight: 500;
        }

        .chat-message.assistant .message-bubble {
            background: white;
            border: 1px solid rgba(106, 13, 173, 0.2);
            border-bottom-left-radius: 2px;
            color: #1e293b;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }

        .chat-message.assistant .message-bubble strong {
            color: #6a0dad;
        }

        #ai-chat-input-area {
            padding: 12px 15px;
            border-top: 1px solid rgba(106, 13, 173, 0.15);
            background: white;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        #ai-chat-input {
            flex-grow: 1;
            background: #f1f5f9;
            border: 1.5px solid rgba(106, 13, 173, 0.2);
            border-radius: 8px;
            color: #1e293b;
            padding: 8px 12px;
            font-size: 13px;
            outline: none;
            font-family: inherit;
            transition: all 0.2s;
        }

        #ai-chat-input:focus {
            border-color: #6a0dad;
            box-shadow: 0 0 8px rgba(106, 13, 173, 0.15);
            background: white;
        }

        .btn-send-chat {
            background: #6a0dad;
            color: white;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            flex-shrink: 0;
            transition: all 0.2s;
        }

        .btn-send-chat:hover {
            background: #9c4ade;
            transform: translateY(-1px);
        }

        .typing-dots {
            display: flex;
            gap: 4px;
            align-items: center;
            height: 12px;
        }

        .typing-dots span {
            width: 6px;
            height: 6px;
            background: #6a0dad;
            border-radius: 50%;
            animation: typingBounce 1.4s infinite ease-in-out both;
        }

        .typing-dots span:nth-child(1) { animation-delay: -0.32s; }
        .typing-dots span:nth-child(2) { animation-delay: -0.16s; }

        @keyframes typingBounce {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1.0); }
        }

        @media (max-width: 480px) {
            #ai-chat-window {
                width: 300px;
                height: 420px;
                right: 15px;
                bottom: 85px;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Clouds Background Elements -->
    <img src="img/awan-1.png" alt="Cloud Left" class="cloud-left">
    <img src="img/awan-2.png" alt="Cloud Right" class="cloud-right">

    <div class="menu-container p-4 md:p-6">
        
        <div class="flex flex-col items-center justify-center relative z-10">
            <img src="img/logo2024.png" alt="BMI Logo" class="w-20 mb-2">
            
            <h2 class="primary-color text-xl font-bold mb-1">
                Asset & GA
            </h2>
            
            <p class="text-gray-600 mb-3 text-center text-xs">
                Sistem Pengaduan & Pengajuan Online
            </p>
            
            <div class="flex flex-col w-full gap-2 mb-2">
                <a href="/pengaduan-aset" class="menu-btn flex items-center justify-between px-4 py-2.5 rounded-xl text-white font-medium group">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-full p-1.5 mr-3">
                            <i class="fas fa-exclamation-circle menu-icon text-sm"></i>
                        </div>
                        <span class="text-sm">Aduan Aset</span>
                    </div>
                    <i class="fas fa-chevron-right opacity-70 group-hover:opacity-100 transition-opacity text-xs"></i>
                </a>
                
                <a href="/ajuan-rutin" class="menu-btn flex items-center justify-between px-4 py-2.5 rounded-xl text-white font-medium group">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-full p-1.5 mr-3">
                            <i class="fas fa-calendar-check menu-icon text-sm"></i>
                        </div>
                        <span class="text-sm">Ajuan Rutin Bulanan</span>
                    </div>
                    <i class="fas fa-chevron-right opacity-70 group-hover:opacity-100 transition-opacity text-xs"></i>
                </a>
                
                <!-- <a href="/pendataan-stok" class="menu-btn flex items-center justify-between px-4 py-2.5 rounded-xl text-white font-medium group">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-full p-1.5 mr-3">
                            <i class="fas fa-exclamation-triangle menu-icon text-sm"></i>
                        </div>
                        <span class="text-sm">Ajuan Darurat</span>
                    </div>
                    <i class="fas fa-chevron-right opacity-70 group-hover:opacity-100 transition-opacity text-xs"></i>
                </a> -->
                
                <a href="{{ route('peminjaman.create') }}" class="menu-btn flex items-center justify-between px-4 py-2.5 rounded-xl text-white font-medium group">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-full p-1.5 mr-3">
                            <i class="fas fa-car menu-icon text-sm"></i>
                        </div>
                        <span class="text-sm">Peminjaman Kendaraan</span>
                    </div>
                    <i class="fas fa-chevron-right opacity-70 group-hover:opacity-100 transition-opacity text-xs"></i>
                </a>

                <a href="{{ route('peminjaman-event.create') }}" class="menu-btn flex items-center justify-between px-4 py-2.5 rounded-xl text-white font-medium group">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-full p-1.5 mr-3">
                            <i class="fas fa-car menu-icon text-sm"></i>
                        </div>
                        <span class="text-sm">Peminjaman Barang Event</span>
                    </div>
                    <i class="fas fa-chevron-right opacity-70 group-hover:opacity-100 transition-opacity text-xs"></i>
                </a>


                <a href="{{ route('peminjaman.jadwal') }}" class="menu-btn flex items-center justify-between px-4 py-2.5 rounded-xl text-white font-medium group">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-full p-1.5 mr-3">
                            <i class="fas fa-calendar-alt menu-icon text-sm"></i>
                        </div>
                        <span class="text-sm">Cek Jadwal Kendaraan</span>
                    </div>
                    <i class="fas fa-chevron-right opacity-70 group-hover:opacity-100 transition-opacity text-xs"></i>
                </a>

                <a href="/login" class="menu-btn flex items-center justify-between px-4 py-2.5 rounded-xl text-white font-medium group">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-full p-1.5 mr-3">
                            <i class="fas fa-user-shield menu-icon text-sm"></i>
                        </div>
                        <span class="text-sm">Admin</span>
                    </div>
                    <i class="fas fa-chevron-right opacity-70 group-hover:opacity-100 transition-opacity text-xs"></i>
                </a>

                <a href="/about" class="menu-btn flex items-center justify-between px-4 py-2.5 rounded-xl text-white font-medium group">
                    <div class="flex items-center">
                        <div class="bg-white bg-opacity-20 rounded-full p-1.5 mr-3">
                            <i class="fa-brands fa-angellist menu-icon text-sm"></i>
                        </div>
                        <span class="text-sm">Tentang Kami</span>
                    </div>
                    <i class="fas fa-chevron-right opacity-70 group-hover:opacity-100 transition-opacity text-xs"></i>
                </a>
            </div>
            
            <div class="mt-2 text-xs text-gray-500 text-center">
                <p id="current-date"></p>
            </div>
        </div>
    </div>

    <!-- AI Chatbot Floating Widget -->
    <div id="ai-chat-widget">
        <!-- Chat Bubble Prompt -->
        <div id="ai-chat-prompt" class="fixed bottom-24 right-5 bg-white text-purple-700 px-4 py-2 rounded-2xl shadow-xl text-sm font-medium z-40 cursor-pointer hover:bg-gray-50 transition-all duration-300 animate-bounce">
            Kalo bingung tanya disini aja! 👋
            <!-- Tail of the bubble -->
            <div class="absolute -bottom-2 right-6 w-4 h-4 bg-white transform rotate-45 shadow-sm"></div>
        </div>

        <!-- Floating Toggle Button (Habi Icon) -->
        <button id="ai-chat-toggle" class="fixed bottom-5 right-5 w-16 h-16 rounded-full border-4 border-purple-600 shadow-2xl overflow-hidden focus:outline-none hover:scale-110 transition-all z-50">
            <img src="{{ asset('img/habiislami.jpeg') }}" alt="Habi AI" class="w-full h-full object-cover">
            <span class="absolute top-1 right-1 w-3.5 h-3.5 bg-emerald-500 rounded-full border-2 border-white shadow"></span>
        </button>

        <!-- Chat Window -->
        <div id="ai-chat-window" class="chat-window-hidden">
            <div id="ai-chat-header">
                <div class="ai-avatar-info">
                    <div class="ai-avatar-circle">
                        <img src="{{ asset('img/habiislami.jpeg') }}" alt="Habi AI">
                    </div>
                    <div>
                        <div class="ai-chat-title">Habi Support</div>
                        <div class="ai-chat-status">Online</div>
                    </div>
                </div>
                <button id="ai-chat-close" class="btn-close-chat">&times;</button>
            </div>
            
            <div id="ai-chat-messages">
                <div class="chat-message assistant">
                    <div class="message-bubble">
                        Halo! Saya <strong>Habi</strong>. Ada yang bisa saya bantu terkait aplikasi <strong>Manajemen Aset & GA</strong> (Aduan Aset, Ajuan Rutin, Peminjaman Kendaraan, dll.) atau anggota tim kami?
                    </div>
                </div>
            </div>

            <div id="ai-chat-input-area">
                <input type="text" id="ai-chat-input" placeholder="Tulis pertanyaan Anda..." autocomplete="off">
                <button id="ai-chat-send" class="btn-send-chat">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <script>
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', options);
        
        const aiChatToggle = document.getElementById('ai-chat-toggle');
        const aiChatClose = document.getElementById('ai-chat-close');
        const aiChatWindow = document.getElementById('ai-chat-window');
        const aiChatInput = document.getElementById('ai-chat-input');
        const aiChatSend = document.getElementById('ai-chat-send');
        const aiChatMessages = document.getElementById('ai-chat-messages');
        const aiChatPrompt = document.getElementById('ai-chat-prompt');

        if (aiChatPrompt) {
            aiChatPrompt.addEventListener('click', () => {
                aiChatPrompt.style.display = 'none';
                if (aiChatWindow.classList.contains('chat-window-hidden')) {
                    aiChatToggle.click();
                }
            });
        
            setTimeout(() => {
                if(aiChatPrompt) {
                    aiChatPrompt.style.opacity = '0';
                    setTimeout(() => { aiChatPrompt.style.display = 'none'; }, 300);
                }
            }, 15000);
        }

        if (aiChatToggle && aiChatWindow) {
            aiChatToggle.addEventListener('click', () => {
                if (aiChatPrompt) aiChatPrompt.style.display = 'none';
                
                aiChatWindow.classList.toggle('chat-window-hidden');
                if (!aiChatWindow.classList.contains('chat-window-hidden')) {
                    aiChatInput.focus();
                }
            });

            aiChatClose.addEventListener('click', () => {
                aiChatWindow.classList.add('chat-window-hidden');
            });

            // Send message function
            const sendMessage = async () => {
                const query = aiChatInput.value.trim();
                if (!query) return;

                // Clear input
                aiChatInput.value = '';

                // Render User Message
                renderMessage(query, 'user');

                // Render typing indicator
                const typingIndicator = renderTypingIndicator();

                try {
                    // Call Laravel route `/api/chat`
                    const response = await fetch('/api/chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ message: query })
                    });

                    const data = await response.json();
                    typingIndicator.remove();

                    if (response.ok && data.reply) {
                        renderMessage(data.reply, 'assistant');
                    } else {
                        renderMessage(data.error || 'Maaf, sistem AI sedang mengalami gangguan. Silakan coba lagi nanti.', 'assistant');
                    }
                } catch (err) {
                    typingIndicator.remove();
                    renderMessage('Gagal menghubungi AI. Periksa koneksi internet Anda.', 'assistant');
                }
            };

            aiChatSend.addEventListener('click', sendMessage);
            aiChatInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });
        }

        function renderMessage(text, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${sender}`;

            const bubble = document.createElement('div');
            bubble.className = 'message-bubble';
            
            // Simple Markdown translation
            let formattedText = text
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\n/g, '<br>');

            bubble.innerHTML = formattedText;
            messageDiv.appendChild(bubble);
            aiChatMessages.appendChild(messageDiv);

            // Auto Scroll to bottom
            aiChatMessages.scrollTop = aiChatMessages.scrollHeight;
        }

        function renderTypingIndicator() {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'chat-message assistant';

            const bubble = document.createElement('div');
            bubble.className = 'message-bubble';

            const dots = document.createElement('div');
            dots.className = 'typing-dots';
            dots.innerHTML = '<span></span><span></span><span></span>';

            bubble.appendChild(dots);
            messageDiv.appendChild(bubble);
            aiChatMessages.appendChild(messageDiv);

            aiChatMessages.scrollTop = aiChatMessages.scrollHeight;
            return messageDiv;
        }
    </script>
</body>
</html>