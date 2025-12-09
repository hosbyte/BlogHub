<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    /**
     * فیلدهای قابل پر شدن
     */
    protected $fillable = [
        'user_id',      // آیدی کاربر
        'action',       // عمل انجام شده
        'description',  // توضیحات
        'data',         // داده‌های اضافی JSON
        'ip_address',   // آی‌پی
        'user_agent',   // اطلاعات مرورگر
    ];

    /**
     * تبدیل نوع داده‌ها
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * رابطه یک به چند با جدول users
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * ثبت فعالیت جدید
     * @param array $attributes
     * @return Activity
     */
    public static function log($attributes)
    {
        $defaults = [
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        return self::create(array_merge($defaults, $attributes));
    }

    /**
     * ثبت فعالیت توسط کاربر
     * @param User $user
     * @param string $action
     * @param string $description
     * @param array $data
     * @return Activity
     */
    public static function logByUser($user, $action, $description, $data = [])
    {
        return self::log([
            'user_id' => $user->id,
            'action' => $action,
            'description' => $description,
            'data' => $data,
        ]);
    }

    /**
     * ثبت فعالیت بدون کاربر (سیستمی)
     * @param string $action
     * @param string $description
     * @param array $data
     * @return Activity
     */
    public static function logSystem($action, $description, $data = [])
    {
        return self::log([
            'user_id' => null,
            'action' => $action,
            'description' => $description,
            'data' => $data,
        ]);
    }

    /**
     * دریافت نام کاربر
     * اگر کاربری لاگین نکرده باشد "سیستم" برمی‌گرداند
     * @return string
     */
    public function getUserNameAttribute()
    {
        return $this->user_id ? $this->user->name : 'سیستم';
    }

    /**
     * دریافت ایمیل کاربر
     * @return string|null
     */
    public function getUserEmailAttribute()
    {
        return $this->user_id ? $this->user->email : null;
    }

    /**
     * دریافت زمان به صورت خوانا
     * @return string
     */
    public function getHumanTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * اسکوپ برای فعالیت‌های یک کاربر خاص
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * اسکوپ برای فعالیت‌های سیستمی (بدون کاربر)
     */
    public function scopeSystem($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * اسکوپ برای فعالیت‌های یک عمل خاص
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * اسکوپ برای فعالیت‌های اخیر
     */
    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('created_at', 'desc')
            ->limit($limit);
    }

    /**
     * اسکوپ برای فعالیت‌های بین تاریخ خاص
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * اسکوپ برای جستجو در توضیحات
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('action', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%")
              ->orWhereHas('user', function ($q2) use ($keyword) {
                  $q2->where('name', 'like', "%{$keyword}%")
                     ->orWhere('email', 'like', "%{$keyword}%");
              });
        });
    }
}
