<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        // جمع‌آوری آمار کاربر
        $stats = [
            'total_posts' => Post::where('user_id' , $user->id)->count(),
            'published_posts' => Post::where('user_id' , $user->id)->where('status' , 'published')->count(),
            'draft_posts' => Post::where('user_id' , $user->id)->where('status' , 'draft')->count(),
            'total_comments' => Comment::where('user_id' , $user->id)->count(),
            'approved_comments' => Comment::where('user_id' , $user->id)->where('status' , 'approved')->count(),
            'member_since' => $user->created_at->format('d F Y'),
            'total_views' => Post::where('user_id', $user->id)->sum('view_count'),
            'last_login' => $user->last_login_at ? $user->last_login_at->diffForHumans() : 'اولین ورود',
        ];

        // مقالات اخیر کاربر
        $recent_posts = Post::where('user_id' , $user->id)
            ->orderBy('created_at' , 'desc')
            ->take(5)->get();

        // نظرات اخیر کاربر
        $recent_comments = Comment::where('user_id' , $user->id)
            ->with('post')
            ->orderBy('created_at' , 'desc')
            ->take(5)->get();

        // مقالات پربازدید
        $popular_posts = Post::with(['category'])
            ->where('user_id', $user->id)
            ->orderBy('view_count', 'desc')
            ->limit(5)
            ->get();

        return view('user.dashboard.index' , compact(
            'stats',
            'recent_posts',
            'popular_posts',
            'recent_comments'
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
