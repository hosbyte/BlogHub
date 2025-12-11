<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles =[
            [
                'name' => 'admin',
                'display_name' => 'مدیر کل',
                'description' => 'دسترسی کامل به تمام بخش‌های سیستم',
            ],
            [
                'name' => 'author',
                'display_name' => 'نویسنده',
                'description' => 'دسترسی به ایجاد و مدیریت مقالات خود',
            ],
            [
                'name' => 'user',
                'display_name' => 'کاربر عادی',
                'description' => 'دسترسی به مشاهده مقالات و ارسال نظر',
            ],
        ];

        foreach ($roles as $role)
        {
            Role::updateOrCreate(
                ['name' => $role['name']], // شرط جستجو
                $role // داده‌ها برای ایجاد یا آپدیت
            );
        }

        $this->command->info('✅ نقش‌های سیستم ایجاد شدند: admin, author, user');
    }
}
