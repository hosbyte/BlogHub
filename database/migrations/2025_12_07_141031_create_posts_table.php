<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->text('excerpt')->nullable();
            $table->unsignedBigInteger('thumbnail_id')->nullable(); // تصویر شاخص
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedBigInteger('user_id'); // نویسنده
            $table->unsignedBigInteger('category_id')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            // کلیدهای خارجی
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('thumbnail_id')->references('id')->on('media')->onDelete('set null');

            // ایندکس‌ها
            $table->index('slug');
            $table->index('status');
            $table->index('user_id');
            $table->index('category_id');
            $table->index('published_at');
            $table->index(['status', 'published_at']);
            $table->index(['user_id', 'status']);
            $table->fullText(['title', 'content', 'excerpt']); // جستجوی فول‌تکست
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
