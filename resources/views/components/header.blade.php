@php
    $isActive = function ($patterns) {
        foreach ((array) $patterns as $p) {
            if (request()->is($p)) return true;
        }
        return false;
    };
    $cartCount = is_array(session('cart')) ? array_sum(session('cart')) : 0;
@endphp
<header id="main-header" class="shadow-lg sticky top-0 z-50 transition-all duration-300 bg-[#141640]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Desktop Header (Large screens) -->
    <div class="desktop-header hidden lg:flex justify-between items-center h-16">
            <nav class="flex items-center space-x-6  text-white  gap-16">
                <a href="/" class="flex items-center mr-4 start-1.5" aria-label="الصفحة الرئيسية">
                    <img src="{{ asset('imgs/icons-no-colors/logo.svg') }}" alt="الشعار" class="h-10 w-auto" decoding="async" fetchpriority="high">
                </a>
                <div class="p-1 bg-white/10 rounded-xl  w">
                    <ul class="flex gap-2.5">
                        <li class="rounded-md px-2 py-1 transition-all duration-200 {{ $isActive('art') ? 'bg-[#9B4F9F]' : 'hover:bg-[#9B4F9F]' }}">
                            <a href="/art"
                                class="cursor-pointer flex gap-1 items-center justify-center leading-none {{ $isActive('art') ? 'text-white' : 'hover:text-white' }}"
                                {{ $isActive('art') ? 'aria-current="page"' : '' }}>
                                <img src="{{ $isActive(patterns: 'art') ? asset('imgs/icons-color/eye-category.svg') : asset('imgs/icons-no-colors/eye-category.svg') }}" alt="الاعمال الفنية"
                                    class="w-6 h-6 icon-white flex-shrink-0" loading="lazy" decoding="async">
                                الاعمال الفنية
                            </a>
                        </li>
                        <li class="rounded-md px-2 py-1 transition-all duration-200 {{ $isActive('literary*') ? 'bg-[#9B4F9F]' : 'hover:bg-[#9B4F9F]' }}">
                            <a href="/literary"
                                class="cursor-pointer flex gap-1 items-center justify-center leading-none font-normal {{ $isActive('literary*') ? 'text-white' : 'hover:text-white' }}"
                                {{ $isActive('literary*') ? 'aria-current="page"' : '' }}>
                                <img src="{{ $isActive(patterns: 'literary*') ? asset('imgs/icons-color/peper-category.svg') : asset('imgs/icons-no-colors/peper-category.svg') }}" alt="الاعمال الادبية"
                                    class="w-6 h-6 icon-white flex-shrink-0" loading="lazy" decoding="async">
                                الاعمال الادبية
                            </a>
                        </li>
                        <li class="rounded-md px-2 py-1 transition-all duration-200 {{ $isActive(['workshops', 'workshops*']) ? 'bg-[#9B4F9F]' : 'hover:bg-[#9B4F9F]' }}">
                            <a href="{{ route('workshops.index') }}"
                               class="cursor-pointer flex gap-1 items-center justify-center leading-none font-normal {{ $isActive('workshops*') ? 'text-white' : 'hover:text-white' }}"
                               {{ $isActive('workshops*') ? 'aria-current="page"' : '' }}>
                                <img src="{{ $isActive(patterns: 'workshops*') ? asset('imgs/icons-color/art-icon.svg') : asset('imgs/icons-no-colors/eye-category.svg') }}" alt="ورشات بروزاي"
                                     class="w-6 h-6 icon-white flex-shrink-0" loading="lazy" decoding="async">
                                ورشات بروزاي
                            </a>
                        </li>
                        <li class="rounded-md px-2 py-1 transition-all duration-200 {{ $isActive('art-brwaz*') ? 'bg-[#9B4F9F]' : 'hover:bg-[#9B4F9F]' }}">
                            <a href="{{ route('artbrwaz.index') }}" class="cursor-pointer flex gap-1 items-center justify-center leading-none {{ $isActive('art-brwaz*') ? 'text-white' : 'hover:text-white' }}"
                                {{ $isActive('art-brwaz*') ? 'aria-current="page"' : '' }}>
                                <img src="{{ $isActive(patterns: 'art-brwaz*') ? asset('imgs/icons-color/gallery-icon.svg') : asset('imgs/icons-no-colors/show-category.svg') }}" alt="معرض برواز"
                                    class="w-6 h-6 icon-white flex-shrink-0" loading="lazy" decoding="async">
                                معرض برواز
                            </a>
                        </li>
                        <li class="rounded-md px-2 py-1 transition-all duration-200 {{ $isActive('mazad') ? 'bg-[#9B4F9F]' : 'hover:bg-[#9B4F9F]' }}">
                            <a href="/mazad" class="cursor-pointer flex gap-1 items-center justify-center leading-none {{ $isActive('mazad') ? 'text-white' : 'hover:text-white' }}"
                                {{ $isActive('mazad') ? 'aria-current="page"' : '' }}>
                                <img src="{{ $isActive(patterns: 'mazad*') ? asset('imgs/icons-color/mazad.svg') : asset('imgs/icons-no-colors/mazad-category.svg') }}" alt="المزاد الفني"
                                    class="w-6 h-6 icon-white flex-shrink-0" loading="lazy" decoding="async">
                                المزاد الفني
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- Center - Search bar -->
            <div class="flex-1 max-w-md mx-8">
                <div class="relative">
                    <input type="text" placeholder="البحث..."
                        class="w-full px-4 py-2 pr-10 rounded-xl bg-white/20 text-white placeholder-white/80 border border-white/30 focus:outline-none focus:ring-2 focus:ring-white/50">
                    <button class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white/80">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="flex items-center space-x-4 gap-1 ">
                @auth
                    <a href="{{ route('cart.index') }}" aria-label="سلة المشتريات"
                       class="relative text-white p-2 rounded-md hover:bg-white/20 transition-colors">
                        <img src="{{ asset('imgs/icons-color/chart.svg') }}" alt="السلة" class="w-6 h-6 icon-white" loading="lazy" decoding="async">
                        <span class="absolute -top-1 -right-1 min-w-5 h-5 px-1 text-[11px] leading-5 text-white bg-rose-600 rounded-full text-center {{ $cartCount > 0 ? '' : 'hidden' }}">{{ $cartCount }}</span>
                    </a>

                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open=false">
                        <button @click.stop="open = !open" @keydown.escape.window="open=false" class="flex items-center gap-2 text-white p-2 rounded-md hover:bg-white/20 transition-colors">
                            <div class="h-8 w-8 rounded-full overflow-hidden bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center">
                                @if(auth()->user()->ProfileImage)
                                    <img src="{{ asset('storage/' . auth()->user()->ProfileImage) }}" alt="الصورة الشخصية" class="h-full w-full object-cover">
                                @else
                                    <i class="fas fa-user text-white text-sm"></i>
                                @endif
                            </div>
                            <span class="text-sm">{{ auth()->user()->fname }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>

                        <!-- Dropdown Menu -->
                    <div x-cloak x-show="open"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95">
                            <a href="{{ route('user.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-tachometer-alt"></i>
                                لوحة التحكم
                            </a>
                            <a href="{{ route('user.profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user-edit"></i>
                                تعديل الملف الشخصي
                            </a>
                            <div class="border-t border-gray-100"></div>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt"></i>
                                    تسجيل الخروج
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    @if (!request()->is('login') && !request()->is('forgot-password'))
                        <a href="/login"
                            class="text-white px-4 py-2 rounded-md hover:bg-[#9B4F9F] transition-colors inline-block text-center">
                            تسجيل دخول
                        </a>
                    @endif
                    @if (!request()->is('register') && !request()->is('forgot-password'))
                        <a href="/register"
                            class="text-[#4D5B93] px-4 py-2 rounded-md bg-white hover:bg-white/70 transition-colors inline-block text-center">
                            تسجيل
                        </a>
                    @endif
                @endauth
            </div>
        </div>
        <!-- Tablet Header (Medium screens) -->
    <div class="tablet-header hidden md:flex lg:hidden justify-between items-center h-16">
            <!-- Left side - Logo and compact nav -->
            <div class="flex items-center gap-4">
                <div class="flex items-center">
                    <a href="/" aria-label="الصفحة الرئيسية">
                        <img src="{{ asset('logo.svg') }}" alt="الشعار" class="h-8 w-auto" decoding="async">
                    </a>
                </div>
                <div class="p-1 bg-white/10 rounded-lg">
                    <ul class="flex gap-1">
                        <li class="rounded-md px-1.5 py-1 transition-all duration-200 {{ $isActive('art') ? 'bg-[#9B4F9F]' : 'hover:bg-[#9B4F9F]' }}">
                            <a href="/art"
                                class="cursor-pointer flex items-center justify-center text-white {{ $isActive('art') ? '' : '' }}"
                                {{ $isActive('art') ? 'aria-current="page"' : '' }}>
                                <img src="{{ $isActive(patterns: 'art') ? asset('imgs/icons-color/eye-category.svg') : asset('imgs/icons-no-colors/eye-category.svg') }}" alt="الاعمال الفنية"
                                    class="w-5 h-5 icon-white" loading="lazy" decoding="async">
                            </a>
                        </li>
                        <li class="rounded-md px-1.5 py-1 transition-all duration-200 {{ $isActive('literary*') ? 'bg-[#9B4F9F]' : 'hover:bg-[#9B4F9F]' }}">
                            <a href="/literary"
                                class="cursor-pointer flex items-center justify-center text-white"
                                {{ $isActive('literary*') ? 'aria-current="page"' : '' }}>
                                <img src="{{ $isActive(patterns: 'literary*') ? asset('imgs/icons-color/peper-category.svg') : asset('imgs/icons-no-colors/peper-category.svg') }}" alt="الاعمال الادبية"
                                    class="w-5 h-5 icon-white" loading="lazy" decoding="async">
                            </a>
                        </li>
                        <li class="rounded-md px-1.5 py-1 transition-all duration-200 {{ $isActive('art-brwaz*') ? 'bg-[#9B4F9F]' : 'hover:bg-[#9B4F9F]' }}">
                            <a href="{{ route('artbrwaz.index') }}" class="cursor-pointer flex items-center justify-center text-white"
                                {{ $isActive('art-brwaz*') ? 'aria-current="page"' : '' }}>
                                <img src="{{ $isActive(patterns: 'art-brwaz*') ? asset('imgs/icons-color/gallery-icon.svg') : asset('imgs/icons-no-colors/show-category.svg') }}" alt="معرض برواز"
                                    class="w-5 h-5 icon-white" loading="lazy" decoding="async">
                            </a>
                        </li>
                        <li class="rounded-md px-1.5 py-1 transition-all duration-200 {{ $isActive(['workshops', 'workshops*']) ? 'bg-[#9B4F9F]' : 'hover:bg-[#9B4F9F]' }}">
                            <a href="{{ route('workshops.index') }}"
                               class="cursor-pointer flex items-center justify-center text-white"
                               {{ $isActive('workshops*') ? 'aria-current="page"' : '' }}>
                                <img src="{{ $isActive(patterns: 'workshops*') ? asset('imgs/icons-color/art-icon.svg') : asset('imgs/icons-no-colors/eye-category.svg') }}" alt="ورشات بروزاي"
                                     class="w-5 h-5 icon-white" loading="lazy" decoding="async">
                            </a>
                        </li>
                        <li class="rounded-md px-1.5 py-1 transition-all duration-200 {{ $isActive('mazad') ? 'bg-[#9B4F9F]' : 'hover:bg-[#9B4F9F]' }}">
                            <a href="/mazad" class="cursor-pointer flex items-center justify-center text-white"
                                {{ $isActive('mazad') ? 'aria-current="page"' : '' }}>
                                <img src="{{ $isActive(patterns: 'mazad*') ? asset('imgs/icons-color/mazad.svg') : asset('imgs/icons-no-colors/mazad-category.svg') }}" alt="المزاد الفني"
                                    class="w-5 h-5 icon-white" loading="lazy" decoding="async">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Center - Search bar -->
            <div class="flex-1 max-w-sm mx-4">
                <div class="relative">
                    <input type="text" placeholder="البحث..."
                        class="w-full px-4 py-2 pr-10 rounded-lg bg-white/20 text-white placeholder-white/80 border border-white/30 focus:outline-none focus:ring-2 focus:ring-white/50">
                    <button class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white/80">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Right side - Actions -->
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('cart.index') }}" aria-label="سلة المشتريات"
                       class="relative text-white p-2 rounded-md hover:bg-white/20 transition-colors">
                        <img src="{{ asset('imgs/icons-color/chart.svg') }}" alt="السلة" class="w-5 h-5 icon-white" loading="lazy" decoding="async">
                        <span class="absolute -top-1 -right-1 min-w-4 h-4 px-1 text-[10px] leading-4 text-white bg-rose-600 rounded-full text-center {{ $cartCount > 0 ? '' : 'hidden' }}">{{ $cartCount }}</span>
                    </a>

                    <!-- User Profile Dropdown for Tablet -->
                    <div class="relative" x-data="{ open: false }" @click.outside="open=false">
                        <button @click.stop="open = !open" @keydown.escape.window="open=false" class="flex items-center gap-1 text-white p-2 rounded-md hover:bg-white/20 transition-colors">
                            <div class="h-6 w-6 rounded-full overflow-hidden bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center">
                                @if(auth()->user()->ProfileImage)
                                    <img src="{{ asset('storage/' . auth()->user()->ProfileImage) }}" alt="الصورة الشخصية" class="h-full w-full object-cover">
                                @else
                                    <i class="fas fa-user text-white text-xs"></i>
                                @endif
                            </div>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>

                        <!-- Dropdown Menu -->
                    <div x-cloak x-show="open"
                             class="absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg py-1 z-50"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95">
                            <a href="{{ route('user.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-tachometer-alt"></i>
                                الداشبورد
                            </a>
                            <a href="{{ route('user.profile') }}" class="flex items-center gap-2 px-3 py-2 text-xs text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user-edit"></i>
                                الملف الشخصي
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-2 text-xs text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt"></i>
                                    تسجيل الخروج
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    @if (!request()->is('login') && !request()->is('forgot-password'))
                        <a href="/login"
                            class="text-white px-3 py-1.5 rounded-md hover:bg-[#9B4F9F] transition-colors text-sm">
                            دخول
                        </a>
                    @endif
                    @if (!request()->is('register') && !request()->is('forgot-password'))
                        <a href="/register"
                            class="text-[#4D5B93] px-3 py-1.5 rounded-md bg-white hover:bg-white/90 transition-colors text-sm">
                            تسجيل
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        <!-- Mobile Header (Small screens) -->
    <div class="mobile-header md:hidden flex justify-between items-center h-16">
            <!-- Right side - Menu button -->
            <button id="mobile-menu-btn" class="text-white p-2 rounded-md hover:bg-white/20 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Center - Logo -->
            <div class="flex items-center">
                <a href="/">
                    <img src="{{ asset('logo.svg') }}" alt="وزارة الصحة والسكان" class="h-8 w-auto" decoding="async">
                </a>
            </div>

            <!-- Left side - Search and Profile -->
            <div class="flex items-center gap-2">
                <button id="mobile-search-btn" class="text-white p-2 rounded-md hover:bg-white/20 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
                @auth
                    <a href="{{ route('cart.index') }}" aria-label="سلة المشتريات"
                       class="relative text-white p-1.5 rounded-md hover:bg-white/20 transition-colors">
                        <img src="{{ asset('imgs/icons-color/chart.svg') }}" alt="السلة" class="w-5 h-5 icon-white">
                        <span class="absolute -top-1 -right-1 min-w-4 h-4 px-1 text-[10px] leading-4 text-white bg-rose-600 rounded-full text-center {{ $cartCount > 0 ? '' : 'hidden' }}">{{ $cartCount }}</span>
                    </a>

                    <!-- Mobile User Profile -->
                    <div class="h-7 w-7 rounded-full overflow-hidden bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center">
                        @if(auth()->user()->ProfileImage)
                            <img src="{{ asset('storage/' . auth()->user()->ProfileImage) }}" alt="الصورة الشخصية" class="h-full w-full object-cover">
                        @else
                            <i class="fas fa-user text-white text-xs"></i>
                        @endif
                    </div>
                @endauth
            </div>
        </div>

        <!-- Mobile Search Bar (Hidden by default) -->
        <div id="mobile-search" class="md:hidden hidden px-4 pb-4">
            <div class="relative">
                <input type="text" placeholder="البحث..."
                    class="w-full px-4 py-2 pr-10 rounded-xl bg-white/20 text-white placeholder-white/80 border border-white/30 focus:outline-none focus:ring-2 focus:ring-white/50">
                <button class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white/80">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu (Overlay) -->
    <div id="mobile-menu" class="mobile-menu md:hidden fixed inset-0 bg-[#141640] z-50">
        <div class="flex flex-col h-full">
            <!-- Mobile Menu Header -->
            <div class="flex justify-between items-center h-16 px-4 border-b border-white/20">
                <div class="flex items-center">
                    <a href="/">
                        <img src="{{ asset('logo.svg') }}" alt="وزارة الصحة والسكان" class="h-8 w-auto">
                    </a>
                </div>
                <button id="mobile-menu-close" class="text-white p-2 rounded-md hover:bg-white/20 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu Content -->
            <div class="flex-1 overflow-y-auto p-4 min-h-0">
                <!-- Categories -->
                <div class="space-y-2 mb-6">
                    <a href="/art"
                        class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors text-white {{ $isActive('art') ? 'bg-white/10' : '' }}"
                        {{ $isActive('art') ? 'aria-current="page"' : '' }}>
                        <img src="{{ $isActive(patterns: 'art') ? asset('imgs/icons-color/eye-category.svg') : asset('imgs/icons-no-colors/eye-category.svg') }}" alt="الاعمال الفنية"
                            class="w-6 h-6 icon-white" loading="lazy" decoding="async">
                        <span>الاعمال الفنية</span>
                    </a>
                    <a href="{{ route('workshops.index') }}"
                        class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors text-white {{ $isActive(['workshops', 'workshops*']) ? 'bg-white/10' : '' }}"
                        {{ $isActive(['workshops', 'workshops*']) ? 'aria-current="page"' : '' }}>
                        <img src="{{ $isActive(patterns: 'workshops*') ? asset('imgs/icons-color/art-icon.svg') : asset('imgs/icons-no-colors/eye-category.svg') }}" alt="ورشات بروزاي"
                            class="w-6 h-6 icon-white" loading="lazy" decoding="async">
                        <span>ورشات بروزاي</span>
                    </a>
                    <a href="/literary"
                        class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors text-white {{ $isActive('literary*') ? 'bg-white/10' : '' }}"
                        {{ $isActive('literary*') ? 'aria-current="page"' : '' }}>
                        <img src="{{ $isActive(patterns: 'literary*') ? asset('imgs/icons-color/peper-category.svg') : asset('imgs/icons-no-colors/peper-category.svg') }}" alt="الاعمال الادبية"
                            class="w-6 h-6 icon-white" loading="lazy" decoding="async">
                        <span>الاعمال الادبية</span>
                    </a>
                    <a href="{{ route('artbrwaz.index') }}"
                        class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors text-white {{ $isActive('art-brwaz*') ? 'bg-white/10' : '' }}"
                        {{ $isActive('art-brwaz*') ? 'aria-current="page"' : '' }}>
                        <img src="{{ $isActive(patterns: 'art-brwaz*') ? asset('imgs/icons-color/gallery-icon.svg') : asset('imgs/icons-no-colors/show-category.svg') }}" alt="معرض برواز"
                            class="w-6 h-6 icon-white" loading="lazy" decoding="async">
                        <span>معرض برواز</span>
                    </a>
                    <a href="/mazad"
                        class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors text-white {{ $isActive('mazad') ? 'bg-white/10' : '' }}"
                        {{ $isActive('mazad') ? 'aria-current="page"' : '' }}>
                        <img src="{{ $isActive(patterns: 'mazad*') ? asset('imgs/icons-color/mazad.svg') : asset('imgs/icons-no-colors/mazad-category.svg') }}" alt="المزاد الفني"
                            class="w-6 h-6 icon-white" loading="lazy" decoding="async">
                        <span>المزاد الفني</span>
                    </a>
                </div>

                <div class="border-t border-white/20 pt-4 space-y-2">
                    <a href="#"
                        class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors text-white">
                        <i class="fas fa-mobile-alt text-lg text-white"></i>
                        <span>التطبيق</span>
                    </a>
                    <a href="#"
                        class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors text-white">
                        <i class="fas fa-store text-lg text-white"></i>
                        <span>المتجر</span>
                    </a>
                    <a href="#"
                        class="flex items-center gap-3 p-3 rounded-lg hover:bg-white/10 transition-colors text-white">
                        <i class="fas fa-newspaper text-lg text-white"></i>
                        <span>الأخبار</span>
                    </a>
                </div>
            </div>

            <!-- Mobile Menu Footer -->
            <div class="border-t border-white/20 p-4 space-y-3">
                @auth
                    <!-- User Info in Mobile Menu -->
                    <div class="flex items-center gap-3 p-3 rounded-lg bg-white/10 text-white">
                        <div class="h-10 w-10 rounded-full overflow-hidden bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center">
                            @if(auth()->user()->ProfileImage)
                                <img src="{{ asset('storage/' . auth()->user()->ProfileImage) }}" alt="الصورة الشخصية" class="h-full w-full object-cover">
                            @else
                                <i class="fas fa-user text-white text-lg"></i>
                            @endif
                        </div>
                        <div>
                            <p class="font-medium">{{ auth()->user()->fname }} {{ auth()->user()->lname }}</p>
                            <p class="text-sm text-white/70">{{ auth()->user()->email }}</p>
                        </div>
                    </div>

                    <a href="{{ route('user.dashboard') }}"
                        class="w-full flex items-center justify-center gap-2 text-white px-4 py-2 rounded-md hover:bg-white/20 transition-colors">
                        <i class="fas fa-tachometer-alt"></i>
                        لوحة التحكم
                    </a>
                    <a href="{{ route('user.profile') }}"
                        class="w-full flex items-center justify-center gap-2 text-white px-4 py-2 rounded-md hover:bg-white/20 transition-colors">
                        <i class="fas fa-user-edit"></i>
                        تعديل الملف الشخصي
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="block">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 text-red-300 px-4 py-2 rounded-md hover:bg-red-500/20 transition-colors">
                            <i class="fas fa-sign-out-alt"></i>
                            تسجيل الخروج
                        </button>
                    </form>
                @else
                    @if (!request()->is('login') && !request()->is('forgot-password'))
                        <a href="/login"
                            class="w-full text-white px-4 py-2 rounded-md hover:bg-[#9B4F9F] transition-colors block text-center">
                            تسجيل دخول
                        </a>
                    @endif
                    @if (!request()->is('register') && !request()->is('forgot-password'))
                        <a href="/register"
                            class="w-full text-[#4D5B93] px-4 py-2 rounded-md bg-white hover:bg-white/90 transition-colors block text-center">
                            تسجيل
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</header>
