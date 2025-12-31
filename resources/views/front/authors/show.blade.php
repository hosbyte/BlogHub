@extends('layout')

@section('title', "مقالات {$user->name}")

@section('content')
    <div class="container author-page">
        <!-- هدر پروفایل -->
        <div class="author-header">
            <div class="author-profile">
                <!-- آواتار -->
                <div class="author-avatar">
                    @if ($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="avatar-image">
                    @else
                        <div class="avatar-placeholder">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @endif
                </div>

                <!-- اطلاعات -->
                <div class="author-info">
                    <h1 class="author-name">{{ $user->name }}</h1>

                    @if ($user->bio)
                        <p class="author-bio">{{ $user->bio }}</p>
                    @endif

                    <div class="author-meta">
                        <span class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            عضو از {{ verta($user->created_at)->format('d F Y') }}
                        </span>

                        @if ($user->website)
                            <a href="{{ $user->website }}" target="_blank" class="meta-item">
                                <i class="fas fa-globe"></i>
                                وبسایت
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- آمار -->
            <div class="author-stats">
                <div class="stat-card">
                    <div class="stat-number">{{ $stats['total_posts'] }}</div>
                    <div class="stat-label">مقاله</div>
                </div>

                <div class="stat-card">
                    <div class="stat-number">{{ number_format($stats['total_views']) }}</div>
                    <div class="stat-label">بازدید کل</div>
                </div>

                <div class="stat-card">
                    <div class="stat-number">{{ number_format($stats['avg_views'], 0) }}</div>
                    <div class="stat-label">میانگین بازدید</div>
                </div>
            </div>
        </div>

        <!-- بقیه کدها... -->
        {{-- ادامه front/authors/show.blade.php --}}

        <div class="page-layout">
            <!-- محتوای اصلی -->
            <main class="main-content">
                <!-- هدر مقالات -->
                <div class="section-header">
                    <h2 class="section-title">
                        <i class="fas fa-newspaper"></i>
                        مقالات {{ $user->name }}
                    </h2>
                    <div class="section-actions">
                        <select id="sortArticles" class="form-select">
                            <option value="newest" selected>جدیدترین</option>
                            <option value="popular">پربازدیدترین</option>
                            <option value="oldest">قدیمی‌ترین</option>
                        </select>
                    </div>
                </div>

                <!-- لیست مقالات -->
                @if ($posts->count() > 0)
                    <div class="posts-grid">
                        @foreach ($posts as $post)
                            <article class="post-card author-post-card">
                                <!-- تصویر شاخص -->
                                <div class="post-image">
                                    <a href="{{ route('posts.show', $post->slug) }}">
                                        @if ($post->featured_image)
                                            <img src="{{ asset('storage/' . $post->featured_image) }}"
                                                alt="{{ $post->title }}" loading="lazy">
                                        @else
                                            <div class="image-placeholder">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                    </a>
                                    <!-- دسته‌بندی -->
                                    <a href="{{ route('categories.show', $post->category->slug) }}" class="post-category">
                                        {{ $post->category->name }}
                                    </a>
                                </div>

                                <!-- محتوای کارت -->
                                <div class="post-content">
                                    <div class="post-meta">
                                        <time class="post-date">
                                            <i class="fas fa-calendar"></i>
                                            {{ verta($post->published_at)->format('d F Y') }}
                                        </time>
                                        <span class="post-views">
                                            <i class="fas fa-eye"></i>
                                            {{ number_format($post->view_count) }}
                                        </span>
                                        <span class="post-read-time">
                                            <i class="fas fa-clock"></i>
                                            {{ $post->reading_time }} دقیقه
                                        </span>
                                    </div>

                                    <h3 class="post-title">
                                        <a href="{{ route('posts.show', $post->slug) }}">
                                            {{ $post->title }}
                                        </a>
                                    </h3>

                                    <p class="post-excerpt">
                                        {{ Str::limit(strip_tags($post->excerpt ?: $post->content), 150) }}
                                    </p>

                                    <!-- برچسب‌ها -->
                                    @if ($post->tags->count() > 0)
                                        <div class="post-tags">
                                            @foreach ($post->tags->take(3) as $tag)
                                                <a href="{{ route('tags.show', $tag->slug) }}" class="tag-badge">
                                                    {{ $tag->name }}
                                                </a>
                                            @endforeach
                                            @if ($post->tags->count() > 3)
                                                <span class="tag-more">+{{ $post->tags->count() - 3 }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <!-- صفحه‌بندی -->
                    <div class="pagination-wrapper">
                        {{ $posts->links('vendor.pagination.tailwind') }}
                    </div>
                @else
                    <!-- حالت خالی -->
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-pen-fancy"></i>
                        </div>
                        <h3 class="empty-title">هنوز مقاله‌ای منتشر نکرده است</h3>
                        <p class="empty-description">
                            {{ $user->name }} تاکنون مقاله‌ای منتشر نکرده است.
                        </p>
                    </div>
                @endif
            </main>

            <!-- سایدبار -->
            <aside class="sidebar">
                <!-- دسته‌بندی‌های محبوب نویسنده -->
                @if ($popularCategories->count() > 0)
                    <div class="sidebar-widget">
                        <h3 class="widget-title">
                            <i class="fas fa-folder"></i>
                            دسته‌بندی‌های پرتکرار
                        </h3>
                        <div class="widget-content">
                            <ul class="category-list">
                                @foreach ($popularCategories as $category)
                                    <li class="category-item">
                                        <a href="{{ route('categories.show', $category->slug) }}" class="category-link">
                                            <span class="category-name">{{ $category->name }}</span>
                                            <span class="category-count">{{ $category->posts_count }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <!-- برچسب‌های پرکاربرد نویسنده -->
                @if ($popularTags->count() > 0)
                    <div class="sidebar-widget">
                        <h3 class="widget-title">
                            <i class="fas fa-tags"></i>
                            برچسب‌های پرکاربرد
                        </h3>
                        <div class="widget-content">
                            <div class="tags-cloud">
                                @foreach ($popularTags as $tag)
                                    <a href="{{ route('tags.show', $tag->slug) }}" class="tag-item"
                                        style="font-size: {{ 14 + $tag->posts_count * 0.5 }}px">
                                        {{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- مقالات پربازدید این نویسنده -->
                @if ($popularPosts->count() > 0)
                    <div class="sidebar-widget">
                        <h3 class="widget-title">
                            <i class="fas fa-chart-line"></i>
                            پربازدیدترین‌ها
                        </h3>
                        <div class="widget-content">
                            <div class="popular-posts">
                                @foreach ($popularPosts as $post)
                                    <article class="popular-post">
                                        <div class="popular-post-image">
                                            <a href="{{ route('posts.show', $post->slug) }}">
                                                @if ($post->featured_image)
                                                    <img src="{{ asset('storage/' . $post->featured_image) }}"
                                                        alt="{{ $post->title }}" loading="lazy">
                                                @else
                                                    <div class="image-placeholder small">
                                                        <i class="fas fa-image"></i>
                                                    </div>
                                                @endif
                                            </a>
                                        </div>
                                        <div class="popular-post-content">
                                            <h4 class="popular-post-title">
                                                <a href="{{ route('posts.show', $post->slug) }}">
                                                    {{ Str::limit($post->title, 50) }}
                                                </a>
                                            </h4>
                                            <div class="popular-post-meta">
                                                <span class="post-views">
                                                    <i class="fas fa-eye"></i>
                                                    {{ number_format($post->view_count) }}
                                                </span>
                                                <time class="post-date">
                                                    {{ verta($post->published_at)->format('Y/m/d') }}
                                                </time>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- ویجت اشتراک (اختیاری) -->
                <div class="sidebar-widget">
                    <h3 class="widget-title">دنبال کردن نویسنده</h3>
                    <div class="widget-content">
                        <p class="widget-text">از مقالات جدید این نویسنده با خبر شوید</p>
                        <button class="btn btn-primary btn-block follow-btn" data-author-id="{{ $user->id }}">
                            <i class="fas fa-user-plus"></i>
                            دنبال کردن
                        </button>
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection
// در @push('scripts') در انتهای View
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // مرتب‌سازی مقالات
                const sortSelect = document.getElementById('sortArticles');

                if (sortSelect) {
                    sortSelect.addEventListener('change', function() {
                        const sortBy = this.value;
                        const currentUrl = window.location.href;
                        const url = new URL(currentUrl);

                        // حذف پارامتر صفحه‌بندی
                        url.searchParams.delete('page');

                        // اضافه کردن پارامتر مرتب‌سازی
                        url.searchParams.set('sort', sortBy);

                        // ریدایرکت به URL جدید
                        window.location.href = url.toString();
                    });

                    // تنظیم مقدار فعلی از URL
                    const urlParams = new URLSearchParams(window.location.search);
                    const currentSort = urlParams.get('sort') || 'newest';
                    sortSelect.value = currentSort;
                }

                // سیستم دنبال کردن (اختیاری)
                const followBtn = document.querySelector('.follow-btn');

                if (followBtn) {
                    followBtn.addEventListener('click', function() {
                        const authorId = this.dataset.authorId;
                        const isFollowing = this.classList.contains('following');

                        // شبیه‌سازی API call
                        fetch('/api/authors/' + authorId + '/follow', {
                                method: isFollowing ? 'DELETE' : 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .content,
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    if (isFollowing) {
                                        this.classList.remove('following');
                                        this.innerHTML = '<i class="fas fa-user-plus"></i> دنبال کردن';
                                    } else {
                                        this.classList.add('following');
                                        this.innerHTML = '<i class="fas fa-user-check"></i> دنبال شده';
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    });
                }
            });
        </script>
    @endpush
