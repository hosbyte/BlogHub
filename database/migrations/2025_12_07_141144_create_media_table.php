<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->string('type'); // image, video, document, etc.
            $table->unsignedBigInteger('size')->default(0); // اندازه به بایت
            $table->unsignedBigInteger('user_id'); // آپلود کننده
            $table->string('dimensions')->nullable(); // برای تصاویر: widthxheight
            $table->text('meta')->nullable(); // اطلاعات اضافی JSON
            $table->timestamps();

            // کلیدهای خارجی
            //$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // ایندکس‌ها
            $table->index('type');
            $table->index('user_id');
            $table->index(['type', 'user_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media');
    }
}
