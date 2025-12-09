<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    /**
     * فیلدهای قابل پر شدن
    */
    protected $fillable = [
        'content',      // متن نظر
        'status',       // وضعیت (در انتظار تایید، تایید شده، رد شده)
        'user_id',      // آیدی کاربر (اگر لاگین کرده باشد)
        'guest_name',   // نام مهمان
        'guest_email',  // ایمیل مهمان
        'post_id',      // آیدی مقاله مربوطه
        'parent_id',    // آیدی نظر والد (برای نظرات تودرتو)
        'likes_count',  // تعداد لایک‌ها
        'dislikes_count', // تعداد دیسلایک‌ها
    ];

    /**
     * تبدیل نوع داده‌ها
    */
    protected $casts = [
        'likes_count' => 'integer',
        'dislikes_count' => 'integer',
    ];

    /**
     * تاریخ‌های اضافی
    */
    protected $dates = [
        'deleted_at',
    ];

    /**
     * رابطه یک به چند با جدول users
     * هر نظر می‌تواند متعلق به یک کاربر باشد
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * رابطه یک به چند با جدول posts
     * هر نظر متعلق به یک مقاله است
    */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * رابطه بازگشتی - نظر والد
     * هر نظر می‌تواند یک والد داشته باشد
    */
    public function parent()
    {
        return $this->belongsTo(Comment::class , 'parent_id');
    }

     /**
     * رابطه بازگشتی - پاسخ‌ها
     * هر نظر می‌تواند چندین پاسخ داشته باشد
    */
    public function replies()
    {
        return $this->hasMany(Comment::class , 'parent_id')->orderBy('created_at' , 'asc');
    }

    /**
     * رابطه چند به چند با جدول likes
    */
    public function likes()
    {
        return $this->morphMany(Like::class , 'likeable');
    }

    /**
     * چک می‌کند آیا نظر توسط کاربر مهمان ثبت شده
     * @return bool
    */
    public function isGuestComment()
    {
        return $this->user_id === null;
    }

    /**
     * چک می‌کند آیا نظر تایید شده است
     * @return bool
    */
    public function isApproved()
    {
        return $this->satus === 'approved';
    }

    /**
     * چک می‌کند آیا نظر در انتظار تایید است
     * @return bool
    */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * چک می‌کند آیا نظر رد شده است
     * @return bool
    */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * تایید نظر
    */
    public function approve()
    {
        $this->update (['status' => 'approved']);
    }

    /**
     * رد نظر
    */
    public function reject()
    {
        $this->update(['status' => 'rejected']);
    }

    /**
     * دریافت نام نویسنده نظر
     * اگر کاربر لاگین کرده باشد نام کاربر، در غیر این صورت نام مهمان
     * @return string
    */
    public function getAuthorNameAttribute()
    {
        return $this->user_id ? $this->user->name : $this->guest_name;
    }

    /**
     * دریافت ایمیل نویسنده نظر
     * @return string|null
    */
    public function getAuthorEmailAttribute()
    {
        return $this->user_id ? $this->user->email : $this->guest_email;
    }

    /**
     * چک می‌کند آیا نظر پاسخ دارد
     * @return bool
    */
    public function hasReplies()
    {
        return $this->replies()->count() > 0;
    }

    /**
     * افزایش تعداد لایک
    */
    public function incrementLikes()
    {
        $this->increment('likes_count');
    }

    /**
     * افزایش تعداد دیسلایک
    */
    public function incrementDislikes()
    {
        $this->increment('dislikes_count');
    }

    /**
     * اسکوپ برای نظرات تایید شده
    */
    public function scopeApproved($query)
    {
        return $query->where('status' , 'approved');
    }

    /**
     * اسکوپ برای نظرات در انتظار تایید
    */
    public function scopePending($query)
    {
        return $query->where('status' , 'pending');
    }

    /**
     * اسکوپ برای نظرات رد شده
    */
    public function scopeRejected($query)
    {
        return $query->where('status' , 'rejected');
    }

    /**
     * اسکوپ برای نظرات والد (بدون parent_id)
    */
    public function scopeParentComments($query)
    {
        return $query->wherenull('parent_id');
    }

    /**
     * اسکوپ برای نظرات یک مقاله خاص
    */
    public function scopeForPost($query , $postId)
    {
        return $query->where('post_id' , $postId);
    }

    /**
     * اسکوپ برای نظرات یک کاربر خاص
    */
    public function scopeByUser($query , $userId)
    {
        return $query->where('user_id' , $userId);
    }

    /**
     * اسکوپ برای نظرات اخیر
    */
    public function scopeRecent($query , $limit = 10)
    {
        return $query->orderBy('created_at' , 'desc')->limit($limit);
    }
}
