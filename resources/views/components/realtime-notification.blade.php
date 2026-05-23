@props([
    'notifications' => [],
    'unreadCount' => null,
])

@php
    $sampleNotifications = [
        [
            'type' => 'order',
            'title' => 'Đơn hàng #CB1024 đã thanh toán thành công',
            'message' => 'Hệ thống đang chuẩn bị sách để bàn giao cho đơn vị vận chuyển.',
            'time' => '2 phút trước',
            'unread' => true,
        ],
        [
            'type' => 'chatbot',
            'title' => 'Chatbot AI đã tìm thấy 5 sách phù hợp',
            'message' => 'Gợi ý dựa trên lịch sử xem và thể loại bạn quan tâm.',
            'time' => 'Vừa xong',
            'unread' => true,
        ],
        [
            'type' => 'offer',
            'title' => 'Sách mới từ tác giả bạn theo dõi',
            'message' => 'Một đầu sách mới đã được cập nhật vào CatBook hôm nay.',
            'time' => '15 phút trước',
            'unread' => false,
        ],
    ];

    $items = count($notifications) > 0 ? $notifications : $sampleNotifications;
    $effectiveUnreadCount = $unreadCount ?? collect($items)->where('unread', true)->count();
@endphp

<style>
.cb-notify {
    position: relative;
    display: inline-flex;
    align-items: center;
    z-index: 45;
}

.cb-notify-trigger {
    position: relative;
    width: 38px;
    height: 38px;
    border: 1.5px solid #a1a0a0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.11);
    color: var(--cb-brand-text, #1a1a1a);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
    box-shadow: 0 10px 24px rgba(239, 235, 235, 0.06);
}

.cb-notify-trigger:hover {
    transform: translateY(-1px);
    border-color: var(--cb-brand-accent, #2d6a4f);
    box-shadow: 0 14px 28px rgba(26,26,26,.09);
}

.cb-notify-trigger svg {
    width: 22px;
    height: 22px;
}

.cb-notify-badge {
    position: absolute; top: -5px; right: -5px;
    background: var(--cb-brand-accent);
    background: #dc2626;
    color: #fff;
    min-width: 18px; height: 18px;
    border-radius: 999px;
    font-family: var(--cb-font-sans);
    font-size: 10px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    padding: 0 4px;
    border: 2px solid var(--cb-brand-bg);
}
.cb-notify-panel {
    position: absolute;
    top: calc(100% + 12px);
    right: 0;
    width: min(420px, calc(100vw - 24px));
    background: #fff;
    border: 1px solid var(--cb-brand-border, #e8e3d8);
    border-radius: 20px;
    box-shadow: 0 28px 60px rgba(15,23,42,.16);
    overflow: hidden;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-8px) scale(.98);
    transition: opacity .18s ease, transform .18s ease, visibility .18s ease;
}

.cb-notify:hover .cb-notify-panel,
.cb-notify:focus-within .cb-notify-panel,
.cb-notify.is-open .cb-notify-panel {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

.cb-notify-head {
    padding: 18px 18px 14px;
    background: linear-gradient(180deg, #fff, #faf8f3);
    border-bottom: 1px solid var(--cb-brand-border, #e8e3d8);
}

.cb-notify-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
}

.cb-notify-title {
    margin: 0;
    font-family: var(--cb-font-serif, Georgia, serif);
    font-size: 18px;
    font-weight: 800;
    color: var(--cb-brand-text, #1a1a1a);
}

.cb-notify-sub {
    margin: 4px 0 0;
    font-family: var(--cb-font-sans, system-ui, sans-serif);
    font-size: 12px;
    color: var(--cb-brand-muted, #666);
}

.cb-notify-action {
    border: 1px solid var(--cb-brand-border, #e8e3d8);
    background: #fff;
    color: var(--cb-brand-text, #1a1a1a);
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 8px 12px;
    cursor: pointer;
    white-space: nowrap;
}

.cb-notify-tabs {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    padding-bottom: 4px;
    scrollbar-width: none;
}

.cb-notify-tabs::-webkit-scrollbar {
    display: none;
}

.cb-notify-tab {
    border: 1px solid var(--cb-brand-border, #e8e3d8);
    background: #fff;
    color: var(--cb-brand-muted, #666);
    font-size: 12px;
    font-weight: 600;
    border-radius: 999px;
    padding: 7px 12px;
    cursor: pointer;
    flex: 0 0 auto;
}

.cb-notify-tab.is-active {
    background: var(--cb-brand-accent-light, #d8f3dc);
    border-color: #b7ebc4;
    color: var(--cb-brand-accent, #2d6a4f);
}

.cb-notify-body {
    max-height: 360px;
    overflow: auto;
    padding: 10px 10px 12px;
}

.cb-notify-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.cb-notify-item {
    display: flex;
    gap: 12px;
    align-items: flex-start;
    padding: 12px 12px;
    border: 1px solid transparent;
    border-radius: 16px;
    transition: background .18s ease, border-color .18s ease, transform .18s ease;
}

.cb-notify-item:hover {
    background: #faf8f3;
    border-color: var(--cb-brand-border, #e8e3d8);
    transform: translateY(-1px);
}

.cb-notify-item.is-unread {
    background: #f6fbf8;
    border-color: #d8f3dc;
}

.cb-notify-icon {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    box-shadow: 0 10px 24px rgba(0,0,0,.08);
}

.cb-notify-icon--order { background: linear-gradient(135deg, #2d6a4f, #1b4332); }
.cb-notify-icon--chatbot { background: linear-gradient(135deg, #2563eb, #1d4ed8); }
.cb-notify-icon--offer { background: linear-gradient(135deg, #d97706, #f59e0b); }

.cb-notify-icon svg {
    width: 18px;
    height: 18px;
}

.cb-notify-content {
    min-width: 0;
    flex: 1;
}

.cb-notify-item-title {
    margin: 0;
    font-family: var(--cb-font-sans, system-ui, sans-serif);
    font-size: 13px;
    font-weight: 700;
    color: var(--cb-brand-text, #1a1a1a);
    line-height: 1.35;
}

.cb-notify-item-message {
    margin: 4px 0 0;
    font-family: var(--cb-font-sans, system-ui, sans-serif);
    font-size: 12px;
    line-height: 1.5;
    color: var(--cb-brand-muted, #666);
}

.cb-notify-meta {
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    color: var(--cb-brand-muted, #666);
}

.cb-notify-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #2d6a4f;
    flex-shrink: 0;
}

.cb-notify-item.is-read .cb-notify-dot {
    background: #c4b9aa;
}

.cb-notify-more {
    border: 1px solid var(--cb-brand-border, #e8e3d8);
    background: #fff;
    color: var(--cb-brand-muted, #666);
    width: 30px;
    height: 30px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    cursor: pointer;
}

.cb-notify-foot {
    padding: 12px 16px 16px;
    border-top: 1px solid var(--cb-brand-border, #e8e3d8);
    background: #fff;
}

.cb-notify-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 11px 14px;
    border-radius: 14px;
    background: var(--cb-brand-accent, #2d6a4f);
    color: #fff;
    font-family: var(--cb-font-sans, system-ui, sans-serif);
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    transition: transform .18s ease, background .18s ease;
}

.cb-notify-link:hover {
    background: var(--cb-brand-accent-dark, #1b4332);
    transform: translateY(-1px);
}

.cb-notify-empty {
    padding: 28px 18px;
    text-align: center;
    color: var(--cb-brand-muted, #666);
    font-family: var(--cb-font-sans, system-ui, sans-serif);
    font-size: 13px;
}

@media (max-width: 640px) {
    .cb-notify {
        display: none;
    }
}
</style>

<div class="cb-notify">
    <button type="button" class="cb-notify-trigger" aria-label="Thông báo" aria-haspopup="dialog" aria-expanded="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .5-.2.9-.6 1.3L4 17h5"/>
            <path d="M9.5 19a2.5 2.5 0 0 0 5 0"/>
        </svg>

        @if($effectiveUnreadCount > 0)
            <span class="cb-notify-badge">
                {{ $effectiveUnreadCount > 99 ? '99+' : $effectiveUnreadCount }}
            </span>
        @endif
    </button>

    <div class="cb-notify-panel" role="dialog" aria-label="Bảng thông báo real-time">
        <div class="cb-notify-head">
            <div class="cb-notify-top">
                <div>
                    <h3 class="cb-notify-title">Thông báo</h3>
                </div>
            </div>

            <div class="cb-notify-tabs" aria-label="Bộ lọc thông báo">
                <button type="button" class="cb-notify-tab is-active">Tất cả</button>
                <button type="button" class="cb-notify-tab">Đơn hàng</button>
                <button type="button" class="cb-notify-tab">Chatbot AI</button>
                <button type="button" class="cb-notify-tab">Ưu đãi</button>
            </div>
        </div>

        <div class="cb-notify-body">
            @if(count($items) > 0)
                <div class="cb-notify-list">
                    @foreach($items as $notification)
                        @php
                            $type = $notification['type'] ?? 'offer';
                            $typeLabel = match ($type) {
                                'order' => 'Đơn hàng',
                                'chatbot' => 'Chatbot AI',
                                default => 'Ưu đãi',
                            };
                            $iconClass = match ($type) {
                                'order' => 'cb-notify-icon--order',
                                'chatbot' => 'cb-notify-icon--chatbot',
                                default => 'cb-notify-icon--offer',
                            };
                            $iconSvg = match ($type) {
                                'order' => '<path d="M5 7h14l-1 10H6L5 7zm2-3h10l1 3H6l1-3z"/><path d="M9 10v4m6-4v4"/>',
                                'chatbot' => '<path d="M12 4a7 7 0 0 0-7 7c0 1.9.8 3.6 2.1 4.8L6 19l3.1-1.2c.9.3 1.8.5 2.9.5a7 7 0 1 0 0-14z"/><path d="M9.3 11.2h.01M12 11.2h.01M14.7 11.2h.01"/>',
                                default => '<path d="M4 12c0-4.4 3.6-8 8-8s8 3.6 8 8-3.6 8-8 8-8-3.6-8-8z"/><path d="M12 8v8m-4-4h8"/>',
                            };
                        @endphp

                        <div class="cb-notify-item {{ !empty($notification['unread']) ? 'is-unread' : 'is-read' }}">
                            <div class="cb-notify-icon {{ $iconClass }}" aria-hidden="true">
                                {!! $iconSvg !!}
                            </div>

                            <div class="cb-notify-content">
                                <p class="cb-notify-item-title">{{ $notification['title'] ?? 'Thông báo mới' }}</p>
                                <p class="cb-notify-item-message">{{ $notification['message'] ?? '' }}</p>
                                <div class="cb-notify-meta">
                                    <span class="cb-notify-dot"></span>
                                    <span>{{ $typeLabel }}</span>
                                    <span>•</span>
                                    <span>{{ $notification['time'] ?? 'Vừa xong' }}</span>
                                </div>
                            </div>

                            <button type="button" class="cb-notify-more" aria-label="Tùy chọn thông báo">
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="5" r="1"/>
                                    <circle cx="12" cy="12" r="1"/>
                                    <circle cx="12" cy="19" r="1"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="cb-notify-empty">
                    Chưa có thông báo mới.
                </div>
            @endif
        </div>

        <div class="cb-notify-foot">
            <a href="#" class="cb-notify-link">Xem tất cả thông báo</a>
        </div>
    </div>
</div>
