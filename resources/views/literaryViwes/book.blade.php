<x-layout>
    <section class="py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <nav class="text-sm text-gray-400 mb-6" aria-label="breadcrumb">
                <a href="/" class="hover:underline">الأعمال الأدبية</a>
                <span class="mx-2">›</span>
                <a href="{{ url('/literary') }}" class="hover:underline">الكتب</a>
                <span class="mx-2">›</span>
                <span class="text-gray-500">{{ $book->title }}</span>
            </nav>

            <div class=" grid grid-cols-1 lg:grid-cols-2 gap-8 items-center ">
                <!-- Left column: details -->
                <div class="order-2 lg:order-1">
                    <h1 class="text-3xl font-bold text-indigo-950 arabic-font-bold mb-2">{{ $book->title }}</h1>
                    @php
                        $approved = $book->reviews ?? collect();
                        $count = $approved->count();
                        $avg = $count ? round($approved->avg('rating'), 1) : null;
                        $authors = $book->authors->pluck('name')->join('، ');
                        $hasDiscount = $book->compare_at_price_omr && $book->compare_at_price_omr > $book->price_omr;
                    @endphp
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex items-center gap-1 text-sm text-gray-500">
                            <span class="text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    {!! ($avg && $i <= floor($avg)) ? '★' : '☆' !!}
                                @endfor
                            </span>
                            <span class="text-gray-400 text-xs">{{ $avg ? $avg . '/5' : '—' }}</span>
                            <span class="text-gray-300 text-xs">({{ $count }})</span>
                        </div>
                        <div class="text-sm text-gray-400">|</div>
                        <div class="text-sm text-gray-500">تأليف: <span
                                class="text-indigo-900 font-medium">{{ $authors ?: '—' }}</span></div>
                    </div>

                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex items-baseline gap-3">
                            <div class="text-3xl font-extrabold text-indigo-900">{{ (float) $book->price_omr }} ر.ع
                            </div>
                            @if($hasDiscount)
                                <div class="text-sm text-gray-400 line-through">{{ (float) $book->compare_at_price_omr }}
                                    ر.ع</div>
                            @endif
                        </div>
                        @if($hasDiscount)
                            <div class="text-xs bg-pink-100 text-pink-600 px-2 py-1 rounded-full">عرض</div>
                        @endif
                    </div>

                    <div class="bg-white border border-gray-100 rounded-xl p-4 mb-6 shadow-sm">
                        <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                            <div class="space-y-2">
                                <div class="text-gray-400">دار النشر</div>
                                <div class="text-indigo-900 font-medium">{{ $book->publisher->name ?? '—' }}</div>
                            </div>
                            <div class="space-y-2 t">
                                <div class="text-gray-400">سنة النشر</div>
                                <div class="text-indigo-900 font-medium">{{ $book->publish_year ?? '—' }}</div>
                            </div>
                            <div class="space-y-2">
                                <div class="text-gray-400">عدد الصفحات</div>
                                <div class="text-indigo-900 font-medium">{{ $book->pages ?? '—' }}</div>
                            </div>
                            <div class="space-y-2 text-right">
                                <div class="text-gray-400">اللغة</div>
                                <div class="text-indigo-900 font-medium">{{ $book->language ?? '—' }}</div>
                            </div>
                            <div class="space-y-2">
                                <div class="text-gray-400">النوع</div>
                                <div class="text-indigo-900 font-medium">{{ $book->type ?? '—' }}</div>
                            </div>
                            <div class="space-y-2 text-right">
                                <div class="text-gray-400">التصنيفات</div>
                                <div class="text-indigo-900 font-medium">
                                    {{ $book->categories->pluck('name')->join('، ') ?: '—' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mb-6">
                        <button id="add-to-cart"
                            class="flex-1 rounded-2xl bg-indigo-950 text-white py-3 font-semibold shadow hover:bg-indigo-800 transition">إضافة
                            الى السلة</button>
                        <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                            <button id="qty-decr"
                                class="w-8 h-8 rounded-md bg-white border flex items-center justify-center">-</button>
                            <input id="qty" type="text" value="1" class="w-10 text-center bg-transparent text-sm" />
                            <button id="qty-incr"
                                class="w-8 h-8 rounded-md bg-white border flex items-center justify-center">+</button>
                        </div>
                    </div>



                </div>

                <!-- Right column: cover image -->
                <div class="order-1 lg:order-2 flex flex-col items-center justify-center">
                    <div class="relative w-64 sm:w-80 lg:w-96">
                        <div class=" ">
                            <img src="{{ $book->cover_image_path ? asset('storage/' . ltrim($book->cover_image_path, '/')) : asset('imgs/pic/Book.png') }}"
                                alt="غلاف الكتاب" class="w-full h-48 sm:h-64 lg:h-72 rounded-lg object-contain">
                        </div>
                    </div>
                    <!-- Action buttons below the book -->
                    <div class="flex items-center justify-center gap-3 mt-6">
                        <div class="bg-white rounded-full w-12 h-12 flex items-center justify-center shadow-md hover:shadow-lg transition-all duration-200"
                            title="تكبير الصورة">
                            <i class="fa-solid fa-magnifying-glass-plus text-indigo-700"></i>
                        </div>
                        <button
                            class="w-12 h-12 rounded-full bg-white border border-gray-200 flex items-center justify-center shadow-md hover:shadow-lg hover:border-pink-300 transition-all duration-200"
                            title="أضف للمفضلة">
                            <i class="fas fa-heart text-pink-500"></i>
                        </button>
                        <button
                            class="w-12 h-12 rounded-full bg-white border border-gray-200 flex items-center justify-center shadow-md hover:shadow-lg hover:border-indigo-300 transition-all duration-200"
                            title="حفظ">
                            <i class="fas fa-bookmark text-indigo-700"></i>
                        </button>
                        <button
                            class="w-12 h-12 rounded-full bg-white border border-gray-200 flex items-center justify-center shadow-md hover:shadow-lg hover:border-gray-300 transition-all duration-200"
                            title="مشاركة">
                            <i class="fas fa-share-alt text-gray-600"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="bg-white h-100 overflow-hidden">
        <div class="px-5 sm:px-2 max-w-6xl mx-auto h-full flex flex-col">
            <div class="border-t border-gray-200 text-center flex-shrink-0">
                <nav class="flex gap-2 sm:gap-8 mt-6 text-lg sm:text-xl text-gray-500 items-center justify-center rounded-2xl p-2"
                    id="tabs">
                    <button data-tab="desc"
                        class="tab-btn relative px-4 py-3 border-b-2 border-transparent focus:outline-none text-base sm:text-lg font-medium rounded-xl transition-all duration-300 hover:bg-white hover:text-indigo-900 hover:shadow-sm">
                        <i class="fas fa-book-open ml-2 text-sm"></i>
                        نبذة تعريفية
                    </button>
                    <button data-tab="specs"
                        class="tab-btn relative px-4 py-3 border-b-2 border-transparent focus:outline-none text-base sm:text-lg font-medium rounded-xl transition-all duration-300 hover:bg-white hover:text-indigo-900 hover:shadow-sm">
                        <i class="fas fa-tags ml-2 text-sm"></i>
                        التصنيفات الفرعية
                    </button>
                    <button data-tab="reviews"
                        class="tab-btn relative px-4 py-3 border-b-2 border-transparent focus:outline-none text-base sm:text-lg font-medium rounded-xl transition-all duration-300 hover:bg-white hover:text-indigo-900 hover:shadow-sm">
                        <i class="fas fa-star ml-2 text-sm"></i>
                        التقييمات والمراجعات
                    </button>
                </nav>
            </div>
            <div class="flex-1 overflow-y-auto mt-6">
                @if(!($hasPurchased ?? false))
                    <div class="mt-8 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-2xl p-6 text-right">
                        <div class="font-semibold mb-1">هذا المحتوى متاح بعد الشراء</div>
                        <p class="text-sm">سجّلت الدخول بالفعل. لإظهار تفاصيل الكتاب الكاملة والتقييمات، أكمل شراء الكتاب.</p>
                    </div>
                @endif
                <div id="tab-desc" @class(['tab-panel','mt-8','text-right','bg-white','border','border-gray-100','rounded-2xl','p-6','text-gray-800','leading-8','tracking-wide', 'opacity-50 pointer-events-none'=>!($hasPurchased ?? false)])
                    class="tab-panel mt-8 text-right bg-white border border-gray-100 rounded-2xl p-6  text-gray-800 leading-8 tracking-wide">
                    <p class="text-sm sm:text-base">{{ $book->description ?: 'لا توجد نبذة متاحة لهذا الكتاب.' }}</p>
                </div>
                <div id="tab-specs" @class(['tab-panel','mt-8','hidden','bg-white','border','border-gray-100','rounded-2xl','p-6','shadow-sm','text-gray-700', 'opacity-50 pointer-events-none'=>!($hasPurchased ?? false)])
                    class="tab-panel mt-8 hidden bg-white border border-gray-100 rounded-2xl p-6 shadow-sm text-gray-700">
                    <div class="space-y-6">
                        <div>
                            <h4 class="text-gray-400 text-sm mb-3">كل التصنيفات</h4>
                            <div class="flex flex-wrap gap-2">
                                @forelse($book->categories as $cat)
                                    <span
                                        class="px-4 py-2 rounded-full bg-gray-100 text-gray-600 text-sm">{{ $cat->name }}</span>
                                @empty
                                    <span class="text-gray-400 text-sm">لا توجد تصنيفات</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tab-reviews" @class(['tab-panel','mt-8','hidden', 'opacity-50 pointer-events-none'=>!($hasPurchased ?? false)])>
                    <div class="bg-white  rounded-2xl p-6  text-right">
                        @if (session('success'))
                            <div class="mb-4 p-3 rounded bg-green-50 text-green-700 text-sm">{{ session('success') }}</div>
                        @endif
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                            <div class="text-lg text-gray-800">كل المراجعات <span
                                    class="text-gray-400">({{ $count }})</span>
                            </div>
                            <div class="flex items-center gap-3">
                                @auth
                                    @if(($hasPurchased ?? false))
                                    <button id="write-review"
                                        class="px-5 py-2 rounded-full bg-indigo-950 text-white text-sm hover:bg-indigo-800 transition-colors flex items-center gap-2">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        اكتب مراجعتك
                                    </button>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}"
                                        class="px-5 py-2 rounded-full bg-indigo-950 text-white text-sm hover:bg-indigo-800 transition-colors flex items-center gap-2">
                                        <i class="fa-solid fa-right-to-bracket text-xs"></i>
                                        سجّل الدخول لكتابة مراجعة
                                    </a>
                                @endauth
                                <div class="flex items-center gap-2">
                                    <div class="relative" id="reviews-category-wrapper">
                                        <button id="reviews-category" aria-haspopup="true" aria-expanded="false"
                                            class="flex items-center gap-2 px-4 py-2 rounded-full bg-white border text-sm hover:bg-gray-50 transition focus:outline-none focus:ring-2 focus:ring-indigo-300">
                                            <span id="reviews-category-label">تصنيف</span>
                                            <i class="fas fa-chevron-down text-gray-500 text-xs transition"
                                                id="reviews-category-caret"></i>
                                        </button>
                                        <ul id="reviews-category-menu" role="menu"
                                            class="hidden absolute top-full right-0 mt-2 w-48 bg-white border border-gray-200 rounded-xl shadow-lg py-1 text-sm z-30">
                                            <li>
                                                <button type="button" role="menuitem" data-sort="newest"
                                                    class="w-full text-right px-4 py-2 hover:bg-indigo-50 flex items-center justify-between">
                                                    الأحدث <i class="fa-regular fa-clock text-xs opacity-60"></i>
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" role="menuitem" data-sort="oldest"
                                                    class="w-full text-right px-4 py-2 hover:bg-indigo-50 flex items-center justify-between">
                                                    الأقدم <i
                                                        class="fa-solid fa-clock-rotate-left text-xs opacity-60"></i>
                                                </button>
                                            </li>
                                            <li>
                                                <button type="button" role="menuitem" data-sort="top"
                                                    class="w-full text-right px-4 py-2 hover:bg-indigo-50 flex items-center justify-between">
                                                    الأعلى تقييماً <i
                                                        class="fa-solid fa-star text-xs text-yellow-400"></i>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="reviews-list" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @forelse($approved as $rev)
                                <article
                                    class="group bg-white border border-gray-100 rounded-xl p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
                                    <div>
                                        <div class="flex items-start justify-between">
                                            <button class="text-gray-300 hover:text-gray-500 p-1 rounded-full"><i
                                                    class="fas fa-ellipsis-h"></i></button>
                                            <div class="flex items-center gap-3">
                                                <div class="text-yellow-400">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        {!! ($i <= (int) $rev->rating) ? '★' : '☆' !!}
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <div class="font-semibold text-indigo-900">
                                                {{ optional($rev->user)->name ?? 'مستخدم' }}</div>
                                            <p class="text-gray-600 mt-2 text-sm max-h-20 overflow-hidden">
                                                {{ $rev->content }}</p>
                                        </div>
                                    </div>
                                    <div class="flex justify-end mt-4 text-xs text-gray-400">
                                        {{ $rev->created_at?->format('Y-m-d') }}</div>
                                </article>
                            @empty
                                <div class="col-span-2 text-center text-gray-400">لا توجد مراجعات بعد.</div>
                            @endforelse
                        </div>

                        <div class="text-center mt-8">
                            <button id="load-more-reviews"
                                class="px-6 py-2 rounded-full border border-gray-200 text-sm hover:bg-gray-50 transition-colors">المزيد</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Write Review Modal -->
    @auth
        <div id="write-review-modal" class="hidden fixed inset-0 z-50" aria-hidden="true">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
            <div role="dialog" aria-modal="true" aria-labelledby="write-review-title"
                class="relative mx-auto w-full max-w-lg mt-24 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h3 id="write-review-title" class="text-lg font-semibold text-indigo-950 flex items-center gap-2">
                        <i class="fa-solid fa-pen-to-square text-indigo-600"></i> أضف مراجعتك
                    </h3>
                    <button id="close-write-review" class="text-gray-400 hover:text-gray-600" title="إغلاق">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <form id="write-review-form" class="px-6 pt-5 pb-6 space-y-5" method="POST"
                    action="{{ url('literary/book/' . $book->id . '/review') }}">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm mb-1 text-gray-500">الاسم</label>
                            <input type="text" value="{{ auth()->user()->name }}" disabled
                                class="w-full rounded border-gray-200 bg-gray-50 text-sm p-2">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm mb-1 text-gray-500">التقييم</label>
                            <div id="rating-stars"
                                class="flex flex-row-reverse justify-end gap-1 text-2xl cursor-pointer select-none">
                                <i data-value="5" class="fa-solid fa-star text-yellow-400"></i>
                                <i data-value="4" class="fa-solid fa-star text-yellow-400"></i>
                                <i data-value="3" class="fa-solid fa-star text-yellow-400"></i>
                                <i data-value="2" class="fa-solid fa-star text-yellow-400"></i>
                                <i data-value="1" class="fa-solid fa-star text-yellow-400"></i>
                            </div>
                            <input type="hidden" name="rating" value="5">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm mb-1 text-gray-500">محتوى المراجعة <span
                                    class="text-pink-500">*</span></label>
                            <textarea name="content" rows="4" required
                                class="w-full runde border-gray-200 focus:border-indigo-400 focus:ring focus:ring-indigo-200/40 text-sm placeholder-gray-300 resize-none"
                                placeholder="اكتب مراجعتك هنا..."></textarea>
                            <div class="flex justify-end mt-1">
                                <p id="content-count" class="text-xs text-gray-400">0 حرف</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-3 pt-2">
                        <button type="button" id="cancel-write-review"
                            class="flex-1 px-4 py-2.5 runde border border-gray-200 text-gray-600 text-sm hover:bg-gray-50">
                            إلغاء
                        </button>
                        <button type="submit"
                            class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-950 text-white text-sm font-medium hover:bg-indigo-800 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fa-solid fa-paper-plane"></i>
                            نشر المراجعة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endauth
    <section></section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Quantity controls
            const qtyEl = document.getElementById('qty');
            const incr = document.getElementById('qty-incr');
            const decr = document.getElementById('qty-decr');
            incr && incr.addEventListener('click', () => { qtyEl.value = Math.max(1, parseInt(qtyEl.value || 1) + 1); });
            decr && decr.addEventListener('click', () => { qtyEl.value = Math.max(1, parseInt(qtyEl.value || 1) - 1); });

            // Tabs
            const tabBtns = document.querySelectorAll('.tab-btn');
            const panels = document.querySelectorAll('.tab-panel');
            function showTab(id) {
                panels.forEach(p => p.classList.add('hidden'));
                document.getElementById('tab-' + id).classList.remove('hidden');

                // Reset all tabs to inactive state
                tabBtns.forEach(b => {
                    b.classList.remove('text-indigo-900', 'bg-[#ef4444]', 'shadow-md', 'border-indigo-900');
                    b.classList.add('text-gray-500');
                });

                // Activate selected tab
                const active = Array.from(tabBtns).find(b => b.dataset.tab === id);
                if (active) {
                    active.classList.remove('text-gray-500');
                    active.classList.add('text-indigo-900', 'bg-white', 'shadow-md');
                }
            }
            tabBtns.forEach(b => b.addEventListener('click', () => showTab(b.dataset.tab)));
            showTab('desc');

            // Reviews interactions
            const reviewsList = document.getElementById('reviews-list');
            const writeBtn = document.getElementById('write-review');
            const loadMore = document.getElementById('load-more-reviews');
            // Reviews dropdown sorting
            const reviewsCatBtn = document.getElementById('reviews-category');
            const reviewsCatMenu = document.getElementById('reviews-category-menu');
            const reviewsCatLabel = document.getElementById('reviews-category-label');
            const reviewsCatCaret = document.getElementById('reviews-category-caret');

            // Modal elements
            const modal = document.getElementById('write-review-modal');
            const form = document.getElementById('write-review-form');
            const closeModalBtn = document.getElementById('close-write-review');
            const cancelBtn = document.getElementById('cancel-write-review');
            const ratingStars = document.getElementById('rating-stars');
            const ratingInput = form?.querySelector('input[name="rating"]');
            const contentTextArea = form?.querySelector('textarea[name="content"]');
            const nameInput = form?.querySelector('input[name="name"]');
            const contentCount = document.getElementById('content-count');

            function openModal() {
                if (!modal) return;
                modal.classList.remove('hidden');
                modal.setAttribute('aria-hidden', 'false');
                nameInput?.focus();
                document.body.classList.add('overflow-hidden');
            }
            function closeModal() {
                if (!modal) return;
                modal.classList.add('hidden');
                modal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('overflow-hidden');
                form?.reset();
                if (ratingInput) ratingInput.value = '5';
                ratingPaint(Number(ratingInput?.value || 5));
                hideErrors();
                updateCounter();
            }
            writeBtn?.addEventListener('click', (e) => { e.preventDefault(); openModal(); });
            closeModalBtn?.addEventListener('click', closeModal);
            cancelBtn?.addEventListener('click', closeModal);
            modal?.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal(); });

            function ratingPaint(val) {
                if (!ratingStars) return;
                Array.from(ratingStars.querySelectorAll('i')).forEach(i => {
                    const v = Number(i.getAttribute('data-value'));
                    i.classList.toggle('text-yellow-400', v <= val);
                    i.classList.toggle('text-gray-300', v > val);
                });
            }
            ratingStars?.addEventListener('mousemove', (e) => {
                const target = e.target.closest('i[data-value]');
                if (target) ratingPaint(Number(target.getAttribute('data-value')));
            });
            ratingStars?.addEventListener('mouseleave', () => ratingPaint(Number(ratingInput?.value || 5)));
            ratingStars?.addEventListener('click', (e) => {
                const target = e.target.closest('i[data-value]');
                if (target && ratingInput) {
                    ratingInput.value = target.getAttribute('data-value');
                    ratingPaint(Number(ratingInput.value));
                }
            });
            ratingPaint(Number(ratingInput?.value || 5));

            function hideErrors() { }
            function updateCounter() {
                if (!contentTextArea || !contentCount) return;
                const len = contentTextArea.value.length;
                contentCount.textContent = len + ' حرف';
            }
            contentTextArea?.addEventListener('input', updateCounter);
            updateCounter();

            // Allow normal form submission to backend; no client-side injection

            if (loadMore && reviewsList) {
                loadMore.addEventListener('click', () => {
                    // duplicate first 2 reviews for demo
                    const items = Array.from(reviewsList.children).slice(0, 2).map(n => n.cloneNode(true));
                    items.forEach(i => reviewsList.appendChild(i));
                });
            }

            function toggleMenu(open) {
                if (!reviewsCatMenu) return;
                const isOpen = open !== undefined ? open : reviewsCatMenu.classList.contains('hidden');
                if (isOpen) {
                    reviewsCatMenu.classList.remove('hidden');
                    reviewsCatBtn.setAttribute('aria-expanded', 'true');
                    reviewsCatCaret.classList.add('rotate-180');
                } else {
                    reviewsCatMenu.classList.add('hidden');
                    reviewsCatBtn.setAttribute('aria-expanded', 'false');
                    reviewsCatCaret.classList.remove('rotate-180');
                }
            }
            reviewsCatBtn && reviewsCatBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                toggleMenu();
            });
            document.addEventListener('click', (e) => {
                if (reviewsCatMenu && !reviewsCatMenu.contains(e.target) && !reviewsCatBtn.contains(e.target)) {
                    toggleMenu(false);
                }
            });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') toggleMenu(false); });

            function parseStars(el) {
                // count filled star characters (★) inside first .text-yellow-400 span/div
                const starEl = el.querySelector('.text-yellow-400');
                if (!starEl) return 0;
                const txt = starEl.textContent || '';
                return (txt.match(/★/g) || []).length;
            }
            function sortReviews(mode) {
                if (!reviewsList) return;
                const items = Array.from(reviewsList.children);
                if (mode === 'oldest') {
                    items.reverse();
                } else if (mode === 'top') {
                    items.sort((a, b) => parseStars(b) - parseStars(a));
                } else { /* newest: keep original order (assumed current DOM) */ }
                items.forEach(i => reviewsList.appendChild(i));
            }
            reviewsCatMenu?.querySelectorAll('[data-sort]')?.forEach(btn => {
                btn.addEventListener('click', () => {
                    const mode = btn.getAttribute('data-sort');
                    sortReviews(mode);
                    reviewsCatLabel.textContent = btn.textContent.trim();
                    toggleMenu(false);
                });
            });
        });
    </script>

    <!-- Related Books Section -->
    <section class="py-12 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">كتب أكثر حول</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Book Card 1 -->
                <div class="bg-white rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="relative mb-4">
                        <img src="{{ asset('imgs/pic/Book.png') }}" alt="سيدات القمر"
                            class="w-full h-48 object-contain rounded-lg">
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-1">سيدات القمر</h3>
                    <p class="text-sm text-gray-500 mb-3">جوخة الحارثي</p>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-lg font-bold text-indigo-900">13 ريال</span>
                    </div>
                    <button
                        class="w-full bg-indigo-950 text-white py-2 rounded-full text-sm hover:bg-indigo-800 transition-colors">
                        عرض التفاصيل
                    </button>
                </div>

                <!-- Book Card 2 -->
                <div class="bg-white rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="relative mb-4">
                        <img src="{{ asset('imgs/pic/Book.png') }}" alt="سيدات القمر"
                            class="w-full h-48 object-contain rounded-lg">
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-1">سيدات القمر</h3>
                    <p class="text-sm text-gray-500 mb-3">جوخة الحارثي</p>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-lg font-bold text-indigo-900">13 ريال</span>
                        <span class="text-sm text-gray-400 line-through">16 ريال</span>
                    </div>
                    <button
                        class="w-full bg-white border border-gray-200 text-gray-700 py-2 rounded-full text-sm hover:bg-gray-50 transition-colors">
                        عرض آخر
                    </button>
                </div>

                <!-- Book Card 3 -->
                <div class="bg-white rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="relative mb-4">
                        <img src="{{ asset('imgs/pic/Book.png') }}" alt="سيدات القمر"
                            class="w-full h-48 object-contain rounded-lg">
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-1">سيدات القمر</h3>
                    <p class="text-sm text-gray-500 mb-3">جوخة الحارثي</p>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-lg font-bold text-indigo-900">13 ريال</span>
                        <span class="text-sm text-gray-400 line-through">16 ريال</span>
                    </div>
                    <button
                        class="w-full bg-indigo-950 text-white py-2 rounded-full text-sm hover:bg-indigo-800 transition-colors">
                        عرض التفاصيل
                    </button>
                </div>

                <!-- Book Card 4 -->
                <div class="bg-white rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="relative mb-4">
                        <div class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                            -30%
                        </div>
                        <img src="{{ asset('imgs/pic/Book.png') }}" alt="سيدات القمر"
                            class="w-full h-48 object-contain rounded-lg">
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-1">سيدات القمر</h3>
                    <p class="text-sm text-gray-500 mb-3">جوخة الحارثي</p>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-lg font-bold text-indigo-900">13 ريال</span>
                        <span class="text-sm text-gray-400 line-through">16 ريال</span>
                    </div>
                    <button
                        class="w-full bg-white border border-gray-200 text-gray-700 py-2 rounded-full text-sm hover:bg-gray-50 transition-colors">
                        عرض آخر
                    </button>
                </div>
            </div>
        </div>
    </section>
</x-layout>
