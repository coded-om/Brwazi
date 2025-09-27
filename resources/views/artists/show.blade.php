@php
    $title = trim(($artist->full_name ?: 'فنان') . ' - ملف الفنان');
@endphp
@push('pre-alpine')
    {{-- Page report modal script (reuses global reportModal if loaded) --}}
    <script>
        // Simple fallback if main art-show.js not present
        window.__artistReportModal = window.__artistReportModal || null;
        window.reportModal = window.reportModal || function (endpoint) {
            return {
                open: false,
                step: 1,
                selectedType: '',
                details: '',
                busy: false,
                error: '',
                endpoint,
                defaultEndpoint: endpoint,
                types: [
                    { value: 'spam', label: 'رسائل مزعجة' },
                    { value: 'adult', label: 'محتوى للبالغين' },
                    { value: 'fraud', label: 'احتيال / نصب' },
                    { value: 'illegal_or_harmful', label: 'ضار أو غير قانوني' },
                    { value: 'rights_violation', label: 'ينتهك حقوقي' },
                    { value: 'misleading', label: 'مضلل' }
                ],
                init() {
                    this.defaultEndpoint = this.endpoint;
                    window.__artistReportModal = this;
                },
                toggleType(v) { this.selectedType = this.selectedType === v ? '' : v; },
                goNext() { if (!this.selectedType) { this.error = 'اختر نوع البلاغ'; return; } this.error = ''; if (this.selectedType === 'rights_violation') { this.step = 2 } else { this.submit(); } },
                submit() { if (this.busy) return; if (this.selectedType === 'rights_violation' && !this.details.trim()) { this.error = 'فضلاً اشرح المشكلة'; return; } this.busy = true; fetch(this.endpoint, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify({ type: this.selectedType, details: this.details }) }).then(r => r.json().catch(() => ({})).then(data => { if (!r.ok || !data.success) { this.error = data.message || 'تعذر الإرسال'; return; } this.step = 3; })).catch(() => { this.error = 'تعذر الإرسال'; }).finally(() => { this.busy = false; }); },
                openModal(config = {}) {
                    const { endpoint, preselect } = config || {};
                    this.endpoint = endpoint || this.defaultEndpoint;
                    this.open = true;
                    this.step = 1;
                    this.error = '';
                    this.details = '';
                    this.busy = false;
                    this.selectedType = preselect || '';
                    if (this.selectedType === 'rights_violation') {
                        this.step = 2;
                    }
                },
                close() {
                    this.open = false;
                    setTimeout(() => {
                        this.step = 1;
                        this.selectedType = '';
                        this.details = '';
                        this.error = '';
                        this.endpoint = this.defaultEndpoint;
                    }, 250);
                }
            };
        };
        const openReportModal = (btn, attempt = 0) => {
            if (window.__artistReportModal) {
                const payload = btn ? {
                    endpoint: btn.getAttribute('data-report-endpoint') || undefined,
                    preselect: btn.getAttribute('data-report-preselect') || undefined
                } : {};
                window.__artistReportModal.openModal(payload);
                return;
            }
            if (attempt > 20) {
                console.warn('Report modal instance not ready');
                return;
            }
            setTimeout(() => openReportModal(btn, attempt + 1), 50);
        };

        document.addEventListener('click', (event) => {
            const btn = event.target.closest('[data-report-btn]');
            if (!btn) return;
            event.preventDefault();
            openReportModal(btn);
        });
    </script>
@endpush
<x-layout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <nav class="mb-6 text-sm text-gray-500" aria-label="breadcrumb">
            <ol class="inline-flex items-center gap-2">
                <li><a href="{{ route('home') }}" class="hover:text-indigo-600">الرئيسية</a></li>
                <li>/</li>
                <li><a href="{{ route('artists.index') }}" class="hover:text-indigo-600">الفنانين</a></li>
                <li>/</li>
                <li class="text-gray-700">{{ $artist->full_name }}</li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row gap-8">
            <div class="w-full md:w-72 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-200 overflow-hidden">
                    <div class="h-48 bg-gray-100 relative">
                        <img src="{{ $artist->profile_image_url }}" alt="{{ $artist->full_name }}"
                            class="w-full h-full object-cover transition duration-500 ease-out blur-sm" loading="lazy"
                            onload="this.classList.remove('blur-sm');" />
                        @if($artist->isVerified())
                            <span
                                class="absolute top-3 right-3 inline-flex items-center gap-1 bg-indigo-600/90 text-white text-xs font-medium px-2.5 py-1 rounded-full shadow">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path
                                        d="M9.707 13.293 17 6l1.414 1.414-8.414 8.414L6 12.828l1.414-1.414 2.293 2.293z" />
                                </svg>
                                موثق
                            </span>
                        @endif
                    </div>
                    <div class="p-5 space-y-3">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                                <span>{{ $artist->full_name }}</span>
                                @if($artist->isPremium())
                                    <span title="مميز" class="text-amber-500" aria-label="مستخدم مميز">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 2 9.19 8.26 2 9.27l5.46 4.73L5.82 21 12 17.77 18.18 21l-1.64-6.99L22 9.27l-7.19-1.01L12 2z" />
                                        </svg>
                                    </span>
                                @endif
                            </h1>
                            @if($artist->tagline)
                                <p class="text-sm text-indigo-600/90 font-medium mt-1">{{ $artist->tagline }}</p>
                            @endif
                        </div>
                        @if($artist->bio)
                            <p class="text-sm leading-relaxed text-gray-700 whitespace-pre-line">{{ $artist->bio }}</p>
                        @else
                            <p class="text-sm text-gray-400">لا توجد نبذة مضافة.</p>
                        @endif
                        <div class="flex flex-wrap items-center gap-3 pt-2 text-xs text-gray-500">
                            @if($artist->country)
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full bg-gray-100">
                                    <svg class="w-3.5 h-3.5 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z" />
                                    </svg>
                                    {{ $artist->country }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full bg-gray-100">
                                <svg class="w-3.5 h-3.5 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm8-1h-2.26A8 8 0 0 0 4.26 7H2v2h2.05c-.03.33-.05.66-.05 1a7.96 7.96 0 0 0 .3 2H2v2h2.86A8 8 0 0 0 17.74 14H20v-2h-2.05c.03-.33.05-.66.05-1 0-.68-.07-1.35-.2-2H20V7Z" />
                                </svg>
                                {{ $artist->status_label }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full bg-gray-100">
                                <svg class="w-3.5 h-3.5 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm8-1h-2.26A8 8 0 0 0 4.26 7H2v2h2.05c-.03.33-.05.66-.05 1a7.96 7.96 0 0 0 .3 2H2v2h2.86A8 8 0 0 0 17.74 14H20v-2h-2.05c.03-.33.05-.66.05-1 0-.68-.07-1.35-.2-2H20V7Z" />
                                </svg>
                                مجموع الأعمال: {{ (int) $artworks->total() }}
                            </span>
                        </div>
                        @auth
                            @if(auth()->id() !== $artist->id)
                                <a href="{{ route('user.messages', ['to' => $artist->id]) }}"
                                    class="inline-flex items-center justify-center gap-2 w-full rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2.5 transition">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                        <path
                                            d="M4 4h16v12H5.17L4 17.17V4Zm0-2a2 2 0 0 0-2 2v18l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H4Zm4 9h8v2H8v-2Zm0-3h8v2H8V8Z" />
                                    </svg>
                                    رسالة خاصة
                                </a>
                                <div class="flex justify-end mt-2">
                                    <button type="button" data-report-btn
                                        data-report-endpoint="{{ route('artists.report', $artist) }}" title="إبلاغ عن مشكلة"
                                        class="h-10 w-10 inline-flex items-center justify-center rounded-full border bg-white text-amber-600 hover:bg-amber-50 transition">
                                        <span class="sr-only">إبلاغ عن مشكلة</span>
                                        <i class="fa-solid fa-flag"></i>
                                    </button>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-xl font-bold text-indigo-900 flex items-center gap-2 mb-5">
                    <span>أعمال {{ $artist->full_name }}</span>
                    <img src="{{ asset('imgs/icons-color/eye-category.svg') }}" alt="icon" class="w-6 h-6" />
                </h2>
                @if($artworks->count())
                    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($artworks as $aw)
                            @php
                                // استخدم الأكسسور إن وجد ellers توليد من path
                                $img = $aw->primary_image_url;
                                if (!$img) {
                                    $first = $aw->images->first();
                                    if ($first) {
                                        $p = ltrim($first->path, '/');
                                        // لو أصلاً يبدأ بـ storage/ لا نضيفه مرتين
                                        if (str_starts_with($p, 'storage/')) {
                                            $img = asset($p);
                                        } else {
                                            $img = asset('storage/' . $p);
                                        }
                                    }
                                }
                                if (!$img) {
                                    $img = asset('imgs/placeholder-art.png');
                                }
                            @endphp
                            <div class="relative">
                                @auth
                                    @if(auth()->id() !== $aw->user_id)
                                        <button type="button" data-report-btn data-report-endpoint="{{ route('art.report', $aw) }}"
                                            class="absolute top-3 left-3 z-10 inline-flex items-center justify-center h-9 w-9 rounded-full bg-white/90 text-amber-600 border border-amber-100 shadow-sm hover:bg-amber-50 focus:outline-none focus:ring-2 focus:ring-amber-200 transition"
                                            title="إبلاغ عن هذا العمل">
                                            <span class="sr-only">إبلاغ عن هذا العمل</span>
                                            <i class="fa-solid fa-flag"></i>
                                        </button>
                                    @endif
                                @endauth
                                <a href="{{ route('art.show', $aw->id) }}"
                                    class="group block rounded-xl overflow-hidden bg-white ring-1 ring-gray-200 shadow-sm hover:shadow-md transition">
                                    <div class="h-44 bg-gray-100 relative">
                                        <img src="{{ $img }}" alt="{{ $aw->title }}" loading="lazy"
                                            class="w-full h-full object-cover group-hover:scale-[1.02] transition duration-500 ease-out blur-sm"
                                            onload="this.classList.remove('blur-sm');" />
                                    </div>
                                    <div class="p-4 space-y-1">
                                        <div class="font-semibold text-gray-900 truncate">{{ $aw->title }}</div>
                                        <div class="text-xs text-gray-500 flex items-center justify-between">
                                            <span>{{ number_format((float) $aw->price, 2) }} ر.س</span>
                                            @if($aw->year)
                                                <span class="inline-flex items-center gap-1 text-[11px] text-indigo-600">
                                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                                                        <path
                                                            d="M7 2v2H5v2H3v2h2v4H3v2h2v2h2v2h2v2h2v-2h4v2h2v-2h2v-2h2v-2h-2v-4h2V8h-2V6h-2V4h-2V2h-2v2h-4V2H9v2H7V2H5v2h2V2Zm0 4h2v2H7V6Zm0 4h2v4H7v-4Zm6-4h4v2h2v2h-2v4h2v2h-2v2h-4v-2h-4v2H9v-2H7v-2h2v-4H7V8h2V6h2v2h4V6Z" />
                                                    </svg>
                                                    {{ $aw->year }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6">{{ $artworks->links() }}</div>
                @else
                    <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center text-gray-500">
                        لا توجد أعمال منشورة لهذا الفنان بعد.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>

@auth
    <!-- Report Modal for artist profile -->
    <div data-report-modal-root x-data="reportModal('{{ route('artists.report', $artist) }}')" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center px-4" @keydown.escape.window="close()" x-show="open"
        x-transition.opacity @click.self="close()" aria-modal="true" role="dialog" :aria-hidden="(!open).toString()">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <!-- Step 1 -->
        <template x-if="step===1">
            <div class="relative w-full max-w-sm">
                <div class="relative bg-white rounded-[32px] shadow-[0_24px_80px_rgba(15,23,42,0.12)] overflow-hidden">
                    <button type="button" class="absolute top-4 left-4 text-slate-400 hover:text-slate-600 transition"
                        @click="close()">
                        <i class="fa-solid fa-xmark text-2xl"></i>
                        <span class="sr-only">إغلاق</span>
                    </button>
                    <div class="px-8 pt-9 pb-10 space-y-6">
                        <div class="flex items-start justify-between">
                            <h3 class="text-2xl font-bold text-slate-900">أبلِغ</h3>
                        </div>
                        <div class="space-y-3">
                            <template x-for="t in types" :key="t.value">
                                <label class="block cursor-pointer" @click.prevent="toggleType(t.value)">
                                    <input type="checkbox" class="sr-only" :checked="selectedType===t.value">
                                    <div class="flex items-center justify-between rounded-[20px] px-5 py-4 transition duration-200"
                                        :class="selectedType===t.value
                                                    ? 'bg-white shadow-sm ring-2 ring-indigo-600 text-slate-900'
                                                    : 'bg-[#F5F6FB] text-slate-600 hover:bg-[#E9EBF5]'">
                                        <span class="text-sm font-medium" x-text="t.label"></span>
                                        <span
                                            class="flex items-center justify-center w-7 h-7 rounded-full border transition"
                                            :class="selectedType===t.value
                                                        ? 'border-indigo-600 bg-indigo-600 text-white shadow-inner'
                                                        : 'border-slate-300 bg-white text-transparent'">
                                            <svg class="w-3.5 h-3.5" viewBox="0 0 16 16" fill="currentColor">
                                                <path
                                                    d="M6.173 12.414 2.758 9l1.414-1.414 2.001 2 5.656-5.657L13.243 5.343z" />
                                            </svg>
                                        </span>
                                    </div>
                                </label>
                            </template>
                        </div>
                        <div class="pt-2">
                            <button type="button" @click="goNext()" :disabled="!selectedType || busy"
                                class="w-full h-14 rounded-full bg-indigo-900 text-white font-semibold flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed">
                                <span x-show="!busy">أبلِغ</span>
                                <span x-show="busy" class="flex items-center gap-2"><i
                                        class="fa-solid fa-spinner fa-spin"></i> جارٍ</span>
                            </button>
                            <p class="text-xs text-center text-red-600 mt-3" x-show="error" x-text="error"></p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <!-- Step 2 details -->
        <template x-if="step===2">
            <div class="relative w-full max-w-sm">
                <div class="relative bg-white rounded-[32px] shadow-[0_24px_80px_rgba(15,23,42,0.12)] overflow-hidden">
                    <button type="button" class="absolute top-4 left-4 text-slate-400 hover:text-slate-600 transition"
                        @click="close()">
                        <i class="fa-solid fa-xmark text-2xl"></i>
                        <span class="sr-only">إغلاق</span>
                    </button>
                    <div class="px-8 pt-9 pb-10 space-y-6">
                        <h3 class="text-2xl font-bold text-slate-900">أبلِغ</h3>
                        <div class="space-y-3">
                            <label class="block text-sm font-semibold text-slate-700">ينتهك حقوقي</label>
                            <textarea x-model="details" rows="6"
                                class="w-full rounded-[24px] border border-indigo-100 bg-[#F5F6FB] focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/60 text-sm text-slate-700 p-4 resize-none"
                                placeholder="تفاصيل أكثر حول البلاغ"></textarea>
                            <p class="text-xs text-slate-400">الرجاء توضيح طبيعة الانتهاك</p>
                            <p class="text-xs text-red-600" x-show="error" x-text="error"></p>
                        </div>
                        <div class="flex gap-3 pt-1">
                            <button type="button" @click="step=1"
                                class="flex-1 h-12 rounded-full border border-slate-200 text-slate-700 font-medium hover:bg-slate-50">رجوع</button>
                            <button type="button" @click="submit()" :disabled="busy || !details.trim()"
                                class="flex-1 h-12 rounded-full bg-indigo-900 text-white font-semibold disabled:opacity-40 disabled:cursor-not-allowed">إرسال</button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <!-- Step 3 done -->
        <template x-if="step===3">
            <div class="relative w-full max-w-sm">
                <div
                    class="relative bg-white rounded-[32px] shadow-[0_24px_80px_rgba(15,23,42,0.12)] overflow-hidden text-center px-8 pt-9 pb-10 space-y-6">
                    <button type="button" class="absolute top-4 left-4 text-slate-400 hover:text-slate-600 transition"
                        @click="close()">
                        <i class="fa-solid fa-xmark text-2xl"></i>
                        <span class="sr-only">إغلاق</span>
                    </button>
                    <div
                        class="mx-auto w-16 h-16 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-3xl">
                        <i class="fa-solid fa-flag-checkered"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">تم إرسال البلاغ</h3>
                    <p class="text-sm text-slate-600">شكرًا لك. سيقوم الفريق بمراجعة البلاغ في أقرب وقت.</p>
                    <button type="button" @click="close()"
                        class="w-full h-12 rounded-full bg-indigo-900 text-white font-semibold">إغلاق</button>
                </div>
            </div>
        </template>
    </div>
@endauth