@extends('layout')

@section('title', 'ایجاد مقاله جدید - BlogHub')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/post-form.css') }}">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            border: 2px solid #e0e3ff;
            border-radius: 12px;
            min-height: 50px;
            padding: 5px;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #4361ee;
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.15);
        }
    </style>
@endsection

@section('content')
    <div class="post-form-container">
        <!-- هدر صفحه -->
        <div class="post-form-header">
            <h1 class="post-form-title">
                <i class="fas fa-plus-circle"></i> ایجاد مقاله جدید
            </h1>
            <a href="{{ route('user.posts.index') }}" class="back-to-posts">
                <i class="fas fa-arrow-left"></i> بازگشت به مقالات
            </a>
        </div>

        <!-- پیام‌ها -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>خطا!</strong> لطفا موارد زیر را بررسی کنید:
                <ul class="mt-2 mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- فرم ایجاد مقاله -->
        <div class="post-form-card">
            <div class="post-form-content">
                <form action="{{ route('user.posts.store') }}" method="POST" enctype="multipart/form-data" id="postForm">
                    @csrf

                    <!-- تب‌های فرم -->
                    <div class="form-tabs">
                        <button type="button" class="form-tab active" data-tab="content-tab">
                            <i class="fas fa-edit"></i> محتوا
                        </button>
                        <button type="button" class="form-tab" data-tab="media-tab">
                            <i class="fas fa-image"></i> رسانه
                        </button>
                        <button type="button" class="form-tab" data-tab="seo-tab">
                            <i class="fas fa-chart-line"></i> سئو
                        </button>
                        <button type="button" class="form-tab" data-tab="settings-tab">
                            <i class="fas fa-cog"></i> تنظیمات
                        </button>
                    </div>

                    <!-- تب محتوا -->
                    <div id="content-tab" class="tab-content active">
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-heading"></i> اطلاعات اصلی
                            </h3>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="title" class="form-label">
                                        <span class="required">*</span> عنوان مقاله
                                    </label>
                                    <input type="text" id="title" name="title" value="{{ old('title') }}"
                                        class="form-control" placeholder="عنوان جذاب و کوتاه بنویسید..." required
                                        maxlength="200" oninput="generateSlug(this.value)">
                                    <div class="form-help">
                                        عنوان باید جذاب، واضح و حداکثر ۲۰۰ کاراکتر باشد.
                                    </div>
                                    <div class="error-message" id="title-error"></div>
                                </div>

                                <div class="form-group">
                                    <label for="slug" class="form-label">
                                        <span class="required">*</span> آدرس مقاله (Slug)
                                    </label>
                                    <div class="slug-container">
                                        <span class="slug-prefix">{{ url('/posts/') }}/</span>
                                        <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                                            class="form-control" placeholder="آدرس-مقاله" required pattern="[a-z0-9\-]+"
                                            title="فقط حروف کوچک انگلیسی، اعداد و خط تیره مجاز است">
                                    </div>
                                    <div class="form-help">
                                        فقط حروف کوچک انگلیسی، اعداد و خط تیره. بعداً قابل تغییر نیست.
                                    </div>
                                    <div class="error-message" id="slug-error"></div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="excerpt" class="form-label">چکیده مقاله</label>
                                <textarea id="excerpt" name="excerpt" class="form-control"
                                    placeholder="خلاصه کوتاهی از مقاله بنویسید (حداکثر ۳۰۰ کاراکتر)..." rows="3" maxlength="300">{{ old('excerpt') }}</textarea>
                                <div class="form-help">
                                    این متن در صفحات اصلی و جستجو نمایش داده می‌شود.
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="content" class="form-label">
                                    <span class="required">*</span> محتوای مقاله
                                </label>
                                <div class="editor-container">
                                    <div class="editor-toolbar">
                                        <button type="button" class="editor-btn" data-command="bold">
                                            <i class="fas fa-bold"></i>
                                        </button>
                                        <button type="button" class="editor-btn" data-command="italic">
                                            <i class="fas fa-italic"></i>
                                        </button>
                                        <button type="button" class="editor-btn" data-command="underline">
                                            <i class="fas fa-underline"></i>
                                        </button>
                                        <div class="divider"></div>
                                        <button type="button" class="editor-btn" data-command="insertUnorderedList">
                                            <i class="fas fa-list-ul"></i>
                                        </button>
                                        <button type="button" class="editor-btn" data-command="insertOrderedList">
                                            <i class="fas fa-list-ol"></i>
                                        </button>
                                        <div class="divider"></div>
                                        <button type="button" class="editor-btn" data-command="createLink">
                                            <i class="fas fa-link"></i>
                                        </button>
                                        <button type="button" class="editor-btn" data-command="unlink">
                                            <i class="fas fa-unlink"></i>
                                        </button>
                                        <div class="divider"></div>
                                        <button type="button" class="editor-btn" data-command="formatBlock"
                                            data-value="h2">
                                            <i class="fas fa-heading"></i> H2
                                        </button>
                                        <button type="button" class="editor-btn" data-command="formatBlock"
                                            data-value="h3">
                                            <i class="fas fa-heading"></i> H3
                                        </button>
                                        <button type="button" class="editor-btn" data-command="formatBlock"
                                            data-value="p">
                                            <i class="fas fa-paragraph"></i>
                                        </button>
                                    </div>
                                    <div id="editor-content" class="editor-content" contenteditable="true"
                                        data-placeholder="محتوا مقاله را اینجا بنویسید...">{{ old('content') }}</div>
                                    <textarea id="content" name="content" style="display: none;">{{ old('content') }}</textarea>
                                </div>
                                <div class="form-stats">
                                    <div class="stat-item">
                                        <span id="word-count" class="stat-value">0</span>
                                        <span class="stat-label">کلمه</span>
                                    </div>
                                    <div class="stat-item">
                                        <span id="char-count" class="stat-value">0</span>
                                        <span class="stat-label">کاراکتر</span>
                                    </div>
                                    <div class="stat-item">
                                        <span id="reading-time" class="stat-value">0</span>
                                        <span class="stat-label">دقیقه مطالعه</span>
                                    </div>
                                </div>
                                <div class="error-message" id="content-error"></div>
                            </div>
                        </div>
                    </div>

                    <!-- تب رسانه -->
                    <div id="media-tab" class="tab-content">
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-image"></i> تصویر شاخص
                            </h3>

                            <div class="form-group">
                                <label for="thumbnail" class="form-label">تصویر اصلی مقاله</label>
                                <div class="image-upload-container" id="upload-area">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <div class="upload-text">
                                        برای آپلود کلیک کنید یا فایل را اینجا رها کنید
                                    </div>
                                    <div class="upload-hint">
                                        فرمت‌های مجاز: JPG, PNG, GIF | حداکثر حجم: ۵ مگابایت
                                    </div>
                                    <input type="file" id="thumbnail" name="thumbnail" accept="image/*"
                                        style="display: none;" onchange="handleImageUpload(event)">
                                </div>

                                <div class="image-preview" id="image-preview">
                                    <img id="preview-image" class="preview-image" src="" alt="پیش‌نمایش">
                                    <button type="button" class="remove-image" onclick="removeImage()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <div class="form-help">
                                    تصویر شاخص در صفحات اصلی و شبکه‌های اجتماعی نمایش داده می‌شود.
                                </div>
                                <div class="error-message" id="thumbnail-error"></div>
                            </div>
                        </div>
                    </div>

                    <!-- تب سئو -->
                    <div id="seo-tab" class="tab-content">
                        <div class="form-section">
                            <h3 class="section-title">
                                <i class="fas fa-chart-line"></i> بهینه‌سازی موتور جستجو (SEO)
                            </h3>

                            <div class="form-group">
                                <label for="meta_title" class="form-label">عنوان متا (Meta Title)</label>
                                <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title') }}"
                                    class="form-control" placeholder="عنوان برای موتورهای جستجو..." maxlength="60">
                                <div class="form-help">
                                    حداکثر ۶۰ کاراکتر. اگر خالی بگذارید، از عنوان مقاله استفاده می‌شود.
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="meta_description" class="form-label">توضیحات متا (Meta Description)</label>
                                <textarea id="meta_description" name="meta_description" class="form-control"
                                    placeholder="توضیح مختصری برای موتورهای جستجو..." rows="3" maxlength="160">{{ old('meta_description') }}</textarea>
                                <div class="form-help">
                                    حداکثر ۱۶۰ کاراکتر. اگر خالی بگذارید، از چکیده مقاله استفاده می‌شود.
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="meta_keywords" class="form-label">کلمات کلیدی (Meta Keywords)</label>
                                <input type="text" id="meta_keywords" name="meta_keywords"
                                    value="{{ old('meta_keywords') }}" class="form-control"
                                    placeholder="کلمه کلیدی ۱, کلمه کلیدی ۲, ..." maxlength="255">
                                <div class="form-help">
                                    کلمات کلیدی را با کاما از هم جدا کنید.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- تب تنظیمات -->
                    <div id="settings-tab" class="tab-content">
                        <div class="form-row">
                            <!-- ستون سمت راست: تنظیمات اصلی -->
                            <div class="form-section">
                                <h3 class="section-title">
                                    <i class="fas fa-cog"></i> تنظیمات مقاله
                                </h3>

                                <div class="form-group">
                                    <label for="category_id" class="form-label">
                                        <span class="required">*</span> دسته‌بندی
                                    </label>
                                    <select id="category_id" name="category_id" class="form-control" required>
                                        <option value="">یک دسته‌بندی انتخاب کنید</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="error-message" id="category-error"></div>
                                </div>

                                <div class="form-group">
                                    <label for="tags" class="form-label">برچسب‌ها</label>
                                    <select id="tags" name="tags[]" class="form-control" multiple="multiple">
                                        @foreach ($tags as $tag)
                                            <option value="{{ $tag->id }}"
                                                {{ in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>
                                                {{ $tag->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-help">
                                        برای اضافه کردن برچسب جدید، آن را تایپ کرده و Enter بزنید.
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">وضعیت انتشار</label>
                                    <div class="radio-group">
                                        <div class="radio-item">
                                            <input type="radio" id="status_draft" name="status" value="draft"
                                                {{ old('status', 'draft') == 'draft' ? 'checked' : '' }}>
                                            <label for="status_draft" class="radio-label">
                                                <i class="fas fa-save"></i> ذخیره به عنوان پیش‌نویس
                                            </label>
                                        </div>
                                        <div class="radio-item">
                                            <input type="radio" id="status_published" name="status" value="published"
                                                {{ old('status') == 'published' ? 'checked' : '' }}>
                                            <label for="status_published" class="radio-label">
                                                <i class="fas fa-paper-plane"></i> انتشار فوری
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="checkbox-item">
                                    <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                        {{ old('is_featured') ? 'checked' : '' }}>
                                    <label for="is_featured" class="checkbox-label">
                                        <i class="fas fa-star"></i> مقاله ویژه
                                    </label>
                                    <div class="form-help">
                                        مقالات ویژه در صفحه اصلی و بخش ویژه‌ها نمایش داده می‌شوند.
                                    </div>
                                </div>
                            </div>

                            <!-- ستون سمت چپ: پنل کناری -->
                            <div class="sidebar-panel">
                                <h4 class="panel-title">
                                    <i class="fas fa-info-circle"></i> اطلاعات
                                </h4>

                                <div class="mb-3">
                                    <label class="form-label">نویسنده:</label>
                                    <div class="d-flex align-items-center gap-2">
                                        @if (auth()->user()->avatar)
                                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="آواتار"
                                                class="rounded-circle" width="30" height="30">
                                        @endif
                                        <span>{{ auth()->user()->name }}</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">تاریخ انتشار:</label>
                                    <input type="datetime-local" id="published_at" name="published_at"
                                        value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}"
                                        class="form-control">
                                    <div class="form-help">
                                        برای انتشار زمان‌بندی شده
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">امکان کامنت:</label>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="allow_comments" name="allow_comments" value="1"
                                            {{ old('allow_comments', true) ? 'checked' : '' }}>
                                        <label for="allow_comments" class="checkbox-label">
                                            فعال باشد
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">نمایش در RSS:</label>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="include_in_rss" name="include_in_rss" value="1"
                                            {{ old('include_in_rss', true) ? 'checked' : '' }}>
                                        <label for="include_in_rss" class="checkbox-label">
                                            در خوراک RSS نمایش داده شود
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- دکمه‌های فرم -->
                    <div class="form-actions">
                        <button type="submit" name="action" value="draft" class="draft-btn" id="saveDraft">
                            <i class="fas fa-save"></i> ذخیره پیش‌نویس
                        </button>

                        <div style="display: flex; gap: 15px;">
                            <button type="button" class="preview-btn" id="previewBtn">
                                <i class="fas fa-eye"></i> پیش‌نمایش
                            </button>
                            <button type="submit" name="action" value="publish" class="publish-btn" id="publishBtn">
                                <i class="fas fa-paper-plane"></i> انتشار مقاله
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- لودینگ -->
            <div class="loading-overlay" id="loadingOverlay">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // مقداردهی اولیه
        document.addEventListener('DOMContentLoaded', function() {
            // فعال‌سازی Select2 برای برچسب‌ها
            $('#tags').select2({
                tags: true,
                tokenSeparators: [','],
                createTag: function(params) {
                    return {
                        id: params.term,
                        text: params.term,
                        newTag: true
                    }
                },
                placeholder: 'برچسب‌ها را انتخاب یا وارد کنید...',
                language: 'fa',
                dir: 'rtl'
            });

            // فعال‌سازی تب‌ها
            initTabs();

            // ویرایشگر متن ساده
            initEditor();

            // آپلود تصویر
            initImageUpload();

            // شمارش کلمات و کاراکترها
            initWordCounter();

            // پیش‌نمایش
            initPreview();
        });

        // توابع تب‌ها
        function initTabs() {
            const tabs = document.querySelectorAll('.form-tab');
            const contents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // حذف کلاس active از همه تب‌ها
                    tabs.forEach(t => t.classList.remove('active'));
                    contents.forEach(c => c.classList.remove('active'));

                    // اضافه کردن کلاس active به تب انتخاب شده
                    tab.classList.add('active');
                    const tabId = tab.dataset.tab;
                    document.getElementById(tabId).classList.add('active');
                });
            });
        }

        // ویرایشگر متن ساده
        function initEditor() {
            const editor = document.getElementById('editor-content');
            const hiddenTextarea = document.getElementById('content');
            const toolbarButtons = document.querySelectorAll('.editor-btn');

            // همگام‌سازی با textarea مخفی
            editor.addEventListener('input', function() {
                hiddenTextarea.value = this.innerHTML;
                updateWordCount();
            });

            // دکمه‌های تولبار
            toolbarButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const command = this.dataset.command;
                    const value = this.dataset.value;

                    document.execCommand(command, false, value);
                    editor.focus();
                    hiddenTextarea.value = editor.innerHTML;
                    updateWordCount();
                });
            });

            // جایگزین‌کننده placeholder
            editor.addEventListener('focus', function() {
                if (this.innerHTML === this.dataset.placeholder) {
                    this.innerHTML = '';
                }
            });

            editor.addEventListener('blur', function() {
                if (this.innerHTML === '') {
                    this.innerHTML = this.dataset.placeholder;
                }
            });

            // مقدار اولیه
            if (!editor.innerHTML.trim()) {
                editor.innerHTML = editor.dataset.placeholder;
            }
        }

        // شمارش کلمات و کاراکترها
        function initWordCounter() {
            updateWordCount();
        }

        function updateWordCount() {
            const editor = document.getElementById('editor-content');
            const text = editor.innerText || '';

            // حذف placeholder
            const cleanText = text.replace(/\s+/g, ' ').trim();

            // شمارش کلمات
            const words = cleanText ? cleanText.split(/\s+/).length : 0;
            document.getElementById('word-count').textContent = words;

            // شمارش کاراکترها
            const chars = cleanText.length;
            document.getElementById('char-count').textContent = chars;

            // زمان مطالعه تخمینی (200 کلمه در دقیقه)
            const readingTime = Math.ceil(words / 200);
            document.getElementById('reading-time').textContent = readingTime;
        }

        // تولید خودکار slug از عنوان
        function generateSlug(title) {
            const slugInput = document.getElementById('slug');

            if (!slugInput.value || slugInput.dataset.manual === 'true') {
                return;
            }

            let slug = title
                .toLowerCase()
                .replace(/[^\u0600-\u06FF\uFB8A\u067E\u0686\u06AFa-z0-9\s-]/g, '') // حذف کاراکترهای غیرمجاز
                .replace(/\s+/g, '-') // جایگزینی فاصله با خط تیره
                .replace(/-+/g, '-') // حذف خط تیره‌های تکراری
                .replace(/^-+/, '') // حذف خط تیره از ابتدا
                .replace(/-+$/, ''); // حذف خط تیره از انتها

            slugInput.value = slug;
        }

        // علامت‌گذاری slug دستی
        document.getElementById('slug').addEventListener('input', function() {
            this.dataset.manual = 'true';
        });

        // آپلود تصویر
        function initImageUpload() {
            const uploadArea = document.getElementById('upload-area');
            const fileInput = document.getElementById('thumbnail');

            // کلیک روی area
            uploadArea.addEventListener('click', () => fileInput.click());

            // Drag & Drop
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                uploadArea.classList.add('dragover');
            }

            function unhighlight() {
                uploadArea.classList.remove('dragover');
            }

            // Drop
            uploadArea.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;

                if (files.length > 0) {
                    handleImageUpload({
                        target: {
                            files: files
                        }
                    });
                }
            }
        }

        // مدیریت آپلود تصویر
        function handleImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            // بررسی نوع فایل
            if (!file.type.match('image.*')) {
                alert('لطفا فقط تصویر انتخاب کنید.');
                return;
            }

            // بررسی حجم فایل (حداکثر ۵ مگابایت)
            if (file.size > 5 * 1024 * 1024) {
                alert('حجم فایل نباید بیشتر از ۵ مگابایت باشد.');
                return;
            }

            // نمایش پیش‌نمایش
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('preview-image');
                const previewContainer = document.getElementById('image-preview');

                preview.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }

        // حذف تصویر
        function removeImage() {
            document.getElementById('thumbnail').value = '';
            document.getElementById('image-preview').style.display = 'none';
            document.getElementById('preview-image').src = '';
        }

        // پیش‌نمایش مقاله
        function initPreview() {
            document.getElementById('previewBtn').addEventListener('click', function() {
                // در اینجا می‌توانید modal یا تب جدید برای پیش‌نمایش باز کنید
                alert('پیش‌نمایش در نسخه بعدی اضافه خواهد شد.');
            });
        }

        // اعتبارسنجی فرم
        document.getElementById('postForm').addEventListener('submit', function(e) {
            let isValid = true;

            // اعتبارسنجی عنوان
            const title = document.getElementById('title').value.trim();
            if (!title) {
                document.getElementById('title-error').textContent = 'عنوان مقاله الزامی است.';
                isValid = false;
            } else {
                document.getElementById('title-error').textContent = '';
            }

            // اعتبارسنجی slug
            const slug = document.getElementById('slug').value.trim();
            if (!slug) {
                document.getElementById('slug-error').textContent = 'آدرس مقاله الزامی است.';
                isValid = false;
            } else if (!/^[a-z0-9\-]+$/.test(slug)) {
                document.getElementById('slug-error').textContent =
                    'فقط حروف کوچک انگلیسی، اعداد و خط تیره مجاز است.';
                isValid = false;
            } else {
                document.getElementById('slug-error').textContent = '';
            }

            // اعتبارسنجی محتوا
            const content = document.getElementById('content').value.trim();
            if (!content || content === '<br>') {
                document.getElementById('content-error').textContent = 'محتوای مقاله الزامی است.';
                isValid = false;
            } else {
                document.getElementById('content-error').textContent = '';
            }

            // اعتبارسنجی دسته‌بندی
            const category = document.getElementById('category_id').value;
            if (!category) {
                document.getElementById('category-error').textContent = 'انتخاب دسته‌بندی الزامی است.';
                isValid = false;
            } else {
                document.getElementById('category-error').textContent = '';
            }

            if (!isValid) {
                e.preventDefault();
                // رفتن به تب محتوا
                document.querySelector('.form-tab[data-tab="content-tab"]').click();
            } else {
                // نمایش لودینگ
                document.getElementById('loadingOverlay').style.display = 'flex';
            }
        });

        // ذخیره خودکار پیش‌نویس (هر 30 ثانیه)
        let autoSaveInterval;

        function startAutoSave() {
            autoSaveInterval = setInterval(() => {
                if (document.getElementById('title').value.trim() ||
                    document.getElementById('content').value.trim()) {
                    saveDraft();
                }
            }, 30000); // هر 30 ثانیه
        }

        function saveDraft() {
            // در اینجا می‌توانید با AJAX پیش‌نویس را ذخیره کنید
            console.log('ذخیره خودکار پیش‌نویس...');
        }

        // شروع ذخیره خودکار
        startAutoSave();

        // توقف ذخیره خودکار هنگام ارسال فرم
        document.getElementById('postForm').addEventListener('submit', () => {
            clearInterval(autoSaveInterval);
        });
    </script>
@endsection
