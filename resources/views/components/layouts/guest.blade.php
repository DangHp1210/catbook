<style>
        *, *::before, *::after { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'DM Sans', sans-serif;
            background: var(--cb-brand-bg);
            color: var(--cb-brand-text);
        }

        .cb-auth-page {
            position: relative;
            min-height: calc(100vh - 64px);
            overflow: hidden;
            padding: 48px 16px 72px;
        }

        .cb-auth-page::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(45,106,79,0.05) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }

        .cb-auth-glow-l,
        .cb-auth-glow-r {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .cb-auth-glow-l {
            top: -110px;
            left: -80px;
            width: 360px;
            height: 360px;
            background: radial-gradient(circle, rgba(45,106,79,0.18) 0%, transparent 68%);
        }

        .cb-auth-glow-r {
            bottom: -120px;
            right: -100px;
            width: 360px;
            height: 360px;
            background: radial-gradient(circle, rgba(74,222,128,0.12) 0%, transparent 68%);
        }

        .cb-auth-layout {
            position: relative;
            z-index: 1;
            max-width: 1140px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 440px);
            gap: 44px;
            align-items: center;
        }

        .cb-auth-copy {
            max-width: 560px;
        }

        .cb-auth-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.8px;
            text-transform: uppercase;
            color: var(--cb-brand-accent);
            background: var(--cb-brand-accent-soft);
            padding: 6px 14px;
            border-radius: 999px;
            margin-bottom: 20px;
        }

        .cb-eyebrow-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--cb-brand-accent);
        }

        .cb-auth-copy h1 {
            margin: 0;
            font-family: 'Playfair Display', serif;
            font-size: clamp(36px, 6vw, 60px);
            line-height: 1.05;
            letter-spacing: -1.8px;
            color: var(--cb-brand-text);
        }

        .cb-auth-copy h1 em {
            font-style: italic;
            color: var(--cb-brand-accent);
        }

        .cb-auth-copy p {
            margin: 18px 0 0;
            max-width: 560px;
            font-size: 16px;
            line-height: 1.8;
            color: var(--cb-brand-muted);
        }

        .cb-auth-benefits {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-top: 28px;
        }

        .cb-auth-benefit {
            background: rgba(255,255,255,0.75);
            border: 1px solid var(--cb-brand-border);
            border-radius: 16px;
            padding: 16px 18px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        }

        .cb-auth-benefit strong {
            display: block;
            font-size: 14px;
            color: #0d1b10;
            margin-bottom: 4px;
        }

        .cb-auth-benefit span {
            font-size: 13px;
            color: #6b7280;
        }

        .cb-auth-card {
            position: relative;
            width: 100%;
            background: rgba(255,255,255,0.92);
            border: 1px solid var(--cb-brand-border);
            border-radius: 22px;
            padding: 44px 44px 40px;
            box-shadow: 0 24px 70px rgba(13,27,16,0.08);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .cb-auth-logo {
            text-align: center;
            margin-bottom: 28px;
        }

        .cb-auth-logo a {
            font-family: 'Playfair Display', serif;
            font-size: 30px;
            font-weight: 900;
            color: var(--cb-brand-text);
            text-decoration: none;
            letter-spacing: -0.5px;
            line-height: 1;
        }

        .cb-auth-logo a span { color: var(--cb-brand-accent); }

        .cb-auth-logo p {
            margin: 8px 0 0;
            font-size: 13px;
            color: #6a9e7a;
            font-weight: 500;
        }

        .cb-auth-slot h1,
        .cb-auth-slot h2 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            color: #0d1b10;
            text-align: center;
            margin: 0 0 6px;
            letter-spacing: -0.3px;
        }

        .cb-auth-slot p.cb-auth-sub {
            font-size: 13px;
            color: #6a9e7a;
            text-align: center;
            margin: 0 0 28px;
        }

        .cb-auth-slot label {
            display: block;
            font-size: 12px;
            font-weight: 500;
            color: #6b7280;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
        }

        .cb-auth-slot input[type="text"],
        .cb-auth-slot input[type="email"],
        .cb-auth-slot input[type="password"],
        .cb-auth-slot input[type="tel"],
        .cb-auth-slot select,
        .cb-auth-slot textarea {
            display: block;
            width: 100%;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            padding: 12px 16px;
            border-radius: 12px;
            background: #fff;
            border: 1.5px solid #e0dbd0;
            color: var(--cb-brand-text);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            margin-bottom: 16px;
        }

        .cb-auth-slot input::placeholder,
        .cb-auth-slot textarea::placeholder { color: #b0aa9e; }

        .cb-auth-slot input:focus,
        .cb-auth-slot select:focus,
        .cb-auth-slot textarea:focus {
            border-color: var(--cb-brand-accent);
            box-shadow: 0 0 0 3px rgba(45,106,79,0.1);
        }

        .cb-auth-slot input.error,
        .cb-auth-slot select.error {
            border-color: rgba(248,113,113,0.6);
        }

        .cb-auth-slot .cb-error {
            font-size: 12px;
            color: #dc2626;
            margin: -10px 0 12px;
        }

        .cb-auth-slot .cb-btn-primary {
            display: block;
            width: 100%;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 600;
            padding: 13px;
            border-radius: 999px;
            border: none;
            background: var(--cb-brand-text);
            color: #fff;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
            text-align: center;
            text-decoration: none;
            margin-top: 4px;
        }

        .cb-auth-slot .cb-btn-primary:hover {
            background: var(--cb-brand-accent);
            transform: translateY(-1px);
        }

        .cb-auth-slot .cb-btn-secondary {
            display: block;
            width: 100%;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 500;
            padding: 12px;
            border-radius: 999px;
            border: 1.5px solid #e0dbd0;
            background: transparent;
            color: #444;
            cursor: pointer;
            transition: border-color 0.2s, color 0.2s;
            text-align: center;
            text-decoration: none;
            margin-top: 10px;
        }

        .cb-auth-slot .cb-btn-secondary:hover {
            border-color: var(--cb-brand-text);
            color: var(--cb-brand-text);
        }

        .cb-auth-slot .cb-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: #9ca3af;
            font-size: 12px;
        }

        .cb-auth-slot .cb-divider::before,
        .cb-auth-slot .cb-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--cb-brand-border);
        }

        .cb-auth-slot .cb-auth-footer {
            text-align: center;
            font-size: 13px;
            color: #6b7280;
            margin-top: 24px;
        }

        .cb-auth-slot .cb-auth-footer a {
            color: var(--cb-brand-accent);
            text-decoration: none;
            font-weight: 600;
            transition: opacity 0.2s;
        }

        .cb-auth-slot .cb-auth-footer a:hover { opacity: 0.75; }

        .cb-alert {
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .cb-alert-success {
            background: #ecfdf5;
            border: 1px solid #bbf7d0;
            color: #047857;
        }

        .cb-alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        .cb-alert-info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
        }

        .cb-auth-slot .cb-check-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
        }

        .cb-auth-slot .cb-check-wrap input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            accent-color: var(--cb-brand-accent);
            margin: 0;
            padding: 0;
            flex-shrink: 0;
        }

        .cb-auth-slot .cb-check-wrap label {
            font-size: 13px;
            color: #6b7280;
            margin: 0;
            cursor: pointer;
        }

        .cb-auth-back {
            position: absolute;
            top: 20px;
            left: 24px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 500;
            color: #6b7280;
            text-decoration: none;
            transition: color 0.2s;
            z-index: 2;
        }

        .cb-auth-back:hover { color: var(--cb-brand-accent); }

        .cb-auth-back svg {
            width: 14px;
            height: 14px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        @media (max-width: 920px) {
            .cb-auth-layout { grid-template-columns: 1fr; gap: 28px; }
            .cb-auth-copy { max-width: 100%; }
        }

        @media (max-width: 640px) {
            .cb-auth-page { padding: 28px 12px 56px; }
            .cb-auth-card { padding: 34px 22px 30px; }
            .cb-auth-benefits { grid-template-columns: 1fr; }
        }
    </style>

    <main class="cb-auth-page">
        <div class="cb-auth-glow-l"></div>
        <div class="cb-auth-glow-r"></div>

        <div class="cb-auth-layout">
            <section class="cb-auth-copy" aria-label="Giới thiệu">
                <div class="cb-auth-eyebrow">
                    <span class="cb-eyebrow-dot"></span>
                    Kho sách trực tuyến
                </div>
                <h1>Trở lại để tiếp tục
                    <em>khám phá</em> tri thức</h1>
                <p>Đăng nhập để lưu địa chỉ, theo dõi đơn hàng và trải nghiệm Chatbot thông minh – người bạn đồng hành giúp bạn tìm ra cuốn sách "chuẩn gu" trong tích tắc.</p>

                <div class="cb-auth-benefits">
                    <div class="cb-auth-benefit">
                        <strong>Trợ lý sách AI 24/7</strong>
                        <span>Trò chuyện trực tiếp để tìm kiếm, nhận tóm tắt sách và các gợi ý được cá nhân hóa dành riêng cho bạn.</span>
                    </div>
                    <div class="cb-auth-benefit">
                        <strong>Thanh toán & Giao hàng tiện lợi</strong>
                        <span>Quản lý giỏ hàng, lưu thông tin người nhận và dễ dàng thanh toán qua COD, VNPay hoặc MoMo.</span>
                    </div>
                </div>
            </section>

            <section class="cb-auth-card" aria-label="Biểu mẫu xác thực">
                <a href="{{ route('home') }}" class="cb-auth-back">
                    <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                    Trang chủ
                </a>

                <div class="cb-auth-logo">
                    <a href="{{ route('home') }}">Cat<span>Book</span></a>
                    <p>Kho sách trực tuyến tích hợp AI</p>
                </div>

                @if(session('success'))
                    <div class="cb-alert cb-alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="cb-alert cb-alert-error">{{ session('error') }}</div>
                @endif
                @if(session('info'))
                    <div class="cb-alert cb-alert-info">{{ session('info') }}</div>
                @endif

                <div class="cb-auth-slot">
                    {{ $slot }}
                </div>
            </section>
        </div>
    </main>