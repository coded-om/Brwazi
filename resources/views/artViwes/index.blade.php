@php
    $categories = $categories ?? [];
    $selectedCategory = $selectedCategory ?? null;
    $artworks = $artworks ?? collect();
@endphp
<x-layout>
    @if(auth()->check() && auth()->user()->isBanned())
        @push('styles')
            <style>
                .brw-notify-wrapper {
                    top: calc(5.5rem + 3.25rem);
                }

                @media (max-width: 640px) {
                    .brw-notify-wrapper {
                        top: calc(4.5rem + 3.25rem);
                    }
                }
            </style>
        @endpush
        <div class="fixed inset-x-0 z-50" style="top: 5.5rem;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="rounded-md bg-amber-50 border border-amber-200 text-amber-800 px-3 py-2 sm:px-4 sm:py-3 text-sm flex items-center justify-between gap-3 shadow">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <span>حسابك محظور، يرجى التواصل مع الدعم.</span>
                    </div>
                    <a href="{{ url('/support') }}"
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-amber-600 hover:bg-amber-700 text-white">
                        <i class="fa-solid fa-life-ring text-xs"></i>
                        <span class="text-xs sm:text-sm">اتصل بالدعم</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="h-12 sm:h-14"></div>
    @endif
    @push('styles')
        <style>
            html,
            body {
                overflow-x: hidden;
            }
        </style>
    @endpush
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" data-aos="fade-up">

        <!-- Category filter: dropdown بدل التبويبات -->
        <div class="flex items-center justify-between  gap-3 border-solid border-b border-gray-200 mb-16 pb-7  mt-12"
            data-aos="fade-up" data-aos-delay="100">
            <button type="button" data-slide-target="filters"
                class="h-9 w-9 inline-flex items-center justify-center rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200"
                aria-label="تصفية">
                <i class="fa-solid fa-sliders"></i>
            </button>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-indigo-900"><img
                    src="{{ asset('imgs/icons-color/eye-category.svg') }}" alt="Section Icon"
                    class="inline-block w-6 h-6 ml-1">القسم الفني</h1>
            <div class="flex items-center gap-2">
                <select id="categorySelect" name="category"
                    class="rounded border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                    onchange="this.form.submit()">
                    <option value="" {{ !$selectedCategory ? 'selected' : '' }}>كل الفنون</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ $selectedCategory === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Artists slider (instead of sidebar) -->
        <x-artists-slider :artists="$topArtists" :selected="$selectedArtist" />
        <!-- Category bar (matches provided design) -->
        <div class="mb-10 border-b-2 border-b-gray-700">
            <div
                class="relative mx-auto  w-fit bg-gray-100 border border-gray-200 rounded-1xl px-3 py-2 my-16 shadow-[0_10px_20px_rgba(0,0,0,0.06)]">
                @php
                    // Map known category keys to icons; fallback to 'all' icon
                    // Include common synonyms to avoid fallback
                    $catIcons = [
                        'photography' => 'photography-category.svg',
                        'photo' => 'photography-category.svg',
                        'digital' => 'digital-category.svg',
                        'fine' => 'fine-category.svg',
                        'fine_art' => 'fine-category.svg',
                        'all' => 'all-category.svg',
                    ];
                    $allLabel = 'كل الفنون';
                    $currentLabel = ($selectedCategory && isset($categories[$selectedCategory])) ? $categories[$selectedCategory] : $allLabel;
                    // Order: prefer some keys if they exist, then the rest
                    $orderPref = ['photography', 'digital', 'fine', 'fine_art', 'photo'];
                    // Prepend 'all' to always show reset option
                    $orderedKeys = array_values(array_unique(array_merge(
                        ['all'],
                        array_filter($orderPref, fn($k) => array_key_exists($k, $categories)),
                        array_keys($categories)
                    )));
                    $showKeys = array_slice($orderedKeys, 0, 4);
                @endphp
                <ul class="flex items-center justify-center gap-4 text-center ">
                    @foreach($showKeys as $k)
                        @php
                            $label = ($k === 'all') ? $allLabel : ($categories[$k] ?? $k);
                            // Normalize the category key to a known icon key
                            $mapKey = $k;
                            if ($mapKey === 'photo')
                                $mapKey = 'photography';
                            if ($mapKey === 'fine_art')
                                $mapKey = 'fine';
                            if (!isset($catIcons[$mapKey])) {
                                $lower = strtolower((string) $mapKey);
                                if (str_contains($lower, 'digit') || str_contains((string) $mapKey, 'رقمي')) {
                                    $mapKey = 'digital';
                                } elseif (str_contains($lower, 'photo') || str_contains((string) $mapKey, 'تصوير')) {
                                    $mapKey = 'photography';
                                } elseif (
                                    str_contains($lower, 'fine') ||
                                    str_contains($lower, 'tradit') || /* traditional */
                                    str_contains((string) $mapKey, 'تقلي') || /* تقليدي */
                                    str_contains((string) $mapKey, 'كلاسي') || /* كلاسيكي */
                                    str_contains((string) $mapKey, 'رسم') ||
                                    str_contains((string) $mapKey, 'لوحة')
                                ) {
                                    $mapKey = 'fine';
                                }
                            }
                            $icon = asset('imgs/icons-color/' . ($catIcons[$mapKey] ?? 'all-category.svg'));
                            $isActive = ($k === 'all') ? empty($selectedCategory) : ($selectedCategory === $k);
                            // For 'all', clear the category & page to reset to all arts while keeping other filters
                            if ($k === 'all') {
                                $url = route('art.index', array_filter(array_merge(request()->query(), [
                                    'category' => null,
                                    'page' => null,
                                ])));
                            } else {
                                $url = route('art.index', array_filter(array_merge(request()->query(), [
                                    'category' => $k,
                                    'page' => null,
                                ])));
                            }
                        @endphp
                        <li>
                            <a href="{{ $url }}" class="group flex flex-col items-center gap-2">
                                <img src="{{ $icon }}" alt="{{ $label }}"
                                    class="w-10 h-10 group-hover:scale-105 transition" />
                                <span
                                    class="text-sm {{ $isActive ? 'text-indigo-900 font-semibold' : 'text-gray-700' }}">{{ $label }}</span>
                            </a>
                        </li>
                    @endforeach

                </ul>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6  ">
            <div class="lg:col-span-12">

                <!-- Artworks masonry -->
                <div id="masonryWrap" data-cols="4">
                    @if(isset($artworks) && $artworks->count())
                        <x-masonry :items="$artworks" :columns="4" />
                    @else
                        <div class="text-center text-gray-500 py-12">لا توجد أعمال حاليا.</div>
                    @endif
                </div>

                @if(isset($paginator) && $paginator)
                    <div class="mt-6 flex items-center justify-center">
                        <button id="loadMoreBtn"
                            class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">تحميل
                            المزيد</button>
                    </div>
                    <div id="infiniteSentinel" class="h-1"></div>
                    @if($paginator->nextPageUrl())
                        <a id="nextLink" rel="next" href="{{ $paginator->appends(request()->except('page'))->nextPageUrl() }}"
                            class="hidden" aria-hidden="true">التالي</a>
                    @endif
                @endif
            </div>
        </div>

        @push('scripts')
            @once
                <script>
                    (function () {
                        const onLoad = (el) => el.classList.add('is-loaded');
                        const init = () => document.querySelectorAll('img.lazy-media').forEach(img => {
                            if (img.complete) onLoad(img);
                            else {
                                img.addEventListener('load', () => onLoad(img), { once: true });
                                img.addEventListener('error', () => onLoad(img), { once: true });
                            }
                        });
                        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();

                        const wrap = document.getElementById('masonryWrap');
                        const sentinel = document.getElementById('infiniteSentinel');
                        const loadBtn = document.getElementById('loadMoreBtn');
                        const getNextUrl = () => {
                            const a = document.querySelector('a[rel="next"]');
                            return a ? a.getAttribute('href') : null;
                        };
                        const setNextUrl = (url) => {
                            let a = document.querySelector('a[rel="next"]');
                            if (!a) {
                                a = document.createElement('a');
                                a.rel = 'next';
                                a.className = 'hidden';
                                a.setAttribute('aria-hidden', 'true');
                                document.body.appendChild(a);
                            }
                            if (url) a.href = url; else a.remove();
                        };
                        const appendHtml = (html) => {
                            const temp = document.createElement('div');
                            temp.innerHTML = html;
                            const grid = temp.querySelector('.js-masonry-grid');
                            const cols = wrap?.querySelectorAll('.js-masonry-col') || [];
                            if (grid && cols.length) {
                                const newCols = Array.from(grid.querySelectorAll('.js-masonry-col'));
                                newCols.forEach((col, idx) => {
                                    const target = cols[idx % cols.length];
                                    Array.from(col.children).forEach(card => target.appendChild(card));
                                });
                            } else if (grid) {
                                wrap.innerHTML = '';
                                wrap.appendChild(grid);
                            }
                            init();
                            if (window.AOS && typeof window.AOS.refresh === 'function') window.AOS.refresh();
                        };
                        const loadMore = async () => {
                            const next = getNextUrl();
                            if (!next) { sentinel?.classList.add('hidden'); return; }
                            const url = new URL(next, window.location.origin);
                            url.searchParams.set('partial', '1');
                            url.searchParams.set('cols', wrap?.dataset?.cols || '4');
                            loadBtn?.setAttribute('disabled', 'disabled');
                            try {
                                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                                if (!res.ok) throw new Error('failed');
                                const data = await res.json();
                                appendHtml(data.html);
                                if (data.next) setNextUrl(data.next); else sentinel?.classList.add('hidden');
                            } catch (e) { console.error(e); }
                            finally { loadBtn?.removeAttribute('disabled'); }
                        };
                        loadBtn?.addEventListener('click', loadMore);
                        if ('IntersectionObserver' in window && sentinel) {
                            const io = new IntersectionObserver((entries) => {
                                entries.forEach(e => { if (e.isIntersecting) loadMore(); });
                            }, { rootMargin: '400px 0px' });
                            io.observe(sentinel);
                        }
                    })();
                </script>
            @endonce
        @endpush
    </div><!-- /max-w container -->
    <!-- Advanced Filters Slide-over (reusable component moved outside container) -->
    <x-slide-over id="filters" title="فلترة متقدمة" side="right" maxWidth="max-w-sm">
        <form action="{{ url('/art') }}" method="GET" class="space-y-5">
            @if(request()->filled('mode'))
                <input type="hidden" name="mode" value="{{ request('mode') }}">
            @endif
            <input type="hidden" name="category" value="{{ $selectedCategory }}">
            @if(request()->filled('filter'))
                <input type="hidden" name="filter" value="{{ request('filter') }}">
            @endif

            <div>
                <label class="block text-xs text-gray-600 mb-1">كلمة مفتاحية</label>
                <input type="text" name="q" value="{{ request('q') }}"
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 focus:border-indigo-400 focus:ring-indigo-100"
                    placeholder="ابحث بعنوان العمل أو الفنان">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">السعر الأدنى</label>
                    <input type="number" step="0.01" name="min" value="{{ request('min') }}"
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 focus:border-indigo-400 focus:ring-indigo-100"
                        placeholder="0.00">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">السعر الأقصى</label>
                    <input type="number" step="0.01" name="max" value="{{ request('max') }}"
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 focus:border-indigo-400 focus:ring-indigo-100"
                        placeholder="9999.00">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">من سنة</label>
                    <input type="number" name="year_from" value="{{ request('year_from') }}"
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 focus:border-indigo-400 focus:ring-indigo-100"
                        placeholder="2000">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">إلى سنة</label>
                    <input type="number" name="year_to" value="{{ request('year_to') }}"
                        class="w-full rounded-lg border border-gray-200 bg-gray-50 focus:border-indigo-400 focus:ring-indigo-100"
                        placeholder="2025">
                </div>
            </div>

            <div>
                <label class="block text-xs text-gray-600 mb-1">نوع العمل</label>
                <input type="text" name="type" value="{{ request('type') }}"
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 focus:border-indigo-400 focus:ring-indigo-100"
                    placeholder="لوحة، تصوير فوتوغرافي ...">
            </div>

            <div>
                <label class="block text-xs text-gray-600 mb-1">اسم الفنان</label>
                <input type="text" name="artist_name" value="{{ request('artist_name') }}"
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 focus:border-indigo-400 focus:ring-indigo-100"
                    placeholder="مثال: أحمد">
            </div>

            <div>
                <label class="block text-xs text-gray-600 mb-1">الترتيب</label>
                <select name="sort"
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 focus:border-indigo-400 focus:ring-indigo-100">
                    @php $sort = request('sort', 'latest'); @endphp
                    <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>الأحدث</option>
                    <option value="price-asc" {{ $sort === 'price-asc' ? 'selected' : '' }}>السعر: من الأقل
                        للأعلى</option>
                    <option value="price-desc" {{ $sort === 'price-desc' ? 'selected' : '' }}>السعر: من الأعلى
                        للأقل</option>
                </select>
            </div>

            <div
                class="sticky bottom-0 -mx-4 px-4 pt-2 pb-3 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/70">
                <div class="flex items-center justify-between">
                    <a href="{{ url('/art') }}" class="text-sm text-gray-600 hover:text-indigo-700">إعادة
                        التعيين</a>
                    <button type="submit" style="background-color:#4f46e5"
                        class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-1 font-medium transition disabled:opacity-50 disabled:cursor-not-allowed select-none">
                        تطبيق الفلاتر
                    </button>
                </div>
            </div>
        </form>
    </x-slide-over>
</x-layout>