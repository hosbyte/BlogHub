<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // تنظیمات عمومی
        $generalSettings = [
            [
                'key' => 'site_title',
                'value' => 'BlogHub - سیستم مدیریت وبلاگ',
                'type' => 'text',
                'group' => 'general',
            ],
            [
                'key' => 'site_description',
                'value' => 'سیستم مدیریت وبلاگ چندکاربره با پنل ادمین پیشرفته',
                'type' => 'text',
                'group' => 'general',
            ],
            [
                'key' => 'site_keywords',
                'value' => 'وبلاگ,لاراول,برنامه‌نویسی,مدیریت محتوا',
                'type' => 'text',
                'group' => 'general',
            ],
            [
                'key' => 'site_email',
                'value' => 'info@bloghub.local',
                'type' => 'text',
                'group' => 'general',
            ],
            [
                'key' => 'posts_per_page',
                'value' => '10',
                'type' => 'number',
                'group' => 'general',
            ],
            [
                'key' => 'comments_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'general',
            ],
            [
                'key' => 'registration_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'general',
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'general',
            ],
        ];

        // تنظیمات ظاهری
        $appearanceSettings = [
            [
                'key' => 'theme',
                'value' => 'light',
                'type' => 'text',
                'group' => 'appearance',
            ],
            [
                'key' => 'logo',
                'value' => '',
                'type' => 'text',
                'group' => 'appearance',
            ],
            [
                'key' => 'favicon',
                'value' => '',
                'type' => 'text',
                'group' => 'appearance',
            ],
            [
                'key' => 'header_color',
                'value' => '#4361ee',
                'type' => 'text',
                'group' => 'appearance',
            ],
            [
                'key' => 'footer_text',
                'value' => '© 2024 BlogHub. تمامی حقوق محفوظ است.',
                'type' => 'text',
                'group' => 'appearance',
            ],
        ];

        // تنظیمات SEO
        $seoSettings = [
            [
                'key' => 'meta_author',
                'value' => 'BlogHub Team',
                'type' => 'text',
                'group' => 'seo',
            ],
            [
                'key' => 'meta_robots',
                'value' => 'index, follow',
                'type' => 'text',
                'group' => 'seo',
            ],
            [
                'key' => 'og_image',
                'value' => '',
                'type' => 'text',
                'group' => 'seo',
            ],
            [
                'key' => 'twitter_username',
                'value' => '@bloghub',
                'type' => 'text',
                'group' => 'seo',
            ],
        ];

        // تنظیمات شبکه‌های اجتماعی
        $socialSettings = [
            [
                'key' => 'social_telegram',
                'value' => 'https://t.me/bloghub',
                'type' => 'text',
                'group' => 'social',
            ],
            [
                'key' => 'social_twitter',
                'value' => 'https://twitter.com/bloghub',
                'type' => 'text',
                'group' => 'social',
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/bloghub',
                'type' => 'text',
                'group' => 'social',
            ],
            [
                'key' => 'social_linkedin',
                'value' => 'https://linkedin.com/company/bloghub',
                'type' => 'text',
                'group' => 'social',
            ],
            [
                'key' => 'social_github',
                'value' => 'https://github.com/bloghub',
                'type' => 'text',
                'group' => 'social',
            ],
        ];

        // ادغام همه تنظیمات
        $allSettings = array_merge(
            $generalSettings,
            $appearanceSettings,
            $seoSettings,
            $socialSettings
        );

        // ایجاد تنظیمات
        foreach ($allSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('✅ تنظیمات اولیه سیستم ایجاد شدند');
    }
}
