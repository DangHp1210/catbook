@php
    $chatbotSessionUrl = route('chatbot.session');
    $chatbotMessageUrl = route('chatbot.message');
@endphp

<style>
/* ─── Shell ───────────────────────────────────────────── */
.cb-chatbot-shell {
    position: fixed; right: 0px; bottom: 0px;
    z-index: 140;
    display: flex; align-items: flex-end; gap: 0;
    max-width: calc(100vw - 24px);
}

/* ─── FAB button ──────────────────────────────────────── */
.cb-chat-fab {
    position: relative;
    width: 58px; height: 58px; border-radius: 50%; border: none;
    cursor: pointer; flex-shrink: 0;
    display: inline-flex; align-items: center; justify-content: center;
    background: #ffffff; 
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    transition: transform .2s, box-shadow .2s;
    animation: cb-fab-float 3s ease-in-out infinite;
}
.cb-chat-fab:hover {
   transform: scale(1.08);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.16); 
    background: #ffffff !important; 
    animation: none;
}
@keyframes cb-fab-float {
    0%,100%{ transform:translateY(0); }
    50%     { transform:translateY(-6px); }
}
.cb-chat-fab-dot {
    position: absolute; top: 4px; right: 4px;
    width: 12px; height: 12px; border-radius: 50%;
    background: #007f00; border: 2px solid #fff;
    animation: cb-fab-pulse 2s ease-in-out infinite;
}
@keyframes cb-fab-pulse {
    0%,100%{ opacity:1; transform:scale(1); }
    50%     { opacity:.6; transform:scale(1.3); }
}
.cb-chat-fab-tooltip {
    position: absolute; right: 68px; top: 50%; transform: translateY(-50%);
    background: #0d1b10; color: #fff;
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    padding: 7px 14px; border-radius: 999px; white-space: nowrap;
    opacity: 0; pointer-events: none; transition: opacity .18s;
    box-shadow: 0 6px 18px rgba(0,0,0,.18);
}
.cb-chat-fab:hover .cb-chat-fab-tooltip { opacity: 1; }

/* ─── Chat panel ──────────────────────────────────────── */
.cb-chatbot-panel {
    position: absolute; right: 24px; bottom: calc(100% + 28px);
    width: min(320px, calc(100vw - 24px));
    height: min(480px, calc(100vh - 100px));
    border-radius: 20px; overflow: hidden;
    background: var(--cb-white); border: 1px solid var(--cb-border);
    box-shadow: 0 24px 64px rgba(13,27,16,.18);
    display: flex; flex-direction: column;
    opacity: 0; visibility: hidden;
    transform: translateY(12px) scale(.97);
    transform-origin: bottom right; pointer-events: none;
    transition: opacity .2s ease, transform .2s ease, visibility .2s ease;
}
.cb-chatbot-panel.open {
    opacity: 1; visibility: visible;
    transform: translateY(0) scale(1); pointer-events: auto;
}

/* ─── Panel header ────────────────────────────────────── */
.cb-chatbot-head {
    padding: 14px 16px 12px; border-bottom: 1px solid var(--cb-border);
    background: var(--cb-white); position: relative; flex-shrink: 0;
}
.cb-chatbot-head::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--cb-accent), #4ade80);
}
.cb-chatbot-head-row {
    display: flex; align-items: center; justify-content: space-between; gap: 12px;
}
.cb-chatbot-head-left { display: flex; align-items: center; gap: 10px; min-width: 0; }
.cb-chatbot-avatar {
    width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
    background: var(--cb-accent-light); color: var(--cb-accent);
    display: flex; align-items: center; justify-content: center;
}
.cb-chatbot-avatar svg { width: 20px; height: 20px; }
.cb-chatbot-title {
    font-family: var(--cb-serif); font-size: 16px; font-weight: 700;
    color: var(--cb-text); margin: 0; line-height: 1.2;
}
.cb-chatbot-subtitle {
    font-family: var(--cb-sans); font-size: 11px; color: var(--cb-muted);
    margin: 3px 0 0; display: flex; align-items: center; gap: 5px;
}
.cb-chatbot-online-dot {
    width: 6px; height: 6px; border-radius: 50%; background: #4ade80; flex-shrink: 0;
    animation: cb-fab-pulse 2s ease-in-out infinite;
}
.cb-chatbot-close {
    width: 30px; height: 30px; border-radius: 8px; flex-shrink: 0;
    border: 1.5px solid var(--cb-border); background: transparent;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: var(--cb-muted); transition: all .15s;
}
.cb-chatbot-close:hover { border-color: var(--cb-text); color: var(--cb-text); }
.cb-chatbot-close svg { width: 14px; height: 14px; }

/* ─── Messages area ───────────────────────────────────── */
.cb-chatbot-messages {
    flex: 1; overflow-y: auto; padding: 12px;
    display: flex; flex-direction: column; gap: 10px;
    background: var(--cb-bg);
    scrollbar-width: thin; scrollbar-color: var(--cb-border) transparent;
}
.cb-chatbot-messages::-webkit-scrollbar { width: 4px; }
.cb-chatbot-messages::-webkit-scrollbar-thumb { background: var(--cb-border); border-radius: 999px; }

/* Empty / welcome state */
.cb-chatbot-empty {
    margin: auto 0; text-align: center;
    font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted);
    padding: 16px 8px; line-height: 1.6;
}
.cb-chatbot-empty-icon {
    width: 48px; height: 48px; border-radius: 14px; margin: 0 auto 12px;
    background: var(--cb-accent-light); color: var(--cb-accent);
    display: flex; align-items: center; justify-content: center;
}
.cb-chatbot-empty-title {
    font-family: var(--cb-serif); font-size: 17px; font-weight: 700;
    color: var(--cb-text); margin: 0 0 6px;
}

/* Quick chips */
.cb-bot-chips { display: flex; flex-wrap: wrap; gap: 6px; justify-content: center; margin-top: 14px; }
.cb-bot-chip {
    font-family: var(--cb-sans); font-size: 11px; font-weight: 500;
    padding: 5px 12px; border-radius: 999px;
    border: 1.5px solid var(--cb-border); background: var(--cb-white);
    color: var(--cb-text); cursor: pointer; transition: all .15s;
}
.cb-bot-chip:hover { border-color: var(--cb-accent); color: var(--cb-accent); background: var(--cb-accent-light); }

/* ─── Message rows ────────────────────────────────────── */
.cb-chatbot-msg { display: flex; gap: 8px; align-items: flex-end; }
.cb-chatbot-msg--user { justify-content: flex-end; }
.cb-chatbot-msg--bot  { justify-content: flex-start; }

.cb-chatbot-msg-avatar {
    width: 26px; height: 26px; border-radius: 8px; flex-shrink: 0; margin-bottom: 2px;
    background: var(--cb-accent-light); color: var(--cb-accent);
    display: flex; align-items: center; justify-content: center;
    font-family: var(--cb-serif); font-size: 10px; font-weight: 900;
}

.cb-chatbot-bubble {
    max-width: 80%; 
    width: fit-content; 
    border-radius: 16px; 
    padding: 10px 14px;
    font-family: var(--cb-sans); 
    font-size: 13px;
    line-height: 1.5; 
    word-break: break-word;
    text-align: left;
}
.cb-chatbot-bubble--bot {
    background: var(--cb-white); color: var(--cb-text);
    border: 1px solid var(--cb-border); border-bottom-left-radius: 4px;
}
.cb-chatbot-bubble--user {
    background: var(--cb-accent); color: #fff;
    border-bottom-right-radius: 4px;
}

/* Typing indicator */
.cb-chatbot-typing {
    display: inline-flex; align-items: center; gap: 8px;
    font-family: var(--cb-sans); font-size: 12px; color: var(--cb-muted);
}
.cb-chatbot-dots { display: inline-flex; gap: 4px; }
.cb-chatbot-dots span {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--cb-accent); opacity: .5;
    animation: cb-dot-bounce 1.2s infinite ease-in-out;
}
.cb-chatbot-dots span:nth-child(2) { animation-delay: .15s; }
.cb-chatbot-dots span:nth-child(3) { animation-delay: .3s; }
@keyframes cb-dot-bounce {
    0%,80%,100%{ transform:translateY(0); opacity:.45; }
    40%         { transform:translateY(-5px); opacity:1; }
}

/* Book suggestion cards */
.cb-chatbot-suggestions { display: flex; flex-direction: column; gap: 6px; margin-top: 10px; }
.cb-chatbot-suggestion {
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
    text-decoration: none; border: 1.5px solid var(--cb-border); border-radius: 10px;
    padding: 9px 12px; background: var(--cb-white); color: var(--cb-text);
    transition: border-color .15s, color .15s;
}
.cb-chatbot-suggestion:hover { border-color: var(--cb-accent); color: var(--cb-accent); }
.cb-chatbot-suggestion-title { flex: 1; font-size: 12px; font-weight: 600; line-height: 1.35; }
.cb-chatbot-suggestion-price {
    white-space: nowrap; font-family: var(--cb-serif);
    font-size: 13px; font-weight: 700; color: var(--cb-accent); flex-shrink: 0;
}

/* ─── Input form ──────────────────────────────────────── */
.cb-chatbot-form {
    padding: 10px 12px 12px; border-top: 1px solid var(--cb-border);
    background: var(--cb-white); flex-shrink: 0;
}
.cb-chatbot-input-row { display: flex; gap: 8px; align-items: flex-end; }
.cb-chatbot-input {
    flex: 1; min-height: 44px; max-height: 120px; resize: none;
    border: 1.5px solid var(--cb-border); border-radius: 12px;
    padding: 11px 14px; outline: none;
    font-family: var(--cb-sans); font-size: 13px; line-height: 1.45;
    color: var(--cb-text); background: var(--cb-white);
    transition: border-color .2s, box-shadow .2s;
}
.cb-chatbot-input::placeholder { color: #c0b8b0; }
.cb-chatbot-input:focus {
    border-color: var(--cb-accent);
    box-shadow: 0 0 0 3px rgba(45,106,79,.09);
}
.cb-chatbot-input:disabled { opacity: .65; cursor: not-allowed; }

.cb-chatbot-send {
    width: 44px; height: 44px; border-radius: 12px; border: none; flex-shrink: 0;
    background: var(--cb-text); color: #fff;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: background .2s, transform .15s;
}
.cb-chatbot-send:hover { background: var(--cb-accent); transform: translateY(-1px); }
.cb-chatbot-send:disabled { opacity: .55; cursor: not-allowed; transform: none; }
.cb-chatbot-send svg { width: 17px; height: 17px; }

.cb-chatbot-hint {
    margin: 7px 0 0; font-family: var(--cb-sans);
    font-size: 11px; color: #b0a898; line-height: 1.5;
}

/* ─── Responsive ──────────────────────────────────────── */
@media (max-width: 640px) {
    .cb-chatbot-shell { right: 14px; bottom: 14px; }
    .cb-chat-fab { width: 52px; height: 52px; }
    .cb-chatbot-panel { width: calc(100vw - 20px); height: min(74vh, 540px); }
    .cb-chatbot-bubble { max-width: 85%; }
}
</style>

<div class="cb-chatbot-shell"
     data-chatbot
     data-chatbot-session-url="{{ $chatbotSessionUrl }}"
     data-chatbot-message-url="{{ $chatbotMessageUrl }}">

    {{-- ── Chat panel ──────────────────────────────────── --}}
    <div class="cb-chatbot-panel" data-chatbot-panel
         aria-hidden="true" role="dialog" aria-label="CatBook AI Assistant">

        {{-- Header --}}
        <div class="cb-chatbot-head">
            <div class="cb-chatbot-head-row">
                <div class="cb-chatbot-head-left">
                    <div class="cb-chatbot-avatar" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="cb-chatbot-title">CatBook AI</h3>
                        <p class="cb-chatbot-subtitle">
                            <span class="cb-chatbot-online-dot"></span>
                            Sẵn sàng hỗ trợ
                        </p>
                    </div>
                </div>
                <button type="button" class="cb-chatbot-close"
                        data-chatbot-close aria-label="Đóng chat">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Messages --}}
        <div class="cb-chatbot-messages" data-chatbot-messages>
            <div class="cb-chatbot-empty" data-chatbot-empty>
                <div class="cb-chatbot-empty-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                    </svg>
                </div>
                <p class="cb-chatbot-empty-title">Xin chào! Mình là CatBook AI</p>
                <p>Mình có thể gợi ý sách, tìm sách theo ngân sách và hỗ trợ bạn trong lúc mua hàng.</p>
                <div class="cb-bot-chips">
                    <button type="button" class="cb-bot-chip" data-quick="Gợi ý sách hay dưới 150k">Sách dưới 150k</button>
                    <button type="button" class="cb-bot-chip" data-quick="Sách tâm lý học bán chạy">Tâm lý học</button>
                    <button type="button" class="cb-bot-chip" data-quick="Sách kinh doanh cho người mới">Kinh doanh</button>
                    <button type="button" class="cb-bot-chip" data-quick="Kiểm tra đơn hàng của tôi">Tra đơn hàng</button>
                </div>
            </div>
        </div>

        {{-- Input form --}}
        <form class="cb-chatbot-form" data-chatbot-form>
            <div class="cb-chatbot-input-row">
                <textarea class="cb-chatbot-input"
                          data-chatbot-input rows="1"
                          placeholder="Ví dụ: gợi ý sách tâm lý dưới 200k..."
                          aria-label="Nhập tin nhắn"></textarea>
                <button type="submit" class="cb-chatbot-send"
                        data-chatbot-send aria-label="Gửi tin nhắn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M22 2L11 13"/>
                        <path d="M22 2L15 22l-4-9-9-4 20-7z"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    {{-- ── FAB button ──────────────────────────────────── --}}
    <button id="chat-fab" class="cb-chat-fab" type="button"
            data-chatbot-toggle
            title="Mở trợ lý AI" aria-label="Mở chatbot">
        <span class="cb-chat-fab-tooltip">CatBook AI</span>
        <img src="{{ asset('images/botcat.png') }}" alt="Bot" style="width: 46px; height: 46px; object-fit: contain;">
        
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
    const panel      = root.querySelector('[data-chatbot-panel]');
    const toggle     = root.querySelector('[data-chatbot-toggle]');
    const closeBtn   = root.querySelector('[data-chatbot-close]');
    const messagesEl = root.querySelector('[data-chatbot-messages]');
    const emptyEl    = root.querySelector('[data-chatbot-empty]');
    const form       = root.querySelector('[data-chatbot-form]');
    const input      = root.querySelector('[data-chatbot-input]');
    const sendBtn    = root.querySelector('[data-chatbot-send]');
    const storageKey = 'catbook.chatbot.sessionToken';
    let busy = false;

    /* ── Helpers ── */
    const getToken = () => {
        let t = localStorage.getItem(storageKey);
        if (!t) {
            t = window.crypto?.randomUUID
                ? window.crypto.randomUUID()
                : `cb-${Date.now()}-${Math.random().toString(16).slice(2)}`;
            localStorage.setItem(storageKey, t);
        }
        return t;
    };
    const esc = v => String(v ?? '')
        .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;').replace(/'/g,'&#039;');
    const money = v => new Intl.NumberFormat('vi-VN').format(Number(v||0)) + 'đ';
    const scrollEnd = () => requestAnimationFrame(() => { messagesEl.scrollTop = messagesEl.scrollHeight; });

    const setOpen = open => {
        panel.classList.toggle('open', open);
        panel.setAttribute('aria-hidden', open ? 'false' : 'true');
        if (open) { loadHistory(); setTimeout(() => input?.focus(), 120); }
    };

    const setBusy = state => {
        busy = state;
        if (sendBtn) sendBtn.disabled = state;
        if (input) input.disabled = state;
    };

    /* ── Render suggestion cards ── */
    const renderSuggestions = (suggestions = []) => {
        if (!Array.isArray(suggestions) || !suggestions.length) return '';
        return '<div class="cb-chatbot-suggestions">' +
            suggestions.map(s =>
                `<a class="cb-chatbot-suggestion" href="${esc(s.url||'#')}">
                    <span class="cb-chatbot-suggestion-title">${esc(s.title||'Sách')}</span>
                    <span class="cb-chatbot-suggestion-price">${money(s.price)}</span>
                </a>`
            ).join('') + '</div>';
    };

    /* ── Render message ── */
    const renderMessage = msg => {
        const isUser = msg.sender_type === 'user';
        const row = document.createElement('div');
        row.className = `cb-chatbot-msg cb-chatbot-msg--${isUser ? 'user' : 'bot'}`;

        let html = '';
        if (!isUser) html += '<div class="cb-chatbot-msg-avatar">AI</div>';
        html += `<div class="cb-chatbot-bubble cb-chatbot-bubble--${isUser ? 'user' : 'bot'}">
            ${esc(msg.message_text).replace(/\n/g,'<br>')}
            ${!isUser && Array.isArray(msg.suggestions) && msg.suggestions.length ? renderSuggestions(msg.suggestions) : ''}
        </div>`;

        row.innerHTML = html;
        messagesEl.appendChild(row);
        emptyEl?.remove();
        scrollEnd();
        return row;
    };

    /* ── Typing indicator ── */
    const showTyping = () => {
        const row = document.createElement('div');
        row.className = 'cb-chatbot-msg cb-chatbot-msg--bot';
        row.dataset.typing = '1';
        row.innerHTML = `<div class="cb-chatbot-msg-avatar">AI</div>
            <div class="cb-chatbot-bubble cb-chatbot-bubble--bot">
                <div class="cb-chatbot-typing">
                    Đang soạn
                    <span class="cb-chatbot-dots"><span></span><span></span><span></span></span>
                </div>
            </div>`;
        messagesEl.appendChild(row);
        scrollEnd();
        return row;
    };

    /* ── Load history ── */
    const loadHistory = async () => {
        if (messagesEl.dataset.loaded === '1') return;
        try {
            const res = await fetch(`${sessionUrl}?session_token=${encodeURIComponent(getToken())}`, {
                headers: { Accept: 'application/json' },
            });
            if (!res.ok) return;
            const data = await res.json();
            if (Array.isArray(data.messages) && data.messages.length) {
                messagesEl.innerHTML = '';
                data.messages.forEach(m => renderMessage(m));
            }
            messagesEl.dataset.loaded = '1';
        } catch (e) { console.error('Chatbot history error', e); }
    };

    /* ── Send message ── */
    const sendMessage = async text => {
        const content = String(text || '').trim();
        if (!content || busy) return;
        renderMessage({ sender_type: 'user', message_text: content });
        input.value = '';
        input.style.height = '44px';
        setBusy(true);
        const typingEl = showTyping();
        try {
            const res = await fetch(messageUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({ session_token: getToken(), message: content }),
            });
            const data = await res.json();
            typingEl.remove();
            if (!res.ok || !data.ok) {
                renderMessage({ sender_type: 'bot', message_text: data?.message || 'Xin lỗi, hiện tôi chưa phản hồi được. Vui lòng thử lại.' });
                return;
            }
            if (data.session_token) localStorage.setItem(storageKey, data.session_token);
            const reply = Array.isArray(data.messages) && data.messages.length
                ? data.messages[data.messages.length - 1]
                : { sender_type: 'bot', message_text: data.reply || 'Đã xử lý yêu cầu.' };
            reply.suggestions = data.suggestions || [];
            renderMessage(reply);
            messagesEl.dataset.loaded = '1';
        } catch (e) {
            console.error('Chatbot send error', e);
            typingEl.remove();
            renderMessage({ sender_type: 'bot', message_text: 'Lỗi kết nối. Vui lòng thử lại sau.' });
        } finally {
            setBusy(false);
            input.focus();
        }
    };

    /* ── Events ── */
    toggle?.addEventListener('click', () => setOpen(!panel.classList.contains('open')));
    closeBtn?.addEventListener('click', () => setOpen(false));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') setOpen(false); });

    form?.addEventListener('submit', e => { e.preventDefault(); sendMessage(input.value); });
    input?.addEventListener('input', () => {
        input.style.height = '44px';
        input.style.height = Math.min(input.scrollHeight, 120) + 'px';
    });
    input?.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); form?.requestSubmit(); }
    });

    /* Quick chips */
    root.addEventListener('click', e => {
        const chip = e.target.closest('[data-quick]');
        if (chip) sendMessage(chip.dataset.quick);
    });

    /* External trigger (CTA button on home page) */
    document.addEventListener('click', e => {
        const btn = e.target.closest('#open-chat-btn, [data-open-chat]');
        if (btn && !root.contains(btn)) setOpen(true);
    });
})();
</script>
@endpush