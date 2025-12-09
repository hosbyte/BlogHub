<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    /**
     * فیلدهای قابل پر شدن
     */
    protected $fillable = [
        'user_id',          // آیدی کاربر
        'likeable_id',      // آیدی جدول مورد نظر
        'likeable_type',    // نوع جدول
        'type',             // نوع (like, dislike)
    ];

    /**
     * تبدیل نوع داده‌ها
     */
    protected $casts = [
        'type' => 'string',
    ];

    /**
     * رابطه چند به چند با جدول users
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * رابطه پلی مورفیک
     * می‌تواند متعلق به Post یا Comment باشد
     */
    public function likeable()
    {
        return $this->morphTo();
    }

    /**
     * لایک کردن
     * @param User $user
     * @param Model $likeable
     * @return Like
     */
    public static function like($user, $likeable)
    {
        return self::createOrUpdateReaction($user, $likeable, 'like');
    }

    /**
     * دیسلایک کردن
     * @param User $user
     * @param Model $likeable
     * @return Like
     */
    public static function dislike($user, $likeable)
    {
        return self::createOrUpdateReaction($user, $likeable, 'dislike');
    }

    /**
     * حذف لایک/دیسلایک
     * @param User $user
     * @param Model $likeable
     * @return bool
     */
    public static function removeReaction($user, $likeable)
    {
        return self::where('user_id', $user->id)
            ->where('likeable_id', $likeable->id)
            ->where('likeable_type', get_class($likeable))
            ->delete();
    }

    /**
     * ایجاد یا آپدیت واکنش
     */
    private static function createOrUpdateReaction($user, $likeable, $type)
    {
        $like = self::where('user_id', $user->id)
            ->where('likeable_id', $likeable->id)
            ->where('likeable_type', get_class($likeable))
            ->first();

        if ($like) {
            // اگر قبلاً واکنش داده، آپدیت می‌کنیم
            $like->update(['type' => $type]);
        } else {
            // اگر اولین بار است، ایجاد می‌کنیم
            $like = self::create([
                'user_id' => $user->id,
                'likeable_id' => $likeable->id,
                'likeable_type' => get_class($likeable),
                'type' => $type,
            ]);
        }

        // آپدیت تعداد لایک/دیسلایک در مدل مرتبط
        self::updateLikeableCounts($likeable);

        return $like;
    }

    /**
     * آپدیت تعداد لایک/دیسلایک در مدل مرتبط
     */
    private static function updateLikeableCounts($likeable)
    {
        $likesCount = self::where('likeable_id', $likeable->id)
            ->where('likeable_type', get_class($likeable))
            ->where('type', 'like')
            ->count();

        $dislikesCount = self::where('likeable_id', $likeable->id)
            ->where('likeable_type', get_class($likeable))
            ->where('type', 'dislike')
            ->count();

        $likeable->update([
            'likes_count' => $likesCount,
            'dislikes_count' => $dislikesCount,
        ]);
    }

    /**
     * چک می‌کند آیا کاربر لایک کرده است
     * @param User $user
     * @param Model $likeable
     * @return bool
     */
    public static function hasLiked($user, $likeable)
    {
        return self::where('user_id', $user->id)
            ->where('likeable_id', $likeable->id)
            ->where('likeable_type', get_class($likeable))
            ->where('type', 'like')
            ->exists();
    }

    /**
     * چک می‌کند آیا کاربر دیسلایک کرده است
     * @param User $user
     * @param Model $likeable
     * @return bool
     */
    public static function hasDisliked($user, $likeable)
    {
        return self::where('user_id', $user->id)
            ->where('likeable_id', $likeable->id)
            ->where('likeable_type', get_class($likeable))
            ->where('type', 'dislike')
            ->exists();
    }

    /**
     * چک می‌کند آیا کاربر واکنش داده است
     * @param User $user
     * @param Model $likeable
     * @return bool
     */
    public static function hasReacted($user, $likeable)
    {
        return self::where('user_id', $user->id)
            ->where('likeable_id', $likeable->id)
            ->where('likeable_type', get_class($likeable))
            ->exists();
    }

    /**
     * دریافت نوع واکنش کاربر
     * @param User $user
     * @param Model $likeable
     * @return string|null (like, dislike, null)
     */
    public static function getUserReaction($user, $likeable)
    {
        $reaction = self::where('user_id', $user->id)
            ->where('likeable_id', $likeable->id)
            ->where('likeable_type', get_class($likeable))
            ->first();

        return $reaction ? $reaction->type : null;
    }

    /**
     * اسکوپ برای لایک‌ها
     */
    public function scopeLikes($query)
    {
        return $query->where('type', 'like');
    }

    /**
     * اسکوپ برای دیسلایک‌ها
     */
    public function scopeDislikes($query)
    {
        return $query->where('type', 'dislike');
    }

    /**
     * اسکوپ برای یک مدل خاص
     */
    public function scopeForModel($query, $modelType, $modelId)
    {
        return $query->where('likeable_type', $modelType)
            ->where('likeable_id', $modelId);
    }

    /**
     * اسکوپ برای یک کاربر خاص
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
