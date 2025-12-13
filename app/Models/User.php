<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',      // آیدی نقش کاربر
        'avatar',       // آواتار کاربر
        'bio',          // بیوگرافی
        'status',       // وضعیت حساب
        'last_login_at', // آخرین زمان ورود
        'last_login_ip', // آخرین آی‌پی ورود
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     *
      * فیلدهایی که باید مخفی شوند (مثلاً در JSON responses)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // 'password' => 'hashed',
        'last_login_at' => 'datetime',
        'status' => 'string',
    ];
     /**
     * رابطه یک به چند با جدول posts
     * یک کاربر می‌تواند چندین مقاله داشته باشد
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * رابطه یک به چند با جدول comments
     * یک کاربر می‌تواند چندین نظر داشته باشد
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * رابطه یک به چند با جدول media
     * یک کاربر می‌تواند چندین فایل آپلود کرده باشد
     */
    public function media()
    {
        return $this->hasMany(Media::class);
    }

    /**
     * رابطه یک به چند با جدول activities
     * یک کاربر می‌تواند چندین فعالیت ثبت کرده باشد
     */
    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * رابطه یک به چند با جدول categories
     * یک کاربر می‌تواند چندین دسته‌بندی ایجاد کرده باشد
     */
    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    /**
     * رابطه چند به چند با جدول likes
     * یک کاربر می‌تواند چندین لایک داده باشد
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * رابطه یک به چند با جدول roles
     * هر کاربر یک نقش دارد
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * چک می‌کند آیا کاربر نقش خاصی دارد یا نه
     * @param string $role نام نقش (مثل 'admin', 'author')
     * @return bool
     */
    public function hasRole($role)
    {
        // اگر کاربر نقش داشته باشد و نام نقش برابر با پارامتر باشد
        return $this->role && $this->role->name === $role;
    }

    /**
     * چک می‌کند آیا کاربر ادمین است یا نه
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * چک می‌کند آیا کاربر نویسنده است یا نه
     * @return bool
     */
    public function isAuthor()
    {
        return $this->hasRole('author');
    }

    /**
     * چک می‌کند آیا کاربر عادی است یا نه
     * @return bool
     */
    public function isUser()
    {
        return $this->hasRole('user');
    }

    /**
     * چک می‌کند آیا کاربر دسترسی خاصی دارد یا نه
     * @param string $permission نام دسترسی (مثل 'create-post')
     * @return bool
     */
    public function hasPermission($permission)
    {
        // اگر کاربر نقش نداشته باشد
        if (!$this->role) {
            return false;
        }

        // چک می‌کند آیا نقش کاربر این دسترسی را دارد
        return $this->role->permissions()
            ->where('name', $permission)
            ->exists();
    }

    /**
     * آپدیت زمان آخرین ورود کاربر
     * @return void
     */
    public function updateLastLogin()
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }

    /**
     * دریافت آدرس کامل آواتار کاربر
     * اگر آواتار ندارد، آواتار پیش‌فرض را برمی‌گرداند
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // آواتار پیش‌فرض
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * اسکوپ برای کاربران فعال
     * فقط کاربرانی که وضعیت active دارند
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * اسکوپ برای نویسندگان
     * کاربرانی که نقش author دارند
     */
    public function scopeAuthors($query)
    {
        return $query->whereHas('role', function ($q) {
            $q->where('name', 'author');
        });
    }

    /**
     * اسکوپ برای ادمین‌ها
     * کاربرانی که نقش admin دارند
     */
    public function scopeAdmins($query)
    {
        return $query->whereHas('role', function ($q) {
            $q->where('name', 'admin');
        });
    }
}
