<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::where('email' , 'hosbyte@gmail.com')->first();

        // دسته‌بندی‌های اصلی
        $mainCategories = [
            [
                'name' =>  'برنامه‌نویسی',
                'slug' => 'programming',
                'description' => 'مقالات مرتبط با برنامه‌نویسی و توسعه نرم‌افزار',
                'user_id' => $admin->id,
            ],
            [
                'name' => 'طراحی وب',
                'slug' => 'web-design',
                'description' => 'مقالات مربوط به حوزهطراحی سایت',
                'user_id' => $admin->id,
            ],
            [
                'name' => 'هوش مصنوعی',
                'slug' => 'artificial-intelligence',
                'description' => 'مقالات مرتبط با هوش مصنوعی و یادگیری ماشین',
                'user_id' => $admin->id,
            ],
            [
                'name' => 'امنیت',
                'slug' => 'security',
                'description' => 'مقالات امنیت اطلاعات و شبکه',
                'user_id' => $admin->id,
            ],
            [
                'name' => 'داده‌کاوی',
                'slug' => 'data-mining',
                'description' => 'مقالات مربوط به تحلیل داده و داده‌کاوی',
                'user_id' => $admin->id,
            ],
        ];

        $createdCategories = [];

        foreach($mainCategories as $category)
        {
            $cat = Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
            $createdCategories[$category['slug']] = $cat->id;
        }

        // زیردسته‌بندی‌ها
        $subCategories = [
            [
                'name' => 'لاراول',
                'slug' => 'laravel',
                'description' => 'مقالات آموزش و ترفندهای لاراول',
                'parent_id' => $createdCategories['programming'],
                'user_if'=> $admin->id,
            ],
            [
                'name' => 'ری‌اکت',
                'slug' => 'react',
                'description' => 'مقالات مربوط به React.js و اکوسیستم آن',
                'parent_id' => $createdCategories['programming'],
                'user_id' => $admin->id,
            ],
            [
                'name' => 'ویو جی‌اس',
                'slug' => 'vuejs',
                'description' => 'مقالات Vue.js و کامپوننت‌ها',
                'parent_id' => $createdCategories['programming'],
                'user_id' => $admin->id,
            ],
            [
                'name' => 'پایتون',
                'slug' => 'python',
                'description' => 'مقالات زبان برنامه‌نویسی پایتون',
                'parent_id' => $createdCategories['programming'],
                'user_id' => $admin->id,
            ],
            [
                'name' => 'CSS',
                'slug' => 'css',
                'description' => 'مقالات مربوط به CSS و استایل‌دهی',
                'parent_id' => $createdCategories['web-design'],
                'user_id' => $admin->id,
            ],
            [
                'name' => 'جاوااسکریپت',
                'slug' => 'javascript',
                'description' => 'مقالات پیشرفته جاوااسکریپت',
                'parent_id' => $createdCategories['web-design'],
                'user_id' => $admin->id,
            ],
        ];

        foreach($subCategories as $subCategory)
        {
            Category::updateOrCreate(
                ['slug' => $subCategory['slug']],
                $subCategory
            );

            $this->command->info('✅ دسته‌بندی‌های اولیه سیستم ایجاد شدند');
        }
    }
}
