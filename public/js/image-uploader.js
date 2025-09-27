/**
 * مكتبة JavaScript للتعامل مع رفع وقص الصور
 */

class ImageUploader {
    constructor(options = {}) {
        this.options = {
            maxFileSize: 5 * 1024 * 1024, // 5MB
            allowedTypes: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'],
            cropWidth: 300,
            cropHeight: 300,
            quality: 0.8,
            ...options
        };

        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }

    /**
     * تحقق من صحة الملف
     */
    validateFile(file) {
        const errors = [];

        if (!file) {
            errors.push('لم يتم اختيار ملف');
            return errors;
        }

        if (file.size > this.options.maxFileSize) {
            errors.push(`حجم الملف كبير جداً. الحد الأقصى ${(this.options.maxFileSize / 1024 / 1024).toFixed(1)}MB`);
        }

        if (!this.options.allowedTypes.includes(file.type)) {
            errors.push('نوع الملف غير مدعوم. الأنواع المدعومة: JPG, PNG, GIF');
        }

        return errors;
    }

    /**
     * معاينة الصورة قبل الرفع
     */
    previewImage(file, imgElement, infoElement = null) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            reader.onload = function(e) {
                imgElement.src = e.target.result;
                imgElement.style.display = 'block';

                if (infoElement) {
                    infoElement.innerHTML = `
                        <div class="text-sm text-gray-600 mt-2">
                            <p><strong>اسم الملف:</strong> ${file.name}</p>
                            <p><strong>الحجم:</strong> ${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                            <p><strong>النوع:</strong> ${file.type}</p>
                        </div>
                    `;
                }

                resolve(e.target.result);
            };

            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    /**
     * قص الصورة باستخدام Canvas
     */
    cropImage(file, width = this.options.cropWidth, height = this.options.cropHeight) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();

            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    canvas.width = width;
                    canvas.height = height;

                    // حساب الأبعاد للقص من المنتصف
                    const sourceX = Math.max(0, (img.width - width) / 2);
                    const sourceY = Math.max(0, (img.height - height) / 2);
                    const sourceWidth = Math.min(img.width, width);
                    const sourceHeight = Math.min(img.height, height);

                    // رسم الصورة مع القص
                    ctx.drawImage(img, sourceX, sourceY, sourceWidth, sourceHeight, 0, 0, width, height);

                    // تحويل إلى blob
                    canvas.toBlob(resolve, 'image/jpeg', this.options.quality);
                };
                img.src = e.target.result;
            };

            reader.onerror = reject;
            reader.readAsDataURL(file);
        }.bind(this));
    }

    /**
     * رفع الصورة إلى الخادم
     */
    async upload(file, endpoint, additionalData = {}) {
        const formData = new FormData();
        formData.append('image', file);

        // إضافة بيانات إضافية
        Object.keys(additionalData).forEach(key => {
            formData.append(key, additionalData[key]);
        });

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'فشل في رفع الصورة');
            }

            return result;
        } catch (error) {
            throw new Error(`خطأ في الشبكة: ${error.message}`);
        }
    }

    /**
     * رفع صورة مع القص التلقائي
     */
    async uploadWithCrop(file, endpoint, additionalData = {}) {
        // تحقق من صحة الملف
        const errors = this.validateFile(file);
        if (errors.length > 0) {
            throw new Error(errors.join(', '));
        }

        // قص الصورة
        const croppedFile = await this.cropImage(file);

        // رفع الصورة المقصوصة
        return this.upload(croppedFile, endpoint, additionalData);
    }

    /**
     * رفع صور متعددة
     */
    async uploadMultiple(files, endpoint, additionalData = {}) {
        const formData = new FormData();

        // إضافة جميع الملفات
        Array.from(files).forEach((file, index) => {
            formData.append(`images[${index}]`, file);
        });

        // إضافة بيانات إضافية
        Object.keys(additionalData).forEach(key => {
            formData.append(key, additionalData[key]);
        });

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'فشل في رفع الصور');
            }

            return result;
        } catch (error) {
            throw new Error(`خطأ في الشبكة: ${error.message}`);
        }
    }

    /**
     * عرض شريط التحميل
     */
    showProgressBar(element, progress) {
        element.innerHTML = `
            <div class="bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: ${progress}%"></div>
            </div>
            <div class="text-sm text-gray-600 mt-1">${Math.round(progress)}%</div>
        `;
    }

    /**
     * إخفاء شريط التحميل
     */
    hideProgressBar(element) {
        element.innerHTML = '';
    }
}

// مثال على الاستخدام
document.addEventListener('DOMContentLoaded', function() {
    const imageUploader = new ImageUploader({
        maxFileSize: 2 * 1024 * 1024, // 2MB
        cropWidth: 300,
        cropHeight: 300
    });

    // مثال لرفع صورة البروفايل
    const profileInput = document.getElementById('profileImage');
    const profilePreview = document.getElementById('profilePreview');
    const profileInfo = document.getElementById('profileInfo');

    if (profileInput) {
        profileInput.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (file) {
                try {
                    await imageUploader.previewImage(file, profilePreview, profileInfo);
                } catch (error) {
                    console.error('خطأ في معاينة الصورة:', error);
                }
            }
        });
    }
});

// تصدير الكلاس للاستخدام العام
window.ImageUploader = ImageUploader;
