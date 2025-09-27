<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    protected $manager;

    public function __construct()
    {
        // إنشاء مدير الصور مع GD driver
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * التحقق الأساسي من أن الملف صورة صالحة
     */
    public function isImage(UploadedFile $file): bool
    {
        if (!$file->isValid())
            return false;
        $mime = $file->getClientMimeType() ?: $file->getMimeType();
        if (!is_string($mime) || stripos($mime, 'image/') !== 0)
            return false;
        $ext = strtolower($file->getClientOriginalExtension());
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        return in_array($ext, $allowed, true);
    }

    /**
     * قراءة الصورة مع تدوير EXIF تلقائياً
     */
    public function readOriented(UploadedFile|string $source)
    {
        $image = is_string($source)
            ? $this->manager->read($source)
            : $this->manager->read($source->getPathname());
        return $image->orient();
    }

    /**
     * تصغير مع الحفاظ على التناسب إلى حد أقصى لأحد البعدين
     */
    public function scaleDown($image, int $maxDim = 1600)
    {
        $w = $image->width();
        $h = $image->height();
        if ($w > $maxDim || $h > $maxDim) {
            if ($w >= $h) {
                $image = $image->scale(width: $maxDim);
            } else {
                $image = $image->scale(height: $maxDim);
            }
        }
        return $image;
    }

    /**
     * تحويل إلى WEBP بجودة محددة وإرجاع bytes
     */
    public function encodeWebp($image, int $quality = 80)
    {
        return $image->encodeByExtension('webp', quality: $quality);
    }

    /**
     * معالجة ورفع صورة مؤقتة إلى مجلد temp_artworks كـ WEBP
     */
    public function processToTempWebp(UploadedFile $file, string $directory = 'temp_artworks', int $maxDim = 1600, int $quality = 80): array
    {
        if (!$this->isImage($file)) {
            return ['success' => false, 'message' => 'الملف المرفوع ليس صورة'];
        }
        $image = $this->readOriented($file);
        $image = $this->scaleDown($image, $maxDim);
        $bytes = $this->encodeWebp($image, $quality);
        $filename = Str::uuid()->toString() . '.webp';
        $path = rtrim($directory, '/') . '/' . $filename;
        Storage::disk('public')->put($path, $bytes);
        return [
            'success' => true,
            'path' => $path,
            'url' => asset('storage/' . $path),
            'width' => $image->width(),
            'height' => $image->height(),
        ];
    }

    /**
     * رفع وقص الصورة
     */
    public function uploadAndCrop(UploadedFile $file, $directory = 'images', $width = 500, $height = 500)
    {
        // إنشاء اسم فريد للصورة
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();

        // قراءة الصورة مع ترتيب الاتجاه
        $image = $this->readOriented($file);

        // معلومات الصورة الأصلية
        $originalInfo = [
            'width' => $image->width(),
            'height' => $image->height(),
            'size' => $file->getSize(), // بالبايت
            'mime_type' => $file->getMimeType(),
            'original_name' => $file->getClientOriginalName()
        ];

        // قص الصورة (crop) من المنتصف وتغيير الحجم
        $image->cover($width, $height);

        // حفظ الصورة
        $path = $directory . '/' . $filename;
        $imageData = $image->encodeByExtension('jpg', quality: 85); // تحويل إلى JPEG بجودة 85%

        Storage::disk('public')->put($path, $imageData);

        return [
            'path' => $path,
            'filename' => $filename,
            'url' => asset('storage/' . $path),
            'cropped_size' => [
                'width' => $width,
                'height' => $height
            ],
            'original_info' => $originalInfo
        ];
    }

    /**
     * إنشاء صور بأحجام مختلفة (thumbnails)
     */
    public function createThumbnails(UploadedFile $file, $directory = 'images')
    {
        $sizes = [
            'thumbnail' => ['width' => 150, 'height' => 150],
            'medium' => ['width' => 300, 'height' => 300],
            'large' => ['width' => 800, 'height' => 600]
        ];

        $results = [];
        $originalImage = $this->readOriented($file);

        foreach ($sizes as $sizeName => $dimensions) {
            $filename = $sizeName . '_' . uniqid() . '_' . time() . '.jpg';
            $path = $directory . '/' . $filename;

            // نسخ الصورة وتغيير حجمها
            $resizedImage = clone $originalImage;
            $resizedImage->cover($dimensions['width'], $dimensions['height']);

            $imageData = $resizedImage->encodeByExtension('jpg', quality: 85);
            Storage::disk('public')->put($path, $imageData);

            $results[$sizeName] = [
                'path' => $path,
                'url' => asset('storage/' . $path),
                'width' => $dimensions['width'],
                'height' => $dimensions['height']
            ];
        }

        return $results;
    }

    /**
     * الحصول على معلومات الصورة
     */
    public function getImageInfo(UploadedFile $file)
    {
        $image = $this->readOriented($file);

        return [
            'width' => $image->width(),
            'height' => $image->height(),
            'size' => $file->getSize(),
            'size_human' => $this->formatBytes($file->getSize()),
            'mime_type' => $file->getMimeType(),
            'extension' => $file->getClientOriginalExtension(),
            'original_name' => $file->getClientOriginalName(),
            'aspect_ratio' => round($image->width() / $image->height(), 2)
        ];
    }

    /**
     * تحويل حجم الملف إلى صيغة مفهومة
     */
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * حذف الصورة
     */
    public function deleteImage($path)
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }

    /**
     * تغيير حجم الصورة فقط (بدون قص)
     */
    public function resizeImage(UploadedFile $file, $directory = 'images', $maxWidth = 800, $maxHeight = 600)
    {
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $image = $this->readOriented($file);

        // تصغير الصورة مع الاحتفاظ بالنسبة
        $w = $image->width();
        $h = $image->height();
        if ($w > $maxWidth || $h > $maxHeight) {
            // اختر البعد الذي يلامس الحد أولاً
            $ratioW = $w / $maxWidth;
            $ratioH = $h / $maxHeight;
            if ($ratioW >= $ratioH) {
                $image = $image->scale(width: $maxWidth);
            } else {
                $image = $image->scale(height: $maxHeight);
            }
        }

        $path = $directory . '/' . $filename;
        $imageData = $image->encodeByExtension('jpg', quality: 85);
        Storage::disk('public')->put($path, $imageData);

        return [
            'path' => $path,
            'url' => asset('storage/' . $path),
            'width' => $image->width(),
            'height' => $image->height()
        ];
    }

    /**
     * قص مربع 1:1 (من الوسط) وإرجاع WEBP
     */
    public function cropSquareWebp(UploadedFile $file, int $size = 1000, string $directory = 'images', int $quality = 80): array
    {
        $image = $this->readOriented($file);
        $image = $image->cover($size, $size);
        $bytes = $this->encodeWebp($image, $quality);
        $filename = Str::uuid()->toString() . '.webp';
        $path = rtrim($directory, '/') . '/' . $filename;
        Storage::disk('public')->put($path, $bytes);
        return ['path' => $path, 'url' => asset('storage/' . $path), 'width' => $size, 'height' => $size];
    }

    /**
     * قص بنسبة عرض:ارتفاع محددة (مثلاً 4:5 أو 16:9) من الوسط وإرجاع WEBP
     */
    public function cropAspectWebp(UploadedFile $file, int $targetW, int $targetH, string $directory = 'images', int $quality = 80): array
    {
        $image = $this->readOriented($file);
        $image = $image->cover($targetW, $targetH);
        $bytes = $this->encodeWebp($image, $quality);
        $filename = Str::uuid()->toString() . '.webp';
        $path = rtrim($directory, '/') . '/' . $filename;
        Storage::disk('public')->put($path, $bytes);
        return ['path' => $path, 'url' => asset('storage/' . $path), 'width' => $targetW, 'height' => $targetH];
    }

    /**
     * قص حر بمربع إحداثيات (x,y,width,height) ثم تحويل إلى WEBP
     */
    public function freeCropWebp(UploadedFile $file, int $x, int $y, int $width, int $height, string $directory = 'images', int $quality = 80): array
    {
        $image = $this->readOriented($file);
        $image = $image->crop($width, $height, $x, $y);
        $bytes = $this->encodeWebp($image, $quality);
        $filename = Str::uuid()->toString() . '.webp';
        $path = rtrim($directory, '/') . '/' . $filename;
        Storage::disk('public')->put($path, $bytes);
        return ['path' => $path, 'url' => asset('storage/' . $path), 'width' => $width, 'height' => $height];
    }
}
