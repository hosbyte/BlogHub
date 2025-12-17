@extends('layout')

@section('title', $category->name . ' - BlogHub')

@section('styles')
    <style>
        /* استایل‌های صفحه دسته‌بندی */
        .category-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 4rem 0;
            margin-bottom: 3rem;
            border-radius: 0 0 var(--radius-xl) var(--radius-xl);
            text-align: center;
        }

        .category-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }

        .category-description {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto 2rem;
            line-height: 1.6;
        }

        .category-meta {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-md);
            min-width: 120px;
        }

        .meta-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .meta-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .breadcrumb {
            background: var(--light-color);
            padding: 1rem;
            border-radius: var(--radius-md);
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .breadcrumb a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .breadcrumb .separator {
            margin: 0 0.5rem;
            color: var(--gray);
        }

        .breadcrumb .current {
            color: var(--dark-color);
            font-weight: 600;
        }

        /* بخش زیردسته‌ها */
        .subcategories-section {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 2rem;
            margin-bottom: 3rem;
            border: 1px solid var(--gray-light);
            box-shadow: var(--shadow-sm);
        }

        .section-title {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--primary-color);
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            color: var(--primary-color);
        }

        .subcategories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }

        .subcategory-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--light-color);
            border-radius: var(--radius-md);
            text-decoration: none;
            color: var(--dark-color);
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .subcategory-card:hover {
            transform: translateY(-3px);
            border-color: var(--primary-color);
            box-shadow: var(--shadow-md);
            background: var(--white);
        }

        .subcategory-icon {
            width: 50px;
            height: 50px;
            background: var(--primary-color);
            color: var(--white);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .subcategory-info h3 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
            font-weight: 600;
        }

        .subcategory-info p {
            font-size: 0.85rem;
            color: var(--gray);
        }

        /* بخش مقالات */
        .posts-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .posts-count {
            color: var(--gray-dark);
            font-size: 0.95rem;
        }

        .sort-options {
            display: flex;
            gap: 0.5rem;
        }

        .sort-btn {
            padding: 0.5rem 1rem;
            background: var(--white);
            border: 1px solid var(--gray-light);
            border-radius: var(--radius-md);
            color: var(--gray-dark);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .sort-btn:hover,
        .sort-btn.active {
            background: var(--primary-color);
            color: var(--white);
            border-color: var(--primary-color);
        }

        /* دسته‌بندی‌های هم‌سطح */
        .sibling-categories {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid var(--gray-light);
        }

        .sibling-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .sibling-card {
            padding: 1rem;
            background: var(--light-color);
            border-radius: var(--radius-md);
            text-decoration: none;
            color: var(--dark-color);
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .sibling-card:hover {
            background: var(--white);
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        /* پیام خالی */
        .empty-state {
            text-align: center;
            padding: 3rem;
            background: var(--light-color);
            border-radius: var(--radius-lg);
            margin: 2rem 0;
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--gray);
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--gray-dark);
            margin-bottom: 1.5rem;
        }

        /* ریسپانسیو */
        @media (max-width: 768px) {
            .category-header {
                padding: 3rem 0;
            }

            .category-title {
                font-size: 2rem;
            }

            .category-meta {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }

            .meta-item {
                width: 100%;
                max-width: 200px;
            }

            .subcategories-grid {
                grid-template-columns: 1fr;
            }

            .posts-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .sort-options {
                width: 100%;
                justify-content: center;
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
                <a href="{{ route('home') }}">صفحه اصلی</a>
                <span class="separator">/</span>
                <a href="{{ route('posts.index') }}">مقالات</a>
                <span class="separator">/</span>
                @if ($category->hasParent() && $category->parent)
                    <a href="{{ route('categories.show', $category->parent->slug) }}">
                        {{ $category->parent->name }}
                    </a>
                    <span class="separator">/</span>
                @endif
                <span class="current">{{ $category->name }}</span>
            </nav>

            <!-- عنوان و توضیحات -->
            <h1 class="category-title">{{ $category->name }}</h1>

            @if ($category->description)
                <p class="category-description">{{ $category->description }}</p>
            @endif

            <!-- آمار دسته‌بندی -->
            <div class="category-meta">
                <div class="meta-item">
                    <div class="meta-value">{{ $posts->total() }}</div>
                    <div class="meta-label">مقاله</div>
                </div>

                @if ($category->hasParent())
                    <div class="meta-item">
                        <div class="meta-value">{{ $subcategories->count() }}</div>
                        <div class="meta-label">زیردسته</div>
                    </div>
                @endif

                <div class="meta-item">
                    <div class="meta-value">
                        {{ $category->created_at ? $category->created_at->format('Y/m/d') : '--' }}
                    </div>
                    <div class="meta-label">تاریخ ایجاد</div>
                </div>

                <div class="meta-item">
                    <div class="meta-value">{{ $category->user->name ?? 'نامشخص' }}</div>
                    <div class="meta-label">ایجاد کننده</div>
                </div>
            </div>
        </div>
    </header>

    <div class="container">

        <!-- زیردسته‌ها -->
        @if ($subcategories->count() > 0)
            <section class="subcategories-section">
                <h2 class="section-title">
                    <i class="fas fa-folder-tree"></i> زیردسته‌های {{ $category->name }}
                </h2>

                <div class="subcategories-grid">
                    @foreach ($subcategories as $subcategory)
                        <a href="{{ route('categories.show', $subcategory->slug) }}" class="subcategory-card">
                            <div class="subcategory-icon">
                                <i class="fas fa-folder"></i>
                            </div>
                            <div class="subcategory-info">
                                <h3>{{ $subcategory->name }}</h3>
                                <p>{{ $subcategory->posts_count }} مقاله</p>
                            </div>
                            <i class="fas fa-chevron-left" style="margin-right: auto;"></i>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- مقالات دسته‌بندی -->
        <section class="posts-section">
            <div class="posts-header">
                <div>
                    <h2 class="section-title" style="border: none; padding: 0; margin: 0;">
                        <i class="fas fa-newspaper"></i> مقالات {{ $category->name }}
                    </h2>
                    <p class="posts-count">
                        نمایش {{ $posts->firstItem() }} تا {{ $posts->lastItem() }} از {{ $posts->total() }} مقاله
                    </p>
                </div>

                <div class="sort-options">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}"
                        class="sort-btn {{ request('sort', 'newest') == 'newest' ? 'active' : '' }}">
                        جدیدترین
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}"
                        class="sort-btn {{ request('sort') == 'popular' ? 'active' : '' }}">
                        پربازدیدترین
                    </a>
                </div>
            </div>

            @if ($posts->count() > 0)
                <div class="posts-grid">
                    @foreach ($posts as $post)
                        @include('front.posts.partials.post-card', ['post' => $post])
                    @endforeach
                </div>

                <!-- صفحه‌بندی -->
                <div class="pagination-container" style="margin-top: 3rem;">
                    {{ $posts->links() }}
                </div>
            @else
                <!-- حالت خالی -->
                <div class="empty-state">
                    <i class="fas fa-newspaper"></i>
                    <h3>هنوز مقاله‌ای در این دسته‌بندی وجود ندارد</h3>
                    <p>اولین نفری باشید که در این دسته‌بندی مطلب منتشر می‌کند!</p>

                    @auth
                        @if (Auth::user()->isAuthor() || Auth::user()->isAdmin())
                            <a href="{{ route('author.posts.create') }}?category={{ $category->id }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> ایجاد مقاله جدید
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> ثبت‌نام و شروع به نوشتن
                        </a>
                    @endauth
                </div>
            @endif
        </section>

        <!-- دسته‌بندی‌های هم‌سطح -->
        @if ($siblingCategories->count() > 0)
            <section class="sibling-categories">
                <h3 class="section-title">
                    <i class="fas fa-layer-group"></i> دسته‌بندی‌های مرتبط
                </h3>

                <div class="sibling-grid">
                    @foreach ($siblingCategories as $sibling)
                        <a href="{{ route('categories.show', $sibling->slug) }}" class="sibling-card">
                            <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">
                                <i class="fas fa-folder"></i>
                            </div>
                            <h4 style="margin-bottom: 0.25rem;">{{ $sibling->name }}</h4>
                            <p style="font-size: 0.85rem; color: var(--gray);">
                                {{ $sibling->posts_count }} مقاله
                            </p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('صفحه دسته‌بندی {{ $category->name }} لود شد');

            // انیمیشن برای کارت‌ها
            const postCards = document.querySelectorAll('.post-card');
            postCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.05}s`;
                card.classList.add('fade-in');
            });

            // انیمیشن برای زیردسته‌ها
            const subcategoryCards = document.querySelectorAll('.subcategory-card');
            subcategoryCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.classList.add('fade-in');
            });

            // تغییر رنگ دکمه‌های مرتب‌سازی فعال
            const urlParams = new URLSearchParams(window.location.search);
            const currentSort = urlParams.get('sort') || 'newest';

            document.querySelectorAll('.sort-btn').forEach(btn => {
                if (btn.classList.contains('active')) {
                    btn.style.transform = 'scale(1.05)';
                }
            });
        });
    </script>
@endsection
