<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأكيد البريد الإلكتروني - برواز</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-purple-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4 max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <!-- الشعار أو الأيقونة -->
            <div class="mb-6">
                <div
                    class="mx-auto w-16 h-16 bg-gradient-to-r from-primary to-secondary rounded-full flex items-center justify-center">
                    <i class="fas fa-envelope text-white text-2xl"></i>
                </div>
            </div>

            <!-- العنوان الرئيسي -->
            <h1 class="text-2xl font-bold text-gray-900 mb-4">
                تأكيد البريد الإلكتروني
            </h1>

            <!-- الرسالة التوضيحية -->
            <div class="text-gray-600 mb-8 space-y-3">
                <p class="text-sm leading-relaxed">
                    تم إرسال رسالة تأكيد إلى بريدك الإلكتروني:
                </p>
                <div class="bg-gray-50 rounded-lg p-3 border-2 border-dashed border-gray-200">
                    <p class="font-semibold text-primary text-lg">
                        {{ Auth::user()->email }}
                    </p>
                </div>
                <p class="text-sm text-gray-500">
                    يرجى فتح البريد والضغط على رابط التأكيد لتفعيل حسابك
                </p>
            </div>

            <!-- رسائل النجاح أو الخطأ -->
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 ml-2"></i>
                        <p class="text-green-700 text-sm">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            <!-- أزرار العمل -->
            <div class="space-y-4">
                <!-- إعادة الإرسال -->
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-primary to-secondary text-white py-3 px-6 rounded-lg font-medium hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-paper-plane ml-2"></i>
                        إعادة إرسال رسالة التأكيد
                    </button>
                </form>

                <!-- الخروج -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full bg-gray-100 text-gray-700 py-3 px-6 rounded-lg font-medium hover:bg-gray-200 transition-colors duration-300">
                        <i class="fas fa-sign-out-alt ml-2"></i>
                        تسجيل الخروج
                    </button>
                </form>
            </div>

            <!-- معلومات إضافية -->
            <div class="mt-8 pt-6 border-t border-gray-100">
                <div class="text-sm text-gray-500 space-y-2">
                    <p class="flex items-center justify-center">
                        <i class="fas fa-clock text-gray-400 ml-2"></i>
                        قد تستغرق الرسالة حتى 5 دقائق للوصول
                    </p>
                    <p class="flex items-center justify-center">
                        <i class="fas fa-folder text-gray-400 ml-2"></i>
                        تحقق من مجلد الرسائل المرفوضة
                    </p>
                </div>
            </div>

            <!-- إرشادات -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-800 mb-2 flex items-center">
                    <i class="fas fa-info-circle text-blue-600 ml-2"></i>
                    تعليمات
                </h3>
                <ul class="text-sm text-blue-700 text-right space-y-1">
                    <li>• ابحث عن رسالة من برواز في بريدك الإلكتروني</li>
                    <li>• اضغط على رابط "تأكيد البريد الإلكتروني"</li>
                    <li>• سيتم توجيهك تلقائياً للوحة التحكم</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- تأثير visual -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div
            class="absolute -top-40 -right-32 w-80 h-80 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-full blur-3xl">
        </div>
        <div
            class="absolute -bottom-40 -left-32 w-80 h-80 bg-gradient-to-tr from-secondary/20 to-primary/20 rounded-full blur-3xl">
        </div>
    </div>
</body>

</html>