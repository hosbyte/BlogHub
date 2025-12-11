<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // دسترسی‌های مدیریت کاربران
        $userPermission = [
            ['name' => 'view-users' , 'display_name' => 'مشاهده کاربران'],
            ['name' => 'create-users' , 'display_name' => 'ایجاد کاربر'],
            ['name' => 'edit-users' , 'display_name' => 'ویرایش کاربر'],
            ['name' => 'delete-users' , 'display_name' => 'حذف کاربر'],
        ];

        // دسترسی‌های مدیریت مقالات
        $postPermission = [
            ['name' => 'view-posts' , 'display_name' => 'مشاهده مقالات'],
            ['name' => 'create-posts' , 'display_name' => 'ایجاد مقاله'],
            ['name' => 'edite-posts' , 'display_name' => 'ویرایش مقاله'],
            ['name' => 'delete-posts' , 'display_name' => 'حذف مقاله'],
            ['name' => 'publish-posts' , 'display_name' => 'انتشار مقاله'],
        ];

        // دسترسی‌های مدیریت نظرات
        $commentPermission = [
            ['name' => 'view-comments' , 'display_name' => 'مشاهده دسته‌بندی‌ها'],
            ['name' => 'edit-comments' , 'display_name' => 'ایجاد دسته‌بندی'],
            ['name' => 'delete-comments' , 'display_name' => 'ایجاد دسته‌بندی'],
            ['name' => 'approve-comments' , 'display_name' => 'ایجاد دسته‌بندی'],
        ];

        // دسترسی‌های مدیریت دسته‌بندی‌ها
        $categoryPermission = [
            ['name' => 'view-categories' , 'display_name' => 'مشاهده دسته‌بندی‌ها'],
            ['name' => 'create-categories' , 'display_name' => 'ایجاد دسته‌بندی'],
            ['name' => 'edit-categories' , 'display_name' => 'ویرایش دسته‌بندی'],
            ['name' => 'delete-categories' , 'display_name' => 'حذف دسته‌بندی'],
        ];

        // دسترسی‌های مدیریت تنظیمات
        $settingPermission = [
            ['name' => 'view-settings' , 'display_name' =>  'مشاهده تنظیمات'],
            ['name' => 'edit-settings' , 'display_name' =>  'ویرایش تنظیمات'],
        ];

        // ادغام همه دسترسی‌ها
        $allPermission = array_merge(
            $userPermission,
            $postPermission,
            $commentPermission,
            $categoryPermission,
            $settingPermission,
        );

        // ایجاد دسترسی‌ها
        foreach($allPermission as $permission)
        {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // اختصاص دسترسی‌ها به نقش‌ها
        $this->assignPermissionsToRoles();

        $this->command->info('✅ دسترسی‌های سیستم ایجاد و به نقش‌ها اختصاص داده شدند');
    }

    /**
    * اختصاص دسترسی به نقش‌ها
    */
    private function assignPermissionsToRoles()
    {
        // نقش ادمین - همه دسترسی‌ها
        $adminRole = Role::where('name' , 'admin')->first();
        $allPermission = Permission::all();
        $adminRole->Permissions()->sync($allPermission->pluck('id'));

        // نقش نویسنده - دسترسی‌های محدود
        $authorRole = Role::where('name' , 'author')->first();
        $auothorPermission = Permission::whereIn('name' , [
            'view-posts',
            'create-posts',
            'edit-posts',
            'delete-posts',
            'publish-posts',
            'view-comments',
            'edit-comments',
            'view-categories',
        ])->get();
        $authorRole->Permissions()->sync($auothorPermission->pluc('id'));

        // نقش کاربر - کمترین دسترسی
        $userRole = Role::where('name' , 'user')->first();
        $userPermission = Permission::wherIn('name' , [
            'view-posts',
            'view-camment',
        ])->get();
        $userRole->Permissions()->sunc($userPermission->pluc('id'));
    }
}
