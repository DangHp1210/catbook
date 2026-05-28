@php
	$chatbotSessionUrl = route('chatbot.session');
	$chatbotMessageUrl = route('chatbot.message');
@endphp

<style>
.cb-chatbot-shell {
    position: fixed;
    right: 18px;
    bottom: 18px;
    z-index: 140;
    display: flex;
    align-items: flex-end;
    gap: 12px;
    max-width: calc(100vw - 18px);
}

.cb-chatbot-pill {
    border: 0;
    border-radius: 999px;
    padding: 13px 18px;
    background: rgba(255, 255, 255, 0.96);
    color: var(--cb-brand-text, #1f2937);
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
    font-family: var(--cb-sans);
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: transform 160ms ease, box-shadow 160ms ease;
    white-space: nowrap;
}

.cb-chatbot-pill:hover {
    transform: translateY(-1px);
    box-shadow: 0 14px 28px rgba(15, 23, 42, 0.16);
}

.cb-chat-fab {
    position: relative;
    width: 58px;
    height: 58px;
    border-radius: 50%;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--cb-brand-accent, #2d6a4f), var(--cb-brand-accent-dark, #1b4332));
    color: #fff;
    box-shadow: 0 14px 30px rgba(45, 106, 79, 0.36);
    transition: transform 180ms ease, box-shadow 180ms ease;
    animation: cb-fab-bounce 3s ease-in-out infinite;
    flex-shrink: 0;
}

.cb-chat-fab:hover {
    transform: scale(1.08);
    box-shadow: 0 18px 36px rgba(45, 106, 79, 0.42);
    animation: none;
}

@keyframes cb-fab-bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

.cb-chat-fab-dot {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 12px;
    height: 12px;
    border-radius: 999px;
    background: #4ade80;
    border: 2px solid #fff;
}

.cb-chat-fab-tooltip {
    position: absolute;
    right: 68px;
    top: 50%;
    transform: translateY(-50%);
    background: #0f172a;
    color: #fff;
    font-family: var(--cb-sans);
    font-size: 12px;
    font-weight: 600;
    padding: 7px 12px;
    border-radius: 999px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 160ms ease;
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.18);
}

.cb-chat-fab:hover .cb-chat-fab-tooltip { opacity: 1; }

.cb-chatbot-panel {
    position: absolute;
    right: 0;
    bottom: calc(100% + 12px);
    width: min(360px, calc(100vw - 24px));
    height: min(540px, calc(100vh - 96px));
    border-radius: 22px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22);
    border: 1px solid rgba(226, 232, 240, 0.95);
    display: flex;
    flex-direction: column;
    opacity: 0;
    visibility: hidden;
    transform: translateY(14px) scale(0.98);
    transform-origin: bottom right;
    pointer-events: none;
    transition: opacity 180ms ease, transform 180ms ease, visibility 180ms ease;
}

.cb-chatbot-panel.open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
    pointer-events: auto;
}

.cb-chatbot-head {
    padding: 16px;
    background: linear-gradient(135deg, #d11d2a 0%, #b81420 100%);
    color: #fff;
}

.cb-chatbot-head-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
}

.cb-chatbot-head-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}

.cb-chatbot-avatar {
    width: 54px;
    height: 54px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.96);
    color: #d11d2a;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.12);
}

.cb-chatbot-avatar svg { width: 28px; height: 28px; }

.cb-chatbot-title {
    margin: 0;
    font-family: var(--cb-serif);
    font-size: 18px;
    line-height: 1.15;
    color: #fff;
}

.cb-chatbot-subtitle {
    margin: 6px 0 0;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-family: var(--cb-sans);
    font-size: 13px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.95);
}

.cb-chatbot-online-dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #86efac;
    box-shadow: 0 0 0 4px rgba(134, 239, 172, 0.16);
    flex-shrink: 0;
}

.cb-chatbot-close {
    width: 30px;
    height: 30px;
    border: 0;
    border-radius: 10px;
    background: rgba(255,255,255,0.16);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
}

.cb-chatbot-close svg { width: 14px; height: 14px; }

.cb-chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 14px;
    background: linear-gradient(180deg, #fff, #f8fafc);
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.cb-chatbot-empty {
    margin: auto 0;
    text-align: center;
    color: #64748b;
    font-family: var(--cb-sans);
}

.cb-chatbot-empty-icon {
    width: 52px;
    height: 52px;
    border-radius: 18px;
    margin: 0 auto 12px;
    background: rgba(209, 29, 42, 0.08);
    color: #d11d2a;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.cb-chatbot-empty-title {
    margin: 0 0 6px;
    font-family: var(--cb-serif);
    font-size: 18px;
    color: #111827;
}

.cb-chatbot-empty p {
    margin: 0;
    font-size: 13px;
    line-height: 1.65;
}

.cb-chatbot-msg {
    display: flex;
    gap: 8px;
    align-items: flex-end;
}

.cb-chatbot-msg--user { justify-content: flex-end; }
.cb-chatbot-msg--bot { justify-content: flex-start; }

.cb-chatbot-msg-avatar {
    width: 28px;
    height: 28px;
    border-radius: 10px;
    background: rgba(209, 29, 42, 0.08);
    color: #d11d2a;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-family: var(--cb-serif);
    font-size: 11px;
    font-weight: 900;
    flex-shrink: 0;
}

.cb-chatbot-bubble {
    max-width: 78%;
    border-radius: 18px;
    padding: 11px 13px;
    font-family: var(--cb-sans);
    font-size: 13px;
    line-height: 1.6;
    word-break: break-word;
    white-space: pre-wrap;
}

.cb-chatbot-bubble--bot {
    background: #fff;
    border: 1px solid #e2e8f0;
    color: #111827;
    border-bottom-left-radius: 6px;
}

.cb-chatbot-bubble--user {
    background: linear-gradient(135deg, #d11d2a, #b81420);
    color: #fff;
    border-bottom-right-radius: 6px;
}

.cb-chatbot-typing {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #64748b;
    font-size: 12px;
}

.cb-chatbot-dots { display: inline-flex; gap: 4px; }

.cb-chatbot-dots span {
    width: 6px;
    height: 6px;
    border-radius: 999px;
    background: #d11d2a;
    opacity: 0.6;
    animation: cb-dot 1.2s infinite ease-in-out;
}

.cb-chatbot-dots span:nth-child(2) { animation-delay: 0.15s; }
.cb-chatbot-dots span:nth-child(3) { animation-delay: 0.3s; }

@keyframes cb-dot {
    0%, 80%, 100% { transform: translateY(0); opacity: 0.45; }
    40% { transform: translateY(-5px); opacity: 1; }
}

.cb-chatbot-suggestions {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 10px;
}

.cb-chatbot-suggestion {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    text-decoration: none;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 10px 12px;
    background: #fff;
    color: #111827;
}

.cb-chatbot-suggestion-title {
    flex: 1;
    font-size: 12px;
    font-weight: 600;
    line-height: 1.35;
}

.cb-chatbot-suggestion-price {
    white-space: nowrap;
    font-size: 13px;
    font-weight: 700;
    color: #d11d2a;
}

.cb-chatbot-form {
    padding: 12px;
    border-top: 1px solid #e2e8f0;
    background: #fff;
}

.cb-chatbot-input-row {
    display: flex;
    gap: 8px;
    align-items: flex-end;
}

.cb-chatbot-input {
    flex: 1;
    min-height: 46px;
    max-height: 120px;
    resize: none;
    border: 1px solid #cbd5e1;
    border-radius: 14px;
    padding: 12px 14px;
    outline: none;
    font-family: var(--cb-sans);
    font-size: 13px;
    line-height: 1.45;
    color: #111827;
}

.cb-chatbot-input:focus {
    border-color: #d11d2a;
    box-shadow: 0 0 0 3px rgba(209, 29, 42, 0.08);
}

.cb-chatbot-send {
    width: 46px;
    height: 46px;
    border-radius: 14px;
    border: 0;
    background: linear-gradient(135deg, #d11d2a, #b81420);
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    box-shadow: 0 10px 20px rgba(209, 29, 42, 0.22);
}

.cb-chatbot-send svg {
    width: 17px;
    height: 17px;
}

.cb-chatbot-hint {
    margin: 8px 0 0;
    color: #94a3b8;
    font-family: var(--cb-sans);
    font-size: 11px;
    line-height: 1.45;
}

@media (max-width: 640px) {
    .cb-chatbot-shell {
        right: 12px;
        bottom: 12px;
        gap: 10px;
    }

    .cb-chatbot-pill {
        display: none;
    }

    .cb-chatbot-panel {
        width: calc(100vw - 16px);
        height: min(74vh, 520px);
    }

    .cb-chatbot-bubble {
        max-width: 84%;
    }
}
</style>

<div class="cb-chatbot-shell"
     data-chatbot
     data-chatbot-session-url="{{ $chatbotSessionUrl }}"
     data-chatbot-message-url="{{ $chatbotMessageUrl }}">
    <div class="cb-chatbot-panel" data-chatbot-panel aria-hidden="true" role="dialog" aria-label="CatBook AI Assistant">
        <div class="cb-chatbot-head">
            <div class="cb-chatbot-head-row">
                <div class="cb-chatbot-head-left">
                    <div class="cb-chatbot-avatar" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="cb-chatbot-title">ChatBotAI CatBook</h3>
                        <p class="cb-chatbot-subtitle"><span class="cb-chatbot-online-dot"></span> CatBook đang online</p>
                    </div>
                </div>
                <button type="button" class="cb-chatbot-close" data-chatbot-close aria-label="Đóng chat">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <div class="cb-chatbot-messages" data-chatbot-messages>
            <div class="cb-chatbot-empty" data-chatbot-empty>
                <div class="cb-chatbot-empty-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"></path>
                    </svg>
                </div>
                <p class="cb-chatbot-empty-title">Xin chào! Mình là trợ lý CatBook</p>
                <p>Mình có thể gợi ý sách, tìm sách theo nhu cầu và hỗ trợ bạn trong lúc mua hàng.</p>
            </div>
        </div>

        <form class="cb-chatbot-form" data-chatbot-form>
            <div class="cb-chatbot-input-row">
                <textarea class="cb-chatbot-input" data-chatbot-input rows="1" placeholder="Nhập tin nhắn..." aria-label="Nhập tin nhắn"></textarea>
                <button type="submit" class="cb-chatbot-send" data-chatbot-send aria-label="Gửi tin nhắn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M22 2L11 13"></path>
                        <path d="M22 2L15 22l-4-9-9-4 20-7z"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <button id="chat-fab" class="cb-chat-fab" type="button" data-chatbot-toggle title="Mở trợ lý AI" aria-label="Mở chatbot">
        <span class="cb-chat-fab-tooltip">ChatBotAI CatBook</span>
        <svg width="24" height="24" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"></path>
        </svg>
        <span class="cb-chat-fab-dot"></span>
    </button>
</div>

@push('scripts')
<script>
(() => {
    const root = document.querySelector('[data-chatbot]');
    if (!root) return;

    const sessionUrl = root.dataset.chatbotSessionUrl;
    const messageUrl = root.dataset.chatbotMessageUrl;
    const panel = root.querySelector('[data-chatbot-panel]');
    const toggle = root.querySelector('[data-chatbot-toggle]');
    const pill = root.querySelector('[data-open-chat]');
    const closeBtn = root.querySelector('[data-chatbot-close]');
    const messagesEl = root.querySelector('[data-chatbot-messages]');
    const emptyEl = root.querySelector('[data-chatbot-empty]');
    const form = root.querySelector('[data-chatbot-form]');
    const input = root.querySelector('[data-chatbot-input]');
    const sendBtn = root.querySelector('[data-chatbot-send]');
    const storageKey = 'catbook.chatbot.sessionToken';
    let busy = false;

    const getToken = () => {
        let token = localStorage.getItem(storageKey);
        if (!token) {
            token = window.crypto?.randomUUID ? window.crypto.randomUUID() : `cb-${Date.now()}-${Math.random().toString(16).slice(2)}`;
            localStorage.setItem(storageKey, token);
        }
        return token;
    };

    const escapeHtml = (value) => String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const scrollToBottom = () => requestAnimationFrame(() => {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    });

    const setOpen = (open) => {
        panel.classList.toggle('open', open);
        panel.setAttribute('aria-hidden', open ? 'false' : 'true');
        if (open) {
            loadHistory();
            setTimeout(() => input?.focus(), 100);
        }
    };

    const setBusy = (state) => {
        busy = state;
        if (sendBtn) sendBtn.disabled = state;
        if (input) input.disabled = state;
    };

    const renderMessage = (message) => {
        const isUser = message.sender_type === 'user';
        const row = document.createElement('div');
        row.className = `cb-chatbot-msg cb-chatbot-msg--${isUser ? 'user' : 'bot'}`;

        let html = '';
        if (!isUser) {
            html += '<div class="cb-chatbot-msg-avatar">AI</div>';
        }

        html += `<div class="cb-chatbot-bubble cb-chatbot-bubble--${isUser ? 'user' : 'bot'}">${escapeHtml(message.message_text).replaceAll('\n', '<br>')}</div>`;
        row.innerHTML = html;
        messagesEl.appendChild(row);
        emptyEl?.remove();
        scrollToBottom();
        return row;
    };

    const showTyping = () => {
        const row = document.createElement('div');
        row.className = 'cb-chatbot-msg cb-chatbot-msg--bot';
        row.dataset.typing = '1';
        row.innerHTML = '<div class="cb-chatbot-msg-avatar">AI</div><div class="cb-chatbot-bubble cb-chatbot-bubble--bot"><div class="cb-chatbot-typing">Đang soạn <span class="cb-chatbot-dots"><span></span><span></span><span></span></span></div></div>';
        messagesEl.appendChild(row);
        scrollToBottom();
        return row;
    };

    const loadHistory = async () => {
        if (messagesEl.dataset.loaded === '1') return;

        try {
            const response = await fetch(`${sessionUrl}?session_token=${encodeURIComponent(getToken())}`, {
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) return;

            const payload = await response.json();
            if (Array.isArray(payload.messages) && payload.messages.length) {
                messagesEl.innerHTML = '';
                payload.messages.forEach((message) => renderMessage(message));
            }

            messagesEl.dataset.loaded = '1';
        } catch (error) {
            console.error('Chatbot history error', error);
        }
    };

    const sendMessage = async (text) => {
        const content = String(text || '').trim();
        if (!content || busy) return;

        renderMessage({ sender_type: 'user', message_text: content });
        input.value = '';
        input.style.height = '46px';
        setBusy(true);
        const typingEl = showTyping();

        try {
            const response = await fetch(messageUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({
                    session_token: getToken(),
                    message: content,
                }),
            });

            const payload = await response.json();
            typingEl.remove();

            if (!response.ok || !payload.ok) {
                renderMessage({ sender_type: 'bot', message_text: payload?.message || 'Xin lỗi, hiện tôi chưa phản hồi được. Vui lòng thử lại.' });
                return;
            }

            if (payload.session_token) {
                localStorage.setItem(storageKey, payload.session_token);
            }

            const reply = Array.isArray(payload.messages) && payload.messages.length
                ? payload.messages[payload.messages.length - 1]
                : { sender_type: 'bot', message_text: payload.reply || 'Đã xử lý yêu cầu.' };

            renderMessage(reply);
            messagesEl.dataset.loaded = '1';
        } catch (error) {
            console.error('Chatbot send error', error);
            typingEl.remove();
            renderMessage({ sender_type: 'bot', message_text: 'Lỗi kết nối. Vui lòng thử lại sau.' });
        } finally {
            setBusy(false);
            input.focus();
        }
    };

    toggle?.addEventListener('click', () => setOpen(!panel.classList.contains('open')));
    pill?.addEventListener('click', () => setOpen(true));
    closeBtn?.addEventListener('click', () => setOpen(false));

    form?.addEventListener('submit', (event) => {
        event.preventDefault();
        sendMessage(input.value);
    });

    input?.addEventListener('input', () => {
        input.style.height = '46px';
        input.style.height = Math.min(input.scrollHeight, 120) + 'px';
    });

    input?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            form?.requestSubmit();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    });
})();
</script>
@endpush