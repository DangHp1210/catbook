@php
    $user = auth()->user();
    if (!$user) return;

    $items = $user->notifications()
        ->latest()
        ->limit(10)
        ->get()
        ->map(function ($notification) {
            $data = is_array($notification->data) ? $notification->data : (array) $notification->data;
            $role = auth()->user()?->role ?? 'customer';
            $roleOrdersUrl = $role === 'staff'
                ? route('staff.orders.index')
                : ($role === 'admin' ? route('admin.orders.index') : route('orders.index'));

            return [
                'id'         => $notification->id,
                'type'       => $data['type'] ?? 'order',
                'title'      => $data['title'] ?? 'Thông báo mới',
                'message'    => $data['message'] ?? '',
                'time'       => optional($notification->created_at)->diffForHumans() ?? 'Vừa xong',
                'unread'     => is_null($notification->read_at),
                'url'        => !empty($data['url'])
                    ? $data['url']
                    : ((($data['type'] ?? 'order') === 'order' && !empty($data['order_code']))
                        ? $roleOrdersUrl . '?open=' . urlencode((string) $data['order_code'])
                        : $roleOrdersUrl),
                'order_code' => $data['order_code'] ?? null,
                'open_url'   => route('notifications.open', $notification->id),
            ];
        })
        ->all();

    $unreadCount = $user->unreadNotifications()->count();
@endphp

<style>
/* ─── Notification bell ───────────────────────────────── */
:root {
    --cb-bg:           #f8f6f1;
    --cb-border:       #e8e3d8;
    --cb-text:         #1a1a1a;
    --cb-muted:        #777;
    --cb-white:        #ffffff;
    --cb-accent:       #2d6a4f;
    --cb-accent-dark:  #1b4332;
    --cb-accent-light: #d8f3dc;
    --cb-serif:        'Playfair Display', Georgia, serif;
    --cb-sans:         'DM Sans', system-ui, sans-serif;
}

.rn-wrap {
    position: relative;
    display: inline-flex; align-items: center;
    z-index: 45;
}

/* ─── Bell button ─────────────────────────────────────── */
.rn-bell {
    position: relative;
    width: 38px; height: 38px; border-radius: 50%;
    border: 1.5px solid #a0a1a0;
    background: transparent; color: var(--cb-text);
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; transition: border-color .18s, transform .18s;
    flex-shrink: 0;
}
.rn-bell:hover {
    border-color: var(--cb-accent);
    transform: translateY(-1px);
}
.rn-bell svg { width: 20px; height: 20px; }

/* Badge */
.rn-badge {
    position: absolute; top: -5px; right: -5px;
    background: #dc2626; color: #fff;
    min-width: 18px; height: 18px; border-radius: 999px;
    font-family: var(--cb-sans); font-size: 10px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    padding: 0 4px; border: 2px solid var(--cb-bg);
    animation: rn-pop .3s ease;
}
@keyframes rn-pop {
    0%  { transform: scale(0); }
    70% { transform: scale(1.2); }
    100%{ transform: scale(1); }
}

/* ─── Dropdown panel ──────────────────────────────────── */
.rn-panel {
    position: absolute; top: calc(100% + 10px); right: 0;
    width: min(380px, calc(100vw - 24px));
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 20px; box-shadow: 0 20px 56px rgba(0,0,0,.13);
    overflow: hidden;
    opacity: 0; visibility: hidden;
    transform: translateY(-8px) scale(.97);
    transition: opacity .18s, transform .18s, visibility .18s;
    pointer-events: none;
}
.rn-wrap.is-open .rn-panel {
    opacity: 1; visibility: visible;
    transform: translateY(0) scale(1);
    pointer-events: auto;
}

/* Panel header */
.rn-head {
    padding: 16px 18px 12px;
    border-bottom: 1px solid var(--cb-border);
    display: flex; align-items: flex-end; justify-content: space-between;
    position: relative;
}
.rn-head::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--cb-accent), #4ade80);
}
.rn-head-left {}
.rn-head-title {
    font-family: var(--cb-serif); font-size: 17px; font-weight: 700;
    color: var(--cb-text); margin: 0;
}
.rn-head-sub {
    font-family: var(--cb-sans); font-size: 11px; color: var(--cb-muted);
    margin: 3px 0 0;
}
.rn-head-count {
    font-family: var(--cb-sans); font-size: 11px; font-weight: 700;
    padding: 3px 10px; border-radius: 999px;
    background: #fff1f2; color: #dc2626; border: 1px solid #fecdd3;
    flex-shrink: 0; white-space: nowrap;
}

/* Scrollable body */
.rn-body {
    max-height: 340px; overflow-y: auto;
    padding: 8px;
    scrollbar-width: thin; scrollbar-color: var(--cb-border) transparent;
}
.rn-body::-webkit-scrollbar { width: 4px; }
.rn-body::-webkit-scrollbar-thumb { background: var(--cb-border); border-radius: 999px; }

/* Notification list */
.rn-list { display: flex; flex-direction: column; gap: 4px; }

/* Single item */
.rn-item {
    display: flex; gap: 11px; align-items: flex-start;
    padding: 11px 12px; border-radius: 12px;
    border: 1px solid transparent;
    text-decoration: none;
    transition: background .15s, border-color .15s, transform .15s;
    cursor: pointer;
}
.rn-item:hover {
    background: var(--cb-bg); border-color: var(--cb-border);
    transform: translateY(-1px);
}
.rn-item.is-unread {
    background: #f0fdf4; border-color: #86efac;
}
.rn-item.is-unread:hover { background: #dcfce7; }

/* Icon */
.rn-icon {
    width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    color: #fff;
}
.rn-icon--order   { background: linear-gradient(135deg, var(--cb-accent), var(--cb-accent-dark)); }
.rn-icon--message { background: linear-gradient(135deg, #6366f1, #4f46e5); }
.rn-icon--promo   { background: linear-gradient(135deg, #f59e0b, #d97706); }
.rn-icon--default { background: linear-gradient(135deg, #0ea5e9, #0284c7); }
.rn-icon svg { width: 16px; height: 16px; }

/* Text */
.rn-content { flex: 1; min-width: 0; }
.rn-item-title {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 700;
    color: var(--cb-text); line-height: 1.35; margin: 0;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.rn-item-msg {
    font-family: var(--cb-sans); font-size: 11px; color: var(--cb-muted);
    margin: 3px 0 0; line-height: 1.45;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    overflow: hidden;
}
.rn-item-meta {
    display: flex; align-items: center; gap: 5px; margin-top: 5px;
    font-family: var(--cb-sans); font-size: 10px; color: #b0a898;
}
.rn-meta-dot {
    width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0;
}
.rn-meta-dot--unread { background: var(--cb-accent); }
.rn-meta-dot--read   { background: #c4b9aa; }

/* Unread indicator on right */
.rn-unread-pip {
    width: 7px; height: 7px; border-radius: 50%;
    background: var(--cb-accent); flex-shrink: 0; margin-top: 4px;
}

/* Empty state */
.rn-empty {
    padding: 36px 16px; text-align: center;
}
.rn-empty-icon {
    width: 48px; height: 48px; border-radius: 50%;
    background: var(--cb-bg); border: 1px solid var(--cb-border);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 12px; color: #c4b9aa;
}
.rn-empty-text {
    font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted);
}

/* Panel footer */
.rn-foot {
    padding: 10px 12px 14px;
    border-top: 1px solid var(--cb-border);
    display: flex; gap: 8px;
}
.rn-foot-mark {
    flex: 1; font-family: var(--cb-sans); font-size: 12px; font-weight: 500;
    padding: 9px 12px; border-radius: 9px;
    border: 1.5px solid var(--cb-border); background: transparent;
    color: var(--cb-muted); cursor: pointer; text-align: center;
    transition: border-color .15s, color .15s;
}
.rn-foot-mark:hover { border-color: var(--cb-accent); color: var(--cb-accent); }
.rn-foot-all {
    flex: 1; font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    padding: 9px 12px; border-radius: 9px; border: none;
    background: var(--cb-text); color: #fff; text-decoration: none;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: background .15s;
}
.rn-foot-all:hover { background: var(--cb-accent); }

@media (max-width: 640px) { .rn-wrap { display: none; } }
</style>

<div class="rn-wrap" id="rn-wrap">

    {{-- Bell trigger --}}
    <button type="button" class="rn-bell" id="rn-bell"
            aria-label="Thông báo" aria-haspopup="dialog" aria-expanded="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2.9-.6 1.3L4 17h5"/>
            <path d="M9.5 19a2.5 2.5 0 005 0"/>
        </svg>
        @if($unreadCount > 0)
            <span class="rn-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div class="rn-panel" id="rn-panel" role="dialog" aria-label="Thông báo">

        {{-- Head --}}
        <div class="rn-head">
            <div class="rn-head-left">
                <h3 class="rn-head-title">Thông báo</h3>
                <p class="rn-head-sub">
                    @if($unreadCount > 0)
                        {{ $unreadCount }} thông báo chưa đọc
                    @else
                        Tất cả đã được đọc
                    @endif
                </p>
            </div>
            @if($unreadCount > 0)
                <span class="rn-head-count">{{ $unreadCount }} mới</span>
            @endif
        </div>

        {{-- Body --}}
        <div class="rn-body">
            @if(count($items) > 0)
                <div class="rn-list">
                    @foreach($items as $notif)
                        @php
                            $iconClass = match($notif['type']) {
                                'order'   => 'rn-icon--order',
                                'message' => 'rn-icon--message',
                                'promo', 'promotion' => 'rn-icon--promo',
                                default   => 'rn-icon--default',
                            };
                        @endphp
                        <a href="{{ $notif['open_url'] }}"
                           class="rn-item {{ $notif['unread'] ? 'is-unread' : '' }}">

                            {{-- Icon --}}
                            <div class="rn-icon {{ $iconClass }}" aria-hidden="true">
                                @if($notif['type'] === 'order')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                                        <line x1="3" y1="6" x2="21" y2="6"/>
                                        <path d="M16 10a4 4 0 01-8 0"/>
                                    </svg>
                                @elseif($notif['type'] === 'message')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                                    </svg>
                                @elseif(in_array($notif['type'], ['promo','promotion']))
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                @else
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                    </svg>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="rn-content">
                                <p class="rn-item-title">{{ $notif['title'] }}</p>
                                @if($notif['message'])
                                    <p class="rn-item-msg">{{ $notif['message'] }}</p>
                                @endif
                                <div class="rn-item-meta">
                                    <span class="rn-meta-dot {{ $notif['unread'] ? 'rn-meta-dot--unread' : 'rn-meta-dot--read' }}"></span>
                                    <span>{{ match($notif['type']) {
                                        'order'   => 'Đơn hàng',
                                        'message' => 'Tin nhắn',
                                        'promo','promotion' => 'Khuyến mãi',
                                        default   => 'Thông báo',
                                    } }}</span>
                                    <span>·</span>
                                    <span>{{ $notif['time'] }}</span>
                                </div>
                            </div>

                            {{-- Unread pip --}}
                            @if($notif['unread'])
                                <span class="rn-unread-pip" aria-hidden="true"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            @else
                <div class="rn-empty">
                    <div class="rn-empty-icon">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2.9-.6 1.3L4 17h5"/>
                            <path d="M9.5 19a2.5 2.5 0 005 0"/>
                        </svg>
                    </div>
                    <p class="rn-empty-text">Chưa có thông báo nào</p>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="rn-foot">
            <form method="POST" action="{{ route('notifications.mark_all') }}" style="flex:1;margin:0">
                @csrf
                <button type="submit" class="rn-foot-mark">
                    ✓ Đánh dấu đã đọc
                </button>
            </form>
        </div>

    </div>{{-- /.rn-panel --}}
</div>{{-- /.rn-wrap --}}

<script>
(function () {
    const wrap  = document.getElementById('rn-wrap');
    const bell  = document.getElementById('rn-bell');
    if (!wrap || !bell) return;

    const open  = () => { wrap.classList.add('is-open');  bell.setAttribute('aria-expanded', 'true'); };
    const close = () => { wrap.classList.remove('is-open'); bell.setAttribute('aria-expanded', 'false'); };
    const toggle= () => wrap.classList.contains('is-open') ? close() : open();

    bell.addEventListener('click', (e) => { e.stopPropagation(); toggle(); });

    document.addEventListener('click', (e) => {
        if (!wrap.contains(e.target)) close();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
    });
})();
</script>