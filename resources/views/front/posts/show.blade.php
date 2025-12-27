@extends('layout')

@section('title', $post->title . ' - BlogHub')
@section('meta_description', $post->excerpt)

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/post.css') }}">
    <link rel="stylesheet" href="{{ asset('css/comments.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <style>
        /* استایل‌های اضافی */
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

        .article-category {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 20px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .article-category:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            border-color: rgba(255, 255, 255, 0.4);
        }

        .featured-badge-article {
            background: linear-gradient(135deg, #f8961e, #f3722c);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-right: 15px;
        }

        /* استایل‌های جدید برای صفحه */
        .article-main {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 40px;
            margin: 40px auto;
            max-width: 1400px;
            padding: 0 20px;
        }

        @media (max-width: 992px) {
            .article-main {
                grid-template-columns: 1fr;
            }
        }

        /* استایل‌های اشتراک‌گذاری */
        .share-widget {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            position: sticky;
            top: 100px;
        }

        .share-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--dark);
            text-align: center;
        }

        .share-buttons-vertical {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .share-btn-vertical {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .share-btn-vertical i {
            font-size: 1.3rem;
            width: 24px;
        }

        .share-telegram {
            background: rgba(0, 136, 204, 0.1);
            color: #0088cc;
            border-color: rgba(0, 136, 204, 0.2);
        }

        .share-telegram:hover {
            background: #0088cc;
            color: white;
            transform: translateX(-5px);
        }

        .share-whatsapp {
            background: rgba(37, 211, 102, 0.1);
            color: #25D366;
            border-color: rgba(37, 211, 102, 0.2);
        }

        .share-whatsapp:hover {
            background: #25D366;
            color: white;
            transform: translateX(-5px);
        }

        .share-linkedin {
            background: rgba(0, 119, 181, 0.1);
            color: #0077b5;
            border-color: rgba(0, 119, 181, 0.2);
        }

        .share-linkedin:hover {
            background: #0077b5;
            color: white;
            transform: translateX(-5px);
        }

        .share-twitter {
            background: rgba(29, 161, 242, 0.1);
            color: #1da1f2;
            border-color: rgba(29, 161, 242, 0.2);
        }

        .share-twitter:hover {
            background: #1da1f2;
            color: white;
            transform: translateX(-5px);
        }

        /* استایل‌های نویسنده */
        .author-card {
            background: linear-gradient(135deg, #f8f9ff, #f1f3ff);
            border-radius: 20px;
            padding: 30px;
            border: 2px solid #e0e3ff;
            margin: 40px 0;
            display: flex;
            gap: 25px;
            align-items: center;
            box-shadow: 0 10px 30px rgba(67, 97, 238, 0.1);
        }

        .author-avatar-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .author-info-large h3 {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .author-info-large h3 a {
            color: inherit;
            text-decoration: none;
        }

        .author-info-large h3 a:hover {
            color: var(--primary);
        }

        .author-bio {
            color: var(--gray);
            line-height: 1.7;
            margin-bottom: 15px;
        }

        /* استایل‌های خوانش */
        .reading-progress {
            position: fixed;
            top: 0;
            right: 0;
            width: 100%;
            height: 4px;
            background: rgba(67, 97, 238, 0.1);
            z-index: 1001;
        }

        .reading-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            width: 0%;
            transition: width 0.3s ease;
        }

        /* دکمه بازگشت به بالا */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            left: 30px;
            width: 50px;
            height: 50px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 1.2rem;
            box-shadow: 0 5px 20px rgba(67, 97, 238, 0.3);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            background: var(--primary-dark);
            transform: translateY(-5px);
        }

        /* متا اطلاعات بهبود یافته */
        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .meta-item i {
            color: #adb5bd;
        }
    </style>
@endsection

@section('content')
    <!-- نوار پیشرفت خوانش -->
    <div class="reading-progress">
        <div class="reading-progress-bar" id="readingProgress"></div>
    </div>

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
            <h1 class="post-header">{{ $post->title }}</h1>

            <!-- اطلاعات مقاله -->
            <div class="post-meta-large">
                <div class="meta-item">
                    <i class="fas fa-user"></i>
                    <span>{{ $post->user->name }}</span>
                </div>

                <div class="meta-item">
                    <i class="far fa-calendar"></i>
                    <span>{{ $post->created_at->diffForHumans() }}</span>
                </div>

                <div class="meta-item">
                    <i class="far fa-clock"></i>
                    <span>{{ ceil(str_word_count(strip_tags($post->content)) / 200) }} دقیقه</span>
                </div>

                <div class="meta-item">
                    <i class="far fa-eye"></i>
                    <span>{{ number_format($post->view_count) }} بازدید</span>
                </div>
            </div>

            <!-- برچسب‌های مهم -->
            @if ($post->tags->count() > 0)
                <div class="tags-list mt-4">
                    @foreach ($post->tags->take(3) as $tag)
                        <a href="{{ route('tags.show', $tag->slug) }}" class="tag">
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                    @if ($post->tags->count() > 3)
                        <span class="text-white opacity-75">+{{ $post->tags->count() - 3 }} بیشتر</span>
                    @endif
                </div>
            @endif
        </div>
    </header>

    <div class="article-main">
        <!-- //TODO: محتوای اصلی -->
        <main class="article-content-wrapper">
            <!-- تصویر شاخص -->
            @if ($post->featured_image)
                <div class="featured-image-container mb-5">
                    <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="featured-image">
                    @if ($post->is_featured)
                        <div class="featured-badge-article mt-3">
                            <i class="fas fa-star"></i> مقاله ویژه
                        </div>
                    @endif
                </div>
            @endif

            <!-- محتوای مقاله -->
            <article class="post-content">
                {!! $post->content !!}
            </article>

            <!-- برچسب‌ها کامل -->
            @if ($post->tags->count() > 0)
                <div class="tags-container">
                    <h3 class="tags-title">برچسب‌های مقاله</h3>
                    <div class="tags-list">
                        @foreach ($post->tags as $tag)
                            <a href="{{ route('tags.show', $tag->slug) }}" class="tag">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- اشتراک‌گذاری افقی -->
            <div class="share-section mt-5">
                <h3 class="share-title">اشتراک‌گذاری مقاله</h3>
                <div class="share-buttons">
                    <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}"
                        target="_blank" class="share-btn share-telegram">
                        <i class="fab fa-telegram"></i> تلگرام
                    </a>
                    <a href="https://wa.me/?text={{ urlencode($post->title . ' ' . url()->current()) }}" target="_blank"
                        class="share-btn share-whatsapp">
                        <i class="fab fa-whatsapp"></i> واتساپ
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($post->title) }}"
                        target="_blank" class="share-btn share-linkedin">
                        <i class="fab fa-linkedin"></i> لینکدین
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}"
                        target="_blank" class="share-btn share-twitter">
                        <i class="fab fa-twitter"></i> توییتر
                    </a>
                </div>
            </div>

            <!-- نویسنده -->
            <div class="author-card">
                <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->name }}" class="author-avatar-large">
                <div class="author-info-large">
                    <h3>
                        <a href="{{ route('authors.show', $post->user->name) }}">
                            {{ $post->user->name }}
                        </a>
                    </h3>
                    @if ($post->user->bio)
                        <p class="author-bio">{{ Str::limit($post->user->bio, 150) }}</p>
                    @endif
                    <div class="author-stats">
                        <span class="text-muted mr-3">
                            <i class="fas fa-newspaper"></i> {{ $post->user->posts()->count() }} مقاله
                        </span>
                        <span class="text-muted">
                            <i class="fas fa-calendar"></i> عضو از {{ $post->user->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <a href="{{ route('authors.show', $post->user->name) }}" class="btn btn-outline mt-3">
                        <i class="fas fa-user"></i> مشاهده پروفایل نویسنده
                    </a>
                </div>
            </div>

            <!-- نظرات -->
            <div class="comments-section">
                <h2 class="comments-title">
                    <i class="far fa-comments"></i> نظرات
                    <small style="font-size: 1rem; color: #6c757d; margin-right: 10px;">
                        ({{ $post->comments()->where('status', 'approved')->count() }} نظر)
                    </small>
                </h2>

                <!-- لیست نظرات -->
                @if ($post->comments()->where('status', 'approved')->count() > 0)
                    <div class="comments-list">
                        @foreach ($post->comments()->where('status', 'approved')->whereNull('parent_id')->get() as $comment)
                            @include('front.posts.partials._comment', ['comment' => $comment])
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
                        <form action="{{ route('comments.store') }}" method="POST" id="comment-form">
                            @csrf
                            <input type="hidden" name="post_id" value="{{ $post->id }}">
                            <input type="hidden" name="parent_id" id="parent_id" value="">

                            <div class="reply-to mb-3" id="reply-to-container" style="display: none;">
                                <div class="alert alert-info">
                                    <span>در پاسخ به <strong id="reply-to-name"></strong></span>
                                    <button type="button" id="cancel-reply" class="btn btn-sm btn-link text-danger">
                                        × لغو
                                    </button>
                                </div>
                            </div>

                            <div class="form-group">
                                <textarea name="content" id="comment-content" rows="4" class="form-control"
                                    placeholder="نظر خود را بنویسید..." required></textarea>
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
                        <div class="auth-buttons">
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i> ورود به حساب
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-outline">
                                <i class="fas fa-user-plus"></i> ثبت‌نام
                            </a>
                        </div>
                    </div>
                @endauth
            </div>
        </main>

        <!-- // سایدبار -->
        <aside class="article-sidebar">
            <!-- //FIXME: اشتراک‌گذاری عمودی -->
            {{-- <div class="share-widget">
                <h4 class="share-title">اشتراک‌گذاری</h4>
                <div class="share-buttons-vertical">
                    <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}"
                        target="_blank" class="share-btn-vertical share-telegram">
                        <i class="fab fa-telegram"></i>
                        <span>تلگرام</span>
                    </a>
                    <a href="https://wa.me/?text={{ urlencode($post->title . ' ' . url()->current()) }}" target="_blank"
                        class="share-btn-vertical share-whatsapp">
                        <i class="fab fa-whatsapp"></i>
                        <span>واتساپ</span>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($post->title) }}"
                        target="_blank" class="share-btn-vertical share-linkedin">
                        <i class="fab fa-linkedin"></i>
                        <span>لینکدین</span>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}"
                        target="_blank" class="share-btn-vertical share-twitter">
                        <i class="fab fa-twitter"></i>
                        <span>توییتر</span>
                    </a>
                </div>
            </div> --}}

            <!-- اطلاعات مقاله -->
            <div class="sidebar widget">
                <h3 class="widget-title">اطلاعات مقاله</h3>
                <div class="widget-content">
                    <div class="info-item">
                        <i class="fas fa-user text-primary"></i>
                        <span>نویسنده:</span>
                        <strong>{{ $post->user->name }}</strong>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-calendar text-primary"></i>
                        <span>تاریخ انتشار:</span>
                        <strong>{{ $post->created_at->format('Y/m/d') }}</strong>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-clock text-primary"></i>
                        <span>زمان مطالعه:</span>
                        <strong>{{ ceil(str_word_count(strip_tags($post->content)) / 200) }} دقیقه</strong>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-eye text-primary"></i>
                        <span>بازدید:</span>
                        <strong>{{ number_format($post->view_count) }}</strong>
                    </div>
                    @if ($post->category)
                        <div class="info-item">
                            <i class="fas fa-folder text-primary"></i>
                            <span>دسته‌بندی:</span>
                            <strong>{{ $post->category->name }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            <!-- مقالات مرتبط -->
            @if ($relatedPosts && $relatedPosts->count() > 0)
                <div class="sidebar widget">
                    <h3 class="widget-title">مقالات مرتبط</h3>
                    <div class="widget-content">
                        <div class="recent-posts">
                            @foreach ($relatedPosts as $relatedPost)
                                <div class="recent-post">
                                    @if ($relatedPost->featured_image)
                                        <img src="{{ $relatedPost->featured_image_url }}"
                                            alt="{{ $relatedPost->title }}" class="recent-post-image">
                                    @endif
                                    <div class="recent-post-content">
                                        <h4 class="recent-post-title">
                                            <a href="{{ route('posts.show', $relatedPost->slug) }}">
                                                {{ Str::limit($relatedPost->title, 50) }}
                                            </a>
                                        </h4>
                                        <div class="recent-post-date">
                                            {{ $relatedPost->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- دسته‌بندی‌ها -->
            @if ($categories && $categories->count() > 0)
                <div class="sidebar widget">
                    <h3 class="widget-title">دسته‌بندی‌ها</h3>
                    <div class="widget-content">
                        <ul class="category-list">
                            @foreach ($categories as $category)
                                <li>
                                    <a href="{{ route('categories.show', $category->slug) }}">
                                        {{ $category->name }}
                                        <span class="category-count">{{ $category->posts_count ?? 0 }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </aside>
    </div>

    <!-- دکمه بازگشت به بالا -->
    <a href="#" class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </a>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // پیشرفت خوانش
            function updateReadingProgress() {
                const article = document.querySelector('.post-content');
                const articleHeight = article.offsetHeight;
                const articleTop = article.offsetTop;
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                if (scrollTop > articleTop) {
                    const progress = ((scrollTop - articleTop) / articleHeight) * 100;
                    document.getElementById('readingProgress').style.width = Math.min(progress, 100) + '%';
                } else {
                    document.getElementById('readingProgress').style.width = '0%';
                }
            }

            // دکمه بازگشت به بالا
            function toggleBackToTop() {
                const backToTop = document.getElementById('backToTop');
                if (window.pageYOffset > 300) {
                    backToTop.classList.add('show');
                } else {
                    backToTop.classList.remove('show');
                }
            }

            // افزایش شمارنده بازدید
            function incrementViewCount() {
                fetch('{{ route('post.incrementView', $post->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });
            }

            // سیستم پاسخ به نظرات
            document.querySelectorAll('.reply-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const commentId = this.dataset.commentId;
                    const commentElement = document.querySelector(
                        `.comment[data-comment-id="${commentId}"]`);
                    const userName = commentElement.querySelector('.comment-author').textContent;

                    document.getElementById('parent_id').value = commentId;
                    document.getElementById('reply-to-name').textContent = userName;
                    document.getElementById('reply-to-container').style.display = 'block';

                    document.getElementById('comment-form').scrollIntoView({
                        behavior: 'smooth'
                    });
                    document.getElementById('comment-content').focus();
                });
            });

            // لغو پاسخ
            document.getElementById('cancel-reply')?.addEventListener('click', function() {
                document.getElementById('parent_id').value = '';
                document.getElementById('reply-to-container').style.display = 'none';
                document.getElementById('comment-content').focus();
            });

            // اعتبارسنجی فرم نظر
            document.getElementById('comment-form')?.addEventListener('submit', function(e) {
                const content = document.getElementById('comment-content').value.trim();
                if (!content) {
                    e.preventDefault();
                    alert('لطفا متن نظر را وارد کنید.');
                }
            });

            // اسکرول به بالا
            document.getElementById('backToTop').addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // رویدادهای اسکرول
            window.addEventListener('scroll', function() {
                updateReadingProgress();
                toggleBackToTop();
            });

            // افزایش بازدید
            incrementViewCount();

            // مقداردهی اولیه
            updateReadingProgress();
            toggleBackToTop();
        });
    </script>
@endsection
