@extends('layout')

@section('title', $post->title . ' - BlogHub')

@section('styles')
    <style>
        /* استایل‌های صفحه مقاله */
        .article-header {
            background: linear-gradient(135deg, #2d3047 0%, #1a1a2e 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 40px;
            border-radius: 0 0 20px 20px;
            position: relative;
            overflow: hidden;
        }

        .article-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" opacity="0.1"><path d="M0,50 Q250,0 500,50 T1000,50" fill="none" stroke="white" stroke-width="2"/></svg>');
            background-size: cover;
        }

        .article-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .article-category {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 20px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .article-category:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .article-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            line-height: 1.3;
        }

        .article-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .meta-item i {
            font-size: 0.8rem;
        }

        .author-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #4361ee;
        }

        .author-details h4 {
            margin-bottom: 5px;
            color: #2d3047;
        }

        .author-details p {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .article-thumbnail {
            width: 100%;
            height: 400px;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 30px;
            position: relative;
        }

        .article-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .article-thumbnail:hover img {
            transform: scale(1.05);
        }

        .featured-badge-article {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #f8961e;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            z-index: 2;
        }

        .article-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #2d3047;
            margin-bottom: 50px;
        }

        .article-content h2 {
            font-size: 1.8rem;
            margin: 30px 0 15px;
            color: #1a1a2e;
            padding-bottom: 10px;
            border-bottom: 2px solid #4361ee;
        }

        .article-content h3 {
            font-size: 1.5rem;
            margin: 25px 0 12px;
            color: #2d3047;
        }

        .article-content p {
            margin-bottom: 20px;
        }

        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin: 20px 0;
        }

        .article-content blockquote {
            border-right: 4px solid #4361ee;
            padding: 20px;
            margin: 30px 0;
            background: #f8f9ff;
            border-radius: 0 10px 10px 0;
            font-style: italic;
            color: #2d3047;
        }

        .article-content pre {
            background: #2d3047;
            color: #e9ecef;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
        }

        .article-content code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            color: #f72585;
        }

        .article-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 30px 0;
            padding: 20px 0;
            border-top: 1px solid #e9ecef;
            border-bottom: 1px solid #e9ecef;
        }

        .tag {
            background: #f8f9fa;
            color: #495057;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .tag:hover {
            background: #4361ee;
            color: white;
            transform: translateY(-2px);
        }

        .article-actions {
            display: flex;
            gap: 15px;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            color: #495057;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .action-btn:hover {
            background: #4361ee;
            color: white;
            border-color: #4361ee;
        }

        .action-btn.liked {
            background: #f72585;
            color: white;
            border-color: #f72585;
        }

        .action-btn.disliked {
            background: #4361ee;
            color: white;
            border-color: #4361ee;
        }

        .related-articles {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px solid #4361ee;
        }

        .related-title {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #2d3047;
        }

        .comments-section {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 2px solid #4361ee;
        }

        .comments-title {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #2d3047;
        }

        .empty-comments {
            text-align: center;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 20px 0;
        }

        .empty-comments i {
            font-size: 3rem;
            color: #adb5bd;
            margin-bottom: 15px;
        }

        .comments-list {
            margin: 30px 0;
        }

        .comment-form-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-top: 30px;
            border: 1px solid #e9ecef;
        }

        .form-title {
            font-size: 1.3rem;
            margin-bottom: 20px;
            color: #2d3047;
        }

        @media (max-width: 768px) {
            .article-title {
                font-size: 1.8rem;
            }

            .article-thumbnail {
                height: 250px;
            }

            .article-meta {
                flex-direction: column;
                gap: 10px;
            }

            .author-info {
                flex-direction: column;
                text-align: center;
            }

            .article-actions {
                flex-wrap: wrap;
            }

            .article-container {
                padding: 0 15px;
            }
        }
    </style>
@endsection

@section('content')
    <!-- هدر مقاله -->
    <header class="article-header">
        <div class="article-container">
            <!-- دسته‌بندی -->
            @if ($post->category)
                <a href="{{ route('categories.show', $post->category->slug) }}" class="article-category">
                    <i class="fas fa-folder"></i> {{ $post->category->name }}
                </a>
            @endif

            <!-- عنوان مقاله -->
            <h1 class="article-title">{{ $post->title }}</h1>

            <!-- اطلاعات مقاله -->
            <div class="article-meta">
                <div class="meta-item">
                    <i class="fas fa-user"></i>
                    <span>نویسنده: {{ $post->user->name }}</span>
                </div>

                <div class="meta-item">
                    <i class="far fa-calendar"></i>
                    {{-- <span>انتشار: {{ $post->published_at ? $post->published_at->jdate('Y/m/d') : 'بدون تاریخ' }}</span> --}}
                </div>

                <div class="meta-item">
                    <i class="far fa-clock"></i>
                    <span>زمان مطالعه: {{ ceil(str_word_count(strip_tags($post->content)) / 200) }} دقیقه</span>
                </div>

                <div class="meta-item">
                    <i class="far fa-eye"></i>
                    <span>{{ number_format($post->view_count) }} بازدید</span>
                </div>
            </div>
        </div>
    </header>

    <div class="article-container">
        <!-- اطلاعات نویسنده -->
        <div class="author-info">
            {{-- <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->name }}" class="author-avatar"> --}}
            <div class="author-details">
                <h4>{{ $post->user->name }}</h4>
                @if ($post->user->bio)
                    <p>{{ $post->user->bio }}</p>
                @endif
                <div style="display: flex; gap: 10px;">
                    {{-- <a href="{{ route('author.profile', $post->user->id) }}" class="btn btn-sm btn-outline"> --}}
                        <i class="fas fa-user"></i> پروفایل نویسنده
                    </a>
                    {{-- <a href="{{ route('posts.index', ['author' => $post->user->id]) }}" class="btn btn-sm btn-outline"> --}}
                        <i class="fas fa-newspaper"></i> مقالات دیگر
                    </a>
                </div>
            </div>
        </div>

        <!-- تصویر شاخص -->
        <div class="article-thumbnail">
            <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}">
            @if ($post->is_featured)
                <span class="featured-badge-article">
                    <i class="fas fa-star"></i> مقاله ویژه
                </span>
            @endif
        </div>

        <!-- محتوای مقاله -->
        <article class="article-content">
            {!! $post->content !!}
        </article>

        <!-- برچسب‌ها -->
        @if ($post->tags->count() > 0)
            <div class="article-tags">
                <span style="font-weight: 600; color: #2d3047; margin-left: 10px;">برچسب‌ها:</span>
                @foreach ($post->tags as $tag)
                    <a href="{{ route('posts.index', ['tag' => $tag->slug]) }}" class="tag">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- اقدامات (لایک، اشتراک‌گذاری) -->
        <div class="article-actions">
            <button class="action-btn like-btn" data-post-id="{{ $post->id }}">
                <i class="far fa-thumbs-up"></i>
                <span class="like-count">{{ $post->likes_count }}</span>
            </button>

            <button class="action-btn dislike-btn" data-post-id="{{ $post->id }}">
                <i class="far fa-thumbs-down"></i>
                <span class="dislike-count">{{ $post->dislikes_count }}</span>
            </button>

            <button class="action-btn" onclick="copyArticleLink()">
                <i class="fas fa-link"></i> کپی لینک
            </button>

            <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ $post->title }}" target="_blank"
                class="action-btn">
                <i class="fab fa-twitter"></i> توییت
            </a>

            <a href="https://telegram.me/share/url?url={{ url()->current() }}&text={{ $post->title }}" target="_blank"
                class="action-btn">
                <i class="fab fa-telegram"></i> تلگرام
            </a>
        </div>

        <!-- مقالات مرتبط -->
        @if ($relatedPosts->count() > 0)
            <section class="related-articles">
                <h2 class="related-title">
                    <i class="fas fa-link"></i> مقالات مرتبط
                </h2>
                <div class="posts-grid">
                    @foreach ($relatedPosts as $relatedPost)
                        @include('front.posts.partials.post-card', ['post' => $relatedPost])
                    @endforeach
                </div>
            </section>
        @endif

        <!-- بخش نظرات -->
        <section class="comments-section">
            <h2 class="comments-title">
                <i class="far fa-comments"></i> نظرات
                <small style="font-size: 1rem; color: #6c757d; margin-right: 10px;">
                    ({{ $comments->count() }} نظر)
                </small>
            </h2>

            @if ($comments->count() > 0)
                <div class="comments-list">
                    @foreach ($comments as $comment)
                        @include('front.comments.partials.comment-item', ['comment' => $comment])
                    @endforeach
                </div>
            @else
                <div class="empty-comments">
                    <i class="far fa-comment-dots"></i>
                    <h3>هنوز نظری ثبت نشده است</h3>
                    <p>اولین نفری باشید که درباره این مقاله نظر می‌دهد.</p>
                </div>
            @endif

            <!-- فرم ثبت نظر -->
            @auth
                <div class="comment-form-section">
                    <h3 class="form-title">ثبت نظر جدید</h3>
                    <form action="{{ route('comments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $post->id }}">

                        <div style="margin-bottom: 15px;">
                            <textarea name="content" rows="4" class="filter-input" placeholder="نظر خود را بنویسید..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> ارسال نظر
                        </button>
                    </form>
                </div>
            @else
                <div class="comment-form-section">
                    <h3 class="form-title">برای ثبت نظر وارد شوید</h3>
                    <p>برای ثبت نظر درباره این مقاله، باید وارد حساب کاربری خود شوید.</p>
                    <div style="display: flex; gap: 10px; margin-top: 15px;">
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> ورود به حساب
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline">
                            <i class="fas fa-user-plus"></i> ثبت‌نام
                        </a>
                    </div>
                </div>
            @endauth
        </section>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('صفحه مقاله لود شد');

            // کپی لینک مقاله
            window.copyArticleLink = function() {
                const url = window.location.href;
                navigator.clipboard.writeText(url).then(() => {
                    alert('لینک مقاله کپی شد!');
                });
            };

            // سیستم لایک/دیسلایک
            const likeBtn = document.querySelector('.like-btn');
            const dislikeBtn = document.querySelector('.dislike-btn');

            if (likeBtn && dislikeBtn) {
                likeBtn.addEventListener('click', function() {
                    const postId = this.dataset.postId;
                    likePost(postId);
                });

                dislikeBtn.addEventListener('click', function() {
                    const postId = this.dataset.postId;
                    dislikePost(postId);
                });
            }

            // اسکرول نرم به بخش‌ها
            const smoothScroll = (target) => {
                const element = document.querySelector(target);
                if (element) {
                    element.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            };

            // هایلایت کدها
            document.querySelectorAll('pre code').forEach((block) => {
                hljs.highlightBlock(block);
            });
        });

        // توابع لایک/دیسلایک
        function likePost(postId) {
            fetch(`/posts/${postId}/like`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector('.like-count').textContent = data.likes;
                        document.querySelector('.dislike-count').textContent = data.dislikes;

                        // تغییر استایل دکمه‌ها
                        document.querySelector('.like-btn').classList.add('liked');
                        document.querySelector('.dislike-btn').classList.remove('disliked');
                    }
                });
        }

        function dislikePost(postId) {
            fetch(`/posts/${postId}/dislike`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector('.like-count').textContent = data.likes;
                        document.querySelector('.dislike-count').textContent = data.dislikes;

                        // تغییر استایل دکمه‌ها
                        document.querySelector('.dislike-btn').classList.add('disliked');
                        document.querySelector('.like-btn').classList.remove('liked');
                    }
                });
        }

        // تخمین زمان مطالعه
        function estimateReadingTime(content) {
            const wordsPerMinute = 200;
            const wordCount = content.trim().split(/\s+/).length;
            return Math.ceil(wordCount / wordsPerMinute);
        }
    </script>

    <!-- هایلایت سینتکس (اختیاری) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
    <script>
        hljs.highlightAll();
    </script>
@endsection
