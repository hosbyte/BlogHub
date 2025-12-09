<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

     /**
     * فیلدهای قابل پر شدن
     */
    protected $fillable = [
        'name',         // نام نقش (مثل admin)
        'display_name', // نام نمایشی (مثل مدیر کل)
        'description',  // توضیحات
    ];

    /**
     * رابطه یک به چند با جدول users
     * یک نقش می‌تواند متعلق به چندین کاربر باشد
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * رابطه چند به چند با جدول permissions
     * یک نقش می‌تواند چندین دسترسی داشته باشد
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission')
            ->withTimestamps();
    }

    /**
     * چک می‌کند آیا این نقش دسترسی خاصی دارد یا نه
     * @param string $permissionName نام دسترسی
     * @return bool
     */
    public function hasPermission($permissionName)
    {
        return $this->permissions()
            ->where('name', $permissionName)
            ->exists();
    }

    /**
     * اختصاص دسترسی به نقش
     * @param array|string $permissions
     * @return void
     */
    public function givePermissionTo($permissions)
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        $permissionIds = Permission::whereIn('name', $permissions)->pluck('id');
        $this->permissions()->syncWithoutDetaching($permissionIds);
    }

    /**
     * حذف دسترسی از نقش
     * @param array|string $permissions
     * @return void
     */
    public function revokePermissionTo($permissions)
    {
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        $permissionIds = Permission::whereIn('name', $permissions)->pluck('id');
        $this->permissions()->detach($permissionIds);
    }

    /**
     * حذف همه دسترسی‌ها و اختصاص دسترسی‌های جدید
     * @param array $permissions
     * @return void
     */
    public function syncPermissions($permissions)
    {
        if (is_array($permissions)) {
            $permissionIds = Permission::whereIn('name', $permissions)->pluck('id');
            $this->permissions()->sync($permissionIds);
        }
    }
}
