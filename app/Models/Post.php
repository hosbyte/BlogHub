<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory; // SoftDeletes;

    /**
     * فیلدهای قابل پر شدن
     */
    protected $fillable = [
        'title',        // عنوان مقاله
        'slug',         // نامک برای URL
        'content',      // محتوای مقاله
        'excerpt',      // خلاصه مقاله
        'thumbnail_id', // آیدی تصویر شاخص
        'status',       // وضعیت (پیش‌نویس، منتشر شده، آرشیو)
        'is_featured',  // آیا مقاله ویژه است؟
        'view_count',   // تعداد بازدید
        'user_id',      // آیدی نویسنده
        'category_id',  // آیدی دسته‌بندی
        'published_at', // تاریخ انتشار
    ];

    /**
     * تبدیل نوع داده‌ها
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'view_count' => 'integer',
        'published_at' => 'datetime',
        'status' => 'string',
    ];

    /**
     * تاریخ‌های اضافی برای سریالایز کردن
     */
    protected $dates = [
        'published_at',
        'deleted_at',
    ];

    // ======== روابط ========

    /**
     * رابطه یک به چند با جدول users (نویسنده)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * رابطه یک به چند با جدول categories
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * رابطه یک به چند با جدول media (تصویر شاخص)
     */
    public function thumbnail()
    {
        return $this->belongsTo(Media::class, 'thumbnail_id');
    }

    /**
     * رابطه یک به چند با جدول comments
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * رابطه چند به چند با جدول tags
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tag')
            ->withTimestamps();
    }

    /**
     * رابطه چند به چند با جدول likes
     */
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    // ======== اسکوپ‌ها ========

    /**
     * اسکوپ برای مقالات منتشر شده
     */
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

    /**
     * اسکوپ برای مقالات یک نویسنده خاص
     */
    public function scopeByAuthor($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * اسکوپ برای مقالات یک دسته‌بندی خاص
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * جستجو در مقالات
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('content', 'like', "%{$keyword}%")
              ->orWhere('excerpt', 'like', "%{$keyword}%");
        });
    }

    // ======== متدهای کمکی ========

    /**
     * دریافت URL کامل مقاله
     * @return string
     */
    public function getUrlAttribute()
    {
        return route('posts.show', $this->slug);
    }

    /**
     * دریافت آدرس کامل تصویر شاخص
     * @return string
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail->path);
        }

        // تصویر پیش‌فرض
        return asset('images/default-thumbnail.jpg');
    }

    /**
     * چک می‌کند آیا مقاله منتشر شده است
     * @return bool
     */
    public function isPublished()
    {
        return $this->status === 'published';
    }

    /**
     * چک می‌کند آیا مقاله پیش‌نویس است
     * @return bool
     */
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    /**
     * چک می‌کند آیا مقاله آرشیو شده است
     * @return bool
     */
    public function isArchived()
    {
        return $this->status === 'archived';
    }

    /**
     * انتشار مقاله
     */
    public function publish()
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * افزایش تعداد بازدید
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    /**
     * دریافت تعداد نظرات تایید شده
     * @return int
     */
    public function getApprovedCommentsCountAttribute()
    {
        return $this->comments()
            ->where('status', 'approved')
            ->count();
    }

    /**
     * دریافت تعداد لایک‌ها
     * @return int
     */
    public function getLikesCountAttribute()
    {
        return $this->likes()
            ->where('type', 'like')
            ->count();
    }

    /**
     * دریافت تعداد دیسلایک‌ها
     * @return int
     */
    public function getDislikesCountAttribute()
    {
        return $this->likes()
            ->where('type', 'dislike')
            ->count();
    }
}
