<?php

namespace App\Http\Controllers\Front;

use App\Models\Category;
use App\Models\Post;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $category = Category::where('slug', $slug)
            ->withCount(['posts' => function($query) {
                $query->where('status', 'published')
                    ->where('published_at', '<=', now());
            }])
            ->firstOrFail();

        $posts = $category->posts()
            ->with(['user', 'category', 'tags'])
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        // دسته‌بندی‌های دیگر برای سایدبار
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

        // مقالات اخیر
        $recentPosts = Post::with(['user', 'category'])
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        return view('front.categories.show', compact(
            'category',
            'posts',
            'categories',
            'recentPosts'
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
}
