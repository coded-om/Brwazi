<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رفع وقص الصور</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-6">
        <div class="max-w-2xl mx-auto">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">رفع وقص الصور</h1>

            <!-- رفع صورة البروفايل -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">رفع صورة البروفايل</h2>

                <form id="profileImageForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="profileImage" class="block text-sm font-medium text-gray-700 mb-2">
                            اختر صورة البروفايل
                        </label>
                        <input type="file" id="profileImage" name="image" accept="image/*"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>

                    <!-- معاينة الصورة -->
                    <div id="profilePreview" class="hidden mb-4">
                        <img id="profilePreviewImg" src="" alt="معاينة" class="max-w-xs h-auto rounded-lg border">
                        <div id="profileImageInfo" class="mt-2 text-sm text-gray-600"></div>
                    </div>

                    <button type="submit" id="uploadProfileBtn"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:bg-gray-400">
                        رفع الصورة
                    </button>
                </form>
            </div>

            <!-- رفع صور المنتجات -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">رفع صور المنتجات (متعددة)</h2>

                <form id="productImagesForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="productImages" class="block text-sm font-medium text-gray-700 mb-2">
                            اختر صور المنتجات
                        </label>
                        <input type="file" id="productImages" name="images[]" accept="image/*" multiple
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                    </div>

                    <!-- معاينة الصور -->
                    <div id="productPreviews" class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4"></div>

                    <button type="submit" id="uploadProductBtn"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled:bg-gray-400">
                        رفع الصور
                    </button>
                </form>
            </div>

            <!-- نتائج الرفع -->
            <div id="uploadResults" class="bg-white rounded-lg shadow-md p-6 hidden">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">نتائج الرفع</h2>
                <div id="resultsContent"></div>
            </div>
        </div>
    </div>

    <script>
        // إعداد CSRF Token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // معاينة صورة البروفايل
        document.getElementById('profileImage').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = document.getElementById('profilePreview');
                    const img = document.getElementById('profilePreviewImg');
                    const info = document.getElementById('profileImageInfo');

                    img.src = e.target.result;
                    preview.classList.remove('hidden');

                    // عرض معلومات الصورة
                    info.innerHTML = `
                        <strong>اسم الملف:</strong> ${file.name}<br>
                        <strong>الحجم:</strong> ${(file.size / 1024 / 1024).toFixed(2)} MB<br>
                        <strong>النوع:</strong> ${file.type}
                    `;
                };
                reader.readAsDataURL(file);
            }
        });

        // معاينة صور المنتجات
        document.getElementById('productImages').addEventListener('change', function (e) {
            const files = Array.from(e.target.files);
            const previewContainer = document.getElementById('productPreviews');
            previewContainer.innerHTML = '';

            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="معاينة ${index + 1}" class="w-full h-32 object-cover rounded-lg border">
                        <div class="text-xs text-gray-600 mt-1">
                            ${file.name}<br>
                            ${(file.size / 1024 / 1024).toFixed(2)} MB
                        </div>
                    `;
                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });

        // رفع صورة البروفايل
        document.getElementById('profileImageForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const button = document.getElementById('uploadProfileBtn');

            button.disabled = true;
            button.textContent = 'جاري الرفع...';

            fetch('/images/upload-profile', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
                .then(response => response.json())
                .then(data => {
                    showResults(data);
                    if (data.success) {
                        this.reset();
                        document.getElementById('profilePreview').classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('خطأ:', error);
                    showResults({ success: false, message: 'حدث خطأ أثناء رفع الصورة' });
                })
                .finally(() => {
                    button.disabled = false;
                    button.textContent = 'رفع الصورة';
                });
        });

        // رفع صور المنتجات
        document.getElementById('productImagesForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const button = document.getElementById('uploadProductBtn');

            button.disabled = true;
            button.textContent = 'جاري الرفع...';

            fetch('/images/upload-products', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
                .then(response => response.json())
                .then(data => {
                    showResults(data);
                    if (data.success) {
                        this.reset();
                        document.getElementById('productPreviews').innerHTML = '';
                    }
                })
                .catch(error => {
                    console.error('خطأ:', error);
                    showResults({ success: false, message: 'حدث خطأ أثناء رفع الصور' });
                })
                .finally(() => {
                    button.disabled = false;
                    button.textContent = 'رفع الصور';
                });
        });

        // عرض النتائج
        function showResults(data) {
            const resultsDiv = document.getElementById('uploadResults');
            const contentDiv = document.getElementById('resultsContent');

            if (data.success) {
                contentDiv.innerHTML = `
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        ${data.message}
                    </div>
                    <pre class="bg-gray-100 p-4 rounded text-sm overflow-auto">${JSON.stringify(data, null, 2)}</pre>
                `;
            } else {
                contentDiv.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        ${data.message || 'حدث خطأ'}
                    </div>
                `;
            }

            resultsDiv.classList.remove('hidden');
        }
    </script>
</body>

</html>