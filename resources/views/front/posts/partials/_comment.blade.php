<div class="comment-item" data-comment-id="{{ $comment->id }}">
    <div class="comment-header">
        <div class="comment-user">
            @if ($comment->user->avatar)
                <img src="{{ asset('storage/' . $comment->user->avatar) }}" alt="{{ $comment->user->name }}"
                    class="comment-avatar">
            @else
                <div class="comment-avatar"
                    style="background: #4361ee; color: white; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                    {{ substr($comment->user->name, 0, 1) }}
                </div>
            @endif
            <div class="comment-meta">
                <span class="comment-author">{{ $comment->user->name }}</span>
                <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
        </div>
        @if ($comment->status === 'pending')
            <span class="comment-status status-pending">در انتظار تایید</span>
        @elseif($comment->status === 'approved')
            <span class="comment-status status-approved">تایید شده</span>
        @endif
    </div>

    <div class="comment-content">
        {{ $comment->content }}
    </div>

    <div class="comment-actions">
        @auth
            <button type="button" class="reply-btn" data-comment-id="{{ $comment->id }}">
                <i class="fas fa-reply"></i> پاسخ
            </button>
        @endauth
    </div>

    <!-- پاسخ‌ها -->
    @if ($comment->replies->where('status', 'approved')->count() > 0)
        <div class="comment-replies">
            @foreach ($comment->replies->where('status', 'approved') as $reply)
                @include('front.posts.partials._comment', ['comment' => $reply])
            @endforeach
        </div>
    @endif
</div>
