<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;

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

        // جستجو  
        if ($request->has('search'))
        {
            $query->where(function ($q) use ($request) {
                $q->where('title' , 'like' , '%' . $request->search . '%')
                  ->osWhere('content' , 'like' , '%' . $request->search . '%');
            });
        }

        $posts = $query->orderBy('created_at' , 'desc')->paginate(10)->withQueryString();

        $statuses = [
            'draft' => 'پیش نویس',
            'published' => 'منتشر شده',
            'archived' => 'آرشیو',
        ];

        return view('user.posts.index' , compact('posts' , 'statuses'));
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

        return view('user.posts.create' , compact('categories' , 'tag'));
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
}
