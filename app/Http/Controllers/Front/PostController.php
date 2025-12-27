<?php

namespace App\Http\Controllers\Front;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $posts = Post::with(['user', 'category', 'tags'])
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $categories = Category::withCount(['posts' => function($query) {
            $query->where('status', 'published')
                  ->where('published_at', '<=', now());
        }])
        ->whereHas('posts', function($query) {
            $query->where('status', 'published')
                  ->where('published_at', '<=', now());
        })
        ->orderBy('name')
        ->get();

        $recentPosts = Post::with(['user', 'category'])
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        $popularTags = Tag::withCount(['posts' => function($query) {
            $query->where('status', 'published')
                  ->where('published_at', '<=', now());
        }])
        ->orderBy('posts_count', 'desc')
        ->limit(10)
        ->get();

        return view('front.posts.index', compact(
            'posts',
            'categories',
            'recentPosts',
            'popularTags'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $post = Post::with(['user', 'category', 'tags', 'comments.user'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        // مقالات مرتبط
        $relatedPosts = Post::with(['user', 'category'])
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->inRandomOrder()
            ->limit(3)
            ->get();

        // دسته‌بندی‌های فعال
        $categories = Category::withCount(['posts' => function($query) {
            $query->where('status', 'published')
                  ->where('published_at', '<=', now());
        }])
        ->whereHas('posts', function($query) {
            $query->where('status', 'published')
                  ->where('published_at', '<=', now());
        })
        ->orderBy('name')
        ->get();

        // برچسب‌های پرکاربرد
        $popularTags = Tag::withCount(['posts' => function($query) {
            $query->where('status', 'published')
                  ->where('published_at', '<=', now());
        }])
        ->orderBy('posts_count', 'desc')
        ->limit(10)
        ->get();

        return view('front.posts.show', compact(
            'post',
            'relatedPosts',
            'categories',
            'popularTags'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

     // افزایش بازدید
    public function incrementView($id)
    {
        $post = Post::findOrFail($id);
        $post->increment('view_count');

        return response()->json(['success' => true]);
    }
}
