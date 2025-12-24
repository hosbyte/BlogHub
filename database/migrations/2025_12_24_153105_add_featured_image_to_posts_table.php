<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            // فقط فیلدهایی را اضافه کن که وجود ندارند
            
            if (!Schema::hasColumn('posts', 'featured_image')) {
                $table->string('featured_image')->nullable()->after('content');
            }
            
            if (!Schema::hasColumn('posts', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('featured_image');
            }
            
            if (!Schema::hasColumn('posts', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            
            if (!Schema::hasColumn('posts', 'view_count')) {
                $table->integer('view_count')->default(0)->after('meta_description');
            }
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            // فقط فیلدهایی را حذف کن که توسط این migration اضافه شده‌اند
            $columnsToDrop = ['featured_image', 'meta_title', 'meta_description', 'view_count'];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('posts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};