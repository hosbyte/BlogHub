<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $category = $request->get('category');
        $tag = $request->get('tag');
        $sort = $request->get('sort', 'newest');

        // اگر هیچ پارامتری نیست، صفحه خالی نشان بده
        if (empty($query) && empty($category) && empty($tag)) {
            return view('front.search.index', [
                'query' => '',
                'results' => collect(),
                'totalResults' => 0,
                'categories' => Category::has('posts')->get(),
                'popularTags' => Tag::withCount('posts')
                    ->orderBy('posts_count', 'desc')
                    ->limit(20)
                    ->get(),
                'selectedCategory' => null,
                'selectedTag' => null,
                'selectedSort' => $sort,
            ]);
        }

        // شروع جستجو
        $searchQuery = Post::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->with(['category', 'user', 'tags']);

        // جستجوی متن
        if (!empty($query)) {
            $searchQuery->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('content', 'LIKE', "%{$query}%")
                  ->orWhere('excerpt', 'LIKE', "%{$query}%");
            });
        }

        // فیلتر دسته‌بندی
        if (!empty($category)) {
            $searchQuery->whereHas('category', function ($q) use ($category) {
                $q->where('slug', $category);
            });
        }

        // فیلتر برچسب
        if (!empty($tag)) {
            $searchQuery->whereHas('tags', function ($q) use ($tag) {
                $q->where('slug', $tag);
            });
        }

        // مرتب‌سازی
        switch ($sort) {
            case 'oldest':
                $searchQuery->orderBy('published_at', 'asc');
                break;
            case 'popular':
                $searchQuery->orderBy('view_count', 'desc');
                break;
            default: // newest
                $searchQuery->orderBy('published_at', 'desc');
        }

        $results = $searchQuery->paginate(12);
        $totalResults = $results->total();

        return view('front.search.index', [
            'query' => $query,
            'results' => $results,
            'totalResults' => $totalResults,
            'categories' => Category::has('posts')->get(),
            'popularTags' => Tag::withCount('posts')
                ->orderBy('posts_count', 'desc')
                ->limit(20)
                ->get(),
            'selectedCategory' => $category,
            'selectedTag' => $tag,
            'selectedSort' => $sort,
        ]);
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
    public function show($id)
    {
        //
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
