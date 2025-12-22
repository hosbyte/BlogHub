@extends('layout')

@section('title', 'مقالات من - BlogHub')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/user-posts.css') }}">
    <style>
        /* استایل‌های اضافی برای select2 (اگر استفاده می‌کنید) */
        .select2-container--default .select2-selection--single {
            border: 2px solid #e0e3ff;
            border-radius: 10px;
            height: 46px;
            padding: 8px;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.15);
        }
    </style>
@endsection

@section('content')
    <div class="user-posts-container">
        <!-- هدر صفحه -->
        <div class="user-posts-header">
            <h1 class="user-posts-title">
                <i class="fas fa-newspaper"></i> مقالات من
            </h1>
            <a href="{{ route('user.posts.create') }}" class="new-post-btn">
                <i class="fas fa-plus"></i> مقاله جدید
            </a>
        </div>

        <!-- پیام‌های موفقیت/خطا -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- فیلترها و جستجو -->
        <div class="posts-filters">
            <div class="filter-header">
                <h3 class="filter-title">
                    <i class="fas fa-filter"></i> فیلتر و جستجو
                </h3>
                <div class="filter-actions">
                    <button type="button" class="filter-btn reset" onclick="resetFilters()">
                        <i class="fas fa-redo"></i> بازنشانی
                    </button>
                </div>
            </div>

            <form action="{{ route('user.posts.index') }}" method="GET" class="filter-form" id="filterForm">
                <div class="filter-group">
                    <label for="status" class="filter-label">وضعیت</label>
                    <select name="status" id="status" class="filter-select">
                        <option value="">همه وضعیت‌ها</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>پیش‌نویس</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منتشر شده
                        </option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>آرشیو</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="search" class="filter-label">جستجو</label>
                    <input type="text" name="search" id="search" class="filter-input"
                        placeholder="عنوان یا محتوای مقاله..." value="{{ request('search') }}">
                </div>

                <div class="filter-group">
                    <label for="category" class="filter-label">دسته‌بندی</label>
                    <select name="category_id" id="category" class="filter-select">
                        <option value="">همه دسته‌بندی‌ها</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <label for="sort" class="filter-label">مرتب‌سازی</label>
                    <select name="sort" id="sort" class="filter-select">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>جدیدترین</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>قدیمی‌ترین</option>
                        <option value="most_viewed" {{ request('sort') == 'most_viewed' ? 'selected' : '' }}>پربازدیدترین
                        </option>
                        <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>عنوان (صعودی)
                        </option>
                        <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>عنوان (نزولی)
                        </option>
                    </select>
                </div>

                <div class="filter-group" style="grid-column: 1 / -1;">
                    <button type="submit" class="filter-btn" style="width: 100%;">
                        <i class="fas fa-search"></i> اعمال فیلتر
                    </button>
                </div>
            </form>
        </div>

        <!-- بخش انتخاب گروهی (اختیاری) -->
        <div class="bulk-actions" id="bulkActions">
            <span class="bulk-selected-count" id="selectedCount">0 مقاله انتخاب شده</span>
            <button type="button" class="bulk-action-btn" onclick="changeStatusSelected('published')">
                <i class="fas fa-check"></i> منتشر کردن
            </button>
            <button type="button" class="bulk-action-btn" onclick="changeStatusSelected('draft')">
                <i class="fas fa-edit"></i> پیش‌نویس کردن
            </button>
            <button type="button" class="bulk-action-btn" onclick="deleteSelected()">
                <i class="fas fa-trash"></i> حذف انتخاب‌ها
            </button>
            <button type="button" class="bulk-action-btn" onclick="clearSelection()">
                <i class="fas fa-times"></i> لغو انتخاب
            </button>
        </div>

        <!-- جدول مقالات -->
        <div class="posts-table-container">
            @if ($posts->count() > 0)
                <table class="posts-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">
                                <input type="checkbox" id="selectAll" class="select-checkbox"
                                    onchange="toggleSelectAll(this)">
                            </th>
                            <th style="width: 80px;">تصویر</th>
                            <th>عنوان مقاله</th>
                            <th style="width: 120px;">دسته‌بندی</th>
                            <th style="width: 120px;">وضعیت</th>
                            <th style="width: 150px;">تاریخ</th>
                            <th style="width: 100px;">بازدید</th>
                            <th style="width: 180px;">عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($posts as $post)
                            <tr data-post-id="{{ $post->id }}">
                                <td>
                                    <input type="checkbox" class="post-checkbox select-checkbox"
                                        value="{{ $post->id }}">
                                </td>
                                <td>
                                    @if ($post->thumbnail)
                                        <img src="{{ asset('storage/' . $post->thumbnail->path) }}"
                                            alt="{{ $post->title }}" class="post-thumbnail"
                                            onerror="this.src='{{ asset('images/default-thumbnail.jpg') }}'">
                                    @else
                                        <div class="thumbnail-placeholder">
                                            <i class="far fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong class="d-block mb-1">
                                        <a href="{{ route('posts.show', $post->slug) }}"
                                            class="text-decoration-none text-dark hover:text-primary">
                                            {{ Str::limit($post->title, 50) }}
                                        </a>
                                    </strong>
                                    <small class="text-muted">
                                        {{ Str::limit(strip_tags($post->excerpt), 80) }}
                                    </small>
                                </td>
                                <td>
                                    @if ($post->category)
                                        <span class="badge bg-light text-dark border">
                                            {{ $post->category->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">بدون دسته</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $post->status }}">
                                        @if ($post->status == 'draft')
                                            پیش‌نویس
                                        @elseif($post->status == 'published')
                                            منتشر شده
                                        @else
                                            آرشیو
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <small class="text-muted">ایجاد:</small>
                                        <span>{{ $post->created_at->diffForHumans() }}</span>
                                        @if ($post->published_at)
                                            <small class="text-muted mt-1">انتشار:</small>
                                            <span>{{ $post->published_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-eye text-muted"></i>
                                        <span>{{ number_format($post->view_count) }}</span>
                                    </div>
                                </td>
                                <td class="actions-cell">
                                    <div class="actions-group">
                                        <a href="{{ route('posts.show', $post->slug) }}" class="action-btn view"
                                            title="مشاهده" target="_blank">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>

                                        <a href="{{ route('user.posts.edit', $post) }}" class="action-btn edit"
                                            title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('user.posts.change-status', $post) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            <button type="submit" class="action-btn status" title="تغییر وضعیت"
                                                onclick="return confirm('آیا از تغییر وضعیت مقاله مطمئنید؟')">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('user.posts.destroy', $post) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete" title="حذف"
                                                onclick="return confirm('آیا از حذف این مقاله مطمئنید؟')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <!-- حالت خالی -->
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="far fa-newspaper"></i>
                    </div>
                    <h3 class="empty-title">مقاله‌ای یافت نشد</h3>
                    <p class="empty-description">
                        هنوز مقاله‌ای ایجاد نکرده‌اید. برای شروع نوشتن، روی دکمه "مقاله جدید" کلیک کنید.
                    </p>
                    <a href="{{ route('user.posts.create') }}" class="new-post-btn">
                        <i class="fas fa-plus"></i> ایجاد اولین مقاله
                    </a>
                </div>
            @endif
        </div>

        <!-- پagination -->
        @if ($posts->count() > 0)
            <div class="pagination-container">
                <div class="pagination-info">
                    نمایش {{ $posts->firstItem() }} تا {{ $posts->lastItem() }} از {{ $posts->total() }} مقاله
                </div>

                <ul class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($posts->onFirstPage())
                        <li class="disabled" aria-disabled="true">
                            <span><i class="fas fa-chevron-right"></i></span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $posts->previousPageUrl() }}" rel="prev">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)
                        @if ($page == $posts->currentPage())
                            <li class="active" aria-current="page">
                                <span>{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($posts->hasMorePages())
                        <li>
                            <a href="{{ $posts->nextPageUrl() }}" rel="next">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    @else
                        <li class="disabled" aria-disabled="true">
                            <span><i class="fas fa-chevron-left"></i></span>
                        </li>
                    @endif
                </ul>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        // بازنشانی فیلترها
        function resetFilters() {
            document.getElementById('filterForm').reset();
            window.location.href = "{{ route('user.posts.index') }}";
        }

        // انتخاب گروهی مقالات
        let selectedPosts = [];

        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.post-checkbox');
            const bulkActions = document.getElementById('bulkActions');

            checkboxes.forEach(cb => {
                cb.checked = checkbox.checked;
                if (checkbox.checked) {
                    if (!selectedPosts.includes(cb.value)) {
                        selectedPosts.push(cb.value);
                        cb.closest('tr').classList.add('selected-row');
                    }
                } else {
                    selectedPosts = selectedPosts.filter(id => id !== cb.value);
                    cb.closest('tr').classList.remove('selected-row');
                }
            });

            updateBulkActions();
        }

        function updateSelection(checkbox) {
            const row = checkbox.closest('tr');

            if (checkbox.checked) {
                if (!selectedPosts.includes(checkbox.value)) {
                    selectedPosts.push(checkbox.value);
                    row.classList.add('selected-row');
                }
            } else {
                selectedPosts = selectedPosts.filter(id => id !== checkbox.value);
                row.classList.remove('selected-row');
                document.getElementById('selectAll').checked = false;
            }

            updateBulkActions();
        }

        function updateBulkActions() {
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');

            selectedCount.textContent = selectedPosts.length + ' مقاله انتخاب شده';

            if (selectedPosts.length > 0) {
                bulkActions.classList.add('show');
            } else {
                bulkActions.classList.remove('show');
            }
        }

        function clearSelection() {
            selectedPosts = [];
            document.querySelectorAll('.post-checkbox').forEach(cb => {
                cb.checked = false;
                cb.closest('tr').classList.remove('selected-row');
            });
            document.getElementById('selectAll').checked = false;
            updateBulkActions();
        }

        // تغییر وضعیت گروهی
        function changeStatusSelected(status) {
            if (selectedPosts.length === 0) return;

            if (!confirm(`آیا از تغییر وضعیت ${selectedPosts.length} مقاله به "${getStatusName(status)}" مطمئنید؟`)) {
                return;
            }

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('status', status);
            selectedPosts.forEach(id => formData.append('posts[]', id));

            fetch('{{ route('user.posts.bulk-status') }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'خطا در تغییر وضعیت');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('خطا در برقراری ارتباط');
                });
        }

        // حذف گروهی
        function deleteSelected() {
            if (selectedPosts.length === 0) return;

            if (!confirm(`آیا از حذف ${selectedPosts.length} مقاله مطمئنید؟ این عمل قابل بازگشت نیست.`)) {
                return;
            }

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            selectedPosts.forEach(id => formData.append('posts[]', id));

            fetch('{{ route('user.posts.bulk-delete') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'خطا در حذف مقالات');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('خطا در برقراری ارتباط');
                });
        }

        function getStatusName(status) {
            const statuses = {
                'draft': 'پیش‌نویس',
                'published': 'منتشر شده',
                'archived': 'آرشیو'
            };
            return statuses[status] || status;
        }

        // رویدادها
        document.addEventListener('DOMContentLoaded', function() {
            // ثبت رویداد برای checkboxها
            document.querySelectorAll('.post-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateSelection(this);
                });
            });

            // جستجوی سریع
            const searchInput = document.getElementById('search');
            let searchTimeout;

            searchInput?.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    document.getElementById('filterForm').submit();
                }, 500);
            });
        });
    </script>
@endsection
