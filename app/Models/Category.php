<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * فیلدهای قابل پر شدن
     */
    protected $fillable = [
        'name',         // نام دسته‌بندی
        'slug',         // نامک برای URL
        'description',  // توضیحات
        'parent_id',    // آیدی دسته‌بندی والد (برای سلسله‌مراتبی)
        'user_id',      // آیدی کاربر ایجاد کننده
    ];

    /**
     * تبدیل نوع داده parent_id
    */
    protected $casts = [
        'parent_id' => 'integer',
    ];

    /**
     * رابطه یک به چند با جدول posts
     * یک دسته‌بندی می‌تواند چندین مقاله داشته باشد
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * رابطه یک به چند با جدول users
     * هر دسته‌بندی توسط یک کاربر ایجاد شده
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * رابطه بازگشتی - دسته‌بندی والد
     * هر دسته‌بندی می‌تواند یک والد داشته باشد
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * رابطه بازگشتی - دسته‌بندی‌های فرزند
     * یک دسته‌بندی می‌تواند چندین زیردسته داشته باشد
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * دریافت همه فرزندان (تودرتو)
     */
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    /**
     * چک می‌کند آیا دسته‌بندی والد دارد یا نه
     * @return bool
     */
    public function hasParent()
    {
        return $this->parent_id !== null;
    }

    /**
     * چک می‌کند آیا دسته‌بندی فرزند دارد یا نه
     * @return bool
     */
    public function hasChildren()
    {
        return $this->children()->exists();
    }

    /**
     * دریافت تعداد مقالات در این دسته‌بندی (شامل زیردسته‌ها)
     * @return int
     */
    public function getPostsCountAttribute()
    {
        $count = $this->posts()->count();

        // اضافه کردن مقالات زیردسته‌ها
        foreach ($this->children as $child) {
            $count += $child->posts_count;
        }

        return $count;
    }

    /**
     * اسکوپ برای دسته‌بندی‌های اصلی (بدون والد)
     */
    public function scopeMainCategories($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
    * اسکوپ برای دسته‌بندی‌های دارای مقاله
    */
    public function scopeHasPosts($query)
    {
        return $query->whereHas('posts', function ($q) {
            $q->where('status', 'published'); // فقط مقالات منتشر شده
        });
    }
}
