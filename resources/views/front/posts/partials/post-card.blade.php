<article class="post-card">
    <a href="{{ route('posts.show', $post->slug) }}" class="post-card-link">

        {{-- تصویر شاخص --}}
        <div class="post-thumbnail">
            <img src="{{ $post->thumbnail_url }}"
                 alt="{{ $post->title }}"
                 class="post-image"
                 onerror="this.src='{{ asset('images/default-thumbnail.jpg') }}'">

            {{-- نشان ویژه --}}
            @if($post->is_featured)
                <span class="featured-badge">
                    <i class="fas fa-star"></i> ویژه
                </span>
            @endif
        </div>

        {{-- محتوا --}}
        <div class="post-card-content">

            {{-- عنوان --}}
            <h3 class="post-title">
                {{ $post->title }}
            </h3>

            {{-- خلاصه --}}
            @if($post->excerpt)
                <p class="post-excerpt">
                    {{ Str::limit($post->excerpt, 120) }}
                </p>
            @endif

            {{-- اطلاعات مقاله --}}
            <div class="post-meta">

                {{-- نویسنده --}}
                <div class="meta-item">
                    <i class="fas fa-user"></i>
                    <span>{{ $post->user->name }}</span>
                </div>

                {{-- تاریخ --}}
                <div class="meta-item">
                    <i class="far fa-calendar"></i>
                    <span>
                        {{ $post->published_at ? $post->published_at->format('Y/m/d') : 'بدون تاریخ' }}
                    </span>
                </div>

                {{-- بازدید --}}
                <div class="meta-item">
                    <i class="far fa-eye"></i>
                    <span>{{ number_format($post->view_count) }}</span>
                </div>

                {{-- دسته‌بندی --}}
                @if($post->category)
                    <div class="meta-item">
                        <i class="fas fa-folder"></i>
                        <a href="{{ route('categories.show', $post->category->slug) }}">
                            {{ $post->category->name }}
                        </a>
                    </div>
                @endif

            </div>

        </div>

    </a>
</article>
