<style>
.cb-footer {
    font-family: var(--cb-sans);
    background: #ffffff;
    color: var(--cb-text);
    border-top: 1px solid #1e5131;
    border-radius: 20px; /* Độ bo góc, bạn có thể chỉnh tăng/giảm (VD: 16px, 24px) */
    border: 1px solid #e8e3d8; /* Đường viền mỏng đồng màu hệ thống CatBook */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03); /* Đổ bóng cực kỳ nhẹ giúp footer nổi bật */
    /* Giữ nguyên khoảng cách mỏng vừa chỉnh */
    padding: 24px 20px 16px; 
    /* Để các góc bo không bị lỗi lấn lề nếu bạn có dùng margin */
}

.cb-footer-top {
    display: grid; 
    grid-template-columns: 2fr 1fr 1fr; 
    /* 2. Giảm khoảng cách giữa 3 cột và giảm lề dưới */
    gap: 24px; 
    max-width: 1270px; 
    margin: 0 auto 16px; 
}

@media(max-width:768px){ 
    .cb-footer-top { grid-template-columns: 1fr; gap: 20px; } 
}

.cb-footer-brand { max-width: 320px; }

.cb-footer-logo {
    font-family: var(--cb-serif); 
    /* 3. Thu nhỏ logo và khoảng cách dưới logo */
    font-size: 24px; 
    font-weight: 900;
    color: #000; 
    margin-bottom: 8px;
}
.cb-footer-logo span { color: var(--cb-accent); }

.cb-footer-desc { 
    /* 4. Giảm cỡ chữ mô tả và khoảng cách */
    font-size: 13px; 
    line-height: 1.5; 
    color: #202221; 
    margin-bottom: 12px; 
}

.cb-footer-contact p {
    margin: 0 0 6px; 
    font-size: 13px; /* Thu nhỏ chữ liên hệ */
    display: flex; align-items: center; gap: 8px;
}

.cb-footer-heading {
    color: #1d1c1c; 
    /* 5. Thu nhỏ tiêu đề các cột */
    font-size: 14px; 
    font-weight: 700;
    text-transform: uppercase; 
    letter-spacing: 0.5px; 
    margin: 0 0 12px; 
}

.cb-footer-links { 
    display: flex; flex-direction: column; 
    /* 6. Giảm khoảng cách giữa các đường link */
    gap: 8px; 
}
.cb-footer-links a {
    color: #202221; text-decoration: none; font-size: 13px;
    transition: all .2s ease; width: fit-content;
}
.cb-footer-links a:hover { color: #0a6816; transform: translateX(4px); }

.cb-footer-bottom {
    display: flex; align-items: center; justify-content: space-between;
    max-width: 1270px; margin: 0 auto;
    /* 7. Thu nhỏ chữ phần bản quyền và thêm đường kẻ mờ ngăn cách */
    font-size: 12px; 
    color: #555; 
    flex-wrap: wrap; gap: 16px;
    padding-top: 12px;
    border-top: 1px solid rgba(0, 0, 0, 0.05);
}

.cb-socials { display: flex; align-items: center; gap: 16px; }
.cb-socials a { color: #0e0e0e; text-decoration: none; font-size: 13px; transition: color .2s; }
.cb-socials a:hover { color: var(--cb-accent); }
</style>

<footer class="cb-footer">
    <div class="cb-footer-top">
        <div class="cb-footer-brand">
            <div class="cb-footer-logo">Cat<span>Book</span></div>
            <p class="cb-footer-desc">
                Tiệm sách nhỏ mang đến những giá trị lớn. Khám phá hàng ngàn đầu sách hay và 100% chính hãng cùng CatBook ngay hôm nay!
            </p>
            <div class="cb-footer-contact">
                <p>📞 <strong>Hotline:</strong> 1900 1210</p>
                <p>📧 <strong>Email:</strong> cskh@catbook.vn</p>
            </div>
        </div>
        <div class="cb-footer-col">
            <h4 class="cb-footer-heading">Về CatBook</h4>
            <div class="cb-footer-links">
                <a href="#">Giới thiệu chung</a>
                <a href="#">Tuyển dụng</a>
                <a href="#">Chính sách bảo mật</a>
                <a href="#">Điều khoản sử dụng</a>
            </div>
        </div>
        <div class="cb-footer-col">
            <h4 class="cb-footer-heading">Hỗ trợ khách hàng</h4>
            <div class="cb-footer-links">
                <a href="#">Hướng dẫn mua hàng</a>
                <a href="#">Phương thức thanh toán</a>
                <a href="#">Chính sách đổi trả</a>
                <a href="#">Tra cứu đơn hàng</a>
            </div>
        </div>
    </div>
    <div class="cb-footer-bottom">
        <div class="cb-copyright">© {{ date('Y') }} CatBook. Tất cả các quyền được bảo lưu.</div>
        <div class="cb-socials">
            <a href="#" title="Facebook">Facebook</a>
            <a href="#" title="Twitter">Twitter</a>
            <a href="#" title="Instagram">Instagram</a>
        </div>
    </div>
</footer>
