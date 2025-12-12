<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // کاربر ادمین اصلی
        $adminRole = Role::where('name' , 'admin')->first();

        User::updateOrCreatw(
            ['email' => 'hosbyte@gmail.com'],
            [
                'name' => 'مدیر سیستم',
                'email' => 'hosbyte@gmail.com',
                'password' => Hash::make('Hosein.s81'),
                'role_id' => $adminRole->id,
                'avatar' => null,
                'bio' => 'مدیر کل سیستم وبلاگ',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // کاربر نویسنده نمونه
        $authorRole = Role::where('name' , 'author')->first();

        User::updateOrCraete(
            ['email' => 'author@bloghub.local'],
            [
                'name' => 'نویسنده نمونه',
                'email' => 'author@bloghub.local',
                'password' => Hash::make('Author@123456'),
                'role_id' => $authorRole->id,
                'avatar' => null,
                'bio' => 'نویسنده حرفه‌ای مقالات برنامه‌نویسی',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // کاربر عادی نمونه
        $userRole = Role::where('name' . 'user')->first();

        User::updateOrCreate(
            ['email' => 'user@bloghub.local'],
            [
                'name' => 'user',
                'email' => 'user@bloghub.local',
                'password' => Hash::make('User@123456'),
                'role_id' => $userRole->id,
                'avatar' => null,
                'bio' => 'علاقه‌مند به تکنولوژی و برنامه‌نویسی',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // ایجاد ۱۰ کاربر تصادفی (برای تست)
        User::factory()->count(10)->create([
            'role_id' => $userRole->id,
            'status' => 'active',
        ]);

        $this->command->info('✅ کاربران اولیه سیستم ایجاد شدند');
        $this->command->info('📧 ادمین: hosbyte@email.com - رمز: Hosein.s81');
        $this->command->info('📧 نویسنده: author@bloghub.local - رمز: Author@123456');
        $this->command->info('📧 کاربر: user@bloghub.local - رمز: User@123456');
    }
}
