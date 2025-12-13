<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // ترتیب اجرای Seederها بسیار مهم است
        $this->call([
            RoleSeeder::class,  // اول: نقش‌ها
            PermissionSeeder::class,  // دوم: دسترسی‌ها (نیاز به نقش‌ها دارد)
            UserSeeder::class,  // سوم: کاربران (نیاز به نقش‌ها دارد)
            CategorySeeder::class,  // چهارم: دسته‌بندی‌ها (نیاز به کاربران دارد)
            SettingsSeeder::class,  // پنجم: تنظیمات
        ]);

        $this->command->info('🎉 تمامی داده‌های اولیه با موفقیت ایجاد شدند!');
        $this->command->info('🔗 آدرس: http://localhost:8000');
        $this->command->info('👤 ادمین: hosbyte@gmail.com - رمز: Hosein.s81');
    }
}
