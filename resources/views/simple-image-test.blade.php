<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تجربة رفع الصور</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-6 max-w-2xl">
        <h1 class="text-2xl font-bold text-center mb-8">تجربة رفع الصور</h1>

        <!-- تجربة بسيطة -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">رفع صورة واحدة</h2>

            <form action="{{ route('images.upload.profile') }}" method="POST" enctype="multipart/form-data"
                id="simpleForm">
                @csrf
                <div class="mb-4">
                    <input type="file" name="image" accept="image/*" required class="w-full border p-2 rounded">
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    رفع الصورة
                </button>
            </form>

            <div id="result" class="mt-4 p-4 hidden"></div>
        </div>
    </div>

    <script>
        document.getElementById('simpleForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const resultDiv = document.getElementById('result');

            resultDiv.innerHTML = '<div class="text-blue-600">جاري الرفع...</div>';
            resultDiv.classList.remove('hidden');

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resultDiv.innerHTML = `
                        <div class="text-green-600 font-semibold">${data.message}</div>
                        <div class="mt-2">
                            <img src="${data.data.uploaded_image.url}" alt="صورة مرفوعة" class="max-w-xs rounded">
                        </div>
                        <div class="text-sm text-gray-600 mt-2">
                            المسار: ${data.data.uploaded_image.path}<br>
                            الحجم الأصلي: ${data.data.original_info.width} x ${data.data.original_info.height}<br>
                            الحجم بعد القص: ${data.data.uploaded_image.cropped_size.width} x ${data.data.uploaded_image.cropped_size.height}
                        </div>
                    `;
                    } else {
                        resultDiv.innerHTML = `<div class="text-red-600">خطأ: ${data.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    resultDiv.innerHTML = `<div class="text-red-600">حدث خطأ في الشبكة</div>`;
                });
        });
    </script>
</body>

</html>