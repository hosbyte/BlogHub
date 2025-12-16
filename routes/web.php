<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

// صفحه اصلی
Route::get('/' , [HomeController::class , 'index'])->name('home');

//مقالات
Route::get('/posts' , [PostController::class , 'index'])->name('posts.index');
Route::get('/posts/{slug}' , [PostController::class , 'show'])->name('posts.show');

// دسته بندی
Route::get('/categories/{slug}' , [CategoryController::class , 'show'])->name('categories.show');



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';





// Route::get('/test-card', function () {
//     // ساخت یک مقاله تست
//     $post = new stdClass();
//     $post->slug = 'test-post';
//     $post->title = 'مقاله آزمایشی برای تست کامپوننت';
//     $post->excerpt = 'این یک مقاله آزمایشی است که برای تست کامپوننت کارت مقاله ایجاد شده است.';
//     $post->thumbnail_url = 'https://via.placeholder.com/400x300/4361ee/ffffff?text=BlogHub';
//     $post->is_featured = true;
//     $post->view_count = 1500;
//     $post->published_at = now();
//     $post->user = (object)['name' => 'نویسنده تست'];
//     $post->category = (object)[
//         'slug' => 'test-category',
//         'name' => 'تست'
//     ];

//     return view('test-card', compact('post'));
// });

// Route::get('/test-multiple', function () {
//     $posts = [];

//     // ۶ مقاله تست
//     for ($i = 1; $i <= 6; $i++) {
//         $post = new stdClass();
//         $post->slug = "test-post-$i";
//         $post->title = "مقاله آزمایشی شماره $i";
//         $post->excerpt = "توضیحات مختصر برای مقاله شماره $i که برای تست کامپوننت ایجاد شده است.";
//         $post->thumbnail_url = 'https://via.placeholder.com/400x300/4361ee/ffffff?text=Post+' . $i;
//         $post->is_featured = $i <= 2;
//         $post->view_count = rand(100, 5000);
//         $post->published_at = now()->subDays($i);
//         $post->user = (object)['name' => 'نویسنده ' . $i];
//         $post->category = (object)[
//             'slug' => 'category-' . $i,
//             'name' => ['برنامه‌نویسی', 'طراحی', 'امنیت', 'داده'][$i % 4]
//         ];

//         $posts[] = $post;
//     }

//     return view('test-multiple', compact('posts'));
// });
