<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    /**
     * فیلدهای قابل پر شدن
    */
    protected $fillable = [
        'name',         // نام دسترسی (مثل create-post)
        'display_name', // نام نمایشی (مثل ایجاد مقاله)
        'description',  // توضیحات
    ];

    /**
     * رابطه چند به چند با جدول roles
     * یک دسترسی می‌تواند متعلق به چندین نقش باشد
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission')
            ->withTimestamps();
    }

    /**
     * چک می‌کند آیا دسترسی خاصی دارد یا نه
     * @param string $roleName نام نقش
     * @return bool
     */
    public function hasRole($roleName)
    {
        return $this->roles()
            ->where('name', $roleName)
            ->exists();
    }

    /**
     * یافتن دسترسی بر اساس نام
     * @param string $name نام دسترسی
     * @return Permission|null
     */
    public static function findByName($name)
    {
        return self::where('name', $name)->first();
    }

    /**
     * ایجاد دسترسی جدید اگر وجود نداشته باشد
     * @param array $attributes
     * @return Permission
     */
    public static function findOrCreate($attributes)
    {
        $permission = self::where('name', $attributes['name'])->first();

        if (!$permission) {
            $permission = self::create($attributes);
        }

        return $permission;
    }
}
