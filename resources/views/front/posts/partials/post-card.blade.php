<link rel="stylesheet" href="{{ asset('css/post.css') }}">
<link rel="stylesheet" href="{{ asset('css/post-form.css') }}">
<link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<article class="post-card">
    @if ($post->featured_image)
        <div class="post-image">
            <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="featured-image">
            @if ($post->category)
                <a href="{{ route('categories.show', $post->category->slug) }}" class="post-category">
                    {{ $post->category->name }}
                </a>
            @endif
        </div>
    @endif

    <div class="post-content">
        <h3 class="post-title">
            <a href="{{ route('posts.show', $post->slug) }}">
                {{ $post->title }}
            </a>
        </h3>

        <p class="post-excerpt">
            {{ Str::limit(strip_tags($post->excerpt), 150) }}
        </p>

        <div class="post-meta">
            <div class="author-info">
                @if ($post->user->avatar)
                    <img src="{{ $post->user->avatar_url }}" alt="{{ $post->user->name }}" class="author-avatar">
                @else
                    <div class="author-avatar default-avatar">
                        {{ substr($post->user->name, 0, 1) }}
                    </div>
                @endif
                <span>{{ $post->user->name }}</span>
            </div>

            <div class="post-date">
                <i class="far fa-clock"></i>
                {{ $post->created_at->diffForHumans() }}
            </div>
        </div>
    </div>
</article>
