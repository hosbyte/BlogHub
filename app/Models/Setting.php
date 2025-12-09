<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * فیلدهای قابل پر شدن
     */
    protected $fillable = [
        'key',      // کلید تنظیمات
        'value',    // مقدار
        'type',     // نوع (text, boolean, number, json, array)
        'group',    // گروه (general, appearance, seo, social)
    ];

    /**
     * تبدیل نوع داده‌ها
     */
    protected $casts = [
        'value' => 'string',
    ];

    /**
     * عدم استفاده از timestamps
     */
    public $timestamps = false;

    /**
     * دریافت تنظیم بر اساس کلید
     * @param string $key کلید تنظیمات
     * @param mixed $default مقدار پیش‌فرض
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return self::castValue($setting->value, $setting->type);
    }

    /**
     * ذخیره تنظیم
     * @param string $key کلید تنظیمات
     * @param mixed $value مقدار
     * @param string $type نوع
     * @param string $group گروه
     * @return void
     */
    public static function setValue($key, $value, $type = 'text', $group = 'general')
    {
        self::updateOrCreate(
            ['key' => $key],
            [
                'value' => self::prepareValue($value, $type),
                'type' => $type,
                'group' => $group,
            ]
        );
    }

    /**
     * تبدیل مقدار بر اساس نوع
     * @param mixed $value
     * @param string $type
     * @return mixed
     */
    private static function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);

            case 'number':
            case 'integer':
                return (int) $value;

            case 'float':
                return (float) $value;

            case 'json':
                return json_decode($value, true);

            case 'array':
                return is_array($value) ? $value : explode(',', $value);

            default:
                return $value;
        }
    }

    /**
     * آماده‌سازی مقدار برای ذخیره
     * @param mixed $value
     * @param string $type
     * @return string
     */
    private static function prepareValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return $value ? '1' : '0';

            case 'json':
            case 'array':
                return is_string($value) ? $value : json_encode($value);

            default:
                return (string) $value;
        }
    }

    /**
     * دریافت همه تنظیمات یک گروه
     * @param string $group نام گروه
     * @return array
     */
    public static function getGroup($group)
    {
        $settings = self::where('group', $group)->get();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting->key] = self::castValue($setting->value, $setting->type);
        }

        return $result;
    }

    /**
     * دریافت تنظیمات سایت
     * @return array
     */
    public static function getSiteSettings()
    {
        return self::getGroup('general');
    }

    /**
     * دریافت تنظیمات ظاهری
     * @return array
     */
    public static function getAppearanceSettings()
    {
        return self::getGroup('appearance');
    }

    /**
     * دریافت تنظیمات SEO
     * @return array
     */
    public static function getSeoSettings()
    {
        return self::getGroup('seo');
    }

    /**
     * دریافت تنظیمات شبکه‌های اجتماعی
     * @return array
     */
    public static function getSocialSettings()
    {
        return self::getGroup('social');
    }

    /**
     * چک می‌کند آیا تنظیمات وجود دارد
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        return self::where('key', $key)->exists();
    }

    /**
     * حذف تنظیمات
     * @param string $key
     * @return bool
     */
    public static function remove($key)
    {
        return self::where('key', $key)->delete();
    }
}
