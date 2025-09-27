<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بروزاي</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body class="bg-gray-50 text-indigo-950">
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-indigo-100">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between gap-6">
            <a href="{{ route('home') }}" class="text-2xl font-black text-indigo-900 tracking-tight">
                Brwaz
            </a>

            <nav class="flex flex-wrap items-center gap-6 text-sm font-medium text-indigo-800">
                <div class="relative group">
                    <a href="{{ route('art.index') }}"
                        class="flex items-center gap-2 rounded-full px-3 py-2 transition hover:bg-indigo-50 hover:text-indigo-900">
                        <span>الأعمال الفنية</span>
                        <svg class="w-3 h-3 transition group-hover:-rotate-90" viewBox="0 0 10 6" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </a>

                    <div
                        class="pointer-events-none absolute top-full right-0 mt-3 w-72 rounded-2xl bg-white p-4 shadow-xl ring-1 ring-indigo-100/70 opacity-0 translate-y-3 transition duration-200 ease-out group-hover:opacity-100 group-hover:translate-y-0 group-hover:pointer-events-auto">
                        <div class="flex flex-col gap-2 text-indigo-900">
                            <a href="{{ route('art.index') }}"
                                class="flex items-start gap-3 rounded-xl px-3 py-2 hover:bg-indigo-50">
                                <span class="mt-0.5 text-indigo-400">🎨</span>
                                <div>
                                    <p class="text-sm font-semibold">كل الأعمال</p>
                                    <p class="text-xs text-indigo-500">استعرض جميع اللوحات والمنحوتات المتاحة للبيع.</p>
                                </div>
                            </a>
                            <a href="{{ route('artists.index') }}"
                                class="flex items-start gap-3 rounded-xl px-3 py-2 hover:bg-indigo-50">
                                <span class="mt-0.5 text-indigo-400">👩‍🎨</span>
                                <div>
                                    <p class="text-sm font-semibold">الفنانون</p>
                                    <p class="text-xs text-indigo-500">تعرف على الفنانين المشاركين وتابع أعمالهم.</p>
                                </div>
                            </a>
                            <a href="{{ route('mazad.index') }}"
                                class="flex items-start gap-3 rounded-xl px-3 py-2 hover:bg-indigo-50">
                                <span class="mt-0.5 text-indigo-400">🪄</span>
                                <div>
                                    <p class="text-sm font-semibold">قسم المزاد</p>
                                    <p class="text-xs text-indigo-500">شارك في المزادات الحية للأعمال النادرة.</p>
                                </div>
                            </a>
                            <a href="{{ route('artbrwaz.index') }}"
                                class="flex items-start gap-3 rounded-xl px-3 py-2 hover:bg-indigo-50">
                                <span class="mt-0.5 text-indigo-400">🏛️</span>
                                <div>
                                    <p class="text-sm font-semibold">معرض Brwaz</p>
                                    <p class="text-xs text-indigo-500">جولة افتراضية في المعرض بتجربة ثلاثية الأبعاد.
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('workshops.index') }}"
                    class="rounded-full px-3 py-2 transition hover:bg-indigo-50 hover:text-indigo-900">
                    ورشات بروزاي
                </a>

                @auth
                    <a href="{{ route('cart.index') }}"
                        class="rounded-full px-3 py-2 transition hover:bg-indigo-50 hover:text-indigo-900">السلة</a>
                    <a href="{{ route('orders.index') }}"
                        class="rounded-full px-3 py-2 transition hover:bg-indigo-50 hover:text-indigo-900">طلباتي</a>
                @endauth
            </nav>

            <div class="flex items-center gap-3 text-sm">
                @guest
                    <a href="{{ route('login') }}"
                        class="hidden md:inline-flex items-center gap-2 rounded-full border border-indigo-200 px-4 py-2 text-indigo-800 transition hover:border-indigo-300 hover:text-indigo-900">
                        تسجيل الدخول
                    </a>
                @endguest
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                            تسجيل الخروج
                        </button>
                    </form>
                @else
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                        أنشئ حسابًا
                    </a>
                @endauth
            </div>
        </div>
    </header>
    @if(session('success'))
        <div class="max-w-6xl mx-auto px-4 mt-4">
            <div class="p-3 rounded bg-green-50 text-green-700">{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-6xl mx-auto px-4 mt-4">
            <div class="p-3 rounded bg-rose-50 text-rose-700">{{ session('error') }}</div>
        </div>
    @endif
    @yield('content')
    <footer class="mt-16 py-10 bg-indigo-950 text-white"></footer>
</body>

</html>