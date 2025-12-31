<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;

class AuthorController extends Controller
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

    // TODO:********************************
    public function show(Request $request , User $user)
    {
        // بررسی فعال بودن حساب کاربر
        if(! $user->is_active)
        {
            abort(404, 'این حساب کاربری غیرفعال شده است.');
        }

        // مقالات منتشر شده نویسنده
        $posts = Post::query()
            ->where('user_id' , $user->id)
            ->published()
            ->with(['category' , 'tags'])
            ->orderBy('published_at' , 'desc')
            ->paginate(12);

        // آمار نویسنده
        $stats = [
            'total_posts' => Post::where('user_id' , $user->id)->published()->count(),
            'total_views' => Post::where('user_id' , $user->id)->published()->sum('view_count'),
            'avg_views' => Post::where('user_id' , $user->id)->published()->avg('view_count'),
        ];

        // دسته‌بندی‌های محبوب نویسنده
        $popularCategories = $user->posts()
            ->published()
            ->join('categories' , 'posts.category_id' , '=' , 'categories.id')
            ->selectRaw('categories.id , categories.name , categories.slug , COUNT(*) as posts_count')
            ->groupBy('categories.id' , 'categories.name' , 'categories.slug')
            ->orderBy('posts_count' , 'desc')
            ->limit(5)
            ->get();

        // برچسب‌های پرکاربرد نویسنده
        $popularTags = $user->posts()
            ->published()
            ->join('post_tag' ,'posts.id' , '=' , 'post_tag.posts_id')
            ->join('tags' ,'post_tag.tag_id' , '=' , 'tags.id')
            ->selectRaw('tags.id , tags.name , tags.slug , COUNT(*) as posts_count')
            ->groupBy('tags.id' , 'tags.name' , 'tags.slug')
            ->orderBy('posts_count' , 'desc')
            ->limit(10)
            ->get();

        // مقالات پربازدید این نویسنده
        $popularPosts = Post::where('user_id' , $user->id)
            ->published()
            ->orderBy('view_count' , 'desc')
            ->limit(5)
            ->get();

        // مرتب‌سازی مقالات
        $sort = $request->get('sort', 'newest');

        $postsQuery = Post::query()
            ->where('user_id', $user->id)
            ->published()
            ->with(['category', 'tags']);

        // اعمال مرتب‌سازی
        switch ($sort) {
            case 'popular':
                $postsQuery->orderBy('view_count', 'desc');
                break;
            case 'oldest':
                $postsQuery->orderBy('published_at', 'asc');
                break;
            default: // newest
                $postsQuery->orderBy('published_at', 'desc');
        }

        $posts = $postsQuery->paginate(12);

        return view('front.authors.show' , compact(
            'posts',
            'stats',
            'popularCategories',
            'popularTags',
            'popularPosts'

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
