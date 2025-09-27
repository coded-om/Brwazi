<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ImageService;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * رفع صورة البروفايل
     */
    public function uploadProfileImage(Request $request)
    {
        if ($request->user() && method_exists($request->user(), 'isBanned') && $request->user()->isBanned()) {
            return response()->json([
                'success' => false,
                'message' => 'تم تعطيل الرفع لحسابك. يرجى التواصل مع قسم الدعم.'
            ], 403);
        }
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048' // 2MB max
            ]);

            $file = $request->file('image');

            // الحصول على معلومات الصورة قبل الرفع
            $imageInfo = $this->imageService->getImageInfo($file);

            // رفع وقص الصورة
            $result = $this->imageService->uploadAndCrop($file, 'profiles', 300, 300);

            return response()->json([
                'success' => true,
                'message' => 'تم رفع الصورة بنجاح',
                'data' => [
                    'uploaded_image' => $result,
                    'original_info' => $imageInfo
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('خطأ في رفع صورة البروفايل: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفع الصورة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * رفع صور المنتجات مع أحجام مختلفة
     */
    public function uploadProductImages(Request $request)
    {
        if ($request->user() && method_exists($request->user(), 'isBanned') && $request->user()->isBanned()) {
            return response()->json([
                'success' => false,
                'message' => 'تم تعطيل الرفع لحسابك. يرجى التواصل مع قسم الدعم.'
            ], 403);
        }
        try {
            $request->validate([
                'images' => 'required|array|min:1',
                'images.*' => 'required|image|mimes:jpeg,png,jpg|max:5120' // 5MB max
            ]);

            $uploadedImages = [];

            foreach ($request->file('images') as $file) {
                // إنشاء أحجام مختلفة
                $thumbnails = $this->imageService->createThumbnails($file, 'products');

                // معلومات الصورة الأصلية
                $originalInfo = $this->imageService->getImageInfo($file);

                $uploadedImages[] = [
                    'thumbnails' => $thumbnails,
                    'original_info' => $originalInfo
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'تم رفع الصور بنجاح',
                'uploaded_images' => $uploadedImages
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('خطأ في رفع صور المنتجات: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفع الصور: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * معاينة معلومات الصورة قبل الرفع
     */
    public function previewImageInfo(Request $request)
    {
        if ($request->user() && method_exists($request->user(), 'isBanned') && $request->user()->isBanned()) {
            return response()->json([
                'success' => false,
                'message' => 'تم تعطيل الرفع لحسابك. يرجى التواصل مع قسم الدعم.'
            ], 403);
        }
        try {
            $request->validate([
                'image' => 'required|image|max:10240' // 10MB max for preview
            ]);

            $file = $request->file('image');
            $info = $this->imageService->getImageInfo($file);

            return response()->json([
                'success' => true,
                'image_info' => $info,
                'recommendations' => $this->getImageRecommendations($info)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('خطأ في معاينة الصورة: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء معاينة الصورة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * توصيات بناء على حجم الصورة
     */
    private function getImageRecommendations($info)
    {
        $recommendations = [];

        if ($info['width'] < 300 || $info['height'] < 300) {
            $recommendations[] = 'الصورة صغيرة جداً، ننصح برفع صورة أكبر من 300x300';
        }

        if ($info['size'] > 2 * 1024 * 1024) { // 2MB
            $recommendations[] = 'حجم الصورة كبير، سيتم ضغطها تلقائياً';
        }

        if ($info['aspect_ratio'] > 2 || $info['aspect_ratio'] < 0.5) {
            $recommendations[] = 'نسبة العرض للارتفاع غير متناسقة، قد يتم قصها';
        }

        if (empty($recommendations)) {
            $recommendations[] = 'الصورة مناسبة للرفع';
        }

        return $recommendations;
    }
}
