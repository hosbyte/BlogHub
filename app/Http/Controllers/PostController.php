<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
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
        // دریافت پارامترهای جستجو و فیلتر
        $search = $request->input('search');
        $category = $request->input('category');
        $author = $request->input('author');
        $sort = $request->input('sort' , 'newest');

        // شروع query
        $query = Post::published();

        // اعمال جستجو
        if($search)
        {
            $query->where(function ($q) use ($search) {
                $q->where('title' , 'like' , "%{$search}%")
                  ->orWhere('content' , 'like' , "%{$search}%")
                  ->orWhere('excerpt' , 'like' , "%{$search}%");
            });
        }

        // فیلتر دسته‌بندی
        if($category)
        {
            $query->whereHas('category' , function($q) use ($category) {
                $q->where('slug' , $category);
            });
        }

        // فیلتر نویسنده
        if($author)
        {
            $query->whereHas('user' , function ($q) use ($author) {
                $q->where('id' , $author);
            });
        }

        // مرتب‌سازی
        switch ($sort)
        {
            case 'popular' :
                $query->orderby('view_count' , 'desc');
            break;
            case 'featured':
                $query->where('is_featured' , 'true')->orderby('published_at' , 'desc');
            break;
            case 'oldest':
                $query->orderby('published_at' , 'asc');
            break;
            default :
                $query->orderby('published_at' , 'desc');
        }

        // دریافت مقالات با صفحه‌بندی
        $posts = $query->paginate(10)->withQueryString();

        // داده‌های اضافی برای صفحه
        $categories = Category::hasPosts()->mainCategories()->get();
        $recentPosts = Post::published()->recent(5)->get();
        $popularPosts = Post::published()->popular(5)->get();

        return view('front.posts.index' , compact(
            'posts',
            'categories',
            'recentPosts',
            'popularPosts',
            'search',
            'sort'
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
        $post = Post::where('slug' , $slug)->firstOrFail();
        $post -> incrementViewCount();

        return view('front.posts.show' , compact('post'));
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
