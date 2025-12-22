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
        $categories = Category::all();
        $tags = Tag::all();

        return view('user.posts.create' , compact('categories' , 'tags'));
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
        if($request->hasFile('thumbnail'))
        {
            $path = $request->file('thumbnail')->store('thumbnails' , 'public');

            // ایجاد رکورد media
            $media = Media::create([
                'name' => $request->file('thumbnail')->getClientOriginalName(),
                'path' => $path,
                'type' => 'image',
                'size' => $request->file('thumbnail')->getSize(),
                'user_id' => Auth::id(),
            ]);

            $validated ['thumbnail_id'] = $media->id; 
        }

        // ایجاد مقاله
        $post = Post::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'content' => $validated['content'],
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
    public function edit(Post $post)
    {
        // اطمینان از اینکه کاربر صاحب مقاله است
        $this->authorize('update' , $post);

        $categories = Category::all();
        $tags = Tag::all();

        return view('user.posts.edit' , compact('post' , 'categories' , 'tags'));
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
    public function destroy(Post $post)
    {
        $this->authorize('delete' , $post);

        $post->delete();

        return redirect()->route('user.posts.index')->with('success', 'مقاله با موفقیت حذف شد.');
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
