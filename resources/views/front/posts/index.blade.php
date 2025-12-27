@extends('layout')

@section('title', 'مقالات - BlogHub')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/post.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
@endsection

@section('content')
    <div class="container">
        <div class="posts-page">
            <!-- هدر صفحه -->
            <div class="page-header">
                <h1 class="page-title">مقالات</h1>
                <p class="page-subtitle">آخرین مقالات منتشر شده در بلاگ‌هاب</p>
            </div>

            <!-- محتوای اصلی -->
            <div class="content-wrapper">
                <!-- مقالات -->
                <main class="posts-main">
                    <!-- فیلترها و مرتب‌سازی -->
                    <div class="posts-filter">
                        <div class="filter-options">
                            <select class="form-select">
                                <option>مرتب‌سازی: جدیدترین</option>
                                <option>پربازدیدترین</option>
                                <option>محبوب‌ترین</option>
                            </select>

                            <select class="form-select">
                                <option>همه دسته‌بندی‌ها</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->slug }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-stats">
                            <span>{{ $posts->total() }} مقاله</span>
                        </div>
                    </div>

                    <!-- گرید مقالات -->
                    @if ($posts->count() > 0)
                        <div class="posts-grid">
                            @foreach ($posts as $post)
                                @include('front.partials.post-card', ['post' => $post])
                            @endforeach
                        </div>

                        <!-- صفحه‌بندی -->
                        @if ($posts->hasPages())
                            <div class="pagination-wrapper">
                                {{ $posts->links() }}
                            </div>
                        @endif
                    @else
                        <div class="empty-posts">
                            <i class="fas fa-newspaper fa-3x"></i>
                            <h3>مقاله‌ای یافت نشد</h3>
                            <p>هنوز مقاله‌ای منتشر نشده است.</p>
                        </div>
                    @endif
                </main>

                <!-- سایدبار -->
                <aside class="posts-sidebar">
                    <!-- جستجو -->
                    <div class="sidebar widget">
                        <h3 class="widget-title">جستجو</h3>
                        <form action="{{ route('search') }}" method="GET" class="search-form">
                            <input type="text" name="q" placeholder="جستجو در مقالات..." class="search-input">
                            <button type="submit" class="search-button">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- دسته‌بندی‌ها -->
                    <div class="sidebar widget">
                        <h3 class="widget-title">دسته‌بندی‌ها</h3>
                        <ul class="category-list">
                            @foreach ($categories as $category)
                                <li>
                                    <a href="{{ route('categories.show', $category->slug) }}">
                                        {{ $category->name }}
                                        <span class="category-count">{{ $category->posts_count }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- مقالات اخیر -->
                    @if ($recentPosts->count() > 0)
                        <div class="sidebar widget">
                            <h3 class="widget-title">مقالات اخیر</h3>
                            <div class="recent-posts">
                                @foreach ($recentPosts as $post)
                                    <div class="recent-post">
                                        @if ($post->featured_image)
                                            <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}"
                                                class="recent-post-image">
                                        @endif
                                        <div class="recent-post-content">
                                            <h4 class="recent-post-title">
                                                <a href="{{ route('posts.show', $post->slug) }}">
                                                    {{ Str::limit($post->title, 50) }}
                                                </a>
                                            </h4>
                                            <div class="recent-post-date">
                                                {{ $post->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- برچسب‌های پرکاربرد -->
                    @if ($popularTags && $popularTags->count() > 0)
                        <div class="sidebar widget">
                            <h3 class="widget-title">برچسب‌های پرکاربرد</h3>
                            <div class="tag-cloud">
                                @foreach ($popularTags as $tag)
                                    <a href="{{ route('tags.show', $tag->slug) }}" class="tag-link">
                                        {{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // جستجوی زنده
        const searchInput = document.querySelector('input[name="q"]');
        const searchForm = document.querySelector('.search-form');

        searchInput.addEventListener('input', function() {
            // می‌توانید AJAX برای جستجوی زنده اضافه کنید
        });
    </script>
@endsection
