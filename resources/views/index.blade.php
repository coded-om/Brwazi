<x-layout>
    <!-- Hero Section -->
    <section class="hero-sec py-3 sm:py-5 mt-3 sm:mt-6" data-aos="fade-up">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-10 xl:px-16">
            <div
                class="block relative hero-sec-img w-full h-[70vh] sm:h-[60vh] md:h-[65vh] lg:h-[45vh] rounded-lg shadow-lg">
                @if(!empty($settings?->hero_bg_image))
                    <img src="{{ asset_url($settings->hero_bg_image) }}" alt="خلفية" loading="eager" decoding="async"
                        class="absolute inset-0 w-full h-full object-cover rounded-lg" aria-hidden="true">
                @endif
                <div class="absolute inset-0 bg-black bg-opacity-40 rounded-lg ">
                    <img src="{{ asset_url($settings?->hero_logo, 'imgs/icons-color/logo-color-word.svg') }}" alt=""
                        loading="eager" decoding="async" class=" w-44 h-auto  p-5">
                    <p class="   p-5 text-white  w-1/2  absolute bottom-0 right-0 ">
                        {{ $settings?->hero_text ?? 'مرحبًا بك في برواز، حيث يلتقي الإبداع بالفُرَص.' }}
                    </p>
                    <img src="{{ asset('imgs/icons-color/flwor.svg') }}" alt="" loading="lazy" decoding="async"
                        class="   bg-white rounded-t-full  me-20 w-28 p-5 text-white   absolute bottom-0 left-0 inverted-bottom-corners inner-shadow  ">
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section py-6 sm:py-8 md:py-12" data-aos="fade-up">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Categories Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6">
                <!-- فن Card -->
                <a href="{{ url('/art') }}"
                    class="block bg-white rounded-xl p-4 sm:p-6 md:p-8 text-center hover:shadow-xl hover:-translate-y-2 transition-all duration-300 cursor-pointer border border-category-art/20 group hover:border-category-art focus:outline-none focus:ring-2 focus:ring-category-art/50">
                    <div class="mb-3 sm:mb-4 md:mb-6 transform group-hover:scale-110 transition-transform duration-300">
                        <img src="{{ asset('imgs/icons-color/art-icon.svg') }}" alt="فن" loading="lazy" decoding="async"
                            class="w-12 h-12 sm:w-14 sm:h-14 md:w-18 md:h-18 lg:w-20 lg:h-20 mx-auto drop-shadow-lg">
                    </div>
                    <h3
                        class="text-base sm:text-lg md:text-xl lg:text-2xl font-bold text-category-art arabic-font-bold mb-1 sm:mb-2">
                        فن</h3>
                    <p class="text-xs sm:text-sm text-gray-500 arabic-font">لوحات وأعمال فنية</p>
                </a>

                <!-- أدب Card -->
                <a href="{{ url('/literary') }}"
                    class="block bg-white rounded-xl p-4 sm:p-6 md:p-8 text-center hover:shadow-xl hover:-translate-y-2 transition-all duration-300 cursor-pointer border border-category-literature/20 group hover:border-category-literature focus:outline-none focus:ring-2 focus:ring-category-literature/50">
                    <div class="mb-3 sm:mb-4 md:mb-6 transform group-hover:scale-110 transition-transform duration-300">
                        <img src="{{ asset('imgs/icons-color/literature-icon.svg') }}" alt="أدب" loading="lazy"
                            decoding="async"
                            class="w-12 h-12 sm:w-14 sm:h-14 md:w-18 md:h-18 lg:w-20 lg:h-20 mx-auto drop-shadow-lg">
                    </div>
                    <h3
                        class="text-base sm:text-lg md:text-xl lg:text-2xl font-bold text-category-literature arabic-font-bold mb-1 sm:mb-2">
                        أدب</h3>
                    <p class="text-xs sm:text-sm text-gray-500 arabic-font">كتب وأعمال أدبية</p>
                </a>

                <!-- معرض Card -->
                <a href="{{ route('artbrwaz.index') }}"
                    class="block bg-white rounded-xl p-4 sm:p-6 md:p-8 text-center hover:shadow-xl hover:-translate-y-2 transition-all duration-300 cursor-pointer border border-category-gallery/20 group hover:border-category-gallery focus:outline-none focus:ring-2 focus:ring-category-gallery/50">
                    <div class="mb-3 sm:mb-4 md:mb-6 transform group-hover:scale-110 transition-transform duration-300">
                        <img src="{{ asset('imgs/icons-color/gallery-icon.svg') }}" alt="معرض" loading="lazy"
                            decoding="async"
                            class="w-12 h-12 sm:w-14 sm:h-14 md:w-18 md:h-18 lg:w-20 lg:h-20 mx-auto drop-shadow-lg">
                    </div>
                    <h3
                        class="text-base sm:text-lg md:text-xl lg:text-2xl font-bold text-category-gallery arabic-font-bold mb-1 sm:mb-2">
                        معرض</h3>
                    <p class="text-xs sm:text-sm text-gray-500 arabic-font">معارض فنية مميزة</p>
                </a>

                <!-- مزاد Card -->
                <a href="{{ url('/mazad') }}"
                    class="block bg-white rounded-xl p-4 sm:p-6 md:p-8 text-center hover:shadow-xl hover:-translate-y-2 transition-all duration-300 cursor-pointer border border-category-auction/20 group hover:border-category-auction focus:outline-none focus:ring-2 focus:ring-category-auction/50">
                    <div class="mb-3 sm:mb-4 md:mb-6 transform group-hover:scale-110 transition-transform duration-300">
                        <img src="{{ asset('imgs/icons-color/auction-icon.svg') }}" alt="مزاد" loading="lazy"
                            decoding="async"
                            class="w-12 h-12 sm:w-14 sm:h-14 md:w-18 md:h-18 lg:w-20 lg:h-20 mx-auto drop-shadow-lg">
                    </div>
                    <h3
                        class="text-base sm:text-lg md:text-xl lg:text-2xl font-bold text-category-auction arabic-font-bold mb-1 sm:mb-2">
                        مزاد</h3>
                    <p class="text-xs sm:text-sm text-gray-500 arabic-font">مزادات حية ومثيرة</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Art Categories Section -->
    <section class="bg-gray-100 py-10 sm:py-14 md:py-20" data-aos="fade-up">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:gap-6 lg:grid-cols-[1.45fr_1fr] lg:min-h-[560px]">
                <!-- Photography Card (Hero) -->
                <div
                    class="flex flex-col justify-end items-start text-right bg-cover bg-center bg-no-repeat relative rounded-xl shadow-lg overflow-hidden h-64 sm:h-80 md:h-[30rem] lg:h-full lg:row-span-2 group hover:scale-105 transition-transform duration-300">
                    <img src="{{ asset('imgs/pic/rec3.jpg') }}" alt="التصوير الضوئي" loading="lazy" decoding="async"
                        class="absolute inset-0 w-full h-full object-cover" aria-hidden="true">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-50 group-hover:bg-opacity-40 transition-all duration-300">
                    </div>
                    <div class="relative z-10 p-4 sm:p-6 md:p-8">
                        <h4 class="text-xl sm:text-2xl md:text-3xl font-bold text-white arabic-font-bold mb-2 sm:mb-4">
                            التصوير الضوئي</h4>
                        <p class="text-white text-xs sm:text-sm md:text-base leading-relaxed arabic-font max-w-md">
                            تصفح، اعرض، واشتر أعمالاً فنية مميزة من فنانين موهوبين في عالم التصوير الضوئي الاحترافي
                        </p>
                    </div>
                </div>

                <!-- Digital Art Card -->
                <div
                    class="flex flex-col justify-end items-start text-right bg-cover bg-center bg-no-repeat relative rounded-xl shadow-lg overflow-hidden h-64 sm:h-72 md:h-80 lg:h-full group hover:scale-105 transition-transform duration-300">
                    <img src="{{ asset('imgs/pic/rec1.jpg') }}" alt="الفن الرقمي" loading="lazy" decoding="async"
                        class="absolute inset-0 w-full h-full object-cover" aria-hidden="true">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-50 group-hover:bg-opacity-40 transition-all duration-300">
                    </div>
                    <div class="relative z-10 p-4 sm:p-5 md:p-6">
                        <h4 class="text-lg sm:text-xl md:text-2xl font-bold text-white arabic-font-bold mb-2">الفن
                            الرقمي</h4>
                        <p class="text-white text-xs sm:text-sm leading-relaxed arabic-font">تصفح، اعرض، واشتر أعمالاً
                            فنية مميزة من فنانين موهوبين</p>
                    </div>
                </div>

                <!-- Traditional Art Card -->
                <div
                    class="flex flex-col justify-end items-start text-right bg-cover bg-center bg-no-repeat relative rounded-xl shadow-lg overflow-hidden h-64 sm:h-72 md:h-80 lg:h-full group hover:scale-105 transition-transform duration-300">
                    <img src="{{ asset('imgs/pic/rec2.jpg') }}" alt="الفن التشكيلي" loading="lazy" decoding="async"
                        class="absolute inset-0 w-full h-full object-cover" aria-hidden="true">
                    <div
                        class="absolute inset-0 bg-black bg-opacity-50 group-hover:bg-opacity-40 transition-all duration-300">
                    </div>
                    <div class="relative z-10 p-4 sm:p-5 md:p-6">
                        <h4 class="text-lg sm:text-xl md:text-2xl font-bold text-white arabic-font-bold mb-2">الفن
                            التشكيلي</h4>
                        <p class="text-white text-xs sm:text-sm leading-relaxed arabic-font">تصفح، اعرض، واشتر أعمالاً
                            فنية مميزة من فنانين موهوبين</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- الفنانين Section -->
    <section class="artists-section py-6 sm:py-8 md:py-12 lg:py-16" data-aos="fade-up">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center sm:text-right mb-6 sm:mb-8 md:mb-12">
                <div>
                    <h2
                        class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 arabic-font-bold mb-2 flex">
                        <img class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 lg:w-14 lg:h-14"
                            src="{{ asset('imgs/icons-color/art-icon.svg') }}" alt="فنانين" loading="lazy"
                            decoding="async"> <span class="mr-2">الفنانين</span>
                    </h2>
                    <p class="text-xs sm:text-sm md:text-base text-gray-600 arabic-font">
                        تعرف على مجموعة من أبرز الفنانين
                    </p>
                </div>
            </div>

            <!-- Artists Grid -->
            <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
                <!-- Featured Artist Card -->
                <div
                    class="relative overflow-hidden rounded-xl sm:rounded-2xl shadow-lg sm:shadow-xl group order-2 lg:order-1">
                    <!-- Artist Image -->
                    <div class="relative h-64 sm:h-80 md:h-96 ">
                        <img src="{{ asset_url($settings?->featured_artist_image, 'imgs/pepole/artist-1.png') }}"
                            loading="lazy" decoding="async" alt="فنان مميز"
                            class="w-full h-full object-cover object-center bg-no-repeat group-hover:scale-105 transition-transform duration-500">
                        <!-- Overlay -->
                        <div
                            class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-30 transition-all duration-300">
                        </div>
                        <!-- Navigation Button -->
                        <button
                            class=" absolute bottom-3 left-3 sm:bottom-4 sm:left-4  bg-white bg-opacity-20 hover:bg-red-500 text-white px-3 py-2 sm:px-4 sm:py-3 rounded-full transition-all duration-300 flex items-center gap-1 sm:gap-2">
                            <span class="text-xs sm:text-sm arabic-font">متابعة</span>
                            <i class="fas fa-chevron-left "></i>
                        </button>
                    </div>
                </div>
                <!-- Artist Info Card -->
                <div
                    class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl sm:rounded-2xl p-4 sm:p-6 lg:p-8 flex flex-col justify-center order-1 lg:order-2">
                    <div class="text-center sm:text-right">
                        <h3
                            class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 arabic-font-bold mb-3 sm:mb-4">
                            {{ $settings?->featured_artist_title ?? 'بائعة الأكياس' }}
                        </h3>
                        <div class="space-y-2 sm:space-y-3 mb-4 sm:mb-6">
                            <p class="text-xs sm:text-sm md:text-base text-gray-700 arabic-font leading-relaxed">
                                {{ $settings?->featured_artist_description ?? 'وصف الفنان المميز.' }}
                            </p>
                            <p class="text-xs sm:text-sm md:text-base text-gray-700 arabic-font leading-relaxed">
                                يتميز أسلوبه الفني بالمزج بين الفن التراثي العربي والتقنيات
                                المعاصرة، حيث يستخدم الألوان الدافئة والخطوط المتدفقة لإبراز
                                الهوية الثقافية العمانية.
                            </p>
                            <p
                                class="text-xs sm:text-sm md:text-base text-gray-700 arabic-font leading-relaxed hidden sm:block">
                                حصل على عدة جوائز محلية وإقليمية، وتُعرض أعماله في صالات عرض
                                مرموقة عبر الخليج العربي.
                            </p>
                        </div>
                        <button
                            class="bg-gray-800 hover:bg-gray-700 text-white px-6 sm:px-8 py-2 sm:py-3 rounded-full arabic-font font-medium transition-all duration-300 shadow-lg hover:shadow-xl text-sm sm:text-base">
                            أكثر
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- المزادات Section (Dynamic) -->
    <section class=" bg-gray-100 py-6 sm:py-8 md:py-12 lg:py-16" data-aos="fade-up">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center sm:text-right mb-6 sm:mb-8 md:mb-12">
                <h2
                    class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 arabic-font-bold mb-2 flex items-start justify-center sm:justify-start">
                    <img class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 lg:w-14 lg:h-14 ml-3"
                        src="{{ asset('imgs/icons-color/mazad.svg') }}" alt="المزادات">
                    <span>المزادات</span>
                </h2>
                <p class="text-xs sm:text-sm md:text-base text-gray-600 arabic-font">
                    شارك في مزادات حية ومثيرة للحصول على أفضل الأعمال الفنية النادرة
                </p>
            </div>

            @php
                $auctionsCollection = collect($auctions ?? []);
                $featured = $auctionsCollection->first();
                $others = $auctionsCollection->slice(1);
            @endphp

            <!-- Mobile Layout -->
            <div class="block lg:hidden">
                @if($featured)
                    <a href="{{ route('mazad.show', $featured->id) }}"
                        class="w-full h-64 sm:h-80 rounded-2xl overflow-hidden relative shadow-lg mb-6 group block">
                        <img src="{{ $featured->cover_image_url ?? asset('imgs/pic/img4.png') }}" loading="lazy"
                            decoding="async"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            alt="مزاد مميز">
                        <div class="absolute top-3 right-3 flex items-center gap-2">
                            @if($featured->isLive())
                                <span
                                    class="bg-auction-live text-white text-xs px-3 py-1.5 rounded-full font-medium shadow-lg flex items-center gap-1">
                                    <i class="fa-solid fa-circle text-xs animate-pulse"></i><span class="arabic-font">مباشر
                                        الآن</span>
                                </span>
                            @elseif($featured->isSoon())
                                <span
                                    class="bg-auction-soon text-brwazi-dark text-xs px-3 py-1.5 rounded-full font-medium shadow-lg flex items-center gap-1">
                                    <i class="fa-solid fa-hourglass-start text-xs"></i><span class="arabic-font">قريباً</span>
                                </span>
                            @else
                                <div class="absolute inset-0 z-10 bg-black/70 flex items-center justify-center">
                                    <span class="text-white text-lg sm:text-xl font-bold arabic-font">انتهى</span>
                                </div>
                            @endif
                        </div>
                        <div class="absolute bottom-4 left-4 bg-black/60 backdrop-blur-sm rounded-lg px-3 py-2 text-right">
                            <p class="text-white text-xs arabic-font mb-1">الوقت المتبقي</p>
                            <h2 class="text-lg sm:text-xl font-bold text-white arabic-font-bold"
                                data-countdown-end="{{ $featured->ends_at?->timestamp }}">--:--:--</h2>
                        </div>
                    </a>
                @endif

                @if($others->isNotEmpty())
                    <div class="relative">
                        <h3 class="text-sm sm:text-base font-bold text-gray-700 arabic-font-bold mb-3 text-right">مزادات
                            أخرى</h3>
                        <div class="flex gap-3 overflow-x-auto scrollbar-hide pb-2 snap-x-mandatory">
                            @foreach($others as $auc)
                                <a href="{{ route('mazad.show', $auc->id) }}"
                                    class="flex-shrink-0 w-32 sm:w-36 h-44 sm:h-48 rounded-xl overflow-hidden relative shadow-md group snap-start block">
                                    <img src="{{ $auc->cover_image_url ?? asset('imgs/pic/img.png') }}" loading="lazy"
                                        decoding="async" alt="مزاد"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @if($auc->isLive())
                                        <span
                                            class="absolute top-2 right-2 bg-auction-live text-white text-xs px-2 py-1 rounded-full font-medium shadow-sm flex items-center gap-1">
                                            <i class="fa-solid fa-circle text-xs animate-pulse"></i><span
                                                class="arabic-font">مباشر</span>
                                        </span>
                                    @elseif($auc->isSoon())
                                        <span
                                            class="absolute top-2 right-2 bg-auction-soon text-brwazi-dark text-xs px-2 py-1 rounded-full font-medium shadow-sm">
                                            <i class="fa-solid fa-hourglass-start text-xs"></i><span
                                                class="arabic-font ml-1">قريباً</span>
                                        </span>
                                    @else
                                        <div class="absolute inset-0 z-10 bg-black/70 flex items-center justify-center">
                                            <span class="text-white text-sm sm:text-base font-bold arabic-font">انتهى</span>
                                        </div>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            <!-- Desktop Layout -->
            <div class="hidden lg:flex gap-6">
                <div class="flex gap-4">
                    @foreach($others->take(3) as $auc)
                        <a href="{{ route('mazad.show', $auc->id) }}"
                            class="{{ $loop->first ? 'w-28' : 'w-40' }} h-96 rounded-2xl overflow-hidden relative shadow-lg group hover:shadow-xl transition-all duration-300 block">
                            <img src="{{ $auc->cover_image_url ?? asset('imgs/pic/img.png') }}" loading="lazy"
                                decoding="async"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                alt="مزاد">
                            @if($auc->isLive())
                                <span
                                    class="absolute top-2 right-2 bg-auction-live text-white text-xs px-3 py-1 rounded-full font-medium shadow-sm flex items-center gap-1">
                                    <i class="fa-solid fa-circle text-xs animate-pulse"></i><span
                                        class="arabic-font">مباشر</span>
                                </span>
                            @elseif($auc->isSoon())
                                <span
                                    class="absolute top-2 right-2 bg-auction-soon text-brwazi-dark text-xs px-2 py-1 rounded-full font-medium shadow-sm">
                                    <i class="fa-solid fa-hourglass-start text-xs"></i><span
                                        class="arabic-font ml-1">قريباً</span>
                                </span>
                            @else
                                <div class="absolute inset-0 z-10 bg-black/70 flex items-center justify-center">
                                    <span class="text-white text-base font-bold arabic-font">انتهى</span>
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
                @if($featured)
                    <a href="{{ route('mazad.show', $featured->id) }}"
                        class="flex-1 h-96 rounded-2xl overflow-hidden relative shadow-lg bg-white group hover:shadow-xl transition-all duration-300 block">
                        <img src="{{ $featured->cover_image_url ?? asset('imgs/pic/img4.png') }}" loading="lazy"
                            decoding="async"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            alt="مزاد مميز">
                        @if($featured->isLive())
                            <span
                                class="absolute top-4 right-4 bg-auction-live text-white text-sm px-4 py-2 rounded-full font-medium shadow-lg flex items-center gap-2">
                                <i class="fa-solid fa-circle text-xs animate-pulse"></i><span class="arabic-font">مباشر
                                    الآن</span>
                            </span>
                        @elseif($featured->isSoon())
                            <span
                                class="absolute top-4 right-4 bg-auction-soon text-brwazi-dark text-sm px-4 py-2 rounded-full font-medium shadow-lg flex items-center gap-2">
                                <i class="fa-solid fa-hourglass-start text-xs"></i><span class="arabic-font">قريباً</span>
                            </span>
                        @else
                            <div class="absolute inset-0 z-10 bg-black/70 flex items-center justify-center">
                                <span class="text-white text-xl font-bold arabic-font">انتهى</span>
                            </div>
                        @endif
                        <div class="absolute bottom-6 left-6 text-right bg-black/60 backdrop-blur-sm rounded-lg px-4 py-3">
                            <p class="text-white text-lg arabic-font mb-1">الوقت المتبقي</p>
                            <h2 class="text-3xl font-bold text-gray-200 arabic-font-bold"
                                data-countdown-end="{{ $featured->ends_at?->timestamp }}">--:--:--</h2>
                        </div>
                    </a>
                @endif
            </div>
        </div>
    </section>

    <!--قسم الفن Section -->
    <section class="bg-white font-sans py-10 sm:py-12" data-aos="fade-up" id="art-section">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
            <!-- Image Slider Container -->
            <div class="relative">
                <!-- Image Slider -->
                <div id="art-carousel" class="relative overflow-hidden rounded-2xl" tabindex="0"
                    aria-roledescription="carousel" aria-label="عارض الأعمال الفنية" aria-live="off">
                    <div id="image-slider" class="relative select-none w-60 sm:w-72 md:w-80 mx-auto aspect-[3/4]">
                        @php $slides = $settings?->art_slides ?? []; @endphp
                        @forelse($slides as $i => $slide)
                            <div class="absolute inset-0 rounded-2xl overflow-hidden shadow-lg bg-gray-100 {{ $i === 0 ? '' : 'opacity-0 pointer-events-none' }} transition-opacity duration-500 ease-in-out"
                                data-index="{{ $i }}" aria-hidden="{{ $i === 0 ? 'false' : 'true' }}">
                                <img src="{{ asset_url($slide['image'] ?? '') }}" alt="{{ $slide['title'] ?? 'عمل فني' }}"
                                    loading="{{ $i === 0 ? 'eager' : 'lazy' }}" draggable="false"
                                    class="w-full h-full object-cover pointer-events-none select-none">
                            </div>
                        @empty
                            <div class="absolute inset-0 rounded-2xl overflow-hidden shadow-lg bg-gray-100 transition-opacity duration-500 ease-in-out"
                                data-index="0" aria-hidden="false">
                                <img src="{{ asset('imgs/pic/img9.png') }}" alt="عمل فني"
                                    class="w-full h-full object-cover">
                            </div>
                        @endforelse
                    </div>
                </div>
                <h2></h2><!-- Navigation Arrows -->

                <!-- Slide Indicators -->
                <div class="flex flex-col items-center mt-4" dir="ltr">
                    <div class="flex justify-center space-x-2" role="tablist" aria-label="الشرائح">
                        <button
                            class="slide-indicator w-3 h-3 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors duration-300"
                            data-slide="0" role="tab" aria-label="الشريحة 1" aria-controls="image-slider"></button>
                        <button
                            class="slide-indicator w-3 h-3 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors duration-300"
                            data-slide="1" role="tab" aria-label="الشريحة 2" aria-controls="image-slider"></button>
                        <button
                            class="slide-indicator w-3 h-3 rounded-full bg-gray-300 hover:bg-gray-400 transition-colors duration-300"
                            data-slide="2" role="tab" aria-label="الشريحة 3" aria-controls="image-slider"></button>
                    </div>
                    <div id="slide-counter" class="text-xs text-gray-500 mt-2">1 / 3</div>
                    <!-- Dynamic Thumbnails (generated by JS from slides) -->
                    <div id="art-thumbs" class="mt-3 flex justify-center gap-2"></div>
                </div>
            </div>

            <!-- Text Section -->
            <div class="text-right" data-aos="fade-left">
                <h2
                    class="text-2xl md:text-3xl font-bold text-category-art mb-4 flex items-center justify-start arabic-font-bold">
                    <span class="ml-3">
                        <img src="{{ asset('imgs/icons-color/idea.svg') }}" alt="فن" class="w-8 h-8">
                    </span>
                    فن
                </h2>
                <h3 id="artwork-title" class="text-xl md:text-2xl font-semibold text-gray-700 mb-3 arabic-font-bold">
                    بائعة الأكياس</h3>
                <p id="artwork-description" class="text-gray-600 leading-relaxed mb-6 arabic-font text-sm md:text-base">
                    لوحة فنية تشكيلية رائعة تجسد التراث العماني الأصيل، حيث تظهر امرأة عمانية تبيع الأكياس التقليدية في
                    سوق مطرح التاريخي. تعكس هذه اللوحة جمال الحياة اليومية والعمل النسائي في المجتمع العماني.
                </p>
                <div class="flex gap-3">
                    <button
                        class="px-6 py-3 rounded-full  bg-red-300 text-white font-semibold shadow-lg hover:bg-red-500 transition-all duration-200 hover:scale-105 arabic-font">
                        تفاصيل أكثر
                    </button>
                    <button
                        class="px-6 py-3 rounded-full bg-gray-100 text-gray-800 font-semibold shadow hover:bg-gray-200 transition-all duration-300 arabic-font">
                        إضافة للمفضلة <i class="fa-solid fa-bookmark ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>
    <!-- معرض الأدب Section -->
    <section class="py-6 sm:py-8 md:py-12 lg:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center sm:text-right mb-6 sm:mb-8 md:mb-12">
                <h2
                    class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-gray-800 arabic-font-bold mb-2 flex items-center justify-start sm:justify-start">
                    <img class="w-8 h-8 sm:w-10 sm:h-10 md:w-12 md:h-12 lg:w-14 lg:h-14 ml-3"
                        src="{{ asset('imgs/icons-color/literature-icon.svg') }}" alt="الأدب">
                    <span>أدب</span>
                </h2>
                <p class="text-xs sm:text-sm md:text-base text-gray-600 arabic-font">
                    اكتشف مجموعة متنوعة من الكتب والأعمال الأدبية المميزة
                </p>
            </div>

            <!-- Books Grid -->
            @php
                $booksCollection = collect($books ?? []);
            @endphp
            @if($booksCollection->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($booksCollection as $book)
                        @php
                            $authorNames = $book->authors?->pluck('name')->filter()->take(2)->implode('، ');
                            $displayAuthor = $authorNames !== '' ? $authorNames : ($book->publisher?->name ?? '');
                            $priceValue = (float) ($book->price_omr ?? 0);
                            $priceFormatted = number_format($priceValue, 3, '.', ',');
                            $bookUrl = url('/literary/book/' . $book->id);
                        @endphp
                        <div
                            class="bg-gray-100 rounded-2xl shadow-lg p-6 flex flex-col items-center group hover:shadow-xl transition-all duration-300">
                            <div class="bg-white rounded-lg p-2 mb-4 shadow-sm">
                                <img src="{{ asset_url($book->cover_image_path, 'imgs/pic/Book.png') }}" alt="{{ $book->title }}"
                                    class="w-36 h-44 sm:w-28 sm:h-36 object-cover rounded">
                            </div>

                            <div class="text-center mb-4">
                                <h3 class="text-lg font-bold text-gray-800 arabic-font-bold mb-1">{{ $book->title }}</h3>
                                @if($displayAuthor)
                                    <p class="text-gray-500 text-sm arabic-font">{{ $displayAuthor }}</p>
                                @endif
                            </div>

                            <div class="flex items-center justify-center gap-1 mb-4">
                                <span class="text-lg font-bold text-gray-900 arabic-font-bold">{{ $priceFormatted }}</span>
                                <span class="text-gray-600 text-sm arabic-font">ريال عماني</span>
                            </div>

                            <div class="flex gap-2 w-full flex-col sm:flex-row">
                                <a href="{{ $bookUrl }}"
                                    class="w-full sm:flex-1 bg-blue-900 text-white px-4 py-3 rounded-lg text-sm arabic-font font-medium hover:bg-blue-800 transition-colors duration-300 text-center">
                                    شراء
                                </a>
                                <button
                                    class="w-full sm:w-auto border text-blue-900 px-3 py-3 rounded-lg hover:bg-blue-50 transition-colors duration-300"
                                    type="button" aria-label="إضافة إلى المفضلة">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                @php
                    $pagesCount = max(1, (int) ceil($booksCollection->count() / 4));
                @endphp
                @if($pagesCount > 1)
                    <!-- Pagination -->
                    <div class="flex justify-center mt-8 gap-3">
                        @for($i = 1; $i <= $pagesCount; $i++)
                            <button
                                class="w-3 h-3 rounded-full {{ $i === 1 ? 'bg-blue-900' : 'bg-gray-300 hover:bg-gray-400 transition-colors duration-300' }}"
                                type="button" aria-label="صفحة {{ $i }}">
                            </button>
                        @endfor
                    </div>
                @endif
            @else
                <p class="text-center text-gray-500 arabic-font mt-6">لا توجد كتب متاحة حالياً.</p>
            @endif
        </div>
    </section>
    <section class="py-6 sm:py-8 md:py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Title -->
            <h2 class="text-2xl font-bold text-indigo-950 mb-6">معارض وفعاليات قادمة</h2>

            <!-- Events List -->
            @php $events = $settings?->events ?? []; @endphp
            <div class="space-y-6">
                @forelse($events as $ev)
                    <div class="flex items-center justify-between border-b pb-6">
                        <a href="{{ $ev['link'] ?? '#' }}"
                            class="bg-indigo-950 text-white px-6 py-2 rounded-md text-sm hover:bg-indigo-800">
                            التفاصيل والتسجيل
                        </a>
                        <div class="flex-1 text-right pr-6">
                            <h3 class="font-bold text-lg text-indigo-950">{{ $ev['title'] ?? '' }}</h3>
                            <p class="text-gray-600 text-sm">{{ $ev['description'] ?? '' }}</p>
                        </div>
                        <div class="bg-cyan-300 text-center px-6 py-3 rounded-md">
                            <p class="text-xl font-bold text-indigo-950">{{ $ev['day'] ?? '' }}</p>
                            <p class="text-sm text-indigo-950">{{ $ev['month'] ?? '' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">لا توجد فعاليات حالياً.</p>
                @endforelse
            </div>
        </div>
    </section>
    <section>
        @push('scripts')
            <script id="artworks-data"
                type="application/json">{!! json_encode(($settings?->art_slides ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
            <script src="{{ asset('js/home-slider.js') }}" defer></script>
        @endpush
    </section>

</x-layout>
