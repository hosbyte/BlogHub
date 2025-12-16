<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
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
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag')
            ->withTimestamps();
    }

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
        // $slug = str($name)->slug();
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

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * اسکوپ برای مقالات ویژه
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * اسکوپ برای مقالات اخیر
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('published_at', 'desc')
            ->limit($limit);
    }

    /**
     * اسکوپ برای مقالات پربازدید
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('view_count', 'desc')
            ->limit($limit);
    }
}
