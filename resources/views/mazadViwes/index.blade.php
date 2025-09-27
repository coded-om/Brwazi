<x-layout>
    @php
        $sortKey = request('sort', 'latest');
        $sortLabel = match ($sortKey) {
            'price-desc' => 'الأعلى سعراً',
            'price-asc' => 'الأقل سعراً',
            'bids-desc' => 'أكثر مزايدات',
            default => 'الأحدث',
        };
    @endphp
    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-10" data-aos="fade-up">
        <div class="flex items-center justify-between gap-6 mb-5 w-full sticky top-16 z-30 bg-white/90 backdrop-blur-sm px-2 py-3   border border-gray-200/60"
            data-aos="fade-up">
            <button id="open-filters"
                class="w-12 h-12 rounded-full bg-white border flex items-center justify-center hover:bg-gray-50 shadow-sm transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                title="فلاتر" aria-haspopup="dialog" aria-controls="filter-drawer">
                <i class="fas fa-sliders-h text-lg text-indigo-950"></i>
            </button>
            <h4 class="flex items-center gap-3 text-2xl font-extrabold text-indigo-950 leading-tight">
                <img src="{{ asset('imgs/icons-color/mazad.svg') }}" alt="مزاد" class="w-6 h-6 object-contain block"
                    loading="lazy" decoding="async" />
                <span class="leading-none mt-1">قسم المزاد</span>
            </h4>
            <div class="flex items-center gap-2 sm:gap-3 md:gap-4">
                <form id="filters-form" method="GET" action="{{ url('/mazad') }}" class="hidden">
                    <input type="hidden" name="sort" id="q-sort" value="{{ request('sort', 'latest') }}">
                    {{-- Keep min/max empty unless user actually sets a value > 0 so 0 doesn't act as a filter --}}
                    <input type="hidden" name="min" id="q-min"
                        value="{{ (request()->has('min') && request('min') !== '0') ? request('min') : '' }}">
                    <input type="hidden" name="max" id="q-max"
                        value="{{ (request()->has('max') && request('max') !== '0') ? request('max') : '' }}">
                    @php
                        $qs = (array) request('status', []);
                    @endphp
                    @foreach($qs as $s)
                        <input type="hidden" name="status[]" value="{{ $s }}">
                    @endforeach
                </form>
                <div class="relative" id="sort-menu-wrapper">
                    <button id="sort-toggle" aria-haspopup="listbox" aria-expanded="false"
                        class="h-9 sm:h-10 md:h-11 bg-white border px-4 sm:px-5 md:px-6 flex items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs md:text-sm text-gray-600 hover:bg-gray-50 shadow-sm transition rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span class="hidden sm:inline">ترتيب حسب:</span>
                        <span id="sort-current" class="text-indigo-900 font-medium">{{ $sortLabel }}</span>
                        <i class="fas fa-chevron-down text-[10px] sm:text-[11px] md:text-xs transition"
                            id="sort-caret"></i>
                    </button>
                    <ul id="sort-menu" role="listbox" tabindex="-1"
                        class="absolute top-full right-0 mt-1 w-48 bg-white border border-gray-200 rounded-md shadow-lg overflow-hidden hidden z-40">
                        <li>
                            <button data-sort="latest" role="option"
                                class="w-full text-right px-4 py-2 text-[12px] sm:text-sm hover:bg-indigo-50 text-indigo-900 font-medium flex items-center justify-between">
                                الأحدث <i class="far fa-clock text-xs opacity-60"></i>
                            </button>
                        </li>
                        <li>
                            <button data-sort="price-desc" role="option"
                                class="w-full text-right px-4 py-2 text-[12px] sm:text-sm hover:bg-indigo-50 flex items-center justify-between">
                                الأعلى سعراً <i class="fas fa-arrow-up-wide-short text-xs opacity-60"></i>
                            </button>
                        </li>
                        <li>
                            <button data-sort="price-asc" role="option"
                                class="w-full text-right px-4 py-2 text-[12px] sm:text-sm hover:bg-indigo-50 flex items-center justify-between">
                                الأقل سعراً <i class="fas fa-arrow-down-short-wide text-xs opacity-60"></i>
                            </button>
                        </li>
                        <li>
                            <button data-sort="bids-desc" role="option"
                                class="w-full text-right px-4 py-2 text-[12px] sm:text-sm hover:bg-indigo-50 flex items-center justify-between">
                                أكثر مزايدات <i class="fas fa-users text-xs opacity-60"></i>
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="w-full h-px bg-gray-200 my-3"></div>

        <div id="auction-grid" class=" md:p-6 grid gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            data-aos="fade-up" data-aos-delay="100">
            @forelse ($auctions as $a)
                @php
                    $img = $a->artwork?->primary_image_url ?? asset('imgs/mazad/mazad1.png');
                    $author = $a->artwork?->user?->full_name ?? '—';
                    $title = $a->artwork?->title ?? ('#' . $a->artwork_id);
                    $bids = (int) ($a->bids_count ?? 0);
                    $price = (float) ($a->highest_bid_amount ?? $a->start_price ?? 0);
                    $statusLabel = match ($a->status) {
                        'live' => 'مباشر الآن',
                        'scheduled' => 'قريباً',
                        'ended' => 'منتهي',
                        default => $a->status,
                    };
                    $statusColor = match ($a->status) {
                        'live' => 'bg-rose-500 text-white',
                        'scheduled' => 'bg-yellow-500 text-white',
                        'ended' => 'bg-gray-800 text-white',
                        default => 'bg-gray-200 text-gray-700',
                    };
                    $timeLabel = $a->status === 'live' ? 'الوقت المتبقي' : ($a->status === 'scheduled' ? 'يبدأ خلال' : 'انتهى');
                    $timeValue = $a->ends_at ? $a->ends_at->diffForHumans() : '—';
                @endphp
                <div class="bg-white rounded overflow-hidden shadow-sm hover:shadow-md transition group border border-gray-100 flex flex-col"
                    data-price="{{ $price }}" data-bids="{{ $bids }}" data-original-index="{{ $loop->index }}"
                    data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
                    <div class="relative w-full aspect-[4/3] overflow-hidden">
                        <img src="{{ $img }}" alt="{{ $title }}" loading="lazy" decoding="async"
                            class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-500" />
                        <div class="absolute top-3 right-4">
                            <span
                                class="text-[11px] font-medium px-3 py-1 rounded-full flex items-center gap-1 {{ $statusColor }} shadow-sm">
                                @if($a->status === 'live')
                                    <i class="fa-solid fa-circle text-[8px] animate-pulse"></i>
                                @elseif($a->status === 'scheduled')
                                    <i class="fa-solid fa-clock"></i>
                                @elseif($a->status === 'ended')
                                    <i class="fa-solid fa-circle-check"></i>
                                @endif
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col">
                        <div class="p-5 flex-1 flex flex-col">
                            <h3 class="text-lg font-bold text-indigo-950 tracking-tight mb-1">{{ $title }}</h3>
                            <p class="text-sm text-gray-500 mb-4">{{ $author }}</p>
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                                <div class="flex items-center gap-1">
                                    <span>مزاید</span>
                                    <span class="flex items-center gap-1"><i
                                            class="fas fa-users text-[11px]"></i>{{ $bids }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <span>أعلى مزايدة</span>
                                    <span class="text-indigo-900 font-bold text-sm">{{ number_format($price) }} <span
                                            class="font-normal">ريال</span></span>
                                </div>
                            </div>
                            <div class="flex items-stretch gap-2">
                                <div
                                    class="flex-1 text-center rounded-md text-xs font-medium px-3 py-2 bg-gray-100 text-gray-600">
                                    {{ $timeValue }}
                                </div>
                                <div
                                    class="flex-1 text-center rounded-md text-xs font-medium px-3 py-2 bg-rose-50 text-rose-800">
                                    {{ $timeLabel }}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-stretch gap-2 px-5 pb-5">
                            <a href="{{ url('/mazad/' . $a->id) }}"
                                class="flex-1 flex items-center justify-center gap-2 h-11 rounded-md border border-indigo-950 text-indigo-950 text-sm font-medium hover:bg-indigo-50 transition">
                                <i class="far fa-eye"></i>
                            </a>
                            <a href="{{ url('/mazad/' . $a->id) }}"
                                class="flex-[2] h-11 rounded-md bg-indigo-950 text-white text-sm font-medium hover:bg-indigo-900 transition flex items-center justify-center">
                                زايد الآن
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center text-gray-500">لا توجد مزادات حالياً</div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($auctions instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="mt-8">
                {{ $auctions->appends(request()->query())->links() }}
            </div>
        @endif


        <div class="text-center mt-14" data-aos="fade-up" data-aos-delay="150">
            <button
                class="px-10 h-12 rounded-full bg-gradient-to-l from-indigo-950 via-indigo-800 to-indigo-600 text-white text-sm font-semibold shadow-sm hover:from-indigo-900 hover:via-indigo-800 hover:to-indigo-700 active:scale-[.98] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">عرض
                المزيد</button>
        </div>
        <!-- Filter Drawer (Modal) -->
        <div id="filter-overlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden"></div>
        <aside id="filter-drawer" role="dialog" aria-modal="true" aria-labelledby="filter-title"
            class="fixed top-0 right-0 h-full w-80 max-w-[90%] bg-white shadow-xl z-50 translate-x-[115%] pointer-events-none opacity-0 invisible transition-all duration-300 flex flex-col will-change-transform">
            <div class="flex items-center justify-between px-5 py-4 border-b">
                <h3 id="filter-title" class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                    <i class="fas fa-sliders-h text-indigo-900"></i>
                    <span>الفلتر و البحث</span>
                </h3>
                <button id="close-filters"
                    class="w-9 h-9 flex items-center justify-center rounded-md hover:bg-gray-100 text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    aria-label="إغلاق">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="p-5 space-y-8 overflow-y-auto custom-scrollbar">
                <!-- Search -->
                <div>
                    <label class="sr-only" for="search-input">بحث</label>
                    <div class="relative">
                        <input id="search-input" type="text" placeholder="ابحث في المزادات (قريباً)"
                            class="w-full h-12 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-4 text-sm placeholder-gray-400" />
                        <i class="fas fa-search absolute top-1/2 -translate-y-1/2 left-4 text-gray-400 text-sm"></i>
                    </div>
                </div>
                <!-- Type Section -->
                <div>
                    <button type="button"
                        class="w-full flex items-center justify-between text-indigo-950 font-bold mb-3 group"
                        data-collapse-target="#type-body">
                        <span>نوع العمل</span>
                        <i class="fas fa-chevron-down text-xs transition group-[.open]:rotate-180"></i>
                    </button>
                    <div id="type-body" class="space-y-4" data-collapsible>
                        <label class="flex items-center gap-3 text-sm font-medium text-gray-700 cursor-pointer">
                            <input type="checkbox" class="accent-indigo-950 w-4 h-4" checked>
                            <span>التصوير الضوئي</span>
                        </label>
                        <label class="flex items-center gap-3 text-sm font-medium text-gray-700 cursor-pointer">
                            <input type="checkbox" class="accent-indigo-950 w-4 h-4">
                            <span>الفن التشكيلي</span>
                        </label>
                        <label class="flex items-center gap-3 text-sm font-medium text-gray-700 cursor-pointer">
                            <input type="checkbox" class="accent-indigo-950 w-4 h-4">
                            <span>الفن الرقمي</span>
                        </label>
                    </div>
                </div>
                <!-- Price Range (Enhanced) -->
                <div>
                    <button type="button"
                        class="w-full flex items-center justify-between text-indigo-950 font-bold mb-5 group"
                        data-collapse-target="#price-body">
                        <span>السعر</span>
                        <i class="fas fa-chevron-down text-xs transition group-[.open]:rotate-180"></i>
                    </button>
                    <div id="price-body" data-collapsible>
                        <div class="space-y-4">
                            <div class="relative pt-6 pb-4" id="price-track">
                                <div class="relative h-2 bg-gray-200/80 rounded-full">
                                    <div id="price-range-bar"
                                        class="absolute h-full bg-gradient-to-l from-indigo-900 via-indigo-800 to-indigo-600 rounded-full">
                                    </div>
                                    <input id="min-price" type="range" min="0" max="10000"
                                        value="{{ (int) (request()->has('min') ? request('min') : 0) }}" step="10"
                                        aria-label="السعر الأدنى"
                                        class="absolute w-full h-2 top-0 pointer-events-none appearance-none focus:outline-none [&::-webkit-slider-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-5 [&::-webkit-slider-thumb]:h-5 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-white [&::-webkit-slider-thumb]:border-2 [&::-webkit-slider-thumb]:border-indigo-900 [&::-webkit-slider-thumb]:shadow [&::-webkit-slider-thumb]:cursor-pointer [&::-webkit-slider-thumb]:transition" />
                                    <input id="max-price" type="range" min="0" max="10000"
                                        value="{{ (int) (request()->has('max') ? request('max') : 0) }}" step="10"
                                        aria-label="السعر الأعلى"
                                        class="absolute w-full h-2 top-0 pointer-events-none appearance-none focus:outline-none [&::-webkit-slider-thumb]:pointer-events-auto [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:w-5 [&::-webkit-slider-thumb]:h-5 [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:bg-white [&::-webkit-slider-thumb]:border-2 [&::-webkit-slider-thumb]:border-indigo-900 [&::-webkit-slider-thumb]:shadow [&::-webkit-slider-thumb]:cursor-pointer [&::-webkit-slider-thumb]:transition" />
                                    <span id="min-tooltip"
                                        class="absolute -top-6 translate-x-[-50%] px-2 py-0.5 rounded-md bg-indigo-950 text-white text-[10px] leading-none font-medium shadow">50</span>
                                    <span id="max-tooltip"
                                        class="absolute -top-6 translate-x-[-50%] px-2 py-0.5 rounded-md bg-indigo-950 text-white text-[10px] leading-none font-medium shadow">200</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-gray-700">
                                <div class="flex items-center gap-1">
                                    <label for="min-price-input" class="sr-only">السعر الأدنى</label>
                                    <input id="min-price-input" type="number" min="0" max="10000"
                                        value="{{ (int) (request()->has('min') ? request('min') : 0) }}" step="10"
                                        class="w-24 h-10 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-center text-sm">
                                </div>
                                <span class="text-gray-400">—</span>
                                <div class="flex items-center gap-1">
                                    <label for="max-price-input" class="sr-only">السعر الأعلى</label>
                                    <input id="max-price-input" type="number" min="0" max="10000"
                                        value="{{ (int) (request()->has('max') ? request('max') : 0) }}" step="10"
                                        class="w-24 h-10 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-center text-sm">
                                </div>
                                <span class="ml-auto text-[11px] text-gray-500">ريال عماني</span>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-gray-500">
                                <span>الحد الأدنى: <span id="min-price-value"
                                        class="font-semibold text-indigo-900">{{ (int) (request()->has('min') ? request('min') : 0) }}</span></span>
                                <span>الحد الأعلى: <span id="max-price-value"
                                        class="font-semibold text-indigo-900">{{ (int) (request()->has('max') ? request('max') : 0) }}</span></span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Auction State -->
                <div>
                    <button type="button"
                        class="w-full flex items-center justify-between text-indigo-950 font-bold mb-3 group"
                        data-collapse-target="#state-body">
                        <span>حالة المزاد</span>
                        <i class="fas fa-chevron-down text-xs transition group-[.open]:rotate-180"></i>
                    </button>
                    <div id="state-body" class="space-y-4" data-collapsible>
                        @php($active = (array) request('status', []))
                        <label class="flex items-center gap-3 text-sm font-medium text-gray-700 cursor-pointer">
                            <input type="checkbox" class="accent-indigo-950 w-4 h-4" data-status="live" {{ in_array('live', $active) ? 'checked' : '' }}>
                            <span>مباشر</span>
                        </label>
                        <label class="flex items-center gap-3 text-sm font-medium text-gray-700 cursor-pointer">
                            <input type="checkbox" class="accent-indigo-950 w-4 h-4" data-status="ended" {{ in_array('ended', $active) ? 'checked' : '' }}>
                            <span>منتهي</span>
                        </label>
                        <label class="flex items-center gap-3 text-sm font-medium text-gray-700 cursor-pointer">
                            <input type="checkbox" class="accent-indigo-950 w-4 h-4" data-status="scheduled" {{ in_array('scheduled', $active) ? 'checked' : '' }}>
                            <span>قريباً</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="mt-auto p-5 border-t space-y-3">
                <button id="apply-filters"
                    class="w-full h-12 rounded-xl bg-indigo-950 text-white text-sm font-semibold flex items-center justify-center gap-2 hover:bg-indigo-900 transition">
                    تطبيق الفلتر <i class="fas fa-filter text-xs"></i>
                </button>
                <button id="reset-filters"
                    class="w-full h-12 rounded-xl border border-indigo-950 text-indigo-950 text-sm font-semibold flex items-center justify-center gap-2 hover:bg-indigo-50 transition">
                    <i class="fas fa-rotate-right text-xs"></i> إعادة التعيين
                </button>
            </div>
        </aside>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const openBtn = document.getElementById('open-filters');
            const closeBtn = document.getElementById('close-filters');
            const overlay = document.getElementById('filter-overlay');
            const drawer = document.getElementById('filter-drawer');
            const body = document.body;
            // Sort dropdown
            const sortToggle = document.getElementById('sort-toggle');
            const sortMenu = document.getElementById('sort-menu');
            const sortCurrent = document.getElementById('sort-current');
            const sortCaret = document.getElementById('sort-caret');
            const auctionGrid = document.getElementById('auction-grid');

            function closeSortMenu() {
                if (!sortMenu || sortMenu.classList.contains('hidden')) return;
                sortMenu.classList.add('hidden');
                sortToggle.setAttribute('aria-expanded', 'false');
                sortCaret.classList.remove('rotate-180');
            }
            function openSortMenu() {
                sortMenu.classList.remove('hidden');
                sortToggle.setAttribute('aria-expanded', 'true');
                sortCaret.classList.add('rotate-180');
            }
            sortToggle?.addEventListener('click', (e) => {
                e.stopPropagation();
                if (sortMenu.classList.contains('hidden')) openSortMenu(); else closeSortMenu();
            });
            document.addEventListener('click', (e) => {
                if (!sortMenu) return;
                if (!sortMenu.contains(e.target) && !sortToggle.contains(e.target)) closeSortMenu();
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeSortMenu();
            });

            function sortCards(by) {
                if (!auctionGrid) return;
                const cards = [...auctionGrid.querySelectorAll('[data-price]')];
                let cmp;
                switch (by) {
                    case 'price-desc': cmp = (a, b) => parseInt(b.dataset.price) - parseInt(a.dataset.price); break;
                    case 'price-asc': cmp = (a, b) => parseInt(a.dataset.price) - parseInt(b.dataset.price); break;
                    case 'bids-desc': cmp = (a, b) => parseInt(b.dataset.bids) - parseInt(a.dataset.bids); break;
                    case 'latest':
                    default: cmp = (a, b) => parseInt(a.dataset.originalIndex) - parseInt(b.dataset.originalIndex); break;
                }
                cards.sort(cmp).forEach(c => auctionGrid.appendChild(c));
            }
            sortMenu?.querySelectorAll('[data-sort]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const val = btn.getAttribute('data-sort');
                    sortCards(val);
                    sortCurrent.textContent = btn.textContent.trim();
                    closeSortMenu();
                });
            });

            function openDrawer() {
                overlay.classList.remove('hidden');
                drawer.classList.remove('translate-x-[115%]', 'pointer-events-none', 'opacity-0', 'invisible');
                drawer.classList.add('translate-x-0', 'opacity-100');
                body.classList.add('overflow-hidden');
                drawer.focus();
            }
            function closeDrawer() {
                overlay.classList.add('hidden');
                drawer.classList.remove('translate-x-0', 'opacity-100');
                drawer.classList.add('translate-x-[115%]', 'pointer-events-none', 'opacity-0', 'invisible');
                body.classList.remove('overflow-hidden');
            }
            openBtn?.addEventListener('click', openDrawer);
            closeBtn?.addEventListener('click', closeDrawer);
            overlay?.addEventListener('click', closeDrawer);
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeDrawer(); });

            // Collapsibles
            document.querySelectorAll('[data-collapse-target]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const targetSel = btn.getAttribute('data-collapse-target');
                    const target = document.querySelector(targetSel);
                    if (!target) return;
                    const open = target.dataset.open === 'true';
                    target.dataset.open = (!open).toString();
                    btn.classList.toggle('open', !open);
                    if (open) {
                        target.style.maxHeight = target.scrollHeight + 'px';
                        requestAnimationFrame(() => {
                            target.style.maxHeight = '0px';
                        });
                    } else {
                        target.style.maxHeight = target.scrollHeight + 'px';
                    }
                });
            });
            // Initialize collapsibles
            document.querySelectorAll('[data-collapsible]').forEach(div => {
                div.style.overflow = 'hidden';
                div.style.transition = 'max-height 0.35s ease';
                div.style.maxHeight = div.scrollHeight + 'px';
                div.dataset.open = 'true';
            });

            // Price range logic (enhanced)
            const minRange = document.getElementById('min-price');
            const maxRange = document.getElementById('max-price');
            const minValEl = document.getElementById('min-price-value');
            const maxValEl = document.getElementById('max-price-value');
            const bar = document.getElementById('price-range-bar');
            const minInput = document.getElementById('min-price-input');
            const maxInput = document.getElementById('max-price-input');
            const minTooltip = document.getElementById('min-tooltip');
            const maxTooltip = document.getElementById('max-tooltip');
            function clamp(v, min, max) { return Math.min(Math.max(v, min), max); }
            function updatePrice(source) {
                let minV = parseInt(minRange.value);
                let maxV = parseInt(maxRange.value);
                if (minV > maxV) { [minV, maxV] = [maxV, minV]; }
                // Sync ranges if numeric inputs triggered
                if (source === 'minInput') { minV = clamp(parseInt(minInput.value) || 0, 0, parseInt(minRange.max)); minRange.value = minV; }
                if (source === 'maxInput') { maxV = clamp(parseInt(maxInput.value) || 0, 0, parseInt(maxRange.max)); maxRange.value = maxV; }
                // Reflect back to numeric inputs
                minInput.value = minV; maxInput.value = maxV;
                // Update labels
                minValEl.textContent = minV; maxValEl.textContent = maxV;
                const percentMin = (minV / parseInt(minRange.max)) * 100;
                const percentMax = (maxV / parseInt(maxRange.max)) * 100;
                bar.style.left = percentMin + '%';
                bar.style.right = (100 - percentMax) + '%';
                // Tooltips positions
                const track = document.getElementById('price-track');
                if (track) {
                    minTooltip.style.left = percentMin + '%';
                    maxTooltip.style.left = percentMax + '%';
                    minTooltip.textContent = minV;
                    maxTooltip.textContent = maxV;
                }
            }
            ['input', 'change'].forEach(ev => {
                minRange?.addEventListener(ev, () => updatePrice());
                maxRange?.addEventListener(ev, () => updatePrice());
                minInput?.addEventListener(ev, () => updatePrice('minInput'));
                maxInput?.addEventListener(ev, () => updatePrice('maxInput'));
            });
            updatePrice();

            // Reset button
            document.getElementById('reset-filters')?.addEventListener('click', () => {
                minRange.value = 50; maxRange.value = 200; updatePrice();
                document.querySelectorAll('#filter-drawer input[type=checkbox]').forEach(ch => ch.checked = false);
            });

            // Helper to set or replace hidden status[] inputs
            function setStatuses(statuses) {
                const form = document.getElementById('filters-form');
                [...form.querySelectorAll('input[name="status[]"]')].forEach(i => i.remove());
                statuses.forEach(s => {
                    const i = document.createElement('input');
                    i.type = 'hidden'; i.name = 'status[]'; i.value = s; form.appendChild(i);
                });
            }

            // Apply button: push values to GET form and submit
            document.getElementById('apply-filters')?.addEventListener('click', () => {
                const statuses = [...document.querySelectorAll('#state-body input[type=checkbox][data-status]:checked')].map(ch => ch.getAttribute('data-status'));
                setStatuses(statuses);
                const minV = parseInt(document.getElementById('min-price').value || '0');
                const maxV = parseInt(document.getElementById('max-price').value || '0');
                document.getElementById('q-min').value = (!isNaN(minV) && minV > 0) ? String(minV) : '';
                document.getElementById('q-max').value = (!isNaN(maxV) && maxV > 0) ? String(maxV) : '';
                document.getElementById('filters-form').submit();
                closeDrawer();
            });

            // Sort integrates with query params
            sortMenu?.querySelectorAll('[data-sort]')?.forEach(btn => {
                btn.addEventListener('click', () => {
                    const val = btn.getAttribute('data-sort');
                    document.getElementById('q-sort').value = val;
                    // Clear zero defaults so backend doesn't treat 0 as active filter
                    const qMin = document.getElementById('q-min');
                    const qMax = document.getElementById('q-max');
                    if (qMin && (qMin.value === '0' || qMin.value === 0)) qMin.value = '';
                    if (qMax && (qMax.value === '0' || qMax.value === 0)) qMax.value = '';
                    document.getElementById('filters-form').submit();
                });
            });

            // Initialize price bar on load with current min/max
            updatePrice();
        });
    </script>
</x-layout>