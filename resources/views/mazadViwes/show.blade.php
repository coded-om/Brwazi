<x-layout>
    <section class="w-full bg-gray-50">
        <div class="mx-auto max-w-6xl px-4 py-8 md:py-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Image / Status -->
                <div class="lg:col-span-7">
                    <div class="relative group rounded-3xl overflow-hidden shadow-md bg-gray-100">
                        <div class="w-full h-[260px] sm:h-[340px] md:h-[420px] lg:h-[560px]">
                            <img src="{{ $auction['image_url'] ?? asset('imgs/mazad/' . $auction['image']) }}" alt="{{ $auction['title'] }}"
                                loading="lazy" fetchpriority="high"
                                class="w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-105">
                        </div>
                        <div
                            class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/20 via-black/5 to-transparent">
                        </div>
                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                            <span
                                class="inline-flex items-center gap-2 rounded-full bg-emerald-500 text-white px-4 py-1.5 text-xs font-bold shadow">
                                {{ $auction['status'] }}
                                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                            </span>
                            @if($auction['has_reserve'])
                                @if($auction['reserve_met'])
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-600/90 text-white px-3 py-1 text-[11px] font-semibold shadow">
                                        <i class="fa-solid fa-flag-checkered text-[10px]"></i>
                                        السعر الاحتياطي تحقق
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/90 text-white px-3 py-1 text-[11px] font-semibold shadow">
                                        <i class="fa-regular fa-flag text-[10px]"></i>
                                        سعر احتياطي: {{ number_format($auction['reserve_price']) }}
                                    </span>
                                @endif
                            @endif
                            <span id="countdownBadge"
                                class="hidden items-center gap-1 rounded-full bg-white/90 backdrop-blur px-3 py-1 text-[11px] font-medium text-gray-700 shadow">
                                <i class="far fa-clock text-indigo-600"></i>
                                <span id="countdownBadgeText"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Info -->
                <div class="lg:col-span-5 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6">
                        <div class="text-right flex-1">
                            <h1 class="text-3xl md:text-4xl font-extrabold text-indigo-950 leading-tight">
                                {{ $auction['title'] }}
                            </h1>
                            <div class="mt-2 flex items-center justify-end gap-2 text-gray-500 text-sm">
                                <span>{{ $auction['artist'] }}</span>
                                <span
                                    class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-200 text-gray-600">
                                    <i class="fas fa-user text-[10px]"></i>
                                </span>
                            </div>
                        </div>
                        <div class="text-left">
                            <span class="block text-rose-500 text-sm font-semibold mb-1">الوقت المتبقي</span>
                            <div id="countdown"
                                class="text-3xl md:text-5xl font-extrabold text-rose-600 tracking-wider">
                                --:--:--
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="p-4 bg-white rounded border border-gray-100 shadow-sm">
                            <div class="text-xs text-gray-500 mb-1">عدد المزايدين</div>
                            <div class="text-2xl font-extrabold text-indigo-900">{{ $auction['bidders_count'] }}</div>
                            <div class="text-[11px] text-gray-400">مشارك</div>
                        </div>
                        <div class="p-4 bg-white  rounded border border-grey-300 shadow-sm">
                            <div class="text-xs text-gray-500 mb-1 font-bold">أعلى مزايدة</div>
                            <div class="text-2xl font-extrabold text-indigo-900">
                                {{ number_format($auction['highest_bid']) }}
                            </div>
                            <div class="text-[11px] text-gray-400">{{ $auction['currency'] }}</div>
                        </div>

                        <div class="p-4 bg-white  rounded border border-gray-100 shadow-sm">
                            <div class="text-xs text-gray-500 mb-1">سعر البداية</div>
                            <div class="text-2xl font-extrabold text-indigo-900">
                                {{ number_format($auction['base_price']) }}
                            </div>
                            <div class="text-[11px] text-gray-400">{{ $auction['currency'] }}</div>
                        </div>
                    </div>
                    @if($auction['has_reserve'] && !$auction['reserve_met'])
                        <div class="mt-2 text-xs text-amber-600 font-medium flex items-center gap-1">
                            <i class="fa-regular fa-clock"></i>
                            لم يتم الوصول للسعر الاحتياطي بعد.
                        </div>
                    @endif
                    @if($auction['ended_unsold_by_reserve'])
                        <div class="mt-3 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-semibold flex items-center gap-2">
                            <i class="fa-solid fa-circle-info"></i>
                            انتهى المزاد بدون بيع (السعر الاحتياطي لم يتحقق).
                        </div>
                    @endif
                    <p class="text-gray-600 leading-7 text-sm md:text-base">{{ $auction['description'] }}</p>
                    <div class="flex flex-wrap gap-3 text-sm bg-white border rounded-2xl p-4">
                        <div class="flex items-center gap-1 text-gray-500">
                            <i class="fa-solid fa-maximize text-indigo-600"></i>
                            {{ $auction['size'] }}
                        </div>
                        <div class="flex items-center gap-1 text-gray-500"><i
                                class="far fa-calendar text-indigo-600"></i>{{ $auction['year'] }}</div>
                        <div class="flex items-center gap-1 text-gray-500">

                            <i class="fa-solid fa-list text-indigo-600"></i>
                            {{ $auction['type'] }}
                        </div>
                        <div class="flex items-center gap-1 text-gray-500"><i
                                class="far fa-check-circle text-indigo-600"></i>{{ $auction['condition'] }}</div>
                        <div class="flex items-center gap-1 text-gray-500">حد أدنى للزيادة<i
                                class="fa-solid fa-hand-holding-dollar"></i>:
                            {{ $auction['min_increment'] }} {{ $auction['currency'] }}
                        </div>
                    </div>
                    @php($isLive = $auction['status'] === 'live')
                    @php($isEnded = $auction['status'] === 'ended')
                    <form id="bidForm" class="space-y-4" autocomplete="off" action="{{ url('/mazad/' . $auction['id'] . '/bid') }}"
                        method="post">
                        @csrf
                        <div class="flex items-center gap-3">
                            <div class="relative w-40">
                                <input type="number" name="amount"
                                    min="{{ $auction['highest_bid'] + $auction['min_increment'] }}"
                                    step="{{ $auction['min_increment'] }}" {{ $isLive ? 'required' : 'disabled' }} placeholder="مبلغك"
                                    class="w-full h-14 rounded-2xl bg-gray-100 text-gray-700 placeholder-gray-400 px-4 outline-none focus:ring-2 focus:ring-indigo-500 {{ $isLive ? '' : 'opacity-50 cursor-not-allowed' }}">
                            </div>
                            <button type="submit" {{ $isLive ? '' : 'disabled' }}
                                class="flex-1 h-14 rounded-2xl bg-indigo-950 hover:bg-indigo-900 text-white font-bold tracking-wide transition flex items-center justify-center gap-2 {{ $isLive ? '' : 'opacity-60 cursor-not-allowed' }}">
                                زايد الآن
                                <i class="fas fa-gavel text-sm"></i>
                            </button>
                        </div>
                        @unless($isLive)
                            <div class="text-xs text-rose-600 font-medium">المزاد غير مباشر حالياً. لا يمكن تقديم مزايدات الآن.</div>
                        @endunless
                        <div id="bidFeedback" class="text-xs text-gray-500"></div>
                    </form>

                    @if($isEnded && session('user_is_winner_'.$auction['id']))
                        <form action="{{ url('/mazad/' . $auction['id'] . '/pay') }}" method="post" class="mt-3">
                            @csrf
                            <button type="submit" class="w-full h-12 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold flex items-center justify-center gap-2">
                                ادفع الآن لإكمال الشراء
                                <i class="fa-solid fa-credit-card"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        </div>
    </section>
    <!-- Bid history -->
    <section class="w-full border-t bg-white">
        <div class="mx-auto max-w-5xl px-4 py-10">
            <div class="flex items-center gap-3 mb-6">
                <h2 class="text-2xl md:text-3xl font-extrabold text-indigo-950 tracking-wide">تاريخ المزايدات</h2>
                <span
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full border border-indigo-950/20 text-indigo-950">
                    <i class="fa-solid fa-calendar-days"></i></span>
            </div>
            <div class="h-[360px] overflow-y-auto pr-1 custom-scroll" id="bidsList">
                @foreach($bidItems as $bid)
                    <article
                        class="bg-indigo-50/40 hover:bg-indigo-50 rounded-2xl px-5 md:px-6 py-5 mb-4 flex items-center justify-between border border-gray-100 transition">
                        <div class="text-indigo-950 text-lg font-bold">{{ $bid['user'] }}</div>
                        <div class="text-left">
                            <div class="flex items-end gap-3">
                                <span class="text-indigo-950 text-sm font-semibold">{{ $auction['currency'] }}</span>
                                <span
                                    class="text-indigo-900 text-3xl leading-none font-extrabold">{{ number_format($bid['amount']) }}</span>
                            </div>
                            <div class="mt-2 text-gray-400 text-xs">{{ $bid['time_ago'] }}</div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <script id="auctionData"
        type="application/json">{!! json_encode($auction, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const auctionData = JSON.parse(document.getElementById('auctionData').textContent);
            const endAt = new Date(auctionData.end_at);
            const countdownEl = document.getElementById('countdown');
            const badge = document.getElementById('countdownBadge');
            const badgeText = document.getElementById('countdownBadgeText');
            const bidForm = document.getElementById('bidForm');

            function pad(n) { return n.toString().padStart(2, '0'); }
            function renderCountdown() {
                const now = new Date();
                let diff = Math.max(0, endAt - now);
                const hrs = Math.floor(diff / 1000 / 60 / 60);
                diff -= hrs * 3600 * 1000;
                const mins = Math.floor(diff / 1000 / 60);
                diff -= mins * 60 * 1000;
                const secs = Math.floor(diff / 1000);
                const formatted = `${pad(hrs)}:${pad(mins)}:${pad(secs)}`;
                countdownEl.textContent = formatted;
                badgeText.textContent = formatted;
                if (hrs < 1) { badge.classList.remove('hidden'); badge.classList.add('flex'); }
                if (hrs === 0 && mins === 0 && secs === 0) {
                    countdownEl.classList.add('text-gray-400');
                }
            }
            setInterval(renderCountdown, 1000); renderCountdown();

            // Form now redirects to insurance page; client-side bid simulation removed.
        });
    </script>
</x-layout>
