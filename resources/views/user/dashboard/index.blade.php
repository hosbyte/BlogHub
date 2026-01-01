@extends('layout')

@section('title', 'داشبورد - پنل کاربری')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('content')
    <div class="dashboard-container">
        <!-- هدر داشبورد -->
        <div class="dashboard-header">
            <h1 class="dashboard-title">
                <i class="fas fa-tachometer-alt"></i>
                داشبورد کاربری
            </h1>
            <p class="dashboard-subtitle">خوش آمدید، {{ Auth::user()->name }}!</p>

            <!-- نمایش نقش کاربر -->
            <div class="user-role-info">
                @php
                    $roleNames = [
                        1 => 'مدیر سایت',
                        2 => 'نویسنده',
                        3 => 'کاربر عادی',
                    ];
                    $roleName = $roleNames[auth()->user()->role_id] ?? 'کاربر';
                @endphp
                <span
                    class="badge {{ auth()->user()->role_id == 1 ? 'badge-admin' : (auth()->user()->role_id == 2 ? 'badge-author' : 'badge-user') }}">
                    <i class="fas fa-user-tag"></i> {{ $roleName }}
                </span>
            </div>
        </div>

        <!-- هشدار برای کاربران عادی -->
        @if (auth()->user()->role_id == 3)
            <div class="author-promotion-alert">
                <div class="alert-icon">
                    <i class="fas fa-user-plus fa-2x"></i>
                </div>
                <div class="alert-content">
                    <h4>شما کاربر عادی هستید</h4>
                    <p>برای دسترسی به امکانات نویسندگی و مدیریت مقالات، باید به نویسنده ارتقا یابید.</p>
                    <button class="btn btn-promote" data-bs-toggle="modal" data-bs-target="#requestAuthorModal">
                        <i class="fas fa-paper-plane"></i> درخواست ارتقا به نویسنده
                    </button>
                </div>
            </div>
        @endif

        <!-- کارت‌های آمار -->
        <div class="stats-grid">
            @if (in_array(auth()->user()->role_id, [1, 2]))
                {{-- مدیر یا نویسنده --}}
                <!-- مدیران و نویسندگان همه آمار را می‌بینند -->
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #4361ee, #3a56d4);">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ $stats['total_posts'] ?? 0 }}</h3>
                        <p class="stat-label">تعداد مقالات</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #198754, #157347);">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ $stats['published_posts'] ?? 0 }}</h3>
                        <p class="stat-label">مقالات منتشر شده</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #6c757d, #495057);">
                        <i class="fas fa-save"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ $stats['draft_posts'] ?? 0 }}</h3>
                        <p class="stat-label">پیش‌نویس‌ها</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f8961e, #f3722c);">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-value">{{ number_format($stats['total_views'] ?? 0) }}</h3>
                        <p class="stat-label">بازدید کل مقالات</p>
                    </div>
                </div>
            @endif

            <!-- همه کاربران (شامل عادی، نویسنده و مدیر) نظرات خود را می‌بینند -->
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #7209b7, #560bad);">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $stats['total_comments'] ?? 0 }}</h3>
                    <p class="stat-label">نظرات ثبت شده</p>
                </div>
            </div>

            <!-- کارت وضعیت حساب -->
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #ff6b6b, #ee5a52);">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">
                        @if (auth()->user()->role_id == 1)
                            مدیر
                        @elseif(auth()->user()->role_id == 2)
                            نویسنده
                        @else
                            کاربر
                        @endif
                    </h3>
                    <p class="stat-label">وضعیت حساب</p>
                </div>
            </div>
        </div>

        <!-- دکمه‌های سریع -->
        <div class="quick-actions">
            @if (in_array(auth()->user()->role_id, [1, 2]))
                {{-- مدیر یا نویسنده --}}
                <a href="{{ route('user.posts.create') }}" class="quick-action-btn btn-success">
                    <i class="fas fa-plus"></i>
                    <span>مقاله جدید</span>
                </a>
                <a href="{{ route('user.posts.index') }}" class="quick-action-btn btn-primary">
                    <i class="fas fa-list"></i>
                    <span>مدیریت مقالات</span>
                </a>
            @endif

            <a href="{{ route('user.profile.edit') }}" class="quick-action-btn btn-info">
                <i class="fas fa-user-edit"></i>
                <span>ویرایش پروفایل</span>
            </a>

            @if (auth()->user()->role_id == 3)
                {{-- کاربر عادی --}}
                <button class="quick-action-btn btn-warning" data-bs-toggle="modal" data-bs-target="#requestAuthorModal">
                    <i class="fas fa-user-plus"></i>
                    <span>درخواست نویسنده شدن</span>
                </button>
            @endif
        </div>

        <!-- محتوای اصلی -->
        <div class="dashboard-content">
            @if (in_array(auth()->user()->role_id, [1, 2])) {{-- مدیر یا نویسنده --}}
                <!-- مقالات اخیر - فقط برای مدیران و نویسندگان -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="fas fa-history"></i>
                            مقالات اخیر
                        </h3>
                        <a href="{{ route('user.posts.index') }}" class="section-link">
                            مشاهده همه <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <div class="section-content">
                        @if ($recent_posts->count() > 0)
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>عنوان</th>
                                            <th>دسته‌بندی</th>
                                            <th>وضعیت</th>
                                            <th>تاریخ</th>
                                            <th>عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recent_posts as $post)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('posts.show', $post->slug) }}" target="_blank">
                                                        {{ Str::limit($post->title, 40) }}
                                                    </a>
                                                </td>
                                                <td>
                                                    @if ($post->category)
                                                        <span
                                                            class="badge badge-category">{{ $post->category->name }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($post->status === 'published')
                                                        <span class="badge badge-published">منتشر شده</span>
                                                    @else
                                                        <span class="badge badge-draft">پیش‌نویس</span>
                                                    @endif
                                                </td>
                                                <td>{{ $post->created_at->diffForHumans() }}</td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="{{ route('user.posts.edit', $post->id) }}"
                                                            class="btn btn-sm btn-edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('posts.show', $post->slug) }}" target="_blank"
                                                            class="btn btn-sm btn-view">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-newspaper fa-2x"></i>
                                <p>هنوز مقاله‌ای ایجاد نکرده‌اید.</p>
                                <a href="{{ route('user.posts.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> ایجاد اولین مقاله
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- مقالات پربازدید - فقط برای مدیران و نویسندگان -->
                <div class="dashboard-section">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="fas fa-chart-line"></i>
                            مقالات پربازدید
                        </h3>
                    </div>

                    <div class="section-content">
                        @if ($popular_posts->count() > 0)
                            <div class="popular-posts">
                                @foreach ($popular_posts as $post)
                                    <div class="popular-post-item">
                                        <div class="post-info">
                                            <h4>
                                                <a href="{{ route('posts.show', $post->slug) }}" target="_blank">
                                                    {{ Str::limit($post->title, 50) }}
                                                </a>
                                            </h4>
                                            <div class="post-meta">
                                                <span class="views-count">
                                                    <i class="fas fa-eye"></i> {{ number_format($post->view_count) }}
                                                    بازدید
                                                </span>
                                                <span class="post-date">{{ $post->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        <div class="post-status">
                                            @if ($post->status === 'published')
                                                <span class="badge badge-published">منتشر شده</span>
                                            @else
                                                <span class="badge badge-draft">پیش‌نویس</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fas fa-chart-line fa-2x"></i>
                                <p>هنوز آمار بازدیدی وجود ندارد.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- نظرات اخیر - برای همه کاربران -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-comments"></i>
                        نظرات اخیر شما
                    </h3>
                    <span class="section-subtitle">(فعالیت‌های شما در سایت)</span>
                </div>

                <div class="section-content">
                    @if ($recent_comments->count() > 0)
                        <div class="recent-comments">
                            @foreach ($recent_comments as $comment)
                                <div class="comment-item">
                                    <div class="comment-header">
                                        <div class="comment-post">
                                            <a href="{{ route('posts.show', $comment->post->slug) }}#comment-{{ $comment->id }}"
                                                target="_blank">
                                                <i class="fas fa-newspaper me-1"></i>
                                                {{ Str::limit($comment->post->title, 40) }}
                                            </a>
                                        </div>
                                        <div class="comment-date">{{ $comment->created_at->diffForHumans() }}</div>
                                    </div>
                                    <div class="comment-content">
                                        <i class="fas fa-quote-left text-muted me-1"></i>
                                        {{ Str::limit($comment->content, 100) }}
                                    </div>
                                    <div class="comment-status">
                                        @if ($comment->status === 'approved')
                                            <span class="badge badge-success">
                                                <i class="fas fa-check"></i> تایید شده
                                            </span>
                                        @elseif($comment->status === 'pending')
                                            <span class="badge badge-warning">
                                                <i class="fas fa-clock"></i> در انتظار تایید
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                <i class="fas fa-times"></i> رد شده
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-comments fa-2x"></i>
                            <p>هنوز نظری ثبت نکرده‌اید.</p>
                            @if (auth()->user()->role_id == 3)
                                <p class="text-muted small">با ارسال نظر در مقالات مختلف، فعالیت خود را آغاز کنید.</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- بخش راهنمایی برای کاربران عادی -->
            @if (auth()->user()->role_id == 3)
                <div class="dashboard-section">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="fas fa-lightbulb"></i>
                            چگونه نویسنده شوم؟
                        </h3>
                    </div>
                    <div class="section-content">
                        <div class="guide-steps">
                            <div class="guide-step">
                                <div class="step-number">1</div>
                                <div class="step-content">
                                    <h5>درخواست ارسال کنید</h5>
                                    <p>با استفاده از دکمه "درخواست ارتقا به نویسنده" فرم درخواست را پر کنید.</p>
                                </div>
                            </div>
                            <div class="guide-step">
                                <div class="step-number">2</div>
                                <div class="step-content">
                                    <h5>فعالیت در سایت</h5>
                                    <p>نظرات مفید و سازنده در مقالات مختلف ثبت کنید.</p>
                                </div>
                            </div>
                            <div class="guide-step">
                                <div class="step-number">3</div>
                                <div class="step-content">
                                    <h5>تأیید مدیریت</h5>
                                    <p>پس از بررسی درخواست شما، مدیر سایت تصمیم نهایی را می‌گیرد.</p>
                                </div>
                            </div>
                            <div class="guide-step">
                                <div class="step-number">4</div>
                                <div class="step-content">
                                    <h5>دسترسی نویسندگی</h5>
                                    <p>پس از تأیید، به امکانات کامل نویسندگی دسترسی خواهید داشت.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal درخواست نویسنده شدن (فقط برای کاربران عادی) -->
    @if (auth()->user()->role_id == 3)
        <div class="modal fade" id="requestAuthorModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-user-plus me-2"></i>
                            درخواست ارتقا به نویسنده
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    {{-- <form action="{{ route('user.author.request') }}" method="POST" id="authorRequestForm"> --}}
                        @csrf
                        <div class="modal-body">
                            <div class="mb-4 text-center">
                                <div class="request-icon">
                                    <i class="fas fa-user-edit fa-3x"></i>
                                </div>
                                <p class="text-muted mt-2">با ارسال این درخواست، امیدواریم به جمع نویسندگان ما بپیوندید.
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">چرا می‌خواهید نویسنده شوید؟</label>
                                <textarea name="reason" class="form-control" rows="4"
                                    placeholder="دلایل و انگیزه‌های خود را برای نویسنده شدن بیان کنید..." required minlength="30"></textarea>
                                <div class="form-text">حداقل ۳۰ کاراکتر - این بخش بسیار مهم است</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">تخصص‌ها و علاقه‌مندی‌های شما</label>
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    @foreach (['برنامه‌نویسی', 'تکنولوژی', 'علم و دانش', 'آموزش', 'سبک زندگی', 'فرهنگ و هنر', 'سلامت', 'اقتصاد'] as $skill)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="interests[]"
                                                value="{{ $skill }}" id="interest{{ $loop->index }}">
                                            <label class="form-check-label" for="interest{{ $loop->index }}">
                                                {{ $skill }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="text" name="other_interests" class="form-control mt-2"
                                    placeholder="سایر تخصص‌ها (با کاما جدا کنید)">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">تعهد زمانی</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="time_commitment"
                                        value="part-time" id="partTime" checked>
                                    <label class="form-check-label" for="partTime">
                                        نیمه وقت (۱-۲ مقاله در هفته)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="time_commitment"
                                        value="full-time" id="fullTime">
                                    <label class="form-check-label" for="fullTime">
                                        تمام وقت (۳-۵ مقاله در هفته)
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>
                                ارسال درخواست
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('داشبورد کاربری لود شد');

            // اعتبارسنجی فرم درخواست نویسنده
            const authorRequestForm = document.getElementById('authorRequestForm');
            if (authorRequestForm) {
                authorRequestForm.addEventListener('submit', function(e) {
                    const reason = this.querySelector('textarea[name="reason"]');
                    if (reason.value.trim().length < 30) {
                        e.preventDefault();
                        alert('لطفاً دلایل خود را به طور کامل بیان کنید (حداقل ۳۰ کاراکتر)');
                        reason.focus();
                        return false;
                    }

                    // بررسی انتخاب حداقل یک تخصص
                    const interests = this.querySelectorAll('input[name="interests[]"]:checked');
                    if (interests.length === 0) {
                        const otherInterests = this.querySelector('input[name="other_interests"]');
                        if (!otherInterests.value.trim()) {
                            e.preventDefault();
                            alert('لطفاً حداقل یک تخصص یا علاقه‌مندی را انتخاب کنید.');
                            return false;
                        }
                    }
                });
            }

            // انیمیشن هشدار
            const alertElement = document.querySelector('.author-promotion-alert');
            if (alertElement) {
                setInterval(() => {
                    alertElement.style.transform = alertElement.style.transform === 'scale(1.02)' ?
                        'scale(1)' : 'scale(1.02)';
                }, 2000);
            }
        });
    </script>

    <style>
        /* استایل‌های جدید */
        .user-role-info {
            margin-top: 10px;
        }

        .user-role-info .badge {
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
        }

        .badge-admin {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }

        .badge-author {
            background: linear-gradient(135deg, #198754, #157347);
            color: white;
        }

        .badge-user {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }

        .author-promotion-alert {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border: 2px solid #90caf9;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s ease;
        }

        .author-promotion-alert .alert-icon {
            color: #1976d2;
        }

        .author-promotion-alert .alert-content {
            flex: 1;
        }

        .author-promotion-alert h4 {
            color: #1565c0;
            margin-bottom: 8px;
        }

        .author-promotion-alert p {
            color: #424242;
            margin-bottom: 15px;
        }

        .btn-promote {
            background: linear-gradient(135deg, #1976d2, #1565c0);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-promote:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 118, 210, 0.3);
        }

        .quick-actions {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .quick-action-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .quick-action-btn.btn-success {
            background: linear-gradient(135deg, #198754, #157347);
        }

        .quick-action-btn.btn-primary {
            background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        }

        .quick-action-btn.btn-info {
            background: linear-gradient(135deg, #0dcaf0, #0bb5d4);
        }

        .quick-action-btn.btn-warning {
            background: linear-gradient(135deg, #ffc107, #e0a800);
        }

        .quick-action-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .section-subtitle {
            font-size: 14px;
            color: #6c757d;
            margin-right: 10px;
        }

        /* راهنمایی نویسنده شدن */
        .guide-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .guide-step {
            display: flex;
            gap: 15px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #1976d2;
        }

        .step-number {
            width: 40px;
            height: 40px;
            background: #1976d2;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            flex-shrink: 0;
        }

        .step-content h5 {
            color: #1976d2;
            margin-bottom: 8px;
        }

        .step-content p {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Modal استایل */
        .request-icon {
            color: #1976d2;
            margin-bottom: 15px;
        }

        /* ریسپانسیو */
        @media (max-width: 768px) {
            .author-promotion-alert {
                flex-direction: column;
                text-align: center;
            }

            .quick-actions {
                flex-direction: column;
            }

            .quick-action-btn {
                width: 100%;
                justify-content: center;
            }

            .guide-steps {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection
