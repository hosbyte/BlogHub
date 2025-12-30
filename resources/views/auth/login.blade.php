@extends('layout')

@section('title', 'ورود به حساب کاربری')

@section('content')
    <div class="auth-container">
        <div class="auth-card">
            <!-- هدر فرم -->
            <div class="auth-header">
                <div class="auth-logo">
                    <a href="{{ route('home') }}" class="logo-link">
                        @if (config('app.logo'))
                            <img src="{{ asset('storage/' . config('app.logo')) }}" alt="{{ config('app.name') }}"
                                class="logo-image">
                        @else
                            <div class="logo-text">{{ config('app.name') }}</div>
                        @endif
                    </a>
                </div>
                <h1 class="auth-title">ورود به حساب کاربری</h1>
                <p class="auth-subtitle">خوش آمدید! لطفا وارد شوید</p>
            </div>

            <!-- پیام‌های سشن -->
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <!-- خطاهای اعتبارسنجی -->
            @if ($errors->any())
                <div class="alert alert-error">
                    <ul class="error-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- فرم ورود -->
            <form method="POST" action="{{ route('login') }}" class="auth-form">
                @csrf

                <!-- ایمیل -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="icon icon-email"></i>
                        آدرس ایمیل
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="email" placeholder="example@domain.com"
                        class="form-input @error('email') input-error @enderror">
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- رمز عبور -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="icon icon-password"></i>
                        رمز عبور
                    </label>
                    <div class="password-container">
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            placeholder="رمز عبور خود را وارد کنید"
                            class="form-input @error('password') input-error @enderror">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="icon icon-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- گزینه‌های اضافی -->
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" id="remember" name="remember" class="checkbox-input"
                            {{ old('remember') ? 'checked' : '' }}>
                        <span class="checkbox-custom"></span>
                        <span class="checkbox-text">مرا به خاطر بسپار</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">
                            رمز عبور را فراموش کرده‌اید؟
                        </a>
                    @endif
                </div>

                <!-- دکمه ورود -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-full">
                        <i class="icon icon-login"></i>
                        ورود به حساب
                    </button>
                </div>
            </form>

            <!-- ثبت‌نام و گزینه‌های دیگر -->
            <div class="auth-footer">
                <p class="auth-text">
                    حساب کاربری ندارید؟
                    <a href="{{ route('register') }}" class="auth-link">ثبت‌نام کنید</a>
                </p>

                <div class="divider">
                    <span>یا</span>
                </div>

                <!-- ورود با شبکه‌های اجتماعی (اختیاری) -->
                {{--
            <div class="social-login">
                <button type="button" class="social-btn google">
                    <i class="icon icon-google"></i>
                    ورود با گوگل
                </button>
                <button type="button" class="social-btn github">
                    <i class="icon icon-github"></i>
                    ورود با گیت‌هاب
                </button>
            </div>
            --}}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.password-toggle');
            const icon = toggleButton.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.className = 'icon icon-eye-slash';
            } else {
                passwordInput.type = 'password';
                icon.className = 'icon icon-eye';
            }
        }

        // اعتبارسنجی فرم
        document.querySelector('.auth-form').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!email || !password) {
                e.preventDefault();
                alert('لطفا تمام فیلدها را پر کنید');
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            direction: rtl;
        }

        .auth-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            padding: 40px;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-logo {
            margin-bottom: 20px;
        }

        .logo-link {
            display: inline-block;
            text-decoration: none;
        }

        .logo-image {
            height: 60px;
            width: auto;
        }

        .logo-text {
            font-size: 28px;
            font-weight: bold;
            color: #4f46e5;
        }

        .auth-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .auth-subtitle {
            font-size: 14px;
            color: #6b7280;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .error-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .auth-form {
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-label .icon {
            margin-left: 8px;
            color: #6b7280;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: #f9fafb;
        }

        .form-input:focus {
            outline: none;
            border-color: #4f46e5;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .input-error {
            border-color: #ef4444;
        }

        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            padding: 4px;
        }

        .error-message {
            display: block;
            font-size: 12px;
            color: #ef4444;
            margin-top: 4px;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            font-size: 14px;
        }

        .checkbox-input {
            display: none;
        }

        .checkbox-custom {
            width: 18px;
            height: 18px;
            border: 2px solid #d1d5db;
            border-radius: 4px;
            margin-left: 8px;
            position: relative;
            transition: all 0.3s;
        }

        .checkbox-input:checked+.checkbox-custom {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }

        .checkbox-input:checked+.checkbox-custom::after {
            content: '✓';
            position: absolute;
            color: white;
            font-size: 12px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .checkbox-text {
            color: #4b5563;
        }

        .forgot-link {
            font-size: 14px;
            color: #4f46e5;
            text-decoration: none;
            transition: color 0.3s;
        }

        .forgot-link:hover {
            color: #3730a3;
            text-decoration: underline;
        }

        .form-actions {
            margin-bottom: 30px;
        }

        .btn {
            padding: 14px 28px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-full {
            width: 100%;
        }

        .btn-primary {
            background-color: #4f46e5;
            color: white;
        }

        .btn-primary:hover {
            background-color: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .auth-footer {
            text-align: center;
        }

        .auth-text {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 20px;
        }

        .auth-link {
            color: #4f46e5;
            font-weight: 500;
            text-decoration: none;
        }

        .auth-link:hover {
            text-decoration: underline;
        }

        .divider {
            position: relative;
            margin: 25px 0;
        }

        .divider span {
            background: white;
            padding: 0 15px;
            color: #9ca3af;
            font-size: 14px;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e5e7eb;
            z-index: -1;
        }

        .social-login {
            display: flex;
            gap: 12px;
            flex-direction: column;
        }

        .social-btn {
            padding: 12px;
            border-radius: 8px;
            border: 2px solid #e5e7eb;
            background: white;
            color: #374151;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .social-btn:hover {
            border-color: #4f46e5;
            color: #4f46e5;
        }

        .social-btn.google .icon {
            color: #ea4335;
        }

        .social-btn.github .icon {
            color: #333;
        }

        .icon {
            font-style: normal;
        }

        .icon-email::before {
            content: '📧';
        }

        .icon-password::before {
            content: '🔒';
        }

        .icon-eye::before {
            content: '👁️';
        }

        .icon-eye-slash::before {
            content: '👁️‍🗨️';
        }

        .icon-login::before {
            content: '🚀';
        }

        .icon-google::before {
            content: 'G';
        }

        .icon-github::before {
            content: 'G';
        }

        /* ریسپانسیو */
        @media (max-width: 480px) {
            .auth-card {
                padding: 25px;
            }

            .auth-title {
                font-size: 20px;
            }

            .form-options {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
    </style>
@endpush
