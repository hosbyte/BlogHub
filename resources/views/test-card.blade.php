@extends('layout')

@section('title', 'تست کامپوننت')

@section('content')
<div class="container">
    <h1>تست کامپوننت کارت مقاله</h1>

    <div class="posts-grid">
        @include('front.posts.partials.post-card', ['post' => $post])
    </div>

    <div style="margin-top: 50px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
        <h3>اطلاعات مقاله تست:</h3>
        <pre>{{ print_r($post, true) }}</pre>
    </div>
</div>
@endsection
