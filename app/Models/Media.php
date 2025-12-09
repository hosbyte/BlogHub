<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    /**
     * فیلدهای قابل پر شدن
     */
    protected $fillable = [
        'name',         // نام فایل
        'path',         // مسیر ذخیره‌سازی
        'type',         // نوع فایل (image, video, document)
        'size',         // حجم فایل (بر حسب بایت)
        'user_id',      // آیدی کاربر آپلود کننده
        'dimensions',   // ابعاد (برای تصاویر)
        'meta',         // اطلاعات اضافی JSON
    ];

    /**
     * تبدیل نوع داده‌ها
     */
    protected $casts = [
        'size' => 'integer',
        'meta' => 'array',
    ];

    /**
     * رابطه یک به چند با جدول users
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * رابطه یک به چند با جدول posts (تصویر شاخص)
     */
    public function thumbnailForPost()
    {
        return $this->hasOne(Post::class, 'thumbnail_id');
    }

    /**
     * دریافت URL کامل فایل
     * @return string
     */
    public function getUrlAttribute()
    {
        // اگر مسیر با http شروع شده باشد (لینک خارجی)
        if (str_starts_with($this->path, 'http')) {
            return $this->path;
        }

        // فایل‌های لوکال
        return Storage::url($this->path);
    }

    /**
     * دریافت مسیر کامل فایل در سیستم
     * @return string
     */
    public function getFullPathAttribute()
    {
        return Storage::path($this->path);
    }

    /**
     * چک می‌کند آیا فایل تصویر است
     * @return bool
     */
    public function isImage()
    {
        return str_starts_with($this->type, 'image/') || $this->type === 'image';
    }

    /**
     * چک می‌کند آیا فایل ویدیو است
     * @return bool
     */
    public function isVideo()
    {
        return str_starts_with($this->type, 'video/') || $this->type === 'video';
    }

    /**
     * چک می‌کند آیا فایل سند است
     * @return bool
     */
    public function isDocument()
    {
        $documentTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        return in_array($this->type, $documentTypes) || $this->type === 'document';
    }

    /**
     * دریافت حجم فایل به صورت خوانا
     * @return string
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }

    /**
     * دریافت ابعاد تصویر
     * @return array|null
     */
    public function getDimensionsArrayAttribute()
    {
        if (!$this->dimensions) {
            return null;
        }

        list($width, $height) = explode('x', $this->dimensions);
        return [
            'width' => (int) $width,
            'height' => (int) $height,
        ];
    }

    /**
     * حذف فایل از سیستم فایل
     * @return bool
     */
    public function deleteFile()
    {
        if (Storage::exists($this->path)) {
            return Storage::delete($this->path);
        }

        return false;
    }

    /**
     * اسکوپ برای تصاویر
     */
    public function scopeImages($query)
    {
        return $query->where(function ($q) {
            $q->where('type', 'like', 'image/%')
              ->orWhere('type', 'image');
        });
    }

    /**
     * اسکوپ برای ویدیوها
     */
    public function scopeVideos($query)
    {
        return $query->where(function ($q) {
            $q->where('type', 'like', 'video/%')
              ->orWhere('type', 'video');
        });
    }

    /**
     * اسکوپ برای فایل‌های یک کاربر خاص
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * اسکوپ برای فایل‌های جدید
     */
    public function scopeRecent($query, $limit = 20)
    {
        return $query->orderBy('created_at', 'desc')
            ->limit($limit);
    }
}
