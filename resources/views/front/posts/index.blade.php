@extends('layout')

@section('title', 'مقالات - BlogHub')

@section('styles')
    <style>
        /* استایل‌های صفحه لیست مقالات */
        .page-header {
            background: linear-gradient(135deg, #2d3047 0%, #1a1a2e 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
            border-radius: 0 0 20px 20px;
        }

        .page-title {
            font-size: 2.2rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .page-description {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto;
        }

        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e9ecef;
        }

        .filter-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #2d3047;
            font-size: 0.9rem;
        }

        .filter-select,
        .filter-input {
            padding: 10px 15px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .filter-select:focus,
        .filter-input:focus {
            outline: none;
            border-color: #4361ee;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        .filter-button {
            padding: 10px 20px;
            background: #4361ee;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s ease;
        }

        .filter-button:hover {
            background: #3a56d4;
        }

        .sort-options {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .sort-btn {
            padding: 8px 15px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            color: #6c757d;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .sort-btn:hover,
        .sort-btn.active {
            background: #4361ee;
            color: white;
            border-color: #4361ee;
        }

        .posts-container {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 30px;
        }

        @media (max-width: 992px) {
            .posts-container {
                grid-template-columns: 1fr;
            }
        }

        .sidebar {
            background: white;
            border-radius: 10px;
            padding: 20px;
            border: 1px solid #e9ecef;
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .sidebar-title {
            font-size: 1.2rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #4361ee;
            color: #2d3047;
        }

        .category-list,
        .recent-posts-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .category-item,
        .recent-post-item {
            margin-bottom: 10px;
        }

        .category-link,
        .recent-post-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 6px;
            text-decoration: none;
            color: #495057;
            transition: all 0.3s ease;
        }

        .category-link:hover,
        .recent-post-link:hover {
            background: #4361ee;
            color: white;
            transform: translateX(-5px);
        }

        .category-count,
        .recent-post-date {
            font-size: 0.8rem;
            opacity: 0.7;
        }

        .category-link:hover .category-count,
        .recent-post-link:hover .recent-post-date {
            color: white;
            opacity: 1;
        }

        .empty-posts {
            text-align: center;
            padding: 50px;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 30px 0;
        }

        .empty-posts i {
            font-size: 3rem;
            color: #adb5bd;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <!-- هدر صفحه -->
    <header class="page-header">
        <div class="container">
            <h1 class="page-title">مقالات BlogHub</h1>
            <p class="page-description">
                مجموعه‌ای از بهترین مقالات آموزشی، خبری و تحلیلی در زمینه‌های مختلف فناوری و برنامه‌نویسی
            </p>
        </div>
    </header>

    <div class="container">
        <!-- بخش فیلتر و جستجو -->
        <section class="filter-section">
            <form action="{{ route('posts.index') }}" method="GET" class="filter-form">
                <div class="filter-group">
                    <label class="filter-label">جستجو در مقالات</label>
                    <input type="text" name="search" placeholder="عنوان یا محتوای مقاله..." class="filter-input"
                        value="{{ request('search') }}">
                </div>

                <div class="filter-group">
                    <label class="filter-label">دسته‌بندی</label>
                    <select name="category" class="filter-select">
                        <option value="">همه دسته‌بندی‌ها</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}"
                                {{ request('category') == $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="filter-group">
                    <button type="submit" class="filter-button">
                        <i class="fas fa-search"></i> جستجو
                    </button>
                    @if (request()->hasAny(['search', 'category', 'author']))
                        <a href="{{ route('posts.index') }}" class="btn btn-outline" style="margin-top: 10px;">
                            <i class="fas fa-times"></i> حذف فیلترها
                        </a>
                    @endif
                </div>
            </form>

            <!-- گزینه‌های مرتب‌سازی -->
            <div class="sort-options">
                <span style="font-weight: 600; color: #2d3047; margin-left: 10px;">مرتب‌سازی:</span>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}"
                    class="sort-btn {{ request('sort', 'newest') == 'newest' ? 'active' : '' }}">
                    <i class="fas fa-clock"></i> جدیدترین
                </a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}"
                    class="sort-btn {{ request('sort') == 'popular' ? 'active' : '' }}">
                    <i class="fas fa-fire"></i> پربازدیدترین
                </a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'featured']) }}"
                    class="sort-btn {{ request('sort') == 'featured' ? 'active' : '' }}">
                    <i class="fas fa-star"></i> مقالات ویژه
                </a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}"
                    class="sort-btn {{ request('sort') == 'oldest' ? 'active' : '' }}">
                    <i class="fas fa-history"></i> قدیمی‌ترین
                </a>
            </div>
        </section>

        <!-- نتایج -->
        <div class="posts-container">
            <!-- مقالات -->
            <main class="posts-main">
                @if ($posts->count() > 0)
                    <!-- آمار نتایج -->
                    <div style="margin-bottom: 20px; color: #6c757d; font-size: 0.9rem;">
                        <i class="fas fa-chart-bar"></i>
                        نمایش {{ $posts->firstItem() }} تا {{ $posts->lastItem() }} از {{ $posts->total() }} مقاله
                        @if (request('search'))
                            برای "{{ request('search') }}"
                        @endif
                    </div>

                    <!-- لیست مقالات -->
                    <div class="posts-grid">
                        @foreach ($posts as $post)
                            @include('front.posts.partials.post-card', ['post' => $post])
                        @endforeach
                    </div>

                    <!-- صفحه‌بندی -->
                    <div class="pagination-container" style="margin-top: 40px;">
                        {{ $posts->links() }}
                    </div>
                @else
                    <!-- حالت خالی -->
                    <div class="empty-posts">
                        <i class="fas fa-newspaper"></i>
                        <h3>مقاله‌ای یافت نشد</h3>
                        <p>متأسفانه هیچ مقاله‌ای مطابق با جستجوی شما پیدا نشد.</p>
                        <a href="{{ route('posts.index') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-list"></i> مشاهده همه مقالات
                        </a>
                    </div>
                @endif
            </main>

            <!-- سایدبار -->
            <aside class="sidebar">
                <!-- دسته‌بندی‌ها -->
                <div class="sidebar-widget">
                    <h3 class="sidebar-title">
                        <i class="fas fa-folder"></i> دسته‌بندی‌ها
                    </h3>
                    <ul class="category-list">
                        <li class="category-item">
                            <a href="{{ route('posts.index') }}" class="category-link">
                                <span>همه مقالات</span>
                                <span class="category-count">{{ App\Models\Post::published()->count() }}</span>
                            </a>
                        </li>
                        @foreach ($categories as $category)
                            <li class="category-item">
                                <a href="{{ route('posts.index', ['category' => $category->slug]) }}"
                                    class="category-link">
                                    <span>{{ $category->name }}</span>
                                    <span class="category-count">{{ $category->posts_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- مقالات اخیر -->
                @if (isset($recentPosts) && $recentPosts->count() > 0)
                    <div class="sidebar-widget" style="margin-top: 30px;">
                        <h3 class="sidebar-title">
                            <i class="fas fa-clock"></i> مقالات اخیر
                        </h3>
                        <ul class="recent-posts-list">
                            @foreach ($recentPosts as $post)
                                <li class="recent-post-item">
                                    <a href="{{ route('posts.show', $post->slug) }}" class="recent-post-link">
                                        <span>{{ Str::limit($post->title, 30) }}</span>
                                        <span class="recent-post-date">{{ $post->published_at->format('m/d') }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- آمار -->
                <div class="sidebar-widget"
                    style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <h3 class="sidebar-title" style="font-size: 1rem; border-bottom: none;">
                        <i class="fas fa-chart-pie"></i> آمار مقالات
                    </h3>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 10px;">
                        <div style="text-align: center;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: #4361ee;">
                                {{ App\Models\Post::published()->count() }}
                            </div>
                            <div style="font-size: 0.8rem; color: #6c757d;">مقاله</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: #7209b7;">
                                {{ App\Models\Category::count() }}
                            </div>
                            <div style="font-size: 0.8rem; color: #6c757d;">دسته‌بندی</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: #f72585;">
                                {{ App\Models\User::authors()->count() }}
                            </div>
                            <div style="font-size: 0.8rem; color: #6c757d;">نویسنده</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-size: 1.5rem; font-weight: 700; color: #4cc9f0;">
                                {{ App\Models\Comment::approved()->count() }}
                            </div>
                            <div style="font-size: 0.8rem; color: #6c757d;">نظر</div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // فعال کردن select2 برای فیلترها (اگر نیاز داری)
            console.log('صفحه لیست مقالات لود شد');

            // انیمیشن برای کارت‌ها
            const postCards = document.querySelectorAll('.post-card');
            postCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.05}s`;
                card.classList.add('fade-in');
            });

            // تغییر رنگ دکمه‌های فیلتر فعال
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('search') && urlParams.get('search').trim() !== '') {
                document.querySelector('[name="search"]').style.borderColor = '#4361ee';
                document.querySelector('[name="search"]').style.backgroundColor = '#f8f9ff';
            }
        });
    </script>
@endsection
