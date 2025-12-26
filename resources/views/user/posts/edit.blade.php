@extends('layout')

@section('title', 'ویرایش مقاله - ' . $post->title)

@section('styles')
    <style>
        /* استایل‌های ضروری */
        .form-section-card {
            display: none;
        }

        .form-section-card.active-section {
            display: block;
        }

        /* استایل ویرایشگر */
        .ql-toolbar.ql-snow {
            border-radius: 12px 12px 0 0 !important;
            border: 2px solid #e0e3ff !important;
            background: linear-gradient(135deg, #f8f9ff, #f1f3ff) !important;
        }

        .ql-container.ql-snow {
            border-radius: 0 0 12px 12px !important;
            border: 2px solid #e0e3ff !important;
            border-top: none !important;
            font-family: 'Vazirmatn', sans-serif !important;
            min-height: 350px;
        }

        .ql-editor {
            min-height: 300px;
            font-family: 'Vazirmatn', sans-serif !important;
            line-height: 1.8;
            text-align: right;
            direction: rtl;
        }

        /* استایل modal حذف */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
            animation: modalAppear 0.3s ease;
        }

        @keyframes modalAppear {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    </style>
@endsection

@section('content')
    <div class="post-create-container">
        <!-- هدر صفحه -->
        <div class="post-create-header">
            <div class="header-left">
                <h1 class="post-create-title">
                    <i class="fas fa-edit"></i>
                    ویرایش مقاله
                </h1>
                <p class="page-subtitle">{{ $post->title }}</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('user.posts.index') }}" class="back-to-list">
                    <i class="fas fa-arrow-right"></i>
                    بازگشت به مقالات
                </a>
                <button type="button" class="btn btn-danger" id="deletePostBtn">
                    <i class="fas fa-trash"></i> حذف مقاله
                </button>
            </div>
        </div>

        <!-- نمایش پیام‌ها -->
        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>خطاهای زیر رخ داده:</strong>
                    <ul style="margin: 10px 0 0 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- نوار پیشرفت -->
        <div class="form-progress-bar">
            <div class="progress-steps">
                <div class="progress-step active" data-step="1">
                    <span class="step-number">۱</span>
                    <span class="step-text">اطلاعات اصلی</span>
                </div>
                <div class="progress-step" data-step="2">
                    <span class="step-number">۲</span>
                    <span class="step-text">محتوا</span>
                </div>
                <div class="progress-step" data-step="3">
                    <span class="step-number">۳</span>
                    <span class="step-text">تنظیمات</span>
                </div>
            </div>
            <div class="progress-indicator">
                <div class="progress-fill" id="progressFill" style="width: 33%"></div>
            </div>
        </div>

        <!-- فرم ویرایش -->
        <div class="create-form-card">
            <form action="{{ route('user.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data"
                id="postForm">
                @csrf
                @method('PUT')

                <div class="form-content-wrapper">
                    <!-- بخش ۱: اطلاعات اصلی -->
                    <div class="form-section-card active-section" id="section1" data-section="1">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div>
                                <h3 class="section-title">اطلاعات اصلی مقاله</h3>
                                <p class="section-subtitle">عنوان، توضیح مختصر و دسته‌بندی مقاله</p>
                            </div>
                        </div>

                        <div class="form-grid">
                            <!-- فیلد عنوان -->
                            <div class="form-field">
                                <label class="field-label">
                                    <i class="fas fa-heading"></i>
                                    عنوان مقاله
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" name="title" class="form-input"
                                    placeholder="عنوان جذاب و مختصر مقاله خود را وارد کنید"
                                    value="{{ old('title', $post->title) }}" required data-counter="60" id="titleInput">
                                <div class="field-hint">
                                    <span class="char-count" id="titleCounter">{{ strlen($post->title) }}/60</span>
                                    <span class="field-tip">عنوان باید جذاب و مرتبط با محتوا باشد</span>
                                </div>
                            </div>

                            <!-- فیلد slug -->
                            <div class="form-field">
                                <label class="field-label">
                                    <i class="fas fa-link"></i>
                                    آدرس مقاله (Slug)
                                    <span class="required-star">*</span>
                                </label>
                                <div class="slug-field-group">
                                    <span class="slug-prefix">{{ config('app.url') }}/blog/</span>
                                    <input type="text" name="slug" class="form-input slug-input"
                                        placeholder="عنوان-انگلیسی-مقاله" value="{{ old('slug', $post->slug) }}" required
                                        id="slugInput">
                                </div>
                                <button type="button" class="generate-slug-btn btn-small" id="generateSlugBtn">
                                    <i class="fas fa-sync-alt"></i> ایجاد خودکار
                                </button>
                            </div>

                            <!-- فیلد دسته‌بندی -->
                            <div class="form-field">
                                <label class="field-label">
                                    <i class="fas fa-folder"></i>
                                    دسته‌بندی
                                    <span class="required-star">*</span>
                                </label>
                                <select name="category_id" class="form-input" required id="categorySelect">
                                    <option value="">انتخاب دسته‌بندی</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                        @if ($category->children->count() > 0)
                                            @foreach ($category->children as $child)
                                                <option value="{{ $child->id }}"
                                                    {{ old('category_id', $post->category_id) == $child->id ? 'selected' : '' }}>
                                                    &nbsp;&nbsp;├ {{ $child->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <!-- فیلد چکیده -->
                            <div class="form-field full-width">
                                <label class="field-label">
                                    <i class="fas fa-align-left"></i>
                                    چکیده مقاله
                                    <span class="required-star">*</span>
                                </label>
                                <textarea name="excerpt" class="form-input textarea-field" rows="4"
                                    placeholder="توضیح مختصر (۱۵۰-۲۰۰ کاراکتر) درباره مقاله خود بنویسید" required data-counter="200"
                                    id="excerptInput">{{ old('excerpt', $post->excerpt) }}</textarea>
                                <div class="field-hint">
                                    <span class="char-count" id="excerptCounter">{{ strlen($post->excerpt) }}/200</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- بخش ۲: محتوای مقاله -->
                    <div class="form-section-card" id="section2" data-section="2">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="fas fa-edit"></i>
                            </div>
                            <div>
                                <h3 class="section-title">محتوا و متن مقاله</h3>
                                <p class="section-subtitle">متن اصلی مقاله خود را با ویرایشگر پیشرفته بنویسید</p>
                            </div>
                        </div>

                        <!-- ویرایشگر متن -->
                        <div class="form-field">
                            <label class="field-label">
                                <i class="fas fa-file-alt"></i>
                                محتوای مقاله
                                <span class="required-star">*</span>
                            </label>
                            <textarea name="content" id="editorContent" style="display: none;">{{ old('content', $post->content) }}</textarea>
                            <div id="editorWrapper" class="editor-wrapper"></div>
                            <div class="field-hint">
                                <span class="char-count" id="contentCounter">0 کاراکتر، 0 کلمه</span>
                            </div>
                        </div>

                        <!-- آپلود تصویر شاخص -->
                        <div class="form-field">
                            <label class="field-label">
                                <i class="fas fa-image"></i>
                                تصویر شاخص
                            </label>

                            @if ($post->featured_image)
                                <!-- نمایش تصویر فعلی -->
                                <div class="current-image-container">
                                    <div class="image-preview-container" id="previewContainer">
                                        <div class="preview-wrapper">
                                            <img src="{{ $post->featured_image_url }}" class="preview-image"
                                                alt="تصویر فعلی">
                                            <button type="button" class="remove-image-btn" id="removeCurrentImage">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <p class="image-info">
                                            <small>برای تغییر تصویر، فایل جدید انتخاب کنید</small>
                                        </p>
                                    </div>
                                    <input type="hidden" name="current_image" value="{{ $post->featured_image }}">
                                </div>
                            @endif

                            <!-- آپلودر جدید (پنهان اگر تصویر فعلی وجود دارد) -->
                            <div class="image-upload-area" id="uploadArea"
                                style="{{ $post->featured_image ? 'display: none;' : 'display: block;' }}">
                                <div class="upload-icon-wrapper">
                                    <div class="upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                </div>
                                <div class="upload-text">تصویر شاخص را اینجا رها کنید</div>
                                <p class="upload-hint">فایل‌های JPG, PNG یا WebP تا ۵ مگابایت</p>
                                <button type="button" class="upload-button" id="uploadButton">
                                    <i class="fas fa-search"></i> انتخاب فایل
                                </button>
                                <input type="file" name="featured_image" id="featuredImage" class="file-input"
                                    accept="image/*">
                            </div>

                            <!-- پیش‌نمایش تصویر جدید -->
                            <div class="image-preview-container" id="newPreviewContainer" style="display: none;">
                                <div class="preview-wrapper">
                                    <img id="newImagePreview" class="preview-image" src="" alt="پیش‌نمایش جدید">
                                    <button type="button" class="remove-image-btn" id="removeNewImageBtn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- بخش ۳: تنظیمات -->
                    <div class="form-section-card" id="section3" data-section="3">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="fas fa-cog"></i>
                            </div>
                            <div>
                                <h3 class="section-title">تنظیمات و انتشار</h3>
                                <p class="section-subtitle">تنظیمات نهایی و انتشار مقاله</p>
                            </div>
                        </div>

                        <div class="form-grid">
                            <!-- برچسب‌ها -->
                            <div class="form-field">
                                <label class="field-label">
                                    <i class="fas fa-tags"></i>
                                    برچسب‌ها
                                </label>
                                <select name="tags[]" id="tagsSelect" class="form-input" multiple="multiple">
                                    @php
                                        $oldTags = old('tags', $post->tags->pluck('name')->toArray());
                                    @endphp
                                    @if (is_array($oldTags))
                                        @foreach ($oldTags as $tag)
                                            <option value="{{ $tag }}" selected>{{ $tag }}</option>
                                        @endforeach
                                    @endif
                                    <!-- برچسب‌های موجود از دیتابیس -->
                                    @foreach ($existingTags as $tag)
                                        <option value="{{ $tag->name }}">{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- وضعیت -->
                            <div class="form-field">
                                <label class="field-label">
                                    <i class="fas fa-globe"></i>
                                    وضعیت انتشار
                                </label>
                                <div class="option-group">
                                    <label class="option-item">
                                        <input type="radio" name="status" value="draft"
                                            {{ old('status', $post->status) == 'draft' ? 'checked' : '' }}>
                                        <span class="option-label">
                                            <i class="fas fa-save"></i>
                                            پیش‌نویس
                                        </span>
                                    </label>
                                    <label class="option-item">
                                        <input type="radio" name="status" value="published"
                                            {{ old('status', $post->status) == 'published' ? 'checked' : '' }}>
                                        <span class="option-label">
                                            <i class="fas fa-paper-plane"></i>
                                            منتشر شده
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- تاریخ انتشار -->
                        <div class="form-field">
                            <label class="field-label">
                                <i class="fas fa-calendar-alt"></i>
                                زمان انتشار
                            </label>
                            <input type="datetime-local" name="published_at" class="form-input" id="publishedAt"
                                value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : \Carbon\Carbon::now()->format('Y-m-d\TH:i')) }}">
                            <small class="text-muted">اگر مقاله منتشر شده است، تاریخ انتشار را تنظیم کنید</small>
                        </div>

                        <!-- پنل کناری -->
                        <div class="sidebar-panel">
                            <div class="panel-header">
                                <i class="fas fa-chart-line panel-icon"></i>
                                <h4 class="panel-title">آمار مقاله</h4>
                            </div>
                            <div class="panel-content">
                                <p><i class="fas fa-eye"></i> بازدیدها: <strong>{{ $post->view_count }}</strong></p>
                                <p><i class="fas fa-clock"></i> زمان خوانش: <span id="readTime">0</span> دقیقه</p>
                                <p><i class="fas fa-file-word"></i> تعداد کلمات: <span id="wordCount">0</span></p>
                                <p><i class="fas fa-calendar"></i> ایجاد شده: {{ $post->created_at->diffForHumans() }}</p>
                                @if ($post->published_at)
                                    <p><i class="fas fa-paper-plane"></i> منتشر شده:
                                        {{ $post->published_at->diffForHumans() }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- دکمه‌های فرم -->
                <div class="form-actions">
                    <div class="form-stats">
                        <div class="stat-box">
                            <div class="stat-value" id="totalChars">0</div>
                            <div class="stat-label">کاراکتر</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-value" id="totalWords">0</div>
                            <div class="stat-label">کلمه</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-value" id="totalImages">0</div>
                            <div class="stat-label">تصویر</div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button type="button" class="btn btn-outline" id="prevSectionBtn">
                            <i class="fas fa-arrow-right"></i> بخش قبلی
                        </button>
                        <button type="button" class="btn btn-outline" id="nextSectionBtn">
                            بخش بعدی <i class="fas fa-arrow-left"></i>
                        </button>
                        <button type="submit" name="action" value="draft" class="btn btn-outline">
                            <i class="fas fa-save"></i> ذخیره تغییرات
                        </button>
                        <button type="submit" name="action" value="publish" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> انتشار تغییرات
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- لودینگ -->
        <div class="loading-overlay" id="loadingOverlay">
            <div class="spinner"></div>
            <div class="loading-text">در حال پردازش...</div>
        </div>
    </div>

    <!-- Modal حذف مقاله -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                    حذف مقاله
                </h3>
            </div>

            <div class="modal-body">
                <p>آیا مطمئن هستید که می‌خواهید مقاله <strong>"{{ $post->title }}"</strong> را حذف کنید؟</p>
                <p class="text-muted">این عمل غیرقابل بازگشت است و تمام نظرات مربوطه نیز حذف خواهند شد.</p>

                @if ($post->featured_image)
                    <div class="alert alert-warning">
                        <i class="fas fa-image"></i>
                        تصویر شاخص مقاله نیز حذف خواهد شد.
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <form action="{{ route('user.posts.destroy', $post->id) }}" method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-outline" id="cancelDeleteBtn">
                        لغو
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> حذف مقاله
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Current section tracker
            let currentSection = 1;
            const totalSections = 3;

            // DOM Elements
            const titleInput = document.getElementById('titleInput');
            const slugInput = document.getElementById('slugInput');
            const excerptInput = document.getElementById('excerptInput');
            const featuredImageInput = document.getElementById('featuredImage');
            const uploadArea = document.getElementById('uploadArea');
            const previewContainer = document.getElementById('previewContainer');
            const newPreviewContainer = document.getElementById('newPreviewContainer');
            const newImagePreview = document.getElementById('newImagePreview');
            const removeCurrentImageBtn = document.getElementById('removeCurrentImage');
            const removeNewImageBtn = document.getElementById('removeNewImageBtn');
            const uploadButton = document.getElementById('uploadButton');

            // Navigation elements
            const prevSectionBtn = document.getElementById('prevSectionBtn');
            const nextSectionBtn = document.getElementById('nextSectionBtn');
            const progressFill = document.getElementById('progressFill');
            const postForm = document.getElementById('postForm');
            const loadingOverlay = document.getElementById('loadingOverlay');

            // Character counters
            const titleCounter = document.getElementById('titleCounter');
            const excerptCounter = document.getElementById('excerptCounter');
            const contentCounter = document.getElementById('contentCounter');

            // Stats elements
            const totalCharsEl = document.getElementById('totalChars');
            const totalWordsEl = document.getElementById('totalWords');
            const totalImagesEl = document.getElementById('totalImages');
            const wordCountEl = document.getElementById('wordCount');
            const readTimeEl = document.getElementById('readTime');

            // Delete modal elements
            const deleteModal = document.getElementById('deleteModal');
            const deletePostBtn = document.getElementById('deletePostBtn');
            const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
            const deleteForm = document.getElementById('deleteForm');

            // ==================== INITIALIZATIONS ====================

            // Initialize Select2 for tags
            $('#tagsSelect').select2({
                tags: true,
                tokenSeparators: [','],
                placeholder: 'برچسب‌ها را وارد کنید...',
                language: 'fa',
                dir: 'rtl',
                width: '100%',
                createTag: function(params) {
                    const term = params.term.trim();
                    if (term === '') return null;
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                }
            });

            // Initialize Quill Editor with existing content
            const quill = new Quill('#editorWrapper', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{
                            'header': [1, 2, 3, false]
                        }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{
                            'color': []
                        }, {
                            'background': []
                        }],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'align': []
                        }],
                        ['link', 'image'],
                        ['clean']
                    ]
                },
                placeholder: 'محتوای مقاله خود را اینجا بنویسید...'
            });

            // Set existing content
            const initialContent = document.getElementById('editorContent').value;
            quill.root.innerHTML = initialContent;

            // Update hidden textarea on content change
            quill.on('text-change', function() {
                document.getElementById('editorContent').value = quill.root.innerHTML;
                updateStats();
                updateContentCounter();
            });

            // ==================== EVENT LISTENERS ====================

            // Generate slug from title
            document.getElementById('generateSlugBtn').addEventListener('click', function() {
                const title = titleInput.value.trim();
                if (title) {
                    const slug = title
                        .replace(/[^\u0600-\u06FFa-zA-Z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .toLowerCase();
                    slugInput.value = slug;
                }
            });

            // Update character counters
            titleInput.addEventListener('input', function() {
                const length = this.value.length;
                titleCounter.textContent = `${length}/60`;
                titleCounter.style.color = length > 60 ? '#f72585' : '#28a745';
            });

            excerptInput.addEventListener('input', function() {
                const length = this.value.length;
                excerptCounter.textContent = `${length}/200`;
                excerptCounter.style.color = length > 200 ? '#f72585' : '#28a745';
            });

            // Image upload handling
            uploadButton.addEventListener('click', function() {
                featuredImageInput.click();
            });

            featuredImageInput.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    handleImageUpload(e.target.files[0]);
                }
            });

            // Drag and drop for image upload
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', function() {
                this.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('dragover');
                if (e.dataTransfer.files.length) {
                    handleImageUpload(e.dataTransfer.files[0]);
                }
            });

            // Remove current image
            if (removeCurrentImageBtn) {
                removeCurrentImageBtn.addEventListener('click', function() {
                    // Hide current image preview
                    if (previewContainer) {
                        previewContainer.style.display = 'none';
                    }

                    // Show upload area
                    uploadArea.style.display = 'block';

                    // Add hidden input to indicate image should be removed
                    let removeImageInput = document.getElementById('remove_featured_image');
                    if (!removeImageInput) {
                        removeImageInput = document.createElement('input');
                        removeImageInput.type = 'hidden';
                        removeImageInput.name = 'remove_featured_image';
                        removeImageInput.id = 'remove_featured_image';
                        removeImageInput.value = '1';
                        postForm.appendChild(removeImageInput);
                    }

                    updateStats();
                });
            }

            // Remove new image
            removeNewImageBtn.addEventListener('click', function() {
                featuredImageInput.value = '';
                newPreviewContainer.style.display = 'none';
                uploadArea.style.display = 'block';
                updateStats();
            });

            // Section navigation
            nextSectionBtn.addEventListener('click', function() {
                if (validateCurrentSection()) {
                    navigateToSection(currentSection + 1);
                }
            });

            prevSectionBtn.addEventListener('click', function() {
                navigateToSection(currentSection - 1);
            });

            // Form submission
            postForm.addEventListener('submit', function(e) {
                // Validate all sections before submission
                if (!validateAllSections()) {
                    e.preventDefault();
                    showError('لطفا تمام فیلدهای ضروری را پر کنید');
                    return;
                }

                // Ensure content is copied to hidden textarea
                document.getElementById('editorContent').value = quill.root.innerHTML;

                // Show loading
                loadingOverlay.style.display = 'flex';
            });

            // Delete modal handling
            deletePostBtn.addEventListener('click', function() {
                deleteModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });

            cancelDeleteBtn.addEventListener('click', function() {
                deleteModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            });

            // Close modal when clicking outside
            deleteModal.addEventListener('click', function(e) {
                if (e.target === deleteModal) {
                    deleteModal.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });

            // Delete form submission
            deleteForm.addEventListener('submit', function(e) {
                e.preventDefault();

                // Show confirmation
                if (confirm('آیا مطمئن هستید؟ این عمل غیرقابل بازگشت است.')) {
                    this.submit();
                }
            });

            // ==================== FUNCTIONS ====================

            // Handle image upload
            function handleImageUpload(file) {
                if (!file.type.match('image.*')) {
                    showError('لطفا فقط فایل تصویری انتخاب کنید');
                    return;
                }

                if (file.size > 5 * 1024 * 1024) { // 5MB
                    showError('حجم فایل باید کمتر از ۵ مگابایت باشد');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    newImagePreview.src = e.target.result;
                    newPreviewContainer.style.display = 'block';
                    uploadArea.style.display = 'none';

                    // Hide current image preview if exists
                    if (previewContainer) {
                        previewContainer.style.display = 'none';
                    }

                    updateStats();
                };
                reader.readAsDataURL(file);
            }

            // Navigate between sections
            function navigateToSection(sectionNumber) {
                if (sectionNumber < 1 || sectionNumber > totalSections) return;

                // Hide current section
                document.querySelector(`#section${currentSection}`).classList.remove('active-section');

                // Show new section
                document.querySelector(`#section${sectionNumber}`).classList.add('active-section');

                // Update progress
                currentSection = sectionNumber;
                progressFill.style.width = `${(sectionNumber / totalSections) * 100}%`;

                // Update progress steps
                document.querySelectorAll('.progress-step').forEach((step, index) => {
                    if (index + 1 <= sectionNumber) {
                        step.classList.add('active');
                    } else {
                        step.classList.remove('active');
                    }
                });

                // Update navigation buttons
                prevSectionBtn.style.display = sectionNumber === 1 ? 'none' : 'flex';
                nextSectionBtn.style.display = sectionNumber === totalSections ? 'none' : 'flex';

                // Scroll to top of section
                document.querySelector(`#section${sectionNumber}`).scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }

            // Validate current section
            function validateCurrentSection() {
                switch (currentSection) {
                    case 1:
                        if (!titleInput.value.trim()) {
                            showError('عنوان مقاله الزامی است');
                            titleInput.focus();
                            return false;
                        }
                        if (!slugInput.value.trim()) {
                            showError('آدرس مقاله الزامی است');
                            slugInput.focus();
                            return false;
                        }
                        if (!document.getElementById('categorySelect').value) {
                            showError('لطفا دسته‌بندی را انتخاب کنید');
                            document.getElementById('categorySelect').focus();
                            return false;
                        }
                        if (!excerptInput.value.trim()) {
                            showError('چکیده مقاله الزامی است');
                            excerptInput.focus();
                            return false;
                        }
                        return true;

                    case 2:
                        const content = quill.getText().trim();
                        if (content.length < 100) {
                            showError('محتوای مقاله باید حداقل ۱۰۰ کاراکتر داشته باشد');
                            return false;
                        }
                        return true;

                    default:
                        return true;
                }
            }

            // Validate all sections
            function validateAllSections() {
                // Validate section 1
                if (!titleInput.value.trim() ||
                    !slugInput.value.trim() ||
                    !document.getElementById('categorySelect').value ||
                    !excerptInput.value.trim()) {
                    navigateToSection(1);
                    return false;
                }

                // Validate section 2
                const content = quill.getText().trim();
                if (content.length < 100) {
                    navigateToSection(2);
                    return false;
                }

                return true;
            }

            // Update content counter
            function updateContentCounter() {
                const text = quill.getText();
                const words = text.trim().split(/\s+/).filter(word => word.length > 0);
                const chars = text.length;

                contentCounter.textContent = `${chars} کاراکتر، ${words.length} کلمه`;
            }

            // Update all stats
            function updateStats() {
                const content = quill.getText();
                const htmlContent = quill.root.innerHTML;

                // Count words and characters
                const words = content.trim().split(/\s+/).filter(word => word.length > 0);
                const chars = content.length;

                // Count images
                const editorImages = (htmlContent.match(/<img/g) || []).length;
                const featuredImage = (previewContainer && previewContainer.style.display !== 'none') ||
                    (newPreviewContainer.style.display === 'block') ? 1 : 0;
                const totalImages = editorImages + featuredImage;

                // Calculate reading time (200 words per minute)
                const readTime = Math.ceil(words.length / 200);

                // Update elements
                totalCharsEl.textContent = chars;
                totalWordsEl.textContent = words.length;
                totalImagesEl.textContent = totalImages;
                wordCountEl.textContent = words.length;
                readTimeEl.textContent = readTime;
            }

            // Show error message
            function showError(message) {
                // Remove existing alerts
                const existingAlert = document.querySelector('.alert-danger');
                if (existingAlert) existingAlert.remove();

                // Create new alert
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger';
                alertDiv.innerHTML = `
                <i class="fas fa-exclamation-circle"></i>
                <div>${message}</div>
            `;

                // Add to page
                const container = document.querySelector('.post-create-container');
                container.insertBefore(alertDiv, container.firstChild);

                // Remove after 5 seconds
                setTimeout(() => {
                    alertDiv.style.animation = 'slideOut 0.4s ease';
                    setTimeout(() => alertDiv.remove(), 400);
                }, 5000);

                // Scroll to top
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            // Initialize
            function init() {
                // Update counters with initial values
                titleInput.dispatchEvent(new Event('input'));
                excerptInput.dispatchEvent(new Event('input'));
                updateStats();
                updateContentCounter();

                // Set initial button states
                prevSectionBtn.style.display = 'none';
                nextSectionBtn.style.display = 'flex';

                // Show first section
                navigateToSection(1);
            }

            // Start everything
            init();
        });
    </script>
@endsection
