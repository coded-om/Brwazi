<x-layout>
    {{-- section Publishing houses / literary publishers --}}
    <section class="  py-20 sm:py-8 lg:my-20 ">
        <div class="max-w-6xl mx-auto px-4 sm:px-6" data-aos="fade-up">
            <div class="flex flex-row sm:flex-row items-center justify-between  pb-4  gap-4" data-aos="fade-up">
                <!-- left: tools (keep on left on desktop, top on mobile) -->


                <!-- center: title -->
                <div class="flex-1 w-full">
                    <div class="gap-1 flex items-center justify-center">
                        <img src="{{ asset('imgs/icons-color/books.svg') }}" alt="" class="w-8 h-8 sm:w-auto"
                            loading="lazy" decoding="async">
                        <h2 class="text-center text-xl sm:text-3xl font-bold text-indigo-900 arabic-font-bold mx-2">
                            القسم
                            الأدبي</h2>
                    </div>
                </div>

                <!-- right: view & sort controls (desktop) and mobile menu button -->
                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <div class="hidden sm:flex items-center gap-3">
                        <select id="literary-sort" data-action="sort"
                            class="text-sm py-2 px-3 rounded-md border border-gray-200 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-200">
                            <option value="newest">ترتيب حسب : الأحدث</option>
                            <option value="alpha">الترتيب أبجدي</option>
                            <option value="alpha-desc">الترتيب أبجدي (تنازلي)</option>
                        </select>
                        <button
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg  shadow-sm hover:shadow-md bg-[#141640]">
                            <i class="fas fa-th-large text-white"></i>
                        </button>
                        <button
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white shadow-sm hover:shadow-md">
                            <i class="fas fa-list text-gray-500"></i>
                        </button>

                    </div>

                    <!-- mobile menu button (shows sort + view) -->
                    <div class="sm:hidden">
                        <button id="literary-mobile-menu" aria-expanded="false"
                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white shadow-sm">
                            <i class="fas fa-ellipsis-v text-gray-700"></i>
                        </button>
                    </div>
                </div>
            </div>
            <hr class="mb-5 sm:mb-14 ">

            <!-- Filter Drawer Modal -->
            <div id="filter-overlay" class="fixed inset-0 z-[60] hidden opacity-0 transition-opacity duration-300">
                <div id="filter-backdrop"
                    class="absolute inset-0 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-300">
                </div>
                <aside
                    class="absolute right-0 top-0 h-full w-80 sm:w-96 bg-white shadow-xl rounded-l-2xl p-5 overflow-y-auto translate-x-full transition-transform duration-300 will-change-transform"
                    aria-label="لوحة الفرز" aria-modal="true" role="dialog">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-indigo-950 flex items-center gap-2">
                            <i class="fas fa-sliders-h"></i>
                            الفرز والبحث
                        </h3>
                        <button id="close-filter"
                            class="w-9 h-9 inline-flex items-center justify-center rounded-lg bg-gray-100  text-black focus:ring-2 focus:ring-indigo-300 focus:outline-none">
                            <i class="fas fa-times text-gray-600"></i>
                        </button>
                    </div>
                    <div class="space-y-6">
                        <!-- Search -->
                        <div>
                            <input id="f-search" type="text" placeholder="ابحث عن كتاب أو مؤلف..."
                                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-200" />
                        </div>
                        <!-- Categories (chip-style for better UX) -->
                        <div>
                            <button type="button"
                                class="w-full flex items-center justify-between py-2 text-indigo-950 font-semibold"
                                data-acc="cats">
                                التصنيفات
                                <i class="fas fa-chevron-down text-sm"></i>
                            </button>
                            <div data-acc-panel="cats" class="mt-2">
                                <div class="flex flex-wrap gap-3">
                                    <button id="f-cat-all" type="button" data-value="all" aria-pressed="true"
                                        class="cat-chip px-4 py-2 rounded-full bg-indigo-950 text-white text-sm">جميع
                                        الكتب</button>
                                    <button type="button" data-value="الشعر" aria-pressed="false"
                                        class="cat-chip px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm">شعر</button>
                                    <button type="button" data-value="الرواية" aria-pressed="false"
                                        class="cat-chip px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm">الرواية</button>
                                    <button type="button" data-value="القصة القصيرة" aria-pressed="false"
                                        class="cat-chip px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm">القصة
                                        القصيرة</button>
                                    <button type="button" data-value="المسرح" aria-pressed="false"
                                        class="cat-chip px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm">المسرح</button>
                                    <button type="button" data-value="النقد الأدبي" aria-pressed="false"
                                        class="cat-chip px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-sm">النقد
                                        الأدبي</button>
                                </div>
                            </div>
                        </div>

                        <!-- Price -->
                        <div>
                            <button type="button"
                                class="w-full flex items-center justify-between py-2 text-indigo-950 font-semibold"
                                data-acc="price">
                                السعر
                                <i class="fas fa-chevron-down text-sm"></i>
                            </button>
                            <div data-acc-panel="price" class="mt-3">
                                <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                                    <span><span id="f-price-min-val">0</span> ريال</span>
                                    <span><span id="f-price-max-val">200</span> ريال</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <input id="f-price-min" type="range" min="0" max="200" value="0" class="w-full">
                                    <input id="f-price-max" type="range" min="0" max="200" value="200" class="w-full">
                                </div>
                            </div>
                        </div>

                        <!-- Language -->
                        <div>
                            <button type="button"
                                class="w-full flex items-center justify-between py-2 text-indigo-950 font-semibold"
                                data-acc="lang">
                                اللغة
                                <i class="fas fa-chevron-down text-sm"></i>
                            </button>
                            <div data-acc-panel="lang" class="space-y-3 mt-2">
                                <label class="flex items-center gap-3">
                                    <input id="f-lang-ar" value="ar" type="checkbox" class="accent-indigo-950"
                                        checked />
                                    <span>العربية</span>
                                </label>
                                <label class="flex items-center gap-3">
                                    <input id="f-lang-en" value="en" type="checkbox" class="accent-indigo-950" />
                                    <span>الإنجليزية</span>
                                </label>
                                <label class="flex items-center gap-3">
                                    <input id="f-lang-fr" value="fr" type="checkbox" class="accent-indigo-950" />
                                    <span>الفرنسية</span>
                                </label>
                            </div>
                        </div>

                        <!-- Year -->
                        <div>
                            <button type="button"
                                class="w-full flex items-center justify-between py-2 text-indigo-950 font-semibold"
                                data-acc="year">
                                سنة النشر
                                <i class="fas fa-chevron-down text-sm"></i>
                            </button>
                            <div data-acc-panel="year" class="mt-3">
                                <input id="f-year-min" type="range" min="2000" max="2025" value="2023" class="w-full">
                                <div class="text-center text-sm text-gray-600 mt-1" id="f-year-min-val">2023</div>
                            </div>
                        </div>

                        <div class="space-y-3 pt-2">
                            <button id="f-apply"
                                class="w-full rounded-2xl bg-indigo-950 text-white py-3 font-semibold flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <i class="fas fa-check"></i>
                                تم (إغلاق)
                            </button>
                            <button id="f-reset"
                                class="w-full rounded-2xl border border-gray-300 text-indigo-950 py-3 font-semibold flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                <i class="fas fa-undo"></i>
                                اعادة ضبط الفرز
                            </button>
                            <div id="f-live-indicator" class="text-center text-xs text-gray-500 hidden">تم التحديث</div>
                        </div>
                    </div>
                </aside>
            </div>

            <!-- mobile panel: appears only on small screens when menu button is toggled -->
            <div id="literary-mobile-panel" class="sm:hidden mt-3 hidden bg-white rounded-lg shadow-sm p-3">
                <div class="flex items-center gap-2">
                    <button class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-[#141640] text-white">
                        <i class="fas fa-th-large"></i>
                    </button>
                    <button class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-white border">
                        <i class="fas fa-list text-gray-700"></i>
                    </button>
                    <div class="">
                        <label for="books-type" class="sr-only">نوع الكتاب</label>
                        <select id="books-type"
                            class="w-full text-sm py-2 px-3 rounded-md border border-gray-200 bg-white">
                            <option value="all">كل الأنواع</option>
                            <option value="novel">رواية</option>
                            <option value="poetry">شعر</option>
                            <option value="children">أطفال</option>
                            <option value="history">تاريخ</option>
                            <option value="science">علوم</option>
                            <option value="religion">ديني</option>
                            <option value="education">تعليمي</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- Horizontal Cards (scroll-snap) -->
            <div class="relative" data-aos="fade-up" data-aos-delay="100">
                <a href="literary/all" class="flex gap-1 my-5 text-2xl font-bold hover:underline "><span>
                        <img src="{{ asset('imgs/icons-color/books2.svg') }}" alt="" loading="lazy" decoding="async">
                    </span>دور
                    النشر
                </a>
                <div id="literary-scroll" class="overflow-x-auto scrollbar-hide snap-x snap-mandatory -mx-4 px-4 ">
                    <div class="flex gap-6 items-stretch p-2">
                        @forelse($publishers as $idx => $pub)
                            <div data-original-index="{{ $idx }}"
                                class="snap-start flex-none w-56 sm:w-64 lg:w-72 bg-white rounded-xl shadow-md p-6 flex flex-col items-center text-center"
                                data-aos="fade-up" data-aos-delay="{{ $idx * 60 }}">
                                <div
                                    class="w-28 h-28 sm:w-32 sm:h-32 rounded-xl bg-gray-100 flex items-center justify-center overflow-hidden mb-4">
                                    <img src="{{ $pub->logo_path ? asset('storage/' . ltrim($pub->logo_path, '/')) : asset('imgs/literary/lirt1.png') }}"
                                        alt="{{ $pub->name }}" loading="lazy" decoding="async"
                                        class="w-full h-full object-contain">
                                </div>
                                <h3 class="text-lg font-bold text-indigo-900">{{ $pub->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $pub->type ?? 'دار نشر' }}</p>
                            </div>
                        @empty
                            <div class="text-gray-500">لا يوجد ناشرون.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Pagination / controls -->
            <div class="flex items-center justify-center gap-4 mt-6">
                <button id="literary-prev" aria-label="السابق"
                    class="w-9 h-9 rounded-full bg-white shadow-sm flex items-center justify-center">
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </button>

                <div id="literary-dots" class="flex gap-2 items-center">
                    <!-- dots will be generated by JS -->
                </div>

                <button id="literary-next" aria-label="التالي"
                    class="w-9 h-9 rounded-full bg-white shadow-sm flex items-center justify-center">
                    <i class="fas fa-chevron-left text-gray-400"></i>
                </button>
            </div>
        </div>
    </section>
    <!-- show books  -->
    <section class="py-12 bg-white relative" data-aos="fade-up">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between mb-10 sticky top-16 z-40 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80"
                data-aos="fade-up">
                <h3 class="text-2xl font-bold text-indigo-900 arabic-font-bold flex gap-2"><img
                        src="{{ asset('imgs/icons-color/peper-category.svg') }}" alt="" loading="lazy"
                        decoding="async">الكتب</h3>
                <div class=" flex items-center gap-3 ">
                    <!-- add heaer  -->
                    <div class="flex items-center gap-3 w-full sm:w-auto ">
                        <button id="open-filter" aria-controls="filter-overlay"
                            class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            <i class="fas fa-sliders-h text-gray-500"></i>
                        </button>
                    </div>
                    <div class="flex flex-col gap-2 w-full">
                        <label for="books-type-mobile" class="sr-only">نوع الكتاب</label>
                        <select id="books-type-mobile"
                            class="w-full text-sm py-2 px-3 rounded-md border border-gray-200 bg-white">
                            <option value="all">كل الأنواع</option>
                            <option value="novel">رواية</option>
                            <option value="poetry">شعر</option>
                            <option value="children">أطفال</option>
                            <option value="history">تاريخ</option>
                            <option value="science">علوم</option>
                            <option value="religion">ديني</option>
                            <option value="education">تعليمي</option>
                        </select>
                    </div>
                    <!-- <a href="/literary/books" class="text-sm text-indigo-700 hover:underline">عرض الكل</a> -->
                </div>

            </div>
            <div class="grid grid-cols-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-2 sm:gap-4 md:gap-6"
                id="books-grid" data-aos="fade-up" data-aos-delay="100">
                @forelse($books as $b)
                    @php
                        $price = (int) $b->price_omr;
                        $discount = $b->compare_at_price_omr && $b->compare_at_price_omr > $b->price_omr;
                        $authorName = $b->authors->first()->name ?? '—';
                    @endphp
                    <a href="{{ url('literary/book/' . $b->id) }}"
                        class="relative bg-gray-50 rounded-2xl p-2 sm:p-4 md:p-6 shadow-sm flex flex-col justify-between book-card"
                        data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}" data-type="{{ $b->type ?? 'novel' }}"
                        data-category="{{ $b->categories->first()->name ?? '' }}" data-lang="{{ $b->language }}"
                        data-price="{{ (int) $b->price_omr }}" data-year="{{ (int) ($b->publish_year ?? 0) }}">
                        @if($discount)
                            <span
                                class="absolute top-3 left-3 bg-pink-100 text-pink-600 text-xs px-2 py-1 rounded-full">عرض</span>
                        @endif
                        <div>
                            <div
                                class="w-16 h-24 sm:w-24 sm:h-32 md:w-32 md:h-40 bg-white rounded-lg overflow-hidden shadow-sm m-auto">
                                <img src="{{ $b->cover_image_path ? asset('storage/' . ltrim($b->cover_image_path, '/')) : asset('imgs/pic/Book.png') }}"
                                    alt="{{ $b->title }}" loading="lazy" decoding="async"
                                    class="w-full h-full object-cover">
                            </div>
                            <div class="mt-3">
                                <h4 class="text-sm sm:text-base md:text-lg font-semibold text-gray-900 arabic-font">
                                    {{ $b->title }}</h4>
                                <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ $authorName }}</p>
                            </div>
                        </div>
                        <div class="mt-2">
                            <div class="flex items-center gap-2">
                                @if($discount)
                                    <div class="text-lg font-bold text-indigo-900">{{ (float) $b->price_omr }} ر.ع</div>
                                    <div class="text-sm text-gray-400 line-through">{{ (float) $b->compare_at_price_omr }} ر.ع
                                    </div>
                                @else
                                    <div class="text-lg font-bold text-indigo-900">{{ (float) $b->price_omr }} ر.ع</div>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 my-1">
                                <button
                                    class="px-0 sm:px-4 flex-1 py-2 bg-indigo-950 text-white rounded-lg text-xs sm:text-sm arabic-font shadow hover:bg-indigo-800 transition">شراء
                                    الآن</button>
                                <button
                                    class="w-10 h-10 rounded-lg bg-white border flex items-center justify-center shadow-sm"
                                    aria-label="إضافة للمفضلة">
                                    <i class="fas fa-shopping-cart text-gray-700"></i>
                                </button>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-4 text-center text-gray-500">لا توجد كتب حالياً.</div>
                @endforelse
            </div>
            <div id="books-empty" class="hidden text-center text-gray-500 py-8">لا توجد نتائج مطابقة للفلتر الحالي.
            </div>

            <script>
                // Filter Drawer behavior and dynamic filtering logic (improved)
                document.addEventListener('DOMContentLoaded', function () {
                    const overlay = document.getElementById('filter-overlay');
                    const backdrop = document.getElementById('filter-backdrop');
                    const openBtn = document.getElementById('open-filter');
                    const closeBtn = document.getElementById('close-filter');
                    const drawer = overlay?.querySelector('aside');
                    const grid = document.getElementById('books-grid');
                    const emptyMsg = document.getElementById('books-empty');
                    const filterCountBadge = document.getElementById('filter-count');
                    const liveIndicator = document.getElementById('f-live-indicator');
                    const cards = grid ? Array.from(grid.querySelectorAll('.book-card')) : [];

                    if (!overlay || !openBtn || !grid) return;

                    // Elements
                    const fSearch = document.getElementById('f-search');
                    const catChips = Array.from(document.querySelectorAll('.cat-chip')).filter(Boolean);
                    const priceMin = document.getElementById('f-price-min');
                    const priceMax = document.getElementById('f-price-max');
                    const priceMinVal = document.getElementById('f-price-min-val');
                    const priceMaxVal = document.getElementById('f-price-max-val');
                    const langChecks = ['f-lang-ar', 'f-lang-en', 'f-lang-fr'].map(id => document.getElementById(id)).filter(Boolean);
                    const yearMin = document.getElementById('f-year-min');
                    const yearMinVal = document.getElementById('f-year-min-val');
                    const applyBtn = document.getElementById('f-apply');
                    const resetBtn = document.getElementById('f-reset');
                    const booksType = document.getElementById('books-type');
                    const booksTypeMobile = document.getElementById('books-type-mobile');

                    let lastFocusedBeforeOpen = null;
                    let debounceTimer = null;

                    function trapFocus(e) {
                        if (!overlay || overlay.classList.contains('hidden')) return;
                        if (!drawer.contains(e.target)) {
                            const focusable = drawer.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                            if (focusable.length) focusable[0].focus();
                        }
                    }

                    function animateOpen() {
                        overlay.classList.remove('hidden');
                        requestAnimationFrame(() => {
                            overlay.classList.remove('opacity-0');
                            backdrop.classList.remove('opacity-0');
                            drawer.classList.remove('translate-x-full');
                        });
                        document.body.style.overflow = 'hidden';
                        lastFocusedBeforeOpen = document.activeElement;
                        setTimeout(() => { drawer.querySelector('input,button,select')?.focus(); }, 310);
                    }
                    function animateClose() {
                        overlay.classList.add('opacity-0');
                        backdrop.classList.add('opacity-0');
                        drawer.classList.add('translate-x-full');
                        document.body.style.overflow = '';
                        setTimeout(() => { overlay.classList.add('hidden'); lastFocusedBeforeOpen?.focus(); }, 300);
                    }

                    function openOverlay() { animateOpen(); }
                    function closeOverlay() { animateClose(); }

                    openBtn.addEventListener('click', openOverlay);
                    closeBtn && closeBtn.addEventListener('click', closeOverlay);
                    backdrop && backdrop.addEventListener('click', closeOverlay);
                    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeOverlay(); });
                    document.addEventListener('focus', trapFocus, true);

                    // Accordions toggle hidden state
                    document.querySelectorAll('[data-acc]').forEach(btn => {
                        const id = btn.getAttribute('data-acc');
                        const panel = document.querySelector(`[data-acc-panel="${id}"]`);
                        if (!panel) return;
                        btn.addEventListener('click', () => panel.classList.toggle('hidden'));
                    });

                    function syncPriceLabels() {
                        let min = parseInt(priceMin.value, 10); let max = parseInt(priceMax.value, 10);
                        if (min > max) { [min, max] = [max, min]; priceMin.value = min; priceMax.value = max; }
                        priceMinVal.textContent = min; priceMaxVal.textContent = max;
                    }
                    function syncYearLabel() { yearMinVal.textContent = yearMin.value; }
                    syncPriceLabels(); syncYearLabel();

                    // Category chip logic (with dynamic update)
                    catChips.forEach(chip => {
                        chip.addEventListener('click', () => {
                            const val = chip.getAttribute('data-value');
                            if (val === 'all') {
                                catChips.forEach(c => {
                                    const isAll = c === chip; c.setAttribute('aria-pressed', isAll ? 'true' : 'false');
                                    c.classList.toggle('bg-indigo-950', isAll); c.classList.toggle('text-white', isAll);
                                    c.classList.toggle('bg-gray-100', !isAll); c.classList.toggle('text-gray-700', !isAll);
                                });
                            } else {
                                const isPressed = chip.getAttribute('aria-pressed') === 'true';
                                chip.setAttribute('aria-pressed', String(!isPressed));
                                chip.classList.toggle('bg-indigo-950', !isPressed); chip.classList.toggle('text-white', !isPressed);
                                chip.classList.toggle('bg-gray-100', isPressed); chip.classList.toggle('text-gray-700', isPressed);
                                const anySelected = catChips.some(c => c.getAttribute('data-value') !== 'all' && c.getAttribute('aria-pressed') === 'true');
                                const allChip = catChips.find(c => c.getAttribute('data-value') === 'all');
                                if (allChip) {
                                    allChip.setAttribute('aria-pressed', anySelected ? 'false' : 'true');
                                    allChip.classList.toggle('bg-indigo-950', !anySelected);
                                    allChip.classList.toggle('text-white', !anySelected);
                                    allChip.classList.toggle('bg-gray-100', anySelected);
                                    allChip.classList.toggle('text-gray-700', anySelected);
                                }
                            }
                            triggerFilter();
                        });
                    });

                    function selectedCategories() {
                        if (!catChips.length) return null;
                        const allChip = catChips.find(c => c.getAttribute('data-value') === 'all');
                        if (allChip && allChip.getAttribute('aria-pressed') === 'true') return null;
                        const sel = catChips.filter(c => c.getAttribute('data-value') !== 'all' && c.getAttribute('aria-pressed') === 'true').map(c => c.getAttribute('data-value'));
                        return sel.length ? sel : null;
                    }

                    function filterBooks() {
                        const q = (fSearch?.value || '').toLowerCase().trim();
                        const categories = selectedCategories();
                        const langs = langChecks.filter(c => c.checked).map(c => c.value);
                        const minP = parseInt(priceMin.value, 10);
                        const maxP = parseInt(priceMax.value, 10);
                        const minYear = parseInt(yearMin.value, 10);
                        let visibleCount = 0;
                        cards.forEach(card => {
                            const title = (card.querySelector('h4')?.textContent || '').toLowerCase();
                            const author = (card.querySelector('p')?.textContent || '').toLowerCase();
                            const category = card.dataset.category || '';
                            const lang = card.dataset.lang || '';
                            const price = parseInt(card.dataset.price || '0', 10);
                            const year = parseInt(card.dataset.year || '0', 10);
                            let show = true;
                            if (q && !(title.includes(q) || author.includes(q))) show = false;
                            if (show && categories && categories.length && !categories.includes(category)) show = false;
                            if (show && langs.length && !langs.includes(lang)) show = false;
                            if (show && (price < minP || price > maxP)) show = false;
                            if (show && year < minYear) show = false;
                            card.style.display = show ? '' : 'none';
                            if (show) visibleCount++;
                        });
                        emptyMsg && emptyMsg.classList.toggle('hidden', visibleCount !== 0);
                        updateActiveCount();
                        if (liveIndicator) {
                            liveIndicator.classList.remove('hidden');
                            liveIndicator.textContent = 'تم التحديث (' + visibleCount + ' نتيجة)';
                            clearTimeout(liveIndicator._t);
                            liveIndicator._t = setTimeout(() => liveIndicator.classList.add('hidden'), 1200);
                        }
                    }

                    function updateActiveCount() {
                        let count = 0;
                        const defPriceMin = parseInt(priceMin.min || '0', 10);
                        const defPriceMax = parseInt(priceMax.max || '200', 10);
                        const defYear = 2023; // current default in template
                        if (fSearch && fSearch.value.trim()) count++;
                        const cats = selectedCategories(); if (cats) count += cats.length;
                        const langsChecked = langChecks.filter(c => c.checked).map(c => c.value);
                        if (langsChecked.length && langsChecked.length !== langChecks.length) count++;
                        if (parseInt(priceMin.value, 10) !== defPriceMin || parseInt(priceMax.value, 10) !== defPriceMax) count++;
                        if (parseInt(yearMin.value, 10) !== defYear) count++;
                        if (count > 0) {
                            filterCountBadge.textContent = count > 99 ? '99+' : count;
                            filterCountBadge.classList.remove('hidden');
                        } else {
                            filterCountBadge.classList.add('hidden');
                        }
                    }

                    function triggerFilter() {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(filterBooks, 150);
                    }

                    // Event wiring for dynamic updates
                    fSearch && fSearch.addEventListener('input', triggerFilter);
                    priceMin && priceMin.addEventListener('input', () => { syncPriceLabels(); triggerFilter(); });
                    priceMax && priceMax.addEventListener('input', () => { syncPriceLabels(); triggerFilter(); });
                    yearMin && yearMin.addEventListener('input', () => { syncYearLabel(); triggerFilter(); });
                    langChecks.forEach(c => c.addEventListener('change', triggerFilter));

                    // Apply just closes drawer now (filter already live)
                    applyBtn && applyBtn.addEventListener('click', (e) => { e.preventDefault(); closeOverlay(); });
                    resetBtn && resetBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        if (fSearch) fSearch.value = '';
                        // reset categories
                        catChips.forEach(c => {
                            const isAll = c.getAttribute('data-value') === 'all';
                            c.setAttribute('aria-pressed', isAll ? 'true' : 'false');
                            c.classList.toggle('bg-indigo-950', isAll); c.classList.toggle('text-white', isAll);
                            c.classList.toggle('bg-gray-100', !isAll); c.classList.toggle('text-gray-700', !isAll);
                        });
                        // reset price
                        priceMin.value = priceMin.min || 0; priceMax.value = priceMax.max || 200; syncPriceLabels();
                        // reset languages (example: only Arabic checked by default)
                        langChecks.forEach(c => c.checked = (c.id === 'f-lang-ar'));
                        // reset year
                        yearMin.value = 2023; syncYearLabel();
                        // neutralize selects (not used as primary filters here)
                        if (booksType) { booksType.value = 'all'; booksType.dispatchEvent(new Event('change')); }
                        if (booksTypeMobile) { booksTypeMobile.value = 'all'; booksTypeMobile.dispatchEvent(new Event('change')); }
                        triggerFilter();
                    });

                    // Initial state
                    filterBooks();
                });
            </script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const booksType = document.getElementById('books-type');
                    const booksTypeMobile = document.getElementById('books-type-mobile');
                    const grid = document.getElementById('books-grid');
                    if (!grid) return;
                    const cards = Array.from(grid.querySelectorAll('.book-card'));

                    function filterByType(type) {
                        cards.forEach(c => {
                            const t = c.dataset.type || 'all';
                            if (type === 'all' || type === t) {
                                c.style.display = '';
                            } else {
                                c.style.display = 'none';
                            }
                        });
                    }

                    // sync desktop/mobile selects if present
                    if (booksType && booksTypeMobile) {
                        booksType.addEventListener('change', () => {
                            booksTypeMobile.value = booksType.value;
                            filterByType(booksType.value);
                        });
                        booksTypeMobile.addEventListener('change', () => {
                            booksType.value = booksTypeMobile.value;
                            filterByType(booksTypeMobile.value);
                        });
                        // initial sync
                        booksTypeMobile.value = booksType.value;
                        filterByType(booksType.value);
                    } else if (booksType) {
                        booksType.addEventListener('change', () => filterByType(booksType.value));
                        filterByType(booksType.value);
                    } else if (booksTypeMobile) {
                        booksTypeMobile.addEventListener('change', () => filterByType(booksTypeMobile.value));
                        filterByType(booksTypeMobile.value);
                    }
                });
            </script>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const scroller = document.getElementById('literary-scroll');
            const prevBtn = document.getElementById('literary-prev');
            const nextBtn = document.getElementById('literary-next');
            const dotsWrap = document.getElementById('literary-dots');
            const sortSelect = document.getElementById('literary-sort');
            if (!scroller || !dotsWrap) return;

            const cardsWrap = scroller.querySelector('.flex');
            let cards = Array.from(cardsWrap.children);
            // ensure data-original-index exists
            cards.forEach((c, i) => {
                if (!c.dataset.originalIndex) c.dataset.originalIndex = i;
            });

            let current = 0;
            let timer = null;

            function renderDots() {
                dotsWrap.innerHTML = '';
                cards.forEach((c, i) => {
                    const b = document.createElement('button');
                    b.type = 'button';
                    b.className = 'w-2 h-2 rounded-full bg-gray-300';
                    b.setAttribute('aria-label', `الشريحة ${i + 1}`);
                    b.addEventListener('click', () => scrollTo(i));
                    dotsWrap.appendChild(b);
                });
                updateDots();
            }

            function updateDots() {
                const dots = Array.from(dotsWrap.children);
                dots.forEach((d, i) => {
                    d.classList.toggle('bg-indigo-900', i === current);
                    d.classList.toggle('bg-gray-300', i !== current);
                });
            }

            function scrollTo(index) {
                index = Math.max(0, Math.min(cards.length - 1, index));
                const card = cards[index];
                if (!card) return;
                scroller.scrollTo({ left: card.offsetLeft - scroller.offsetLeft, behavior: 'smooth' });
                current = index;
                updateDots();
            }

            prevBtn && prevBtn.addEventListener('click', () => scrollTo(current - 1));
            nextBtn && nextBtn.addEventListener('click', () => scrollTo(current + 1));

            scroller.addEventListener('scroll', () => {
                if (timer) clearTimeout(timer);
                timer = setTimeout(() => {
                    const left = scroller.scrollLeft;
                    let closest = 0;
                    let minDiff = Infinity;
                    cards.forEach((c, i) => {
                        const diff = Math.abs(c.offsetLeft - scroller.offsetLeft - left);
                        if (diff < minDiff) {
                            minDiff = diff;
                            closest = i;
                        }
                    });
                    current = closest;
                    updateDots();
                }, 80);
            }, { passive: true });

            // Sorting handler
            if (sortSelect) {
                sortSelect.addEventListener('change', () => {
                    const val = sortSelect.value;
                    if (val === 'newest') {
                        cards.sort((a, b) => a.dataset.originalIndex - b.dataset.originalIndex);
                    } else if (val === 'alpha') {
                        cards.sort((a, b) => a.querySelector('h3').textContent.trim().localeCompare(b.querySelector('h3').textContent.trim(), 'ar'));
                    } else if (val === 'alpha-desc') {
                        cards.sort((a, b) => b.querySelector('h3').textContent.trim().localeCompare(a.querySelector('h3').textContent.trim(), 'ar'));
                    }
                    // re-append in new order
                    cards.forEach(c => cardsWrap.appendChild(c));
                    // re-render dots and reset
                    renderDots();
                    scrollTo(0);
                });
            }

            // initial render
            renderDots();
        });
    </script>
    <script>
        // Mobile menu toggle and sync selects
        document.addEventListener('DOMContentLoaded', function () {
            const mobileBtn = document.getElementById('literary-mobile-menu');
            const mobilePanel = document.getElementById('literary-mobile-panel');
            const desktopSelect = document.getElementById('literary-sort');
            const mobileSelect = document.getElementById('literary-sort-mobile');

            if (mobileBtn && mobilePanel) {
                mobileBtn.addEventListener('click', () => {
                    const open = mobilePanel.classList.toggle('hidden');
                    mobileBtn.setAttribute('aria-expanded', String(!open));
                    // toggle visibility class instead of rely on display
                });
            }

            // sync selects
            if (desktopSelect && mobileSelect) {
                mobileSelect.value = desktopSelect.value;
                mobileSelect.addEventListener('change', () => {
                    desktopSelect.value = mobileSelect.value;
                    desktopSelect.dispatchEvent(new Event('change'));
                });
                desktopSelect.addEventListener('change', () => {
                    mobileSelect.value = desktopSelect.value;
                });
            }
        });
    </script>
</x-layout>
