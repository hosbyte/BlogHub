@extends('layout')

@section('title', $category->name . ' - مقالات - BlogHub')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/post.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <style>
        /* استایل‌های مخصوص صفحه دسته‌بندی */
        .category-header {
            background: linear-gradient(135deg, #4361ee, #7209b7);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
            border-radius: 0 0 20px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .category-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" opacity="0.1"><path d="M0,50 Q250,0 500,50 T1000,50" fill="none" stroke="white" stroke-width="2"/></svg>');
            background-size: cover;
        }

        .category-title {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .category-meta {
            font-size: 1.1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .category-description {
            max-width: 700px;
            margin: 20px auto 0;
            font-size: 1.1rem;
            line-height: 1.7;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .breadcrumb {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .breadcrumb-item {
            color: rgba(255, 255, 255, 0.8);
        }

        .breadcrumb-item a {
            color: white;
            text-decoration: none;
            transition: opacity 0.3s;
        }

        .breadcrumb-item a:hover {
            opacity: 0.9;
        }

        .breadcrumb-separator {
            color: rgba(255, 255, 255, 0.6);
        }

        /* محتوای اصلی */
        .category-page {
            padding: 0 0 40px;
        }

        .content-wrapper {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 40px;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        @media (max-width: 992px) {
            .content-wrapper {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .category-title {
                font-size: 2.2rem;
            }

            .category-meta {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
@endsection

@section('content')
    <!-- هدر دسته‌بندی -->
    <header class="category-header">
        <div class="container">
            <!-- مسیر ناوبری -->
            <nav class="breadcrumb">
                <span class="breadcrumb-item">
                    <a href="{{ route('home') }}">خانه</a>
                </span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-item">
                    <a href="{{ route('posts.index') }}">مقالات</a>
                </span>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-item active">
                    {{ $category->name }}
                </span>
            </nav>

            <!-- عنوان دسته‌بندی -->
            <h1 class="category-title">
                <i class="fas fa-folder-open"></i>
                {{ $category->name }}
            </h1>

            <!-- اطلاعات دسته‌بندی -->
            <div class="category-meta">
                <span>
                    <i class="fas fa-newspaper"></i>
                    {{ $category->posts_count }} مقاله
                </span>

                @if ($category->parent)
                    <span>
                        <i class="fas fa-level-up-alt"></i>
                        دسته‌بندی والد:
                        <a href="{{ route('categories.show', $category->parent->slug) }}" style="color: white;">
                            {{ $category->parent->name }}
                        </a>
                    </span>
                @endif

                @if ($category->children->count() > 0)
                    <span>
                        <i class="fas fa-level-down-alt"></i>
                        {{ $category->children->count() }} زیردسته
                    </span>
                @endif
            </div>

            <!-- توضیحات دسته‌بندی -->
            @if ($category->description)
                <div class="category-description">
                    {{ $category->description }}
                </div>
            @endif
        </div>
    </header>

    <div class="category-page">
        <div class="content-wrapper">
            <!-- مقالات این دسته‌بندی -->
            <main class="posts-main">
                <!-- فیلترها -->
                <div class="posts-filter">
                    <div class="filter-header">
                        <h2 class="filter-title">مقالات دسته‌بندی "{{ $category->name }}"</h2>
                        <div class="filter-stats">
                            {{ $posts->total() }} مقاله
                        </div>
                    </div>

                    <div class="filter-options">
                        <select class="filter-select" id="sortSelect">
                            <option value="newest">مرتب‌سازی: جدیدترین</option>
                            <option value="popular">پربازدیدترین</option>
                            <option value="featured">مقالات ویژه</option>
                        </select>
                    </div>
                </div>

                <!-- گرید مقالات -->
                @if ($posts->count() > 0)
                    <div class="posts-grid">
                        @foreach ($posts as $post)
                            @include('front.posts.partials.post-card', ['post' => $post])
                        @endforeach
                    </div>

                    <!-- صفحه‌بندی -->
                    @if ($posts->hasPages())
                        <div class="pagination-wrapper">
                            <ul class="pagination">
                                <!-- Previous Page Link -->
                                @if ($posts->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">&laquo; قبلی</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $posts->previousPageUrl() }}" rel="prev">&laquo;
                                            قبلی</a>
                                    </li>
                                @endif

                                <!-- Pagination Elements -->
                                @foreach (range(1, $posts->lastPage()) as $i)
                                    @if ($i == $posts->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">{{ $i }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $posts->url($i) }}">{{ $i }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                <!-- Next Page Link -->
                                @if ($posts->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $posts->nextPageUrl() }}" rel="next">بعدی
                                            &raquo;</a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">بعدی &raquo;</span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <i class="fas fa-newspaper"></i>
                        <h3>مقاله‌ای در این دسته‌بندی وجود ندارد</h3>
                        <p>هنوز مقاله‌ای در دسته‌بندی "{{ $category->name }}" منتشر نشده است.</p>
                        <a href="{{ route('posts.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-right"></i> مشاهده همه مقالات
                        </a>
                    </div>
                @endif

                <!-- زیردسته‌ها (اگر وجود دارند) -->
                @if ($category->children->count() > 0)
                    <div class="subcategories-section">
                        <h3 class="section-title">
                            <i class="fas fa-folder-tree"></i>
                            زیردسته‌های "{{ $category->name }}"
                        </h3>

                        <div class="subcategories-grid">
                            @foreach ($category->children as $child)
                                <a href="{{ route('categories.show', $child->slug) }}" class="subcategory-card">
                                    <div class="subcategory-icon">
                                        <i class="fas fa-folder"></i>
                                    </div>
                                    <div class="subcategory-info">
                                        <h4>{{ $child->name }}</h4>
                                        <p class="subcategory-count">{{ $child->posts_count }} مقاله</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </main>

            <!-- سایدبار -->
            <aside class="posts-sidebar">
                <!-- جستجو -->
                <div class="sidebar widget">
                    <h3 class="widget-title">جستجو در مقالات</h3>
                    <form action="{{ route('search') }}" method="GET" class="search-form">
                        <input type="text" name="q" placeholder="جستجو در مقالات..." class="search-input">
                        <button type="submit" class="search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- دسته‌بندی‌های دیگر -->
                <div class="sidebar widget">
                    <h3 class="widget-title">دسته‌بندی‌ها</h3>
                    <ul class="category-list">
                        @foreach ($categories as $cat)
                            <li class="{{ $cat->id == $category->id ? 'active' : '' }}">
                                <a href="{{ route('categories.show', $cat->slug) }}">
                                    {{ $cat->name }}
                                    <span class="category-count">{{ $cat->posts_count }}</span>
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

                <!-- برچسب‌های مرتبط -->
                @if ($category->posts->count() > 0)
                    <div class="sidebar widget">
                        <h3 class="widget-title">برچسب‌های مرتبط</h3>
                        <div class="tag-cloud">
                            @php
                                // دریافت برچسب‌های مرتبط با این دسته‌بندی
                                $relatedTags = \App\Models\Tag::whereHas('posts', function ($query) use ($category) {
                                    $query
                                        ->where('category_id', $category->id)
                                        ->where('status', 'published')
                                        ->where('published_at', '<=', now());
                                })
                                    ->withCount([
                                        'posts' => function ($query) use ($category) {
                                            $query
                                                ->where('category_id', $category->id)
                                                ->where('status', 'published')
                                                ->where('published_at', '<=', now());
                                        },
                                    ])
                                    ->orderBy('posts_count', 'desc')
                                    ->limit(15)
                                    ->get();
                            @endphp

                            @foreach ($relatedTags as $tag)
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
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('صفحه دسته‌بندی لود شد');

            // مرتب‌سازی
            const sortSelect = document.getElementById('sortSelect');

            sortSelect.addEventListener('change', function() {
                const sortBy = this.value;
                const currentUrl = new URL(window.location.href);

                if (sortBy !== 'newest') {
                    currentUrl.searchParams.set('sort', sortBy);
                } else {
                    currentUrl.searchParams.delete('sort');
                }

                window.location.href = currentUrl.toString();
            });

            // تنظیم مقدار اولیه مرتب‌سازی
            const urlParams = new URLSearchParams(window.location.search);
            const currentSort = urlParams.get('sort') || 'newest';
            sortSelect.value = currentSort;
        });
    </script>
@endsection
