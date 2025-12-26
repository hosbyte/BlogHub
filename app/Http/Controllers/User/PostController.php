<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Post::where('user_id' , $user->id)->with(['category' , 'tags']);

        // فیلتر بر اساس وضعیت
        if($request->has('status') && in_array($request->status , ['draft', 'published', 'archived']))
        {
            $query->where('status' , $request->status);
        }

        // فیلتر دسته‌بندی
        if ($request->has('category_id'))
        {
            $query->where('category_id', $request->category_id);
        }

        // جستجو
        if ($request->has('search'))
        {
            $query->where(function ($q) use ($request) {
                $q->where('title' , 'like' , '%' . $request->search . '%')
                  ->osWhere('content' , 'like' , '%' . $request->search . '%');
            });
        }

        // مرتب‌سازی
        switch ($request->sort)
        {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'most_viewed':
                $query->orderBy('view_count', 'desc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $posts = $query->orderBy('created_at' , 'desc')->paginate(10)->withQueryString();
        $categories = Category::all(); // برای dropdown دسته‌بندی

        $statuses = [
            'draft' => 'پیش نویس',
            'published' => 'منتشر شده',
            'archived' => 'آرشیو',
        ];

        return view('user.posts.index' , compact('posts' , 'statuses' , 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // دریافت دسته‌بندی‌ها
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        // دریافت برچسب‌های موجود
        $existingTags = Tag::orderBy('name')->get();

        return view('user.posts.create', compact('categories', 'existingTags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // اعتبارسنجی
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'required|string|max:200|unique:posts,slug',
            'content' => 'required|string|min:10',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'excerpt' => 'nullable|string|max:300',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'status' => 'required|in:draft,published',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'allow_comments' => 'boolean',
            'include_in_rss' => 'boolean',
        ]);

        // آپلود تصویر شاخص
        $featuredImagePath = null;
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');

            // ایجاد نام یکتا برای فایل
            $imageName = 'post_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

            // ذخیره در storage
            $path = $image->storeAs('posts/featured', $imageName, 'public');
            $featuredImagePath = $path;
        }

        // ایجاد مقاله
        $post = Post::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'content' => $validated['content'],
            'featured_image' => $featuredImagePath,
            'excerpt' => $validated['excerpt'] ?? null,
            'user_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'thumbnail_id' => $validated['thumbnail_id'] ?? null,
            'status' => $validated['status'],
            'is_featured' => $request->boolean('is_featured'),
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'published_at' => $validated['status'] === 'published'
                ? ($validated['published_at'] ?? now())
                : null,
            'allow_comments' => $request->boolean('allow_comments'),
            'view_count' => 0,
        ]);

        // افزودن برچسب‌ها
        if ($request->has('tags'))
        {
            $post->tags()->attach($request->tags);
        }

        // لاگ فعالیت
        Activity::create([
            'user_id' => Auth::id(),
            'action' => 'create_post',
            'description' => 'مقاله جدید ایجاد کرد: ' . $post->title,
        ]);

         // ریدایرکت بر اساس action
        $action = $request->input('action', 'draft');

        if ($action === 'draft')
        {
            return redirect()->route('user.posts.index')
                ->with('success', 'مقاله با موفقیت به عنوان پیش‌نویس ذخیره شد.');
        }
        else
        {
            return redirect()->route('posts.show', $post->slug)
                ->with('success', 'مقاله با موفقیت منتشر شد.');
        }
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
        // فقط مقالات کاربر جاری
        $post = Post::where('user_id', Auth::id())->findOrFail($id);

        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        $existingTags = Tag::orderBy('name')->get();

        return view('user.posts.edit', compact('post', 'categories', 'existingTags'));
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

        $post = Post::where('user_id', Auth::id())->findOrFail($id);
        // اعتبارسنجی
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'required|string|max:200|unique:posts,slug',
            'content' => 'required|string|min:10',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'excerpt' => 'nullable|string|max:300',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'status' => 'required|in:draft,published',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'allow_comments' => 'boolean',
            'include_in_rss' => 'boolean',
        ]);

        // آپلود تصویر جدید (اگر ارسال شده)
        if ($request->hasFile('featured_image'))
        {
            // حذف تصویر قبلی اگر وجود دارد
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }

            $image = $request->file('featured_image');
            $imageName = 'post_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('posts/featured', $imageName, 'public');
            $validated['featured_image'] = $path;
        }
        else
        {
            // اگر تصویر جدیدی ارسال نشده، تصویر قبلی را حفظ کن
            unset($validated['featured_image']);
        }

        // آپدیت مقاله
        $post->update($validated);

        // آپدیت برچسب‌ها
        if (isset($validated['tags']))
        {
            $tagIds = [];
            foreach ($validated['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(
                    ['name' => trim($tagName)],
                    ['slug' => Str::slug(trim($tagName))]
                );
                $tagIds[] = $tag->id;
            }
            $post->tags()->sync($tagIds);
        }
        else
        {
            $post->tags()->detach();
        }

        return redirect()->route('user.posts.index')
            ->with('success', 'مقاله با موفقیت ویرایش شد.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post , $id)
    {
        $post = Post::where('user_id', Auth::id())->findOrFail($id);

        // حذف تصویر شاخص
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return redirect()->route('user.posts.index')
            ->with('success', 'مقاله با موفقیت حذف شد.');
    }

    /**
     * تغییر وضعیت مقاله
     */
    public function changeStatus(Post $post , Request $request)
    {
        $this->authorize('update' , $post);

        $request->validate([
            'status' => 'required|in:draft,published,archived'
        ]);

        $post->status = $request->status;
        $post->save();

        return back()->with('success', 'وضعیت مقاله با موفقیت تغییر کرد.');
    }

    /**
     * تغییر وضعیت گروهی مقالات
     */
    public function bulkChangeStatus(Request $request)
    {
        $request->validate([
            'posts' => 'required|array',
            'posts.*' => 'exists:posts,id',
            'status' => 'required|in:draft,published,archived'
        ]);

        $user = Auth::user();

        // فقط مقالات متعلق به کاربر
        Post::where('user_id', $user->id)
            ->whereIn('id', $request->posts)
            ->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'وضعیت مقالات با موفقیت تغییر کرد.'
        ]);
    }

    /**
     * حذف گروهی مقالات
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'posts' => 'required|array',
            'posts.*' => 'exists:posts,id'
        ]);

        $user = Auth::user();

        // فقط مقالات متعلق به کاربر
        Post::where('user_id', $user->id)
            ->whereIn('id', $request->posts)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'مقالات با موفقیت حذف شدند.'
        ]);
    }
}
