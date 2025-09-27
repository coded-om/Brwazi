<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>انتهت الجلسة - برواز</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Cairo', sans-serif
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg p-8 text-center relative overflow-hidden">
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-red-100 rounded-full opacity-40"></div>
        <div class="absolute -bottom-10 -right-10 w-52 h-52 bg-indigo-100 rounded-full opacity-40"></div>
        <div class="relative">
            <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-red-50 flex items-center justify-center shadow-inner">
                <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v4m0 4h.01M4.93 4.93l14.14 14.14M9.17 4.93h5.66a4 4 0 0 1 3.77 2.68l2.4 6.79a4 4 0 0 1-2.34 5.07l-6.2 2.48a4 4 0 0 1-2.94 0l-6.2-2.48a4 4 0 0 1-2.34-5.07l2.4-6.8A4 4 0 0 1 9.17 4.93Z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-3">انتهت صلاحية الجلسة</h1>
            <p class="text-gray-600 leading-relaxed mb-6 text-sm">انتهت مدة الجلسة أو حاولت إرسال النموذج بعد وقت
                طويل.<br>لأسباب أمنية تم إبطال الطلب.</p>
            <div class="space-y-3">
                <a href="{{ url()->previous() }}"
                    class="block w-full py-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition">رجوع
                    للصفحة السابقة</a>
                <a href="/login"
                    class="block w-full py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold transition">تسجيل
                    الدخول من جديد</a>
            </div>
            <p class="text-xs text-gray-400 mt-6">رمز الخطأ: 419</p>
        </div>
    </div>
</body>

</html>