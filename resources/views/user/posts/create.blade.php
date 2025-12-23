@extends('layout')

@section('title', 'ایجاد مقاله جدید - BlogHub')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/post-form.css') }}">
@endsection

@section('content')
    <div class="post-form-container">
        <div class="post-form-header">
            <h1 class="post-form-title">
                <i class="fas fa-plus-circle"></i> ایجاد مقاله جدید
            </h1>
            <a href="{{ route('user.posts.index') }}" class="back-to-posts">
                <i class="fas fa-arrow-left"></i> بازگشت به مقالات
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- فرم تست ساده -->
        <div style="background: white; padding: 30px; border-radius: 15px; margin-top: 20px;">
            <h3 style="margin-bottom: 20px;">فرم تست سریع:</h3>

            <form action="{{ route('user.posts.store') }}" method="POST" enctype="multipart/form-data" id="simpleForm">
                @csrf

                <div style="margin-bottom: 15px;">
                    <label>عنوان مقاله *</label>
                    <input type="text" name="title" value="{{ old('title', 'مقاله تست') }}"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>آدرس مقاله (Slug) *</label>
                    <input type="text" name="slug" value="{{ old('slug', 'test-article') }}"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>محتوا *</label>
                    <textarea name="content" rows="6" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"
                        required>
                        {{ old('content', '<p>این یک مقاله تست است.</p>') }}
                    </textarea>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>دسته‌بندی *</label>
                    <select name="category_id"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;" required>
                        <option value="">انتخاب کنید</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>وضعیت</label><br>
                    <input type="radio" name="status" value="draft" checked> پیش‌نویس
                    <input type="radio" name="status" value="published" style="margin-right: 15px;"> منتشر شده
                </div>

                <input type="hidden" name="excerpt" value="چکیده مقاله تست">

                <button type="submit"
                    style="padding: 12px 30px; background: #4361ee; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    <i class="fas fa-paper-plane"></i> ارسال مقاله
                </button>
            </form>
        </div>

        <!-- فرم اصلی (موقتاً غیرفعال) -->
        <div style="display: none;">
            <div class="post-form-card">
                <!-- ... کد فرم اصلی شما ... -->
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            console.log('صفحه ایجاد مقاله لود شد');

            // Select2 برای برچسب‌ها
            $('#tags').select2({
                tags: true,
                tokenSeparators: [','],
                placeholder: 'برچسب‌ها را انتخاب یا وارد کنید...',
                language: 'fa',
                dir: 'rtl'
            });

            // اعتبارسنجی ساده
            $('#simpleForm').on('submit', function(e) {
                console.log('فرم در حال ارسال...');
                return true;
            });
        });
    </script>
@endsection
