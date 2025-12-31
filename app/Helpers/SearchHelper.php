<!-- <php

namespace App\Helpers;

class SearchHelper
{
    /**
     * هایلایت متن جستجو در نتایج
     *
     * @param string $text متن اصلی
     * @param string $query عبارت جستجو
     * @param string $tag تگ HTML برای هایلایت (پیش‌فرض: mark)
     * @param string $class کلاس CSS (پیش‌فرض: search-highlight)
     * @return string
     */
    public static function highlight($text, $query, $tag = 'mark', $class = 'search-highlight')
    {
        // اگر متن یا کوئری خالی باشد
        if (empty($text) || empty($query)) {
            return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8', false);
        }

        // تبدیل HTML entities و حذف تگ‌ها
        $cleanText = html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8');

        // شکستن کلمات جستجو
        $words = array_filter(preg_split('/\s+/u', $query), function($word) {
            return mb_strlen(trim($word), 'UTF-8') > 1;
        });

        if (empty($words)) {
            return htmlspecialchars($cleanText, ENT_QUOTES, 'UTF-8', false);
        }

        $highlighted = $cleanText;

        foreach ($words as $word) {
            $trimmedWord = trim($word);

            // اگر کلمه کوتاه است، نادیده بگیر
            if (mb_strlen($trimmedWord, 'UTF-8') < 2) {
                continue;
            }

            // escape برای regex
            $pattern = preg_quote($trimmedWord, '/');

            // جستجوی case-insensitive
            $highlighted = preg_replace(
                "/({$pattern})/iu",
                "<{$tag} class=\"{$class}\">\$1</{$tag}>",
                $highlighted
            );
        }

        return $highlighted;
    }

    /**
     * ایجاد خلاصه‌ای هوشمند از متن با تمرکز بر کلمات جستجو
     *
     * @param string $text متن اصلی
     * @param string $query عبارت جستجو
     * @param int $length طول خلاصه (پیش‌فرض: 200)
     * @return string
     */
    public static function excerpt($text, $query, $length = 200)
    {
        if (empty($text)) {
            return '';
        }

        $cleanText = html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8');

        // اگر کوئری داریم، سعی کن اطراف کلمه جستجو را پیدا کن
        if (!empty($query)) {
            $words = array_filter(preg_split('/\s+/u', $query), function($word) {
                return mb_strlen(trim($word), 'UTF-8') > 1;
            });

            foreach ($words as $word) {
                $trimmedWord = trim($word);
                $position = mb_stripos($cleanText, $trimmedWord, 0, 'UTF-8');

                if ($position !== false) {
                    // نقطه شروع را کمی قبل از کلمه پیدا شده در نظر بگیر
                    $start = max(0, $position - 30);
                    $excerpt = mb_substr($cleanText, $start, $length, 'UTF-8');

                    // اضافه کردن ... اگر از ابتدای متن شروع نکرده‌ایم
                    if ($start > 0) {
                        $excerpt = '... ' . $excerpt;
                    }

                    // اضافه کردن ... اگر به انتهای متن نرسیده‌ایم
                    if (mb_strlen($cleanText, 'UTF-8') > $start + $length) {
                        $excerpt .= ' ...';
                    }

                    return self::highlight($excerpt, $query);
                }
            }
        }

        // اگر کلمه جستجو پیدا نشد، از ابتدای متن خلاصه بساز
        $excerpt = mb_substr($cleanText, 0, $length, 'UTF-8');
        if (mb_strlen($cleanText, 'UTF-8') > $length) {
            $excerpt .= ' ...';
        }

        return self::highlight($excerpt, $query);
    }

    /**
     * خلاصه ساده (بدون هایلایت)
     *
     * @param string $text
     * @param int $length
     * @return string
     */
    public static function simpleExcerpt($text, $length = 200)
    {
        if (empty($text)) {
            return '';
        }

        $cleanText = html_entity_decode(strip_tags($text), ENT_QUOTES, 'UTF-8');
        $excerpt = mb_substr($cleanText, 0, $length, 'UTF-8');

        if (mb_strlen($cleanText, 'UTF-8') > $length) {
            $excerpt .= ' ...';
        }

        return htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8', false);
    }
} -->
