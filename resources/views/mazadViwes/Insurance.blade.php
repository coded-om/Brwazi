<x-layout>
    <section class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <!-- Header / Breadcrumb -->
            <div class="flex items-center justify-between flex-wrap gap-4 mb-8">
                <div>
                    <nav class="text-xs sm:text-sm text-gray-400 mb-2" aria-label="breadcrumb">
                        <a href="/" class="hover:underline">الرئيسية</a>
                        <span class="mx-1 sm:mx-2">›</span>
                        <a href="/mazad" class="hover:underline">المزاد</a>
                        <span class="mx-1 sm:mx-2">›</span>
                        <span class="text-gray-500">التأمين</span>
                    </nav>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-indigo-950 tracking-tight">إدارة التأمين</h1>
                </div>
                <div
                    class="flex items-center gap-3 text-xs sm:text-sm bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-2 rounded-xl shadow-sm">
                    <i class="fa-solid fa-circle-exclamation text-yellow-500"></i>
                    <span>يُسترجع التأمين خلال 24 ساعة في حال عدم الفوز.</span>
                </div>
            </div>
            <!-- Top Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
                @php
                    $statRaw = \App\Models\Auction::insuranceTopStats();
                    $cards = [
                        [
                            'value' => number_format($statRaw['participants']),
                            'label' => 'المشتركين للإيداع',
                            'icon' => 'fa-users'
                        ],
                        [
                            'value' => number_format($statRaw['required_total'], 2),
                            'label' => 'إجمالي التأمين المطلوب',
                            'icon' => 'fa-sack-dollar'
                        ],
                        [
                            'value' => number_format($statRaw['held_total'], 2),
                            'label' => 'التأمين المودع',
                            'icon' => 'fa-vault'
                        ],
                        [
                            'value' => number_format($statRaw['active_auctions']),
                            'label' => 'مزايدات نشطة',
                            'icon' => 'fa-gavel'
                        ],
                    ];
                @endphp
                @foreach($cards as $s)
                    <div
                        class="bg-white rounded-2xl p-5 sm:p-6 border border-gray-100 shadow-sm flex flex-col items-center text-center group">
                        <div
                            class="w-10 h-10 flex items-center justify-center rounded-full bg-indigo-50 text-indigo-700 mb-3 group-hover:scale-105 transition">
                            <i class="fa-solid {{ $s['icon'] }} text-sm"></i>
                        </div>
                        <div class="text-xl sm:text-2xl font-extrabold text-indigo-950">{{ $s['value'] }}</div>
                        <div class="text-[11px] sm:text-xs text-gray-500 mt-1">{{ $s['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Coverage Ratio -->
        @if(isset($coveragePercent))
            <div class="mb-8">
                <div class="flex items-center justify-between mb-2 text-xs sm:text-sm text-gray-600">
                    <span>نسبة تغطية التأمين الحالية</span>
                    <span class="font-semibold text-indigo-700">{{ $coveragePercent }}%</span>
                </div>
                <div class="h-3 rounded-full bg-gray-200 overflow-hidden">
                    <div id="coverageBar" data-width="{{ $coveragePercent }}"
                        class="h-full bg-indigo-500 transition-all duration-700" style="width:0%"></div>
                </div>
            </div>
        @endif

        <!-- Auctions selectable list -->
        <div class="lg:col-span-8 order-1 lg:order-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <h3 class="font-bold text-indigo-950 flex items-center gap-2 text-base sm:text-lg">
                        <i class="fa-solid fa-list-check text-indigo-600"></i>
                        المزادات المتاحة
                    </h3>
                    <form method="get" class="w-full sm:w-auto flex flex-col sm:flex-row gap-3 sm:items-center">
                        <div class="flex items-center gap-2 w-full">
                            <div class="relative flex-1">
                                <input id="searchAuctions" type="text" name="q" value="{{ request('q') }}"
                                    placeholder="ابحث عن مزاد"
                                    class="w-full h-11 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 pr-9 placeholder-gray-400" />
                                <i
                                    class="fas fa-search absolute top-1/2 -translate-y-1/2 right-3 text-gray-400 text-sm"></i>
                            </div>
                            <button id="selectAll" type="button"
                                class="h-11 px-4 rounded-xl bg-gray-100 hover:bg-gray-200 text-xs font-medium text-gray-600 whitespace-nowrap">تحديد
                                الكل</button>
                        </div>
                        <div class="grid grid-cols-2 md:flex md:items-center gap-2 w-full text-[11px] sm:text-xs">
                            <select name="status[]" multiple
                                class="h-11 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-2 min-w-[120px]">
                                @php $statuses = ['live' => 'مباشر', 'soon' => 'قريباً']; @endphp
                                @foreach($statuses as $key => $label)
                                    <option value="{{ $key }}" {{ (isset($filterStatus) && in_array($key, $filterStatus)) ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <select name="artist"
                                class="h-11 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-2 min-w-[140px]">
                                <option value="any">كل الفنانين</option>
                                @foreach(($artists ?? []) as $artistName)
                                    <option value="{{ $artistName }}" {{ ($artistSelected === $artistName) ? 'selected' : '' }}>{{ $artistName }}</option>
                                @endforeach
                            </select>
                            <input type="number" name="min_start" value="{{ $minStart ?? '' }}" placeholder="حد أدنى"
                                class="h-11 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-2" />
                            <input type="number" name="max_start" value="{{ $maxStart ?? '' }}" placeholder="حد أقصى"
                                class="h-11 rounded-xl border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-2" />
                            <button type="submit"
                                class="h-11 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold">تصفية</button>
                            <a href="{{ route('mazad.insurance') }}"
                                class="h-11 px-3 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600">تصفير</a>
                        </div>
                    </form>
                </div>
                <div id="auctionsContainer" class="max-h-[420px] overflow-y-auto pr-1 custom-scroll grid gap-3">
                    @forelse(($auctionItems ?? []) as $a)
                        <label data-filter-text="{{ $a['title'] }} {{ $a['artist'] }}"
                            class="auction-row flex items-center gap-4 p-4 rounded-xl border bg-white hover:border-indigo-200 cursor-pointer transition group">
                            <input type="checkbox" value="{{ $a['id'] }}" data-amount="{{ $a['fee'] }}"
                                class="accent-indigo-600 w-5 h-5 mt-0.5" />
                            <div class="relative w-16 h-16 rounded-lg overflow-hidden flex-shrink-0">
                                <img src="{{ $a['image'] }}" alt="{{ $a['title'] }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500" />
                                <span
                                    class="absolute inset-0 ring-2 ring-indigo-500 rounded-lg opacity-0 group-has-[:checked]:opacity-100 transition"></span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <h4 class="font-bold text-sm text-indigo-950 truncate">{{ $a['title'] }}</h4>
                                        <p class="text-xs text-gray-500 mt-0.5 truncate">الفنان: {{ $a['artist'] }}</p>
                                        <p class="text-[11px] text-gray-400">رسوم التأمين: <span
                                                class="font-medium text-indigo-900">{{ rtrim(rtrim(number_format($a['fee'], 2, '.', ''), '0'), '.') }}
                                                ريال</span></p>
                                    </div>
                                    <span
                                        class="text-indigo-900 text-lg font-extrabold leading-none">{{ rtrim(rtrim(number_format($a['fee'], 2, '.', ''), '0'), '.') }}</span>
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="p-6 text-center text-sm text-gray-500 bg-gray-50 rounded-xl border">لا توجد مزادات
                            متاحة حالياً للحجز.</div>
                    @endforelse
                </div>
            </div>
        </div>
        </div>

        <!-- Deposit Methods -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-20" id="paymentSection">
            <h2 class="text-lg sm:text-xl font-bold text-indigo-950 mb-6 flex items-center gap-2">
                <i class="fa-solid fa-coins text-indigo-600"></i>
                إيداع التأمين
            </h2>
            <div class="grid sm:grid-cols-3 gap-5 mb-8">
                @php
                    $methods = [
                        ['id' => 'wallet', 'title' => 'محفظة إلكترونية', 'desc' => 'STC Pay • Apple Pay', 'icon' => 'fa-mobile-screen-button'],
                        ['id' => 'bank', 'title' => 'تحويل بنكي', 'desc' => 'تحويل فوري أو آجل', 'icon' => 'fa-building-columns'],
                        ['id' => 'card', 'title' => 'بطاقة ائتمانية', 'desc' => 'Visa • MasterCard • Mada', 'icon' => 'fa-credit-card'],
                    ];
                @endphp
                @foreach($methods as $m)
                    <button data-method="{{ $m['id'] }}" type="button"
                        class="pay-method group flex flex-col items-center gap-3 p-5 rounded-2xl border border-gray-200 bg-white hover:border-indigo-300 hover:bg-indigo-50 transition relative">
                        <span
                            class="absolute top-3 left-3 w-5 h-5 rounded-full border flex items-center justify-center text-[10px] text-transparent group-[.active]:bg-indigo-600 group-[.active]:text-white group-[.active]:border-indigo-600 transition"><i
                                class="fa-solid fa-check"></i></span>
                        <div
                            class="w-14 h-14 rounded-full flex items-center justify-center bg-indigo-50 text-indigo-700 group-[.active]:bg-indigo-600 group-[.active]:text-white transition">
                            <i class="fa-solid {{ $m['icon'] }} text-xl"></i>
                        </div>
                        <div class="text-center">
                            <div class="font-bold text-indigo-900 text-sm mb-1">{{ $m['title'] }}</div>
                            <div class="text-[11px] text-gray-500">{{ $m['desc'] }}</div>
                        </div>
                    </button>
                @endforeach
            </div>
            <div class="space-y-6" id="methodForms">
                <div data-form="wallet" class="hidden">
                    <h3 class="font-semibold text-sm text-gray-500 mb-3">اختر المحفظة</h3>
                    <div class="flex flex-wrap gap-3">
                        <button class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">STC Pay</button>
                        <button class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Apple
                            Pay</button>
                    </div>
                </div>
                <div data-form="bank" class="hidden">
                    <h3 class="font-semibold text-sm text-gray-500 mb-3">بيانات التحويل</h3>
                    <div class="grid sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <label class="block text-[11px] text-gray-400 mb-1">اسم البنك</label>
                            <input type="text"
                                class="w-full h-11 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 text-sm"
                                placeholder="اسم البنك" />
                        </div>
                        <div>
                            <label class="block text-[11px] text-gray-400 mb-1">رقم الحساب</label>
                            <input type="text"
                                class="w-full h-11 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 text-sm"
                                placeholder="SA••••" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] text-gray-400 mb-1">ملاحظات</label>
                            <textarea rows="3"
                                class="w-full rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 text-sm resize-none"
                                placeholder="اكتب أي ملاحظة"></textarea>
                        </div>
                    </div>
                </div>
                <div data-form="card" class="hidden">
                    <h3 class="font-semibold text-sm text-gray-500 mb-3">معلومات البطاقة</h3>
                    <div class="grid sm:grid-cols-2 gap-4 text-sm">
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] text-gray-400 mb-1">رقم البطاقة</label>
                            <input type="text" inputmode="numeric" maxlength="19"
                                class="w-full h-11 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 tracking-widest text-sm"
                                placeholder="0000 0000 0000 0000" />
                        </div>
                        <div>
                            <label class="block text-[11px] text-gray-400 mb-1">تاريخ الانتهاء</label>
                            <input type="text" placeholder="MM/YY" maxlength="5"
                                class="w-full h-11 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 text-sm" />
                        </div>
                        <div>
                            <label class="block text-[11px] text-gray-400 mb-1">CVV</label>
                            <input type="password" maxlength="4"
                                class="w-full h-11 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 text-sm" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-8">
                <button id="confirmDeposit" disabled
                    class="w-full h-12 rounded-xl bg-indigo-300 text-white text-sm font-semibold flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed transition">
                    <i class="fa-solid fa-lock"></i>
                    تأكيد إيداع التأمين
                </button>
                <p class="text-[11px] text-gray-400 mt-3">سيتم تفعيل المزايدات المختارة تلقائياً بعد نجاح الإيداع.
                </p>
            </div>
        </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const coverageBar = document.getElementById('coverageBar');
            if (coverageBar) {
                const w = coverageBar.getAttribute('data-width');
                if (w) coverageBar.style.width = w + '%';
            }
            const rows = document.querySelectorAll('.auction-row input[type="checkbox"]');
            const selectedCount = document.getElementById('selectedCount');
            const totalRequired = document.getElementById('totalRequired');
            const toDeposit = document.getElementById('toDeposit');
            const depositBtn = document.getElementById('depositBtn');
            const selectionHint = document.getElementById('selectionHint');
            const searchInput = document.getElementById('searchAuctions');
            const selectAll = document.getElementById('selectAll');
            const resetSelection = document.getElementById('resetSelection');
            const methodButtons = document.querySelectorAll('.pay-method');
            const methodForms = document.querySelectorAll('[data-form]');
            const confirmDeposit = document.getElementById('confirmDeposit');

            function formatAmount(a) { return a.toLocaleString('ar-EG', { minimumFractionDigits: 0, maximumFractionDigits: 2 }); }
            function recalc() {
                let count = 0; let sum = 0;
                rows.forEach(r => { if (r.checked) { count++; sum += Number(r.dataset.amount); } });
                if (selectedCount) selectedCount.textContent = count;
                if (totalRequired) totalRequired.textContent = formatAmount(sum) + ' ريال';
                if (toDeposit) toDeposit.textContent = formatAmount(sum) + ' ريال';
                const active = count > 0;
                if (depositBtn) {
                    depositBtn.disabled = !active;
                    depositBtn.classList.toggle('bg-indigo-950', active);
                    depositBtn.classList.toggle('bg-indigo-300', !active);
                }
                if (confirmDeposit) {
                    confirmDeposit.disabled = !active;
                    confirmDeposit.classList.toggle('bg-indigo-950', active);
                    confirmDeposit.classList.toggle('bg-indigo-300', !active);
                }
                if (selectionHint) selectionHint.classList.toggle('hidden', active);
            }
            rows.forEach(r => r.addEventListener('change', recalc));
            recalc();

            searchInput?.addEventListener('input', () => {
                const q = searchInput.value.trim().toLowerCase();
                document.querySelectorAll('#auctionsContainer .auction-row').forEach(row => {
                    const t = row.getAttribute('data-filter-text').toLowerCase();
                    row.classList.toggle('hidden', q && !t.includes(q));
                });
            });

            selectAll?.addEventListener('click', () => {
                const allChecked = Array.from(rows).every(r => r.checked);
                rows.forEach(r => r.checked = !allChecked);
                recalc();
            });
            resetSelection?.addEventListener('click', () => { rows.forEach(r => r.checked = false); recalc(); });

            methodButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    methodButtons.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    const id = btn.dataset.method;
                    methodForms.forEach(f => f.classList.toggle('hidden', f.dataset.form !== id));
                });
            });

            depositBtn?.addEventListener('click', () => {
                document.getElementById('paymentSection')?.scrollIntoView({ behavior: 'smooth' });
            });

            confirmDeposit.addEventListener('click', () => {
                if (confirmDeposit.disabled) return;
                confirmDeposit.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> جاري المعالجة';
                setTimeout(() => {
                    confirmDeposit.innerHTML = '<i class="fa-solid fa-check"></i> تم الإيداع';
                    confirmDeposit.classList.remove('bg-indigo-950');
                    confirmDeposit.classList.add('bg-emerald-600');
                }, 1500);
            });
        });
    </script>
</x-layout>