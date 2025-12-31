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
        $startTime = microtime(true);

        // دریافت پارامترهای جستجو
        $query = $request->get('q', '');
        $category = $request->get('category');
        $tag = $request->get('tag');
        $sort = $request->get('sort', 'relevance');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // اگر هیچ پارامتری نداریم، صفحه خالی نشان دهیم
        if (empty($query) && empty($category) && empty($tag))
        {
            return view('front.search.index', [
                'query' => $query,
                'results' => collect(),
                'totalResults' => 0,
                'categories' => Category::hasPosts()->get(),
                'popularTags' => Tag::popular()->limit(20)->get(),
                'executionTime' => 0,
                'suggestions' => $this->getSearchSuggestions(),
            ]);
        }

        // شروع جستجو
        $searchQuery = Post::query()
            ->published()
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

        // فیلتر تاریخ
        if (!empty($dateFrom)) {
            $searchQuery->whereDate('published_at', '>=', $dateFrom);
        }

        if (!empty($dateTo)) {
            $searchQuery->whereDate('published_at', '<=', $dateTo);
        }

        // مرتب‌سازی
        switch ($sort) {
            case 'newest':
                $searchQuery->orderBy('published_at', 'desc');
                break;
            case 'oldest':
                $searchQuery->orderBy('published_at', 'asc');
                break;
            case 'popular':
                $searchQuery->orderBy('view_count', 'desc');
                break;
            default: // relevance
                if (!empty($query)) {
                    $searchQuery->orderByRaw(
                        "CASE
                            WHEN title LIKE ? THEN 1
                            WHEN excerpt LIKE ? THEN 2
                            ELSE 3
                        END",
                        ["%{$query}%", "%{$query}%"]
                    );
                }
                $searchQuery->orderBy('published_at', 'desc');
        }

        // اجرای جستجو
        $results = $searchQuery->paginate(12);
        $totalResults = $results->total();

        // محاسبه زمان اجرا
        $executionTime = round(microtime(true) - $startTime, 3);

        // داده‌های مورد نیاز برای View
        return view('front.search.index', [
            'query' => $query,
            'results' => $results,
            'totalResults' => $totalResults,
            'categories' => Category::hasPosts()->get(),
            'popularTags' => Tag::popular()->limit(20)->get(),
            'selectedCategory' => $category,
            'selectedTag' => $tag,
            'selectedSort' => $sort,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'executionTime' => $executionTime,
            'suggestions' => $this->getSearchSuggestions($query),
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

    /**
     * دریافت پیشنهادات جستجو
     */
    private function getSearchSuggestions($query = null)
    {
        $suggestions = [];

        if (!empty($query) && strlen($query) > 2) {
            // پیشنهادات مشابه
            $similarPosts = Post::published()
                ->where('title', 'LIKE', "%{$query}%")
                ->orWhereHas('tags', function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%");
                })
                ->limit(5)
                ->get(['title', 'slug']);

            $suggestions['similar_posts'] = $similarPosts;

            // دسته‌بندی‌های مرتبط
            $relatedCategories = Category::where('name', 'LIKE', "%{$query}%")
                ->hasPosts()
                ->limit(3)
                ->get(['name', 'slug']);

            $suggestions['related_categories'] = $relatedCategories;
        }

        // محبوب‌ترین جستجوها (می‌توانید از جدول جداگانه استفاده کنید)
        $suggestions['popular_searches'] = [
            'laravel', 'php', 'javascript', 'ویژه', 'آموزش'
        ];

        return $suggestions;
    }
}
