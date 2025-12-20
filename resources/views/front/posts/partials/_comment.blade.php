{{-- این فایل به صورت بازگشتی برای نمایش نظرات و پاسخ‌هایشان فراخوانی می‌شود --}}
<div class="comment mb-6 p-4 border rounded-lg" data-comment-id="{{ $comment->id }}">
    {{-- هدر نظر --}}
    <div class="comment-header flex items-center justify-between mb-2">
        <div class="flex items-center space-x-2">
            <img src="{{ $comment->user->avatar ?? asset('images/default-avatar.png') }}" alt="{{ $comment->user->name }}"
                class="w-8 h-8 rounded-full">
            <div>
                <strong class="text-gray-800">{{ $comment->user->name }}</strong>
                <span class="text-xs text-gray-500 ml-2">
                    {{ $comment->created_at->diffForHumans() }}
                </span>
            </div>
        </div>
        @auth
            <button class="reply-btn text-xs text-blue-600 hover:text-blue-800" data-comment-id="{{ $comment->id }}">
                ↪ پاسخ
            </button>
        @endauth
    </div>

    {{-- متن نظر --}}
    <div class="comment-body mb-3">
        <p class="text-gray-700">{{ $comment->content }}</p>
    </div>

    {{-- پاسخ‌ها (نمایش بازگشتی) --}}
    @if ($comment->replies && $comment->replies->count() > 0)
        <div class="comment-replies ml-8 pl-4 border-l-2 border-gray-200">
            @foreach ($comment->replies as $reply)
                @include('posts.partials._comment', ['comment' => $reply])
            @endforeach
        </div>
    @endif
</div>
