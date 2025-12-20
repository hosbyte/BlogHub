<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
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
        // 1. اعتبارسنجی داده‌های ورودی
        $validated = $request->validate([
            'content' => 'required|min:3|max:1000',
            'post_id' => 'required|exists:posts,id',
            'parent_id' => 'nullable|exists:comments,id'
        ]);

        // 2. بررسی وجود مقاله
        $post = Post::findRoFail($request->post_id);

        // 3. ایجاد نظر در دیتابیس
        $comment = Comment::create([
            'content' => $validated['content'],
            'user_id' => Auth::id(), // کاربر لاگین کرده
            'post_id' => $validated['post_id'],
            'parent_id' => $validated['parent_id'],
            'status' => 'pending'
        ]);

        // 4. هدایت به صفحه مقاله با اسکرول به بخش نظرات
        return redirect()
            ->route('post.show' , $post->slug)
            ->with('success', 'نظر شما با موفقیت ثبت شد و پس از تایید نمایش داده می‌شود.')
            ->withFragment('comments-section');
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
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return back()->with('success', 'نظر حذف شد.');
    }
}
