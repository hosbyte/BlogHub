<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use App\Models\Post;
use Illuminate\Http\Request;

class TagController extends Controller
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
    public function show(Request $request , Tag $tag)
    {

        // تست ۱: بررسی خود برچسب
        // dd($tag->toArray());
        // مقالات مرتبط با این برچسب
        $posts = Post::query()
            ->whereHas('tags' , function($query) use ($tag) {
                $query->where('tags.id' , $tag->id);
            })
            ->published()
            ->with(['category' , 'user' , 'tags'])
            ->orderBy('published_at' , 'desc')
            ->paginate(12);

        // برچسب‌های محبوب برای سایدبار
        $popularTags = Tag::withCount('posts')
            ->orderBy('posts_count' , 'desc')
            ->limit(15)
            ->get();

        return view('front.tags.show' , compact('tag' , 'posts' , 'popularTags'));
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

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $tags = Tag::when($query, function ($q) use ($query) {
            return $q->where('name', 'LIKE', "%{$query}%");
        })
        ->limit(10)
        ->get(['id', 'name']);
        
        return response()->json($tags);
    }
}
