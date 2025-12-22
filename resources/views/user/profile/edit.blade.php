@extends('layout')

@section('title', 'ویرایش پروفایل')

@section('content')
    <div class="container mx-auto py-8 max-w-4xl">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-2xl font-bold text-gray-800">ویرایش پروفایل</h1>
            <a href="{{ route('user.dashboard') }}" class="text-blue-600 hover:text-blue-800">
                ← بازگشت به داشبورد
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6">
                <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- اطلاعات شخصی -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">اطلاعات شخصی</h3>

                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                    نام کامل
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                    class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-300 focus:border-blue-500"
                                    required>
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                    آدرس ایمیل
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                    class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-300 focus:border-blue-500"
                                    required>
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">
                                    بیوگرافی
                                </label>
                                <textarea id="bio" name="bio" rows="3"
                                    class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-300 focus:border-blue-500">{{ old('bio', $user->bio) }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">حداکثر ۵۰۰ کاراکتر</p>
                            </div>
                        </div>

                        <!-- آواتار -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">تصویر پروفایل</h3>

                            <div class="flex flex-col items-center">
                                <!-- نمایش آواتار فعلی -->
                                <div class="mb-4">
                                    <img id="avatar-preview"
                                        src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}"
                                        alt="آواتار فعلی"
                                        class="w-32 h-32 rounded-full object-cover border-4 border-white shadow">
                                </div>

                                <div>
                                    <label for="avatar" class="block text-sm font-medium text-gray-700 mb-1">
                                        تغییر تصویر
                                    </label>
                                    <input type="file" id="avatar" name="avatar" accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                        onchange="previewImage(this)">
                                    <p class="text-xs text-gray-500 mt-1">فرم‌های مجاز: JPG, PNG, GIF (حداکثر ۲ مگابایت)</p>
                                    @error('avatar')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تغییر رمز عبور -->
                    <div class="mt-8 pt-8 border-t">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">تغییر رمز عبور</h3>
                        <p class="text-sm text-gray-500 mb-4">فقط در صورتی پر کنید که می‌خواهید رمز عبور را تغییر دهید.</p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                                    رمز عبور فعلی
                                </label>
                                <input type="password" id="current_password" name="current_password"
                                    class="w-full px-3 py-2 border rounded">
                            </div>

                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">
                                    رمز عبور جدید
                                </label>
                                <input type="password" id="new_password" name="new_password"
                                    class="w-full px-3 py-2 border rounded">
                            </div>

                            <div>
                                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                    تکرار رمز عبور جدید
                                </label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                    class="w-full px-3 py-2 border rounded">
                            </div>
                        </div>
                        @error('current_password')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- دکمه‌ها -->
                    <div class="mt-8 flex justify-end space-x-4">
                        <a href="{{ route('user.dashboard') }}"
                            class="px-6 py-2 border rounded text-gray-700 hover:bg-gray-50">
                            انصراف
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            ذخیره تغییرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('avatar-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
