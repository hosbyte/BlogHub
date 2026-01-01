@extends('layout')

@section('title', 'جستجو' . ($query ? ': ' . $query : ''))

@section('content')
    <div class="container search-page">
        <!-- هدر جستجو -->
        <div class="search-header">
            <h1 class="page-title">
                <i class="fas fa-search"></i>
                جستجو
            </h1>

            <!-- فرم جستجو -->
            <form method="GET" action="{{ route('search.index') }}" class="search-form">
                <div class="search-box">
                    <input type="text" name="q" value="{{ $query }}" placeholder="چه چیزی را جستجو می‌کنید؟"
                        class="search-input" autocomplete="off">
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <!-- فیلترهای ساده -->
                <div class="simple-filters">
                    <select name="category" class="filter-select" onchange="this.form.submit()">
                        <option value="">همه دسته‌بندی‌ها</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->slug }}" {{ $selectedCategory == $cat->slug ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="sort" class="filter-select" onchange="this.form.submit()">
                        <option value="newest" {{ $selectedSort == 'newest' ? 'selected' : '' }}>جدیدترین</option>
                        <option value="oldest" {{ $selectedSort == 'oldest' ? 'selected' : '' }}>قدیمی‌ترین</option>
                        <option value="popular" {{ $selectedSort == 'popular' ? 'selected' : '' }}>پربازدیدترین</option>
                    </select>
                </div>
            </form>

            <!-- آمار نتایج -->
            @if ($totalResults > 0)
                <div class="search-stats">
                    <span class="stat-item">
                        <i class="fas fa-file-alt"></i>
                        {{ number_format($totalResults) }} نتیجه یافت شد
                    </span>
                </div>
            @endif
        </div>

        <!-- نتایج -->
        <div class="search-results">
            @if ($totalResults > 0)
                <!-- لیست نتایج -->
                <div class="results-list">
                    @foreach ($results as $post)
                        <article class="search-result-item">
                            <!-- دسته‌بندی -->
                            <div class="result-category">
                                <a href="{{ route('categories.show', $post->category->slug) }}" class="category-badge">
                                    {{ $post->category->name }}
                                </a>
                            </div>

                            <!-- عنوان -->
                            <h3 class="result-title">
                                <a href="{{ route('posts.show', $post->slug) }}">
                                    {{ $post->title }}
                                </a>
                            </h3>

                            <!-- متا اطلاعات -->
                            <div class="result-meta">
                                <span class="meta-item">
                                    <i class="fas fa-user"></i>
                                    {{ $post->user->name }}
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-calendar"></i>
                                    {{ \Carbon\Carbon::parse($post->published_at)->format('Y/m/d') }}
                                </span>
                                <span class="meta-item">
                                    <i class="fas fa-eye"></i>
                                    {{ number_format($post->view_count) }}
                                </span>
                            </div>

                            <!-- خلاصه -->
                            <div class="result-excerpt">
                                {{ Str::limit(strip_tags($post->excerpt ?: $post->content), 200) }}
                            </div>

                            <!-- لینک ادامه -->
                            <a href="{{ route('posts.show', $post->slug) }}" class="result-read-more">
                                ادامه مطلب
                                <i class="fas fa-arrow-left"></i>
                            </a>
                        </article>
                    @endforeach
                </div>

                <!-- صفحه‌بندی -->
                {{-- <div class="search-pagination">
                    {{ $results->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div> --}}
            @elseif(!empty($query) || !empty($selectedCategory) || !empty($selectedTag))
                <!-- حالت بدون نتیجه -->
                <div class="no-results">
                    <div class="no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>

                    <h3 class="no-results-title">
                        @if (!empty($query))
                            هیچ نتیجه‌ای برای "{{ $query }}" یافت نشد
                        @else
                            مقاله‌ای با فیلترهای انتخاب شده یافت نشد
                        @endif
                    </h3>

                    <p class="no-results-description">
                        سعی کنید عبارت جستجوی خود را تغییر دهید یا از فیلترهای مختلف استفاده کنید.
                    </p>

                    <a href="{{ route('search.index') }}" class="btn btn-primary">
                        <i class="fas fa-times"></i>
                        حذف فیلترها
                    </a>
                </div>
            @else
                <!-- صفحه خالی اولیه -->
                <div class="empty-search">
                    <div class="empty-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="empty-title">چیزی برای جستجو کنید</h3>
                    <p class="empty-description">
                        عبارت مورد نظر خود را در کادر بالا وارد کنید یا از فیلترها استفاده کنید.
                    </p>

                    <!-- برچسب‌های محبوب -->
                    @if ($popularTags->count() > 0)
                        <div class="popular-tags-suggestions">
                            <h4>برچسب‌های محبوب:</h4>
                            <div class="tags-list">
                                @foreach ($popularTags as $tag)
                                    <a href="{{ route('search.index', ['tag' => $tag->slug]) }}" class="tag-suggestion">
                                        {{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* استایل‌های ساده صفحه جستجو */
        .search-page {
            padding: 30px 0;
            min-height: 60vh;
        }

        .search-header {
            margin-bottom: 40px;
            text-align: center;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 20px;
        }

        .search-form {
            max-width: 700px;
            margin: 0 auto;
        }

        .search-box {
            position: relative;
            margin-bottom: 20px;
        }

        .search-input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            font-size: 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            background: white;
            transition: all 0.3s;
        }

        .search-input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .search-button {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #4f46e5;
            font-size: 18px;
            cursor: pointer;
        }

        .simple-filters {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: white;
            font-size: 14px;
            cursor: pointer;
        }

        .filter-select:focus {
            outline: none;
            border-color: #4f46e5;
        }

        .search-stats {
            margin-top: 20px;
            color: #718096;
            font-size: 14px;
        }

        /* نتایج */
        .search-results {
            margin-top: 30px;
        }

        .results-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .search-result-item {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .result-category {
            margin-bottom: 10px;
        }

        .category-badge {
            background: #4f46e5;
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
        }

        .result-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .result-title a {
            color: #2d3748;
            text-decoration: none;
        }

        .result-title a:hover {
            color: #4f46e5;
        }

        .result-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 13px;
            color: #718096;
        }

        .result-excerpt {
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .result-read-more {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
        }

        .result-read-more:hover {
            text-decoration: underline;
        }

        /* حالت‌های خالی */
        .no-results,
        .empty-search {
            text-align: center;
            padding: 50px 20px;
            background: #f8fafc;
            border-radius: 10px;
            border: 2px dashed #cbd5e0;
        }

        .no-results-icon,
        .empty-icon {
            font-size: 60px;
            color: #a0aec0;
            margin-bottom: 20px;
        }

        .no-results-title,
        .empty-title {
            font-size: 22px;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .no-results-description,
        .empty-description {
            color: #718096;
            margin-bottom: 25px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #4338ca;
        }

        /* پیشنهاد برچسب‌ها */
        .popular-tags-suggestions {
            margin-top: 30px;
        }

        .popular-tags-suggestions h4 {
            font-size: 16px;
            color: #4a5568;
            margin-bottom: 15px;
        }

        .tags-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .tag-suggestion {
            padding: 8px 15px;
            background: #f1f5f9;
            border-radius: 20px;
            color: #4a5568;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }

        .tag-suggestion:hover {
            background: #4f46e5;
            color: white;
        }

        /* صفحه‌بندی */
        .search-pagination {
            margin-top: 40px;
            text-align: center;
        }

        /* ریسپانسیو */
        @media (max-width: 768px) {
            .page-title {
                font-size: 24px;
            }

            .simple-filters {
                flex-direction: column;
            }

            .filter-select {
                width: 100%;
            }

            .result-meta {
                flex-wrap: wrap;
            }
        }
    </style>
@endpush
