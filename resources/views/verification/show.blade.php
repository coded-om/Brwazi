<x-layout>
    <div class="max-w-6xl mx-auto px-4 py-8" dir="rtl">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">عرض طلب التوثيق</h1>
            <a href="{{ url()->previous() }}" class="text-sm text-purple-600 hover:underline">رجوع</a>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="md:col-span-1 space-y-4">
                <div class="rounded-xl border border-gray-200 bg-white p-4 text-sm space-y-2">
                    <div class="font-semibold text-gray-900">البيانات الأساسية</div>
                    <div><span class="text-gray-500">الاسم:</span> {{ $request->full_name }}</div>
                    <div><span class="text-gray-500">النوع:</span>
                        {{ $request->form_type === 'visual' ? 'الفنون التشكيلية والرقمية' : 'التصوير الضوئي' }}</div>
                    <div><span class="text-gray-500">الحالة:</span> <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs {{ $request->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($request->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700') }}">{{ $request->status }}</span>
                    </div>
                    <div><span class="text-gray-500">البريد:</span> {{ $request->email }}</div>
                    <div><span class="text-gray-500">الهاتف:</span> {{ $request->phone }}</div>
                    <div><span class="text-gray-500">التخصصات:</span> {{ implode(', ', $request->specialties ?? []) }}
                    </div>
                    <div><span class="text-gray-500">تاريخ الإرسال:</span>
                        {{ $request->submitted_at?->format('Y-m-d H:i') }}</div>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-4 text-sm space-y-2">
                    <div class="font-semibold text-gray-900 mb-2">الملفات الأساسية</div>
                    <ul class="space-y-1 list-disc pr-4">
                        @if($request->id_file_url)
                            <li><a href="{{ $request->id_file_url }}" target="_blank"
                                    class="text-purple-600 hover:underline">الهوية / جواز</a></li>
                        @endif
                        @if($request->avatar_file_url)
                            <li><a href="{{ $request->avatar_file_url }}" target="_blank"
                                    class="text-purple-600 hover:underline">الصورة الشخصية</a></li>
                        @endif
                        @if($request->cv_file_url)
                            <li><a href="{{ $request->cv_file_url }}" target="_blank"
                                    class="text-purple-600 hover:underline">السيرة الفنية</a></li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="md:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">الأعمال ({{ count($works) }})</h2>
                    <div class="text-xs text-gray-500">انقر على أي صورة للتكبير</div>
                </div>
                @if(empty($works))
                    <div
                        class="rounded-xl border border-dashed border-gray-200 bg-white p-8 text-center text-gray-500 text-sm">
                        لا توجد أعمال مرفوعة.</div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3" id="worksGrid">
                        @foreach($works as $i => $url)
                            <button type="button"
                                class="group relative aspect-square w-full overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm hover:shadow focus:outline-none focus:ring-2 focus:ring-purple-500"
                                data-index="{{ $i }}">
                                <img src="{{ $url }}" alt="عمل رقم {{ $i + 1 }}"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                    loading="lazy" />
                                <span
                                    class="absolute top-1 right-1 bg-black/50 text-white text-[10px] px-1.5 py-0.5 rounded">{{ $i + 1 }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif

                {{-- Diagnostics (only visible if debugging; you can remove later) --}}
                @if(!empty($worksDiagnostics))
                    <div class="mt-4 mb-6 rounded-lg border border-amber-300 bg-amber-50 p-3 text-[11px] text-amber-800 space-y-1">
                        <div class="font-semibold">فحص المسارات (لن تظهر للمستخدم النهائي - احذفها بعد التأكد)
                        </div>
                        <div>Storage link موجود؟ : <strong>{{ $storageLinked ? 'نعم' : 'لا' }}</strong></div>
                        <ul class="list-disc pr-4 space-y-1">
                            @foreach($worksDiagnostics as $d)
                                <li>
                                    <code>{{ $d['path'] }}</code>
                                    — موجود بالـ disk؟ <strong>{{ $d['exists'] ? '✔' : '✖' }}</strong>
                                    — URL: <a href="{{ $d['url'] }}" target="_blank"
                                            class="underline text-purple-600">فتح</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Viewer -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" data-close></div>
        <div class="relative z-10 flex h-full w-full items-center justify-center p-4">
            <div class="relative w-full max-w-5xl">
                <div class="flex items-center justify-between mb-2 text-white text-sm">
                    <div id="counter"></div>
                    <button class="px-3 py-1 rounded bg-white/10 hover:bg-white/20" data-close>إغلاق ✕</button>
                </div>
                <div class="relative aspect-video w-full overflow-hidden rounded-xl bg-black">
                    <img id="modalImage" src="" alt="عرض العمل" class="h-full w-full object-contain select-none"
                        draggable="false" />
                    <button type="button" id="prevBtnModal"
                        class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white w-10 h-10 rounded-full grid place-items-center text-xl">‹</button>
                    <button type="button" id="nextBtnModal"
                        class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 text-white w-10 h-10 rounded-full grid place-items-center text-xl">›</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const works = JSON.parse('{!! addslashes(json_encode($works)) !!}');
            const grid = document.getElementById('worksGrid');
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            const counter = document.getElementById('counter');
            const prevBtn = document.getElementById('prevBtnModal');
            const nextBtn = document.getElementById('nextBtnModal');
            let idx = 0;
            function open(i) {
                if (!works.length) return;
                idx = (i + works.length) % works.length;
                modalImg.src = works[idx];
                counter.textContent = `${idx + 1} / ${works.length}`;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
            function close() {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
            function next() { open(idx + 1); }
            function prev() { open(idx - 1); }
            grid?.addEventListener('click', e => {
                const btn = e.target.closest('button[data-index]');
                if (!btn) return;
                open(parseInt(btn.dataset.index, 10));
            });
            modal.addEventListener('click', e => { if (e.target.dataset.close !== undefined) close(); });
            document.addEventListener('keydown', e => {
                if (modal.classList.contains('hidden')) return;
                if (e.key === 'Escape') close();
                if (e.key === 'ArrowRight') next();
                if (e.key === 'ArrowLeft') prev();
            });
            nextBtn.addEventListener('click', next);
            prevBtn.addEventListener('click', prev);
        })();
    </script>
</x-layout>
