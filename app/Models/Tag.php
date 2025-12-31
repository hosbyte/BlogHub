<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    /**
     * فیلدهای قابل پر شدن
     */
    protected $fillable = [
        'name',         // نام برچسب
        'slug',         // نامک برای URL
        'description',  // توضیحات
    ];

    /**
     * رابطه چند به چند با جدول posts
     * یک برچسب می‌تواند متعلق به چندین مقاله باشد
     */
    // public function posts()
    // {
    //     return $this->belongsToMany(Post::class, 'post_tag')
    //         ->withTimestamps();
    // }

    /**
     * دریافت تعداد مقالات این برچسب
     * @return int
     */
    public function getPostsCountAttribute()
    {
        return $this->posts()->count();
    }

    /**
     * دریافت مقالات منتشر شده این برچسب
     */
    public function publishedPosts()
    {
        return $this->posts()->where('status', 'published');
    }

    /**
     * یافتن برچسب بر اساس نامک (slug)
     * @param string $slug
     * @return Tag|null
     */
    public static function findBySlug($slug)
    {
        return self::where('slug', $slug)->first();
    }

    /**
     * ایجاد برچسب جدید اگر وجود نداشته باشد
     * @param string $name نام برچسب
     * @return Tag
     */
    public static function findOrCreate($name)
    {
        // استفاده از Str::slug() برای Laravel 8
        $slug = Str::slug($name);

        $tag = self::where('slug', $slug)->first();

        if (!$tag) {
            $tag = self::create([
                'name' => $name,
                'slug' => $slug,
            ]);
        }

        return $tag;
    }

    /**
     * ایجاد چندین برچسب
     * @param array $tags
     * @return array آیدی برچسب‌های ایجاد شده
     */
    public static function findOrCreateMany($tags)
    {
        $tagIds = [];

        foreach ($tags as $tagName) {
            $tag = self::findOrCreate($tagName);
            $tagIds[] = $tag->id;
        }

        return $tagIds;
    }

    /**
     * Scope برای دریافت برچسب‌های محبوب
     */
    public function scopePopular($query, $limit = 20)
    {
        return $query->withCount(['posts' => function ($query) {
                $query->published(); // فقط مقالات منتشر شده
            }])
            ->having('posts_count', '>', 0)
            ->orderBy('posts_count', 'desc')
            ->limit($limit);
    }

    /**
     * Scope برای دریافت برچسب‌هایی که مقاله دارند
     */
    public function scopeHasPosts($query)
    {
        return $query->whereHas('posts', function ($query) {
            $query->published();
        });
    }

    /**
     * رابطه با مقالات
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
