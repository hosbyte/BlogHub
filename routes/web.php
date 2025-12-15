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

Route::get('/', function () {
    return view('welcome');
});

// صفحه اصلی
Route::get('/' , [HomeController::class , 'index'])->name('home');

//مقالات
Route::get('/' , [PostController::class , 'index'])->name('posts.index');
Route::get('/' , [PostController::class , 'show'])->name('posts.show');

// دسته بندی
Route::get('/' , [CategoryController::class , 'show'])->name('categories.show');



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
