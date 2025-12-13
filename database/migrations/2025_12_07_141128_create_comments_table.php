<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('user_id')->nullable(); // اگر کاربر لاگین کرده باشد
            $table->string('guest_name')->nullable(); // اگر مهمان باشد
            $table->string('guest_email')->nullable(); // اگر مهمان باشد
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('parent_id')->nullable(); // برای نظرات تودرتو
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('dislikes_count')->default(0);
            $table->timestamps();

            // کلیدهای خارجی
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            // $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade');

            // ایندکس‌ها
            $table->index('post_id');
            $table->index('user_id');
            $table->index('parent_id');
            $table->index('status');
            $table->index(['post_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
