<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بروزاي</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body class="bg-gray-50 text-indigo-950">
    <header class="bg-indigo-950 text-white">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="font-bold">Brwaz</a>
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('art.index') }}">الأعمال الفنية</a>
                @auth
                    <a href="{{ route('cart.index') }}">السلة</a>
                    <a href="{{ route('orders.index') }}">طلباتي</a>
                @endauth
            </nav>
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