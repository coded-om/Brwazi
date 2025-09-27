<x-layout>
    {{-- section Publishing houses / literary publishers --}}
    <section class="  py-20 sm:py-8 lg:my-20 ">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">

            <!-- Publishers Listing (Enhanced Grid/List) -->
            <div class="relative">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between  mb-8">
                    <div class="flex items-center gap-3 ">
                        <div class="flex-2 ">
                            <h2
                                class="text-center text-2xl px-4 sm:text-3xl font-bold text-indigo-950 arabic-font-bold flex items-center justify-center gap-2">
                                <img src="{{ asset('imgs/icons-color/books2.svg') }}" alt="أيقونة دور النشر"
                                    class="w-8 h-8">
                                <span>دور النشر</span>
                            </h2>
                        </div>
                        <div class="relative">
                            <input id="publisher-search" type="text" placeholder="ابحث عن دار نشر..."
                                class="peer w-56 sm:w-72 rounded-xl border border-gray-200 bg-white/70 backdrop-blur px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200 focus:border-indigo-300" />
                            <span
                                class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 peer-focus:text-indigo-500"><i
                                    class="fas fa-search text-sm"></i></span>
                        </div>
                        <div class="hidden md:flex gap-2" id="type-filters">
                            <button data-type="all"
                                class="px-3 py-1.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-medium hover:bg-indigo-100">الكل</button>
                            <button data-type="مكتبة"
                                class="px-3 py-1.5 rounded-full bg-white border text-gray-600 text-xs font-medium hover:bg-indigo-50">مكتبة</button>
                            <button data-type="دار نشر"
                                class="px-3 py-1.5 rounded-full bg-white border text-gray-600 text-xs font-medium hover:bg-indigo-50">دار
                                نشر</button>
                            <button data-type="مؤسسة"
                                class="px-3 py-1.5 rounded-full bg-white border text-gray-600 text-xs font-medium hover:bg-indigo-50">مؤسسة</button>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 order-3">
                        <select id="publisher-sort"
                            class="text-sm py-2.5 px-3 rounded-lg border border-gray-200 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-200">
                            <option value="default">الترتيب الافتراضي</option>
                            <option value="alpha">الترتيب أبجدي</option>
                            <option value="alpha-desc">أبجدي (عكسي)</option>
                        </select>
                        <span id="publisher-count"
                            class="text-xs font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded-lg">0 نتيجة</span>
                    </div>
                </div>
                <!-- !! remove this to backend  -->
                @php
                    $publishers = [
                        ['name' => 'مكتبة الرواحي العامة', 'type' => 'مكتبة', 'logo' => 'imgs/literary/lirt1.png', 'location' => 'مسقط', 'since' => 1999],
                        ['name' => 'محبوب', 'type' => 'مكتبة', 'logo' => 'imgs/literary/lirt2.png', 'location' => 'صلالة', 'since' => 2005],
                        ['name' => 'متحف إبــرا', 'type' => 'دار نشر', 'logo' => 'imgs/literary/lirt2.png', 'location' => 'إبرا', 'since' => 2012],
                        ['name' => 'ذاكرة عُمــان', 'type' => 'دار نشر', 'logo' => 'imgs/literary/lirt3.png', 'location' => 'مسقط', 'since' => 2010],
                        ['name' => 'وزارة التنمية الاجتماعية', 'type' => 'مؤسسة', 'logo' => 'imgs/literary/lirt2.png', 'location' => 'مسقط', 'since' => 1990],
                        ['name' => 'مركز الأوراق', 'type' => 'مكتبة', 'logo' => 'imgs/literary/lirt1.png', 'location' => 'نزوى', 'since' => 2018],
                        ['name' => 'دار البيان', 'type' => 'دار نشر', 'logo' => 'imgs/literary/lirt3.png', 'location' => 'البريمي', 'since' => 2016],
                        ['name' => 'مؤسسة القلم', 'type' => 'مؤسسة', 'logo' => 'imgs/literary/lirt2.png', 'location' => 'صحار', 'since' => 2014],
                        ['name' => 'دار السند', 'type' => 'دار نشر', 'logo' => 'imgs/literary/lirt1.png', 'location' => 'صور', 'since' => 2011],
                        ['name' => 'جسور للمعرفة', 'type' => 'مؤسسة', 'logo' => 'imgs/literary/lirt3.png', 'location' => 'مسقط', 'since' => 2019],
                        ['name' => 'مكتبة الأفق', 'type' => 'مكتبة', 'logo' => 'imgs/literary/lirt2.png', 'location' => 'الرستاق', 'since' => 2003],
                        ['name' => 'دار أطياف', 'type' => 'دار نشر', 'logo' => 'imgs/literary/lirt1.png', 'location' => 'مسقط', 'since' => 2021],
                    ];
                @endphp
                <div id="publishers-wrapper"
                    class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 sm:gap-6">
                    @foreach ($publishers as $idx => $pub)
                        <div data-name="{{ $pub['name'] }}" data-type="{{ $pub['type'] }}" data-index="{{ $idx }}"
                            class="publisher-card group relative bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 p-5 flex flex-col gap-4">
                            <div class="flex items-start justify-between">
                                <div
                                    class="w-20 h-20 mx-auto rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 ring-1 ring-gray-200 flex items-center justify-center overflow-hidden">
                                    <img src="{{ asset($pub['logo']) }}" alt="{{ $pub['name'] }}"
                                        class="w-full h-full object-contain">
                                </div>
                                <span
                                    class="absolute top-3 left-3 text-[10px] font-semibold tracking-wide px-2 py-1 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">{{ $pub['type'] }}</span>
                            </div>
                            <div class="text-center flex flex-col gap-1 flex-1">
                                <h3 class="text-sm font-bold text-indigo-950 leading-snug line-clamp-2">{{ $pub['name'] }}
                                </h3>
                                <p class="text-[11px] text-gray-500 flex items-center justify-center gap-1">
                                    <i class="fas fa-map-marker-alt text-[10px] text-gray-400"></i>{{ $pub['location'] }} ·
                                    منذ {{ $pub['since'] }}
                                </p>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-dashed border-gray-200">
                                <a href="#"
                                    class="text-[11px] font-medium text-indigo-700 hover:text-indigo-900 flex items-center gap-1">
                                    التفاصيل <i class="fas fa-arrow-left text-[10px]"></i>
                                </a>
                                <button
                                    class="w-8 h-8 rounded-lg bg-gray-50 hover:bg-indigo-50 text-gray-500 hover:text-indigo-700 flex items-center justify-center transition-colors"
                                    title="مشاركة">
                                    <i class="fas fa-share-alt text-xs"></i>
                                </button>
                            </div>
                            <div
                                class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 pointer-events-none bg-gradient-to-b from-transparent via-transparent to-indigo-50 transition-opacity">
                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="no-publishers" class="hidden text-center text-gray-500 py-12">لا توجد نتائج مطابقة.</div>
                <div class="flex items-center justify-between flex-col sm:flex-row gap-4 mt-10">
                    <div class="flex items-center gap-2">
                        <button id="prev-page"
                            class="px-3 py-1.5 rounded-lg border bg-white text-sm text-gray-600 hover:bg-indigo-50 disabled:opacity-40 disabled:cursor-not-allowed">السابق</button>
                        <div id="pagination" class="flex items-center gap-1"></div>
                        <button id="next-page"
                            class="px-3 py-1.5 rounded-lg border bg-white text-sm text-gray-600 hover:bg-indigo-50 disabled:opacity-40 disabled:cursor-not-allowed">التالي</button>
                    </div>
                    <div class="text-xs text-gray-400">عرض <span id="range-start">1</span>-<span id="range-end">0</span>
                        من <span id="total-count">0</span></div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Enhanced publishers interactivity
            const wrapper = document.getElementById('publishers-wrapper');
            if (!wrapper) return;
            const cards = Array.from(wrapper.querySelectorAll('.publisher-card'));
            const searchInput = document.getElementById('publisher-search');
            const typeFilterWrap = document.getElementById('type-filters');
            const sortSelect = document.getElementById('publisher-sort');
            const countBadge = document.getElementById('publisher-count');
            const noMsg = document.getElementById('no-publishers');
            const pagination = document.getElementById('pagination');
            const prevPageBtn = document.getElementById('prev-page');
            const nextPageBtn = document.getElementById('next-page');
            const rangeStart = document.getElementById('range-start');
            const rangeEnd = document.getElementById('range-end');
            const totalCount = document.getElementById('total-count');

            let currentType = 'all';
            let currentPage = 1;
            const pageSize = 10; // adjust as needed

            function applyFilters() {
                const term = (searchInput?.value || '').trim().toLowerCase();
                let list = cards.filter(c => {
                    const name = c.dataset.name.toLowerCase();
                    const type = c.dataset.type;
                    const matchText = !term || name.includes(term);
                    const matchType = currentType === 'all' || type === currentType;
                    return matchText && matchType;
                });

                // Sorting
                const sortVal = sortSelect?.value || 'default';
                if (sortVal === 'alpha') {
                    list.sort((a, b) => a.dataset.name.localeCompare(b.dataset.name, 'ar'));
                } else if (sortVal === 'alpha-desc') {
                    list.sort((a, b) => b.dataset.name.localeCompare(a.dataset.name, 'ar'));
                } else {
                    list.sort((a, b) => a.dataset.index - b.dataset.index);
                }

                // Pagination bounds
                const total = list.length;
                totalCount.textContent = total;
                const totalPages = Math.max(1, Math.ceil(total / pageSize));
                currentPage = Math.min(currentPage, totalPages);
                const startIdx = (currentPage - 1) * pageSize;
                const pageItems = list.slice(startIdx, startIdx + pageSize);

                // Update counts
                countBadge.textContent = `${total} نتيجة`;
                rangeStart.textContent = total === 0 ? 0 : startIdx + 1;
                rangeEnd.textContent = startIdx + pageItems.length;

                // Render visibility
                cards.forEach(c => c.classList.add('hidden'));
                pageItems.forEach(c => c.classList.remove('hidden'));
                noMsg.classList.toggle('hidden', total !== 0);

                renderPagination(totalPages);
                // view mode removed; always grid
                prevPageBtn.disabled = currentPage === 1;
                nextPageBtn.disabled = currentPage === totalPages;
            }

            function renderPagination(totalPages) {
                pagination.innerHTML = '';
                const maxButtons = 7;
                function addBtn(label, page, active = false, disabled = false) {
                    const b = document.createElement('button');
                    b.textContent = label;
                    b.disabled = disabled;
                    b.className = `min-w-[34px] h-8 px-2 rounded-lg border text-xs font-medium ${active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-300'} disabled:opacity-40 disabled:cursor-not-allowed`;
                    if (!active && !disabled) {
                        b.addEventListener('click', () => { currentPage = page; applyFilters(); });
                    }
                    pagination.appendChild(b);
                }
                let pages = [];
                for (let i = 1; i <= totalPages; i++) pages.push(i);
                if (totalPages > maxButtons) {
                    const first = 1, last = totalPages;
                    const windowSize = 3;
                    let windowStart = Math.max(2, currentPage - 1);
                    let windowEnd = Math.min(totalPages - 1, currentPage + 1);
                    if (currentPage <= 3) { windowStart = 2; windowEnd = 4; }
                    if (currentPage >= totalPages - 2) { windowStart = totalPages - 3; windowEnd = totalPages - 1; }
                    pages = [first];
                    if (windowStart > 2) pages.push('ellipsis');
                    for (let p = windowStart; p <= windowEnd; p++) pages.push(p);
                    if (windowEnd < totalPages - 1) pages.push('ellipsis');
                    pages.push(last);
                }
                pages.forEach(p => {
                    if (p === 'ellipsis') {
                        const span = document.createElement('span');
                        span.textContent = '…';
                        span.className = 'px-1 text-gray-400 text-xs';
                        pagination.appendChild(span);
                    } else {
                        addBtn(String(p), p, p === currentPage);
                    }
                });
            }


            // Events
            searchInput?.addEventListener('input', () => { currentPage = 1; applyFilters(); });
            sortSelect?.addEventListener('change', () => { currentPage = 1; applyFilters(); });
            typeFilterWrap?.querySelectorAll('button[data-type]').forEach(btn => {
                btn.addEventListener('click', () => {
                    typeFilterWrap.querySelectorAll('button').forEach(b => b.classList.remove('bg-indigo-50', 'text-indigo-700'));
                    btn.classList.add('bg-indigo-50', 'text-indigo-700');
                    currentType = btn.dataset.type;
                    currentPage = 1;
                    applyFilters();
                });
            });
            prevPageBtn?.addEventListener('click', () => { if (currentPage > 1) { currentPage--; applyFilters(); } });
            nextPageBtn?.addEventListener('click', () => { currentPage++; applyFilters(); });

            // Init
            applyFilters();
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