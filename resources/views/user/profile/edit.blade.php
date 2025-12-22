@extends('layout')

@section('title', 'ویرایش پروفایل - BlogHub')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <div class="profile-edit-container">
        <!-- هدر صفحه -->
        <div class="profile-header">
            <h1>
                <i class="fas fa-user-edit"></i> ویرایش پروفایل
            </h1>
            <a href="{{ route('user.dashboard') }}" class="back-to-dashboard">
                <i class="fas fa-arrow-right"></i> بازگشت به داشبورد
            </a>
        </div>

        <!-- پیام موفقیت -->
        @if (session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- کارت ویرایش پروفایل -->
        <div class="profile-edit-card">
            <div class="profile-edit-content">
                <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data"
                    id="profileForm">
                    @csrf
                    @method('PUT')

                    <div class="profile-form-grid">
                        <!-- بخش اطلاعات شخصی -->
                        <div class="profile-section">
                            <h3 class="section-title">
                                <i class="fas fa-user-circle"></i> اطلاعات شخصی
                            </h3>

                            <div class="form-group">
                                <label for="name" class="form-label">نام کامل</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                    class="form-input" placeholder="نام و نام خانوادگی خود را وارد کنید" required>
                                @error('name')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">آدرس ایمیل</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="form-input" placeholder="example@email.com" required>
                                @error('email')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="bio" class="form-label">بیوگرافی</label>
                                <textarea id="bio" name="bio" class="form-input" placeholder="درباره خودتان بنویسید...">{{ old('bio', $user->bio) }}</textarea>
                                <span class="form-hint">حداکثر ۵۰۰ کاراکتر</span>
                                @error('bio')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- بخش آواتار -->
                        <div class="profile-section avatar-section">
                            <h3 class="section-title">
                                <i class="fas fa-camera"></i> تصویر پروفایل
                            </h3>

                            <div class="avatar-preview-container">
                                <img id="avatarPreview"
                                    src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}"
                                    alt="آواتار فعلی" class="avatar-preview">
                            </div>

                            <div class="form-group">
                                <div class="avatar-upload">
                                    <label for="avatar" class="avatar-upload-label">
                                        <i class="fas fa-cloud-upload-alt"></i> انتخاب تصویر
                                    </label>
                                    <input type="file" id="avatar" name="avatar" accept="image/*"
                                        onchange="previewImage(this)">
                                </div>
                                <span class="upload-hint">فرمت‌های مجاز: JPG, PNG, GIF | حداکثر حجم: ۲ مگابایت</span>
                                @error('avatar')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- بخش تغییر رمز عبور -->
                    <div class="password-section">
                        <h3 class="section-title">
                            <i class="fas fa-key"></i> تغییر رمز عبور
                        </h3>

                        <div class="password-note">
                            <i class="fas fa-info-circle"></i>
                            <span>فقط در صورتی پر کنید که می‌خواهید رمز عبور را تغییر دهید.</span>
                        </div>

                        <div class="password-grid">
                            <div class="form-group">
                                <label for="current_password" class="form-label">رمز عبور فعلی</label>
                                <input type="password" id="current_password" name="current_password" class="form-input"
                                    placeholder="رمز عبور فعلی">
                                @error('current_password')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="new_password" class="form-label">رمز عبور جدید</label>
                                <input type="password" id="new_password" name="new_password" class="form-input"
                                    placeholder="رمز عبور جدید">
                            </div>

                            <div class="form-group">
                                <label for="new_password_confirmation" class="form-label">تکرار رمز عبور جدید</label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                    class="form-input" placeholder="تکرار رمز عبور جدید">
                            </div>
                        </div>
                    </div>

                    <!-- دکمه‌های فرم -->
                    <div class="form-actions">
                        <a href="{{ route('user.dashboard') }}" class="btn-cancel">
                            <i class="fas fa-times"></i> انصراف
                        </a>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <i class="fas fa-save"></i>
                            <span>ذخیره تغییرات</span>
                            <div class="loading-spinner" id="loadingSpinner"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // پیش‌نمایش تصویر آواتار
        function previewImage(input) {
            const preview = document.getElementById('avatarPreview');
            if (input.files && input.files[0]) {
                const file = input.files[0];

                // بررسی حجم فایل (حداکثر ۲ مگابایت)
                if (file.size > 2 * 1024 * 1024) {
                    alert('حجم فایل نباید بیشتر از ۲ مگابایت باشد.');
                    input.value = '';
                    return;
                }

                // بررسی فرمت فایل
                const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    alert('فقط فرمت‌های JPG, PNG و GIF مجاز هستند.');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.add('pulse-effect');
                    setTimeout(() => preview.classList.remove('pulse-effect'), 2000);
                }
                reader.readAsDataURL(file);
            }
        }

        // نمایش اسپینر هنگام ارسال فرم
        document.getElementById('profileForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            const loadingSpinner = document.getElementById('loadingSpinner');

            submitBtn.disabled = true;
            submitBtn.querySelector('span').textContent = 'در حال ذخیره...';
            loadingSpinner.style.display = 'inline-block';
        });

        // اعتبارسنجی فیلدهای رمز عبور
        document.getElementById('current_password')?.addEventListener('input', validatePasswords);
        document.getElementById('new_password')?.addEventListener('input', validatePasswords);

        function validatePasswords() {
            const currentPassword = document.getElementById('current_password');
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('new_password_confirmation');

            // اگر رمز جدید پر شده باشد
            if (newPassword.value) {
                // رمز فعلی باید پر شود
                if (!currentPassword.value) {
                    currentPassword.classList.add('error');
                } else {
                    currentPassword.classList.remove('error');
                }

                // تأیید رمز جدید
                if (confirmPassword.value && newPassword.value !== confirmPassword.value) {
                    confirmPassword.classList.add('error');
                } else {
                    confirmPassword.classList.remove('error');
                }

                // طول رمز جدید
                if (newPassword.value.length < 8) {
                    newPassword.classList.add('error');
                } else {
                    newPassword.classList.remove('error');
                }
            }
        }
    </script>
@endsection