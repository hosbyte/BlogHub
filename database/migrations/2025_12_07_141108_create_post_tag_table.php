<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            //کلید های خارجی
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cacade');
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cacade');

            // جلوگیری از تکراری بودن رابطه
            $table->unique(['post_id', 'tag_id']);

            // ایندکس‌ها
            $table->index(['post_id', 'tag_id']);
            $table->index('tag_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_tag');
    }
}
