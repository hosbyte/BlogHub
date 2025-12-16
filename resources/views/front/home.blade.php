@extends('layout')

@section('title', 'صفحه اصلی - BlogHub')

@section('styles')
    <style>
        /* استایل‌های خاص صفحه اصلی */
        .hero-section {
            background: linear-gradient(135deg, #4361ee 0%, #3a56d4 100%);
            color: white;
            padding: 80px 0;
            border-radius: 0 0 20px 20px;
            margin-bottom: 50px;
            text-align: center;
        }

        .hero-title {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: 800;
        }

        .hero-description {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 30px;
            opacity: 0.9;
        }

        .section-title {
            font-size: 1.8rem;
            margin: 40px 0 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #4361ee;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: #4361ee;
        }

        .empty-state {
            text-align: center;
            padding: 50px;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 30px 0;
        }

        .empty-state i {
            font-size: 3rem;
            color: #adb5bd;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <!-- بخش هیرو (معرفی) -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">به BlogHub خوش آمدید</h1>
            <p class="hero-description">
                سیستم مدیریت وبلاگ چندکاربره با قابلیت‌های پیشرفته برای نویسندگان و مدیران محتوا
            </p>
            <div class="hero-buttons">
                <a href="{{ route('posts.index') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-newspaper"></i> مشاهده مقالات
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-tachometer-alt"></i> پنل کاربری
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-user-plus"></i> ثبت‌نام نویسنده
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <div class="container">

        <!-- مقالات ویژه -->
        @if (isset($featuredPosts) && $featuredPosts->count() > 0)
            <section class="featured-section">
                <h2 class="section-title">
                    <i class="fas fa-star"></i> مقالات ویژه
                </h2>
                <div class="posts-grid">
                    @foreach ($featuredPosts as $post)
                        @include('front.posts.partials.post-card', [
                            'post' => $post,
                            'class' => 'featured-post',
                        ])
                    @endforeach
                </div>
            </section>
        @endif

        <!-- مقالات اخیر -->
        @if (isset($recentPosts) && $recentPosts->count() > 0)
            <section class="recent-section">
                <h2 class="section-title">
                    <i class="fas fa-clock"></i> جدیدترین مقالات
                </h2>
                <div class="posts-grid">
                    @foreach ($recentPosts as $post)
                        @include('front.posts.partials.post-card', ['post' => $post])
                    @endforeach
                </div>

                {{-- دکمه مشاهده همه --}}
                @if ($recentPosts->count() >= 6)
                    <div class="text-center mt-4">
                        <a href="{{ route('posts.index') }}" class="btn btn-primary">
                            <i class="fas fa-list"></i> مشاهده همه مقالات
                        </a>
                    </div>
                @endif
            </section>
        @else
            <!-- اگر مقاله‌ای وجود نداره -->
            <div class="empty-state">
                <i class="fas fa-newspaper"></i>
                <h3>هنوز مقاله‌ای منتشر نشده است</h3>
                <p>اولین نفری باشید که در BlogHub مطلب منتشر می‌کند!</p>
                @auth
                    {{-- <a href="{{ route('author.posts.create') }}" class="btn btn-primary mt-3"> --}}
                        <i class="fas fa-plus"></i> ایجاد مقاله جدید
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-user-plus"></i> ثبت‌نام و شروع به نوشتن
                    </a>
                @endauth
            </div>
        @endif

        <!-- دسته‌بندی‌ها -->
        @if (isset($categories) && $categories->count() > 0)
            <section class="categories-section">
                <h2 class="section-title">
                    <i class="fas fa-th-large"></i> دسته‌بندی‌ها
                </h2>
                <div class="categories-grid">
                    @foreach ($categories as $category)
                        <a href="{{ route('categories.show', $category->slug) }}" class="category-card">
                            <div class="category-icon">
                                <i class="fas fa-folder"></i>
                            </div>
                            <div class="category-info">
                                <h3>{{ $category->name }}</h3>
                                <p>{{ $category->posts_count ?? 0 }} مقاله</p>
                            </div>
                            <i class="fas fa-chevron-left category-arrow"></i>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- آمار سایت -->
        <section class="stats-section">
            <h2 class="section-title">
                <i class="fas fa-chart-bar"></i> آمار BlogHub
            </h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-newspaper"></i>
                    <div class="stat-info">
                        <h3>{{ $totalPosts ?? 0 }}</h3>
                        <p>مقاله منتشر شده</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <div class="stat-info">
                        <h3>{{ $totalUsers ?? 0 }}</h3>
                        <p>نویسنده فعال</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-comments"></i>
                    <div class="stat-info">
                        <h3>{{ $totalComments ?? 0 }}</h3>
                        <p>نظر ثبت شده</p>
                    </div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-eye"></i>
                    <div class="stat-info">
                        <h3>{{ $totalViews ?? 0 }}</h3>
                        <p>بازدید کل</p>
                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection

@section('scripts')
    <script>
        // اسکریپت‌های خاص صفحه اصلی
        document.addEventListener('DOMContentLoaded', function() {
            console.log('صفحه اصلی BlogHub لود شد');

            // انیمیشن برای کارت‌ها
            const postCards = document.querySelectorAll('.post-card');
            postCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('fade-in');
            });
        });
    </script>
@endsection
