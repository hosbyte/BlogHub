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

            <!-- نوار جستجو -->
            <form method="GET" action="{{ route('search.index') }}" class="search-form">
                <div class="search-box">
                    <input type="text" name="q" value="{{ $query }}" placeholder="چه چیزی را جستجو می‌کنید؟"
                        class="search-input" autocomplete="off">
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>

                <!-- فیلترهای سریع (اگر جستجویی انجام شده) -->
                @if (!empty($query) || !empty($selectedCategory) || !empty($selectedTag))
                    <div class="active-filters">
                        @if (!empty($query))
                            <span class="filter-tag">
                                متن: "{{ $query }}"
                                <a href="{{ route('search.index', request()->except('q', 'page')) }}" class="remove-filter">
                                    &times;
                                </a>
                            </span>
                        @endif

                        @if (!empty($selectedCategory))
                            @php
                                $categoryName =
                                    \App\Models\Category::where('slug', $selectedCategory)->first()->name ??
                                    $selectedCategory;
                            @endphp
                            <span class="filter-tag">
                                دسته: {{ $categoryName }}
                                <a href="{{ route('search.index', request()->except('category', 'page')) }}"
                                    class="remove-filter">
                                    &times;
                                </a>
                            </span>
                        @endif

                        @if (!empty($selectedTag))
                            <span class="filter-tag">
                                برچسب: {{ $selectedTag }}
                                <a href="{{ route('search.index', request()->except('tag', 'page')) }}"
                                    class="remove-filter">
                                    &times;
                                </a>
                            </span>
                        @endif

                        @if ($dateFrom || $dateTo)
                            <span class="filter-tag">
                                تاریخ: {{ $dateFrom ?? 'از ابتدا' }} تا {{ $dateTo ?? 'امروز' }}
                                <a href="{{ route('search.index', request()->except('date_from', 'date_to', 'page')) }}"
                                    class="remove-filter">
                                    &times;
                                </a>
                            </span>
                        @endif

                        @if (!empty($query) || !empty($selectedCategory) || !empty($selectedTag) || $dateFrom || $dateTo)
                            <a href="{{ route('search.index') }}" class="clear-all">
                                حذف همه فیلترها
                            </a>
                        @endif
                    </div>
                @endif
            </form>

            <!-- آمار نتایج -->
            @if ($totalResults > 0)
                <div class="search-stats">
                    <span class="stat-item">
                        <i class="fas fa-file-alt"></i>
                        {{ number_format($totalResults) }} نتیجه
                    </span>
                    <span class="stat-item">
                        <i class="fas fa-clock"></i>
                        {{ $executionTime }} ثانیه
                    </span>
                </div>
            @endif
        </div>

        <div class="page-layout">
            <!-- سایدبار فیلترها -->
            <aside class="sidebar filters-sidebar">
                <div class="filters-accordion">
                    <!-- دسته‌بندی‌ها -->
                    <div class="filter-section">
                        <button class="filter-header" type="button">
                            <i class="fas fa-folder"></i>
                            دسته‌بندی
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="filter-content">
                            <div class="filter-list">
                                @foreach ($categories as $category)
                                    <label class="filter-checkbox">
                                        <input type="radio" name="category" value="{{ $category->slug }}"
                                            {{ $selectedCategory == $category->slug ? 'checked' : '' }}
                                            onchange="this.form.submit()">
                                        <span class="checkbox-label">
                                            {{ $category->name }}
                                            <span class="count">({{ $category->posts_count ?? 0 }})</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- برچسب‌ها -->
                    <div class="filter-section">
                        <button class="filter-header" type="button">
                            <i class="fas fa-tags"></i>
                            برچسب‌ها
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="filter-content">
                            <div class="tags-filter">
                                @foreach ($popularTags as $tag)
                                    <a href="{{ route('search.index', array_merge(request()->all(), ['tag' => $tag->slug, 'page' => 1])) }}"
                                        class="tag-filter {{ $selectedTag == $tag->slug ? 'active' : '' }}">
                                        {{ $tag->name }}
                                        <span class="tag-count">{{ $tag->posts_count }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- بازه تاریخ -->
                    <div class="filter-section">
                        <button class="filter-header" type="button">
                            <i class="fas fa-calendar-alt"></i>
                            بازه تاریخ
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="filter-content">
                            <form method="GET" class="date-filter-form">
                                <div class="form-group">
                                    <label>از تاریخ:</label>
                                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-input"
                                        onchange="this.form.submit()">
                                </div>
                                <div class="form-group">
                                    <label>تا تاریخ:</label>
                                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-input"
                                        onchange="this.form.submit()">
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- مرتب‌سازی -->
                    <div class="filter-section">
                        <button class="filter-header" type="button">
                            <i class="fas fa-sort-amount-down"></i>
                            مرتب‌سازی
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="filter-content">
                            <div class="sort-options">
                                <label class="sort-option">
                                    <input type="radio" name="sort" value="relevance"
                                        {{ $selectedSort == 'relevance' ? 'checked' : '' }} onchange="this.form.submit()">
                                    مرتبط‌ترین
                                </label>
                                <label class="sort-option">
                                    <input type="radio" name="sort" value="newest"
                                        {{ $selectedSort == 'newest' ? 'checked' : '' }} onchange="this.form.submit()">
                                    جدیدترین
                                </label>
                                <label class="sort-option">
                                    <input type="radio" name="sort" value="oldest"
                                        {{ $selectedSort == 'oldest' ? 'checked' : '' }} onchange="this.form.submit()">
                                    قدیمی‌ترین
                                </label>
                                <label class="sort-option">
                                    <input type="radio" name="sort" value="popular"
                                        {{ $selectedSort == 'popular' ? 'checked' : '' }} onchange="this.form.submit()">
                                    پربازدیدترین
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- ادامه در قسمت بعدی... -->
            {{-- ادامه front/search/index.blade.php --}}

            <!-- محتوای اصلی - نتایج جستجو -->
            <main class="search-results">
                @if ($totalResults > 0)
                    <!-- نتایج -->
                    <div class="results-list">
                        @foreach ($results as $post)
                            <article class="search-result-item">
                                <!-- دسته‌بندی -->
                                <div class="result-category">
                                    <a href="{{ route('categories.show', $post->category->slug) }}"
                                        class="category-badge">
                                        {{ $post->category->name }}
                                    </a>
                                </div>

                                <!-- //FIXME: عنوان با هایلایت -->
                                <h3 class="result-title">
                                    <a href="{{ route('posts.show', $post->slug) }}">
                                        @if (!empty($query))
                                            {{-- {!! \App\Helpers\SearchHelper::highlightText($post->title, $query) !!} --}}
                                        @else
                                            {{ $post->title }}
                                        @endif
                                    </a>
                                </h3>

                                <!-- متا اطلاعات -->
                                <div class="result-meta">
                                    <span class="meta-item">
                                        <i class="fas fa-user"></i>
                                        <a href="{{ route('authors.show', $post->user->username) }}">
                                            {{ $post->user->name }}
                                        </a>
                                    </span>
                                    <span class="meta-item">
                                        <i class="fas fa-calendar"></i>
                                        {{ verta($post->published_at)->format('d F Y') }}
                                    </span>
                                    <span class="meta-item">
                                        <i class="fas fa-eye"></i>
                                        {{ number_format($post->view_count) }}
                                    </span>
                                    <span class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        {{ $post->reading_time }} دقیقه
                                    </span>
                                </div>

                                <!-- FIXME: خلاصه با هایلایت -->
                                <div class="result-excerpt">
                                    @if (!empty($query))
                                        {{-- {!! \App\Helpers\SearchHelper::excerpt(Str::limit(strip_tags($post->excerpt ?: $post->content), 250), $query) !!} --}}
                                    @else
                                        {{ Str::limit(strip_tags($post->excerpt ?: $post->content), 250) }}
                                    @endif
                                </div>

                                <!-- برچسب‌ها -->
                                @if ($post->tags->count() > 0)
                                    <div class="result-tags">
                                        @foreach ($post->tags->take(3) as $tag)
                                            <a href="{{ route('tags.show', $tag->slug) }}" class="tag-link">
                                                {{ $tag->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- لینک ادامه -->
                                <a href="{{ route('posts.show', $post->slug) }}" class="result-read-more">
                                    ادامه مطلب
                                    <i class="fas fa-arrow-left"></i>
                                </a>
                            </article>
                        @endforeach
                    </div>

                    <!-- صفحه‌بندی -->
                    <div class="search-pagination">
                        {{ $results->appends(request()->query())->links('vendor.pagination.tailwind') }}
                    </div>
                @else
                    <!-- حالت بدون نتیجه -->
                    <div class="no-results">
                        <div class="no-results-icon">
                            <i class="fas fa-search"></i>
                        </div>

                        <h3 class="no-results-title">
                            @if (!empty($query))
                                هیچ نتیجه‌ای برای "{{ $query }}" یافت نشد
                            @else
                                چیزی برای جستجو وارد کنید
                            @endif
                        </h3>

                        <p class="no-results-description">
                            سعی کنید عبارت جستجوی خود را تغییر دهید یا از فیلترهای مختلف استفاده کنید.
                        </p>

                        <!-- پیشنهادات -->
                        @if (!empty($query) && isset($suggestions['similar_posts']) && $suggestions['similar_posts']->count() > 0)
                            <div class="search-suggestions">
                                <h4 class="suggestions-title">شاید این‌ها را می‌خواهید:</h4>
                                <div class="suggestions-list">
                                    @foreach ($suggestions['similar_posts'] as $suggestion)
                                        <a href="{{ route('posts.show', $suggestion->slug) }}" class="suggestion-item">
                                            {{ $suggestion->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- دسته‌بندی‌های مرتبط -->
                        @if (!empty($query) && isset($suggestions['related_categories']) && $suggestions['related_categories']->count() > 0)
                            <div class="related-categories">
                                <h4 class="categories-title">دسته‌بندی‌های مرتبط:</h4>
                                <div class="categories-list">
                                    @foreach ($suggestions['related_categories'] as $category)
                                        <a href="{{ route('categories.show', $category->slug) }}" class="category-link">
                                            {{ $category->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- جستجوهای محبوب -->
                        <div class="popular-searches">
                            <h4 class="popular-title">جستجوهای محبوب:</h4>
                            <div class="popular-tags">
                                @foreach ($suggestions['popular_searches'] as $popularSearch)
                                    <a href="{{ route('search.index', ['q' => $popularSearch]) }}" class="popular-tag">
                                        {{ $popularSearch }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </main>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* CSS صفحه جستجو */
        .search-page {
            padding: 30px 0;
            min-height: 70vh;
        }

        /* هدر جستجو */
        .search-header {
            margin-bottom: 40px;
            text-align: center;
        }

        .page-title {
            font-size: 32px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .page-title i {
            color: #4f46e5;
        }

        /* فرم جستجو */
        .search-form {
            max-width: 700px;
            margin: 0 auto 25px;
        }

        .search-box {
            position: relative;
            margin-bottom: 15px;
        }

        .search-input {
            width: 100%;
            padding: 18px 25px;
            font-size: 18px;
            border: 3px solid #e2e8f0;
            border-radius: 50px;
            background: white;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .search-input:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 4px 20px rgba(79, 70, 229, 0.15);
        }

        .search-button {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #4f46e5;
            font-size: 20px;
            cursor: pointer;
            padding: 5px;
        }

        /* فیلترهای فعال */
        .active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            align-items: center;
        }

        .filter-tag {
            background: #4f46e5;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .remove-filter {
            color: white;
            text-decoration: none;
            font-size: 18px;
            line-height: 1;
            padding: 0 3px;
            border-radius: 50%;
            transition: background 0.3s;
        }

        .remove-filter:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .clear-all {
            color: #4f46e5;
            text-decoration: none;
            font-size: 14px;
            padding: 5px 10px;
            border: 1px solid #4f46e5;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .clear-all:hover {
            background: #4f46e5;
            color: white;
        }

        /* آمار جستجو */
        .search-stats {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #718096;
            font-size: 14px;
        }

        .stat-item i {
            color: #4f46e5;
        }

        /* Layout صفحه */
        .page-layout {
            display: flex;
            gap: 40px;
            position: relative;
        }

        /* سایدبار فیلترها */
        .filters-sidebar {
            width: 280px;
            flex-shrink: 0;
        }

        .filters-accordion {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .filter-section {
            border-bottom: 1px solid #f1f5f9;
        }

        .filter-section:last-child {
            border-bottom: none;
        }

        .filter-header {
            width: 100%;
            padding: 18px 20px;
            background: none;
            border: none;
            text-align: right;
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.3s;
        }

        .filter-header:hover {
            background: #f8fafc;
        }

        .filter-header i:last-child {
            transition: transform 0.3s;
            font-size: 14px;
        }

        .filter-header.active i:last-child {
            transform: rotate(180deg);
        }

        .filter-content {
            padding: 0 20px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s, padding 0.3s;
        }

        .filter-section.active .filter-content {
            padding: 0 20px 20px;
            max-height: 500px;
        }

        /* فیلتر دسته‌بندی‌ها */
        .filter-list {
            max-height: 300px;
            overflow-y: auto;
            padding: 5px;
        }

        .filter-checkbox {
            display: block;
            margin-bottom: 10px;
            cursor: pointer;
            padding: 8px 10px;
            border-radius: 6px;
            transition: background 0.3s;
        }

        .filter-checkbox:hover {
            background: #f1f5f9;
        }

        .filter-checkbox input {
            margin-left: 8px;
        }

        .checkbox-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: #4a5568;
        }

        .checkbox-label .count {
            font-size: 12px;
            color: #a0aec0;
        }

        /* فیلتر برچسب‌ها */
        .tags-filter {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .tag-filter {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            background: #f1f5f9;
            border-radius: 15px;
            color: #4a5568;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s;
        }

        .tag-filter:hover,
        .tag-filter.active {
            background: #4f46e5;
            color: white;
        }

        .tag-count {
            margin-right: 5px;
            font-size: 11px;
            opacity: 0.7;
        }

        /* فیلتر تاریخ */
        .date-filter-form {
            padding: 10px 0;
        }

        .date-filter-form .form-group {
            margin-bottom: 15px;
        }

        .date-filter-form label {
            display: block;
            margin-bottom: 5px;
            font-size: 13px;
            color: #718096;
        }

        .date-filter-form .form-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 14px;
        }

        /* مرتب‌سازی */
        .sort-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sort-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 14px;
        }

        .sort-option:hover {
            background: #f1f5f9;
        }

        .sort-option input {
            margin: 0;
        }

        /* نتایج جستجو */
        .search-results {
            flex: 1;
        }

        .results-list {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .search-result-item {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .search-result-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .result-category {
            margin-bottom: 12px;
        }

        .category-badge {
            background: #4f46e5;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }

        .result-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .result-title a {
            color: #2d3748;
            text-decoration: none;
        }

        .result-title a:hover {
            color: #4f46e5;
        }

        .result-title mark {
            background: #fef3c7;
            color: #92400e;
            padding: 0 2px;
            border-radius: 3px;
        }

        .result-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 13px;
            color: #718096;
        }

        .result-meta a {
            color: #4f46e5;
            text-decoration: none;
        }

        .result-meta a:hover {
            text-decoration: underline;
        }

        .result-excerpt {
            color: #4a5568;
            line-height: 1.7;
            margin-bottom: 15px;
            font-size: 15px;
        }

        .result-excerpt mark {
            background: #fef3c7;
            color: #92400e;
            padding: 0 2px;
            border-radius: 3px;
            font-weight: 500;
        }

        .result-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tag-link {
            color: #4f46e5;
            font-size: 13px;
            text-decoration: none;
            padding: 4px 10px;
            background: #f1f5f9;
            border-radius: 15px;
            transition: all 0.3s;
        }

        .tag-link:hover {
            background: #4f46e5;
            color: white;
        }

        .result-read-more {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            transition: gap 0.3s;
        }

        .result-read-more:hover {
            gap: 12px;
        }

        /* صفحه‌بندی */
        .search-pagination {
            margin-top: 40px;
            padding-top: 25px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }

        /* حالت بدون نتیجه */
        .no-results {
            text-align: center;
            padding: 60px 20px;
            background: #f8fafc;
            border-radius: 12px;
            border: 2px dashed #cbd5e0;
        }

        .no-results-icon {
            font-size: 72px;
            color: #a0aec0;
            margin-bottom: 25px;
        }

        .no-results-title {
            font-size: 24px;
            color: #2d3748;
            margin-bottom: 15px;
        }

        .no-results-description {
            color: #718096;
            font-size: 16px;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        /* پیشنهادات */
        .search-suggestions,
        .related-categories,
        .popular-searches {
            margin-top: 30px;
            text-align: center;
        }

        .suggestions-title,
        .categories-title,
        .popular-title {
            font-size: 18px;
            color: #4a5568;
            margin-bottom: 15px;
        }

        .suggestions-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: center;
        }

        .suggestion-item {
            color: #4f46e5;
            text-decoration: none;
            font-size: 16px;
            padding: 8px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.3s;
            max-width: 500px;
            width: 100%;
        }

        .suggestion-item:hover {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }

        .categories-list,
        .popular-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .category-link,
        .popular-tag {
            background: #f1f5f9;
            color: #4a5568;
            padding: 8px 15px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }

        .category-link:hover,
        .popular-tag:hover {
            background: #4f46e5;
            color: white;
        }

        /* ریسپانسیو */
        @media (max-width: 1024px) {
            .page-layout {
                flex-direction: column;
            }

            .filters-sidebar {
                width: 100%;
                margin-bottom: 30px;
            }

            .filters-accordion {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 0;
            }

            .filter-section {
                border: 1px solid #f1f5f9;
            }

            .filter-content {
                max-height: 300px !important;
                padding: 0 20px 20px !important;
            }
        }

        @media (max-width: 768px) {
            .search-input {
                font-size: 16px;
                padding: 15px 20px 15px 50px;
            }

            .search-button {
                left: 15px;
            }

            .result-title {
                font-size: 20px;
            }

            .result-meta {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // فعال‌سازی آکاردیون فیلترها
        document.addEventListener('DOMContentLoaded', function() {
            // آکاردیون فیلترها
            const filterHeaders = document.querySelectorAll('.filter-header');

            filterHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    this.classList.toggle('active');
                    const content = this.nextElementSibling;

                    if (content.style.maxHeight && content.style.maxHeight !== '0px') {
                        content.style.maxHeight = '0';
                        content.style.padding = '0 20px';
                    } else {
                        content.style.maxHeight = content.scrollHeight + 'px';
                        content.style.padding = '0 20px 20px';
                    }
                });

                // باز کردن اولیه اگر فیلتری فعال است
                const filterSection = header.parentElement;
                const hasActiveFilter = filterSection.querySelector('input:checked, .active');
                if (hasActiveFilter) {
                    header.click();
                }
            });

            // هایلایت متن در صفحه (اگر Helper نداریم)
            function highlightText(text, query) {
                if (!query) return text;

                const regex = new RegExp(`(${query})`, 'gi');
                return text.replace(regex, '<mark>$1</mark>');
            }

            // اعمال هایلایت روی عناصر
            const query = "{{ $query }}";
            if (query) {
                const titleElements = document.querySelectorAll('.result-title');
                const excerptElements = document.querySelectorAll('.result-excerpt');

                [...titleElements, ...excerptElements].forEach(el => {
                    el.innerHTML = highlightText(el.textContent, query);
                });
            }
        });
    </script>
@endpush
