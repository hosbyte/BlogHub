@extends('layout')

@section('title', "مقالات برچسب: {$tag->name}")

@section('content')
    <div class="container">
        <div class="page-header">
            <!-- هدر صفحه -->
            <div class="tag-header">
                <div class="tag-icon">
                    <i class="fas fa-tag"></i>
                </div>
                <div class="tag-info">
                    <h1 class="page-title">مقالات با برچسب: {{ $tag->name }}</h1>
                    <div class="tag-meta">
                        <span class="meta-item">
                            <i class="fas fa-file-alt"></i>
                            {{ $posts->total() }} مقاله
                        </span>
                        <span class="meta-item">
                            <i class="fas fa-eye"></i>
                            {{ $tag->posts->sum('view_count') }} بازدید
                        </span>
                    </div>
                    @if ($tag->description)
                        <p class="tag-description">{{ $tag->description }}</p>
                    @endif
                </div>
            </div>

            <!-- ابر برچسب‌ها -->
            <div class="tag-cloud">
                @foreach ($popularTags as $popularTag)
                    <a href="{{ route('tags.show', $popularTag->slug) }}"
                        class="tag-item {{ $tag->id == $popularTag->id ? 'active' : '' }}"
                        style="font-size: {{ 14 + $popularTag->posts_count / 5 }}px">
                        {{ $popularTag->name }}
                        <span class="tag-count">({{ $popularTag->posts_count }})</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="page-layout">
            <!-- محتوای اصلی -->
            <main class="main-content">
                @if ($posts->count() > 0)
                    <!-- لیست مقالات -->
                    <div class="posts-grid">
                        @foreach ($posts as $post)
                            @include('front.posts.partials.post-card', ['post' => $post])
                        @endforeach
                    </div>

                    <!-- صفحه‌بندی -->
                    {{-- <div class="pagination-wrapper">
                        {{ $posts->links('vendor.pagination.tailwind') }}
                    </div> --}}
                @else
                    <!-- حالت خالی -->
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-tag"></i>
                        </div>
                        <h3 class="empty-title">مقاله‌ای یافت نشد</h3>
                        <p class="empty-description">
                            هنوز مقاله‌ای با برچسب "{{ $tag->name }}" منتشر نشده است.
                        </p>
                        <a href="{{ route('posts.index') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i>
                            مشاهده همه مقالات
                        </a>
                    </div>
                @endif
            </main>

            <!-- سایدبار -->
            {{-- <aside class="sidebar">
                <!-- ویجت جستجو -->
                @include('front.partials.search-widget')

                <!-- ویجت دسته‌بندی‌ها -->
                @include('front.partials.categories-widget')

                <!-- ویجت مقالات محبوب -->
                @include('front.partials.popular-posts-widget')

                <!-- ویجت خبرنامه (اختیاری) -->
                <div class="sidebar-widget">
                    <h3 class="widget-title">خبرنامه</h3>
                    <p class="widget-text">از جدیدترین مقالات با خبر شوید</p>
                    <form class="newsletter-form">
                        <input type="email" placeholder="ایمیل خود را وارد کنید" class="form-input">
                        <button type="submit" class="btn btn-primary btn-block">عضویت</button>
                    </form>
                </div>
            </aside> --}}
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* استایل صفحه برچسب */
        .tag-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            padding: 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            color: white;
        }

        .tag-icon {
            font-size: 48px;
            opacity: 0.9;
        }

        .tag-info {
            flex: 1;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            color: white;
        }

        .tag-meta {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            opacity: 0.9;
        }

        .tag-description {
            font-size: 16px;
            line-height: 1.6;
            opacity: 0.95;
            margin: 0;
        }

        /* ابر برچسب‌ها */
        .tag-cloud {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 40px;
            padding: 20px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .tag-item {
            display: inline-flex;
            align-items: center;
            padding: 6px 15px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 20px;
            color: #4a5568;
            text-decoration: none;
            transition: all 0.3s;
        }

        .tag-item:hover {
            background: #4f46e5;
            border-color: #4f46e5;
            color: white;
            transform: translateY(-2px);
        }

        .tag-item.active {
            background: #4f46e5;
            border-color: #4f46e5;
            color: white;
        }

        .tag-count {
            font-size: 12px;
            margin-right: 5px;
            opacity: 0.7;
        }

        /* حالت خالی */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: #f8fafc;
            border-radius: 12px;
            border: 2px dashed #cbd5e0;
        }

        .empty-icon {
            font-size: 64px;
            color: #a0aec0;
            margin-bottom: 20px;
        }

        .empty-title {
            font-size: 24px;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .empty-description {
            color: #718096;
            margin-bottom: 30px;
            font-size: 16px;
        }

        /* صفحه‌بندی */
        .pagination-wrapper {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        /* ریسپانسیو */
        @media (max-width: 768px) {
            .tag-header {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }

            .tag-meta {
                justify-content: center;
            }

            .tag-cloud {
                justify-content: center;
            }

            .page-title {
                font-size: 24px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // هایلایت برچسب فعال
        document.addEventListener('DOMContentLoaded', function() {
            const activeTag = document.querySelector('.tag-item.active');
            if (activeTag) {
                activeTag.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'center'
                });
            }
        });
    </script>
@endpush