<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cook Together - Đăng nhập</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="index1.css?v=<?php echo time(); ?>">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        :root {
            --primary: #e27227;
            --primary-hover: #c95e18;
            --primary-light: #fff7ed;
            --primary-ultra-light: #fffaf5;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --bg-page: #fdfbf8;
            --card-bg: #ffffff;
            --border-color: #f0ebe1;
            --radius-lg: 24px;
            --radius-md: 14px;
            --radius-pill: 9999px;
            --shadow-lg: 0 20px 40px -10px rgba(226, 114, 39, 0.12), 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            min-height: 100vh;
            background-color: var(--bg-page);
            background-image: radial-gradient(#e27227 0.8px, transparent 0.8px), radial-gradient(#e27227 0.8px, var(--bg-page) 0.8px);
            background-size: 32px 32px;
            background-position: 0 0, 16px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .auth-container {
            width: 100%;
            max-width: 960px;
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr 1.15fr;
        }

        /* LEFT BANNER */
        .auth-banner {
            background: linear-gradient(135deg, #ea580c 0%, #e27227 60%, #c2410c 100%);
            padding: 48px 40px;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .auth-banner::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .banner-brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            text-decoration: none;
        }

        .banner-brand i {
            font-size: 26px;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px;
            border-radius: 12px;
        }

        .banner-body h2 {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 12px;
        }

        .banner-body p {
            font-size: 15px;
            line-height: 1.6;
            opacity: 0.9;
        }

        .banner-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: var(--radius-pill);
            font-size: 13px;
            font-weight: 600;
            width: fit-content;
        }

        /* RIGHT FORM */
        .auth-form-wrap {
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 28px;
        }

        .form-header h1 {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 6px;
        }

        .form-header p {
            font-size: 14px;
            color: var(--text-muted);
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i.leading-icon {
            position: absolute;
            left: 16px;
            color: #9ca3af;
            font-size: 15px;
            transition: color 0.2s ease;
        }

        .input-field {
            width: 100%;
            padding: 14px 44px 14px 44px;
            border-radius: var(--radius-md);
            border: 1.5px solid var(--border-color);
            background: #faf8f5;
            font-size: 14px;
            color: var(--text-main);
            outline: none;
            transition: all 0.25s ease;
        }

        .input-field:focus {
            background: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(226, 114, 39, 0.12);
        }

        .input-field:focus ~ i.leading-icon {
            color: var(--primary);
        }

        .toggle-password-btn {
            position: absolute;
            right: 16px;
            background: transparent;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            font-size: 15px;
            padding: 4px;
            transition: color 0.2s ease;
        }

        .toggle-password-btn:hover {
            color: var(--text-main);
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            border-radius: var(--radius-pill);
            border: none;
            background: linear-gradient(135deg, #f97316, #e27227);
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(226, 114, 39, 0.35);
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #ea580c, #c95e18);
            box-shadow: 0 6px 20px rgba(226, 114, 39, 0.45);
            transform: translateY(-1px);
        }

        .demo-box {
            margin-top: 24px;
            padding: 14px 16px;
            background: var(--primary-ultra-light);
            border: 1px dashed rgba(226, 114, 39, 0.35);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .demo-text {
            font-size: 12px;
            color: #9a3412;
            line-height: 1.4;
        }

        .demo-text strong {
            display: block;
            margin-bottom: 2px;
        }

        .btn-quick-fill {
            background: #ffffff;
            border: 1px solid rgba(226, 114, 39, 0.3);
            color: var(--primary);
            padding: 6px 12px;
            border-radius: var(--radius-pill);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s ease;
        }

        .btn-quick-fill:hover {
            background: var(--primary);
            color: #ffffff;
        }

        .form-footer {
            margin-top: 24px;
            font-size: 14px;
            color: var(--text-muted);
            text-align: center;
        }

        .form-footer a {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .auth-container {
                grid-template-columns: 1fr;
            }
            .auth-banner {
                padding: 32px 24px;
            }
            .auth-form-wrap {
                padding: 32px 24px;
            }
        }
    </style>
</head>
<body>

    <div class="auth-container">
        
        <!-- LEFT BRAND BANNER -->
        <div class="auth-banner">
            <a href="index.php" class="banner-brand">
                <i class="fa-solid fa-utensils"></i>
                <span>Cook Together</span>
            </a>

            <div class="banner-body">
                <h2>Nấu ăn là sẻ chia yêu thương.</h2>
                <p>Khám phá hàng ngàn món ăn ngon, lưu lại bí quyết và cùng nhau vào bếp mỗi ngày.</p>
            </div>

            <div class="banner-badge">
                <i class="fa-solid fa-fire-burner"></i>
                <span>Cộng đồng hơn 10.000+ công thức</span>
            </div>
        </div>

        <!-- RIGHT LOGIN FORM -->
        <div class="auth-form-wrap">
            <div class="form-header">
                <h1>Đăng nhập</h1>
                <p>Chào mừng bạn quay lại với căn bếp Cook Together</p>
            </div>

            <form action="login.php" method="post">
                <div class="input-group">
                    <label for="emailInput">Địa chỉ Email</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-envelope leading-icon"></i>
                        <input id="emailInput" class="input-field" type="email" name="email" placeholder="name@example.com" required autocomplete="email">
                    </div>
                </div>

                <div class="input-group">
                    <label for="passwordInput">Mật khẩu</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock leading-icon"></i>
                        <input id="passwordInput" class="input-field" type="password" name="password" placeholder="Nhập mật khẩu của bạn" required autocomplete="current-password">
                        <button type="button" class="toggle-password-btn" onclick="togglePassword('passwordInput', this)" title="Ẩn/Hiện mật khẩu">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <span>Đăng nhập vào bếp</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <p class="form-footer">
                Chưa có tài khoản? <a href="index2.php">Đăng ký ngay</a>
            </p>
        </div>

    </div>

    <script>
        function togglePassword(inputId, btn) {
            var input = document.getElementById(inputId);
            var icon = btn.querySelector("i");
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>
