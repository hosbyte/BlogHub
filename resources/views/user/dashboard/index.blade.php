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
        </div>

        <!-- کارت‌های آمار -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4361ee, #3a56d4);">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $stats['total_posts'] }}</h3>
                    <p class="stat-label">تعداد مقالات</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #198754, #157347);">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $stats['published_posts'] }}</h3>
                    <p class="stat-label">مقالات منتشر شده</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #6c757d, #495057);">
                    <i class="fas fa-save"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $stats['draft_posts'] }}</h3>
                    <p class="stat-label">پیش‌نویس‌ها</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f8961e, #f3722c);">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ number_format($stats['total_views']) }}</h3>
                    <p class="stat-label">بازدید کل</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #7209b7, #560bad);">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $stats['total_comments'] }}</h3>
                    <p class="stat-label">نظرات</p>
                </div>
            </div>
        </div>

        <!-- محتوای اصلی -->
        <div class="dashboard-content">
            <!-- مقالات اخیر -->
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
                                                    <span class="badge badge-category">{{ $post->category->name }}</span>
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

            <!-- مقالات پربازدید -->
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
                                                <i class="fas fa-eye"></i> {{ number_format($post->view_count) }} بازدید
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

            <!-- نظرات اخیر -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h3 class="section-title">
                        <i class="fas fa-comments"></i>
                        نظرات اخیر
                    </h3>
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
                                                {{ Str::limit($comment->post->title, 40) }}
                                            </a>
                                        </div>
                                        <div class="comment-date">{{ $comment->created_at->diffForHumans() }}</div>
                                    </div>
                                    <div class="comment-content">
                                        {{ Str::limit($comment->content, 100) }}
                                    </div>
                                    <div class="comment-status">
                                        @if ($comment->status === 'approved')
                                            <span class="badge badge-success">تایید شده</span>
                                        @elseif($comment->status === 'pending')
                                            <span class="badge badge-warning">در انتظار تایید</span>
                                        @else
                                            <span class="badge badge-danger">رد شده</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-comments fa-2x"></i>
                            <p>هنوز نظری ثبت نکرده‌اید.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('داشبورد کاربری لود شد');

            // آپدیت آمار در صورت نیاز
            // می‌توانید AJAX برای آپدیت real-time اضافه کنید
        });
    </script>
@endsection
