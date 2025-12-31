<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Front\PostController as FrontPostController;
use App\Http\Controllers\Front\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Front\AuthorController;
use App\Http\Controllers\Front\SearchController;
use App\Http\Controllers\Front\TagController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\PostController as UserPostController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// صفحه اصلی
Route::get('/' , [HomeController::class , 'index'])->name('home');

// مقالات عمومی
Route::prefix('blog')->name('posts.')->group(function() {
    Route::get('/', [FrontPostController::class, 'index'])->name('index');
    Route::get('/{slug}', [FrontPostController::class, 'show'])->name('show');
    Route::post('/{post}/view', [FrontPostController::class, 'incrementView'])->name('view');
});

// دسته بندی
Route::get('/categories/{slug}' , [CategoryController::class , 'show'])->name('categories.show');

// برچسب ها
Route::get('/tags/{tag:slug}' , [TagController::class , 'show'])->name('tags.show');

// نویسندگان
Route::get('/authors/{username:username}' , [AuthorController::class , 'show'])->name('authors.show');

// جستوجو
Route::get('/search/{username}' , [SearchController::class , 'index'])->name('search.index');

Route::get('/test-search-route', function() {
    return 'Route جستجو تست - OK';
});

// نظرات (نیاز به auth)
Route::middleware('auth')->group(function () {
    Route::post('/comments' , [CommentController::class , 'store'])->name('comments.store');
    Route::delete('/comments/{comment}' , [CommentController::class , 'destroy'])->name('comments.destroy');
});

// افزایش بازدید
// Route::get('/posts/{post}/view' , [FrontPostController::class , 'incrementView'])->name('post.view');
Route::get('/posts/{post}/view' , [FrontPostController::class , 'incrementView'])->name('post.incrementView');

// پنل کاربری
Route::middleware('auth')->prefix('user')->name('user.')->group(function() {
    // داشبورد
    Route::get('/dashboard' , [DashboardController::class , 'index'])->name('dashboard');

    // پروفایل
    Route::get('/profile' , [ProfileController::class , 'edit'])->name('profile.edit');
    Route::put('/profile' , [ProfileController::class , 'update'])->name('profile.update');

    // مقالات کاربر
    Route::get('/posts' , [UserPostController::class , 'index'])->name('posts.index');
    Route::get('/posts/create' , [UserPostController::class , 'create'])->name('posts.create');
    Route::post('/posts', [UserPostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit' , [UserPostController::class , 'edit'])->name('posts.edit');
    Route::put('/posts/{post}' , [UserPostController::class , 'update'])->name('posts.update');
    Route::delete('/posts/{post}' , [UserPostController::class , 'destroy'])->name('posts.destroy');
    Route::post('/posts/{post}/status' , [UserPostController::class , 'changeStatus'])->name('posts.change-status');

    // عملیات گروهی
    Route::post('/posts/bulk-status' , [UserPostController::class , 'bulkChangeStatus'])->name('posts.bulk-status');
    Route::post('/posts/bulk-delete', [UserPostController::class, 'bulkDelete'])->name('posts.bulk-delete');

    // جستوجوی برچسب
    Route::get('/user/tags/search', [TagController::class, 'search'])->name('user.tags.search');
});

require __DIR__.'/auth.php';
