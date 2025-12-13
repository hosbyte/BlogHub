<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('likeable_id');
            $table->string('likeable_type'); // App\Models\Post یا App\Models\Comment
            $table->enum('type', ['like', 'dislike'])->default('like');
            $table->timestamps();

            // کلیدهای خارجی
            //$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // ایندکس‌ها
            $table->index('user_id');
            $table->index(['likeable_id', 'likeable_type']);
            $table->index('type');
            $table->unique(['user_id', 'likeable_id', 'likeable_type']); // هر کاربر یکبار می‌تواند لایک کند
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('likes');
    }
}
