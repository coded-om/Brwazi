@php
    $imgUrls = ($artwork->images ?? collect())->map(fn($i) => asset('storage/' . ltrim($i->path, '/')));
    $isAuction = $artwork->sale_mode === 'auction';
    $isFixed = $artwork->sale_mode === 'fixed';
    $reportImageOptions = ($artwork->images ?? collect())->values()->map(function ($image, $index) {
        $label = $image->is_primary
            ? 'الصورة الرئيسية'
            : 'صورة رقم ' . ($index + 1);
        return [
            'id' => $image->id,
            'url' => asset('storage/' . ltrim($image->path, '/')),
            'label' => $label,
        ];
    });
    $reportImagesJson = $reportImageOptions->toJson(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
@endphp
@push('pre-alpine')
    {{-- Load page script only via Vite (no public/ fallback to avoid 404). Ensure entry exists in vite.config.js inputs.
    --}}
    @vite(['resources/js/pages/art-show.js'])
    {{-- Minimal safety stub so Alpine doesn't break if Vite dev server down --}}
    <script>
        window.__reportModalInstance = window.__reportModalInstance || null;
        window.reportModal = window.reportModal || function (endpoint, options = {}) {
            return {
                open: false,
                step: 1,
                selectedType: '',
                selectedImageId: typeof options.defaultImageId !== 'undefined'
                    ? options.defaultImageId
                    : null,
                details: '',
                busy: false,
                error: '',
                endpoint,
                defaultEndpoint: endpoint,
                images: options.images || [],
                types: [
                    { value: 'spam', label: 'رسائل إلكترونية مزعجة' },
                    { value: 'adult', label: 'محتوى للبالغين' },
                    { value: 'fraud', label: 'الاحتيال/النصب' },
                    { value: 'illegal_or_harmful', label: 'ضار أو غير قانوني' },
                    { value: 'rights_violation', label: 'ينتهك حقوقي' },
                    { value: 'misleading', label: 'التضليل والمعلومات المضللة' },
                ],
                init() {
                    this.defaultEndpoint = this.endpoint;
                    if (!this.selectedImageId && this.images.length === 1) {
                        this.selectedImageId = this.images[0].id;
                    }
                    window.__reportModalInstance = this;
                },
                selectImage(id) {
                    this.selectedImageId = Number(id);
                },
                toggleType(v) {
                    this.selectedType = this.selectedType === v ? '' : v;
                },
                goNext() {
                    if (this.images.length && !this.selectedImageId) {
                        this.error = 'فضلاً اختر الصورة التي تحتوي على المشكلة';
                        return;
                    }
                    if (!this.selectedType) {
                        this.error = 'اختر نوع البلاغ';
                        return;
                    }
                    this.error = '';
                    if (this.selectedType === 'rights_violation') {
                        this.step = 2;
                    } else {
                        this.submit();
                    }
                },
                submit() {
                    if (this.busy) return;
                    this.error = '';
                    if (this.selectedType === 'rights_violation' && !this.details.trim()) {
                        this.error = 'فضلاً اشرح المشكلة المتعلقة بالحقوق';
                        return;
                    }
                    // Degraded fallback: simply close modal.
                    this.step = 3;
                },
                openModal(config = {}) {
                    this.endpoint = config.endpoint || this.defaultEndpoint;
                    this.selectedType = config.preselect || '';
                    if (typeof config.preselectImage !== 'undefined') {
                        this.selectedImageId = config.preselectImage;
                    } else if (this.images.length === 1) {
                        this.selectedImageId = this.images[0].id;
                    } else {
                        this.selectedImageId = null;
                    }
                    this.step = this.selectedType === 'rights_violation' ? 2 : 1;
                    this.error = '';
                    this.details = '';
                    this.busy = false;
                    this.open = true;
                },
                close() {
                    this.open = false;
                    this.step = 1;
                    this.selectedType = '';
                    this.details = '';
                    this.error = '';
                    this.busy = false;
                    this.endpoint = this.defaultEndpoint;
                    this.selectedImageId = this.images.length === 1 ? this.images[0].id : null;
                }
            };
        };
    </script>
@endpush
<x-layout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:underline">الصفحة الرئيسية</a>
            <span class="mx-2">/</span>
            <a href="/art" class="hover:underline">الأعمال الفنية</a>
            <span class="mx-2">/</span>
            <span class="text-gray-700">{{ $artwork->title }}</span>
        </nav>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
            <!-- النص -->
            <div class="max-w-xl">
                <h1 class="text-3xl font-extrabold text-[#1E1B4B]">{{ $artwork->title }}</h1>
                @php $madeYear = $artwork->year ?: ($artwork->published_at ? $artwork->published_at->format('Y') : null); @endphp
                @if($isFixed)
                    <div class="flex items-center flex-wrap gap-3 text-2xl mt-2">
                        <div>{{ number_format((float) ($artwork->price ?? 0), 0) }} <span
                                class="text-gray-500 text-base">ريال</span></div>
                        @if($madeYear)
                            <div class="text-sm text-indigo-700 font-medium flex items-center gap-1">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M7 2v2H5v2H3v2h2v4H3v2h2v2h2v2h2v2h2v-2h4v2h2v-2h2v-2h2v-2h-2v-4h2V8h-2V6h-2V4h-2V2h-2v2h-4V2H9v2H7V2H5v2h2V2Zm0 4h2v2H7V6Zm0 4h2v4H7v-4Zm6-4h4v2h2v2h-2v4h2v2h-2v2h-4v-2h-4v2H9v-2H7v-2h2v-4H7V8h2V6h2v2h4V6Z" />
                                </svg>
                                {{ $madeYear }}
                            </div>
                        @endif
                    </div>
                @elseif($isAuction)
                    <div class="flex items-center flex-wrap gap-3 text-2xl mt-2">
                        <div>{{ number_format((float) ($artwork->auction_start_price ?? 0), 0) }} <span
                                class="text-gray-500 text-base">مزاد</span></div>
                        @if($madeYear)
                            <div class="text-sm text-indigo-700 font-medium flex items-center gap-1">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M7 2v2H5v2H3v2h2v4H3v2h2v2h2v2h2v2h2v-2h4v2h2v-2h2v-2h2v-2h-2v-4h2V8h-2V6h-2V4h-2V2h-2v2h-4V2H9v2H7V2H5v2h2V2Zm0 4h2v2H7V6Zm0 4h2v4H7v-4Zm6-4h4v2h2v2h-2v4h2v2h-2v2h-4v-2h-4v2H9v-2H7v-2h2v-4H7V8h2V6h2v2h4V6Z" />
                                </svg>
                                {{ $madeYear }}
                            </div>
                        @endif
                    </div>
                @endif

                @php
                    $artist = $artwork->user;
                    $artistName = $artist?->full_name;
                    $artistAvatar = $artist?->profile_image_url ?? asset('imgs/default-avatar.png');
                    $artistUrl = $artist?->id ? route('artists.show', $artist->id) : null;
                @endphp
                @if($artistName)
                    <div class="mt-3 flex items-center gap-3">
                        <a href="{{ $artistUrl }}" class="group flex items-center gap-2 hover:opacity-90">
                            <img src="{{ $artistAvatar }}" alt="{{ $artistName }}"
                                class="h-9 w-9 rounded-full object-cover ring-1 ring-gray-200">
                            <div class="leading-tight">
                                <div
                                    class="flex items-center gap-1 text-sm font-semibold text-indigo-900 group-hover:underline">
                                    <span>{{ $artistName }}</span>
                                    @if($artist?->isVerified())
                                        <svg class="w-4 h-4 text-indigo-600" viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M9.707 13.293 17 6l1.414 1.414-8.414 8.414L6 12.828l1.414-1.414 2.293 2.293z" />
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

                <div class="mt-4 text-gray-700 leading-relaxed">{!! nl2br(e($artwork->description)) !!}</div>

                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($artwork->tags as $tag)
                        <span class="px-3 py-1 rounded-full text-xs bg-gray-100 text-gray-700">#{{ $tag->name }}</span>
                    @endforeach
                </div>

                <div class="mt-6 flex items-center gap-3">
                    @if($isFixed)
                        <form method="POST" action="{{ route('cart.add') }}" class="flex items-stretch js-add-to-cart"
                            data-art-id="{{ $artwork->id }}">
                            @csrf
                            <input type="hidden" name="artwork_id" value="{{ $artwork->id }}">
                            <div class="flex items-center border rounded-l-lg">
                                <button type="button" class="px-3 py-2" @disabled($canEdit)
                                    onclick="this.nextElementSibling && this.nextElementSibling.stepDown && this.nextElementSibling.stepDown()">−</button>
                                <input name="quantity" type="number" class="w-14 text-center outline-none" value="1" min="1"
                                    @disabled($canEdit)>
                                <button type="button" class="px-3 py-2" @disabled($canEdit)
                                    onclick="this.previousElementSibling && this.previousElementSibling.stepUp && this.previousElementSibling.stepUp()">+</button>
                            </div>
                            <button type="submit"
                                class="bg-indigo-900 text-white px-5 rounded-r-lg disabled:opacity-50 disabled:cursor-not-allowed js-add-btn"
                                @disabled($canEdit) title="{{ $canEdit ? 'لا يمكنك شراء عملك الخاص' : '' }}">إضافة إلى
                                السلة</button>
                        </form>
                    @elseif($isAuction)
                        <a href="{{ route('user.messages') }}" class="bg-indigo-900 text-white px-5 py-2 rounded-lg">سؤال عن
                            المزاد</a>
                    @endif
                    @if($canEdit)
                        <a href="{{ route('art.edit', $artwork) }}" class="px-4 py-2 rounded-lg border">تعديل العمل</a>
                    @endif
                    @auth
                        <button type="button" data-like-btn data-liked="{{ $artwork->likedBy(auth()->user()) ? '1' : '0' }}"
                            data-id="{{ $artwork->id }}"
                            class="h-10 w-10 inline-flex items-center justify-center rounded-full border bg-white text-pink-600 hover:bg-pink-50 transition"
                            title="المفضلة">
                            <i class="fa{{ $artwork->likedBy(auth()->user()) ? 's' : 'r' }} fa-heart text-lg"></i>
                        </button>
                        <button type="button" data-report-btn data-report-endpoint="{{ route('art.report', $artwork) }}"
                            class="h-10 w-10 inline-flex items-center justify-center rounded-full border bg-white text-amber-600 hover:bg-amber-50 transition"
                            title="إبلاغ عن مشكلة">
                            <span class="sr-only">إبلاغ عن مشكلة</span>
                            <i class="fa-solid fa-flag"></i>
                        </button>

                    @endauth
                </div>
            </div>
            <!-- السلايدر -->
            <div class="lg:w-[340px] lg:ml-auto">
                <x-artwork-gallery :urls="$imgUrls->values()" :title="$artwork->title" />
            </div>
        </div>

        @if(!$artwork->tags->isEmpty())
            <div class="mt-10">
                <h3 class="text-xl mb-4">أعمال مشابهة</h3>
                <div class="text-gray-500">لاحقاً: عرض شبكة أعمال مشابهة حسب الوسوم.</div>
            </div>
        @endif
    </div>

    @auth
        <!-- Report Modal (multi-step) -->
        <div data-report-modal-root
            x-data="reportModal('{{ route('art.report', $artwork) }}', { images: {{ $reportImagesJson }} })" x-cloak
            @keydown.escape.window="close()" x-show="open" x-transition.opacity class="hidden"
            :class="open ? 'fixed inset-0 z-50 flex items-center justify-center px-4' : 'hidden'" @click.self="close()"
            aria-modal="true" role="dialog" :aria-hidden="(!open).toString()">
            <div x-show="open" x-transition.opacity class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
            <!-- Step Select Type -->
            <template x-if="step===1">
                <div
                    class="relative z-10 bg-white rounded-[22px] shadow-[0_16px_40px_rgba(15,23,42,0.14)] w-full max-w-[320px] max-h-[85vh] p-4 sm:p-5 space-y-4 overflow-y-auto">
                    <button type="button" class="absolute top-3 left-3 text-slate-400 hover:text-slate-600 transition"
                        @click.stop.prevent="close()"><span class="sr-only">إغلاق</span><i
                            class="fa-solid fa-xmark text-lg"></i></button>
                    <div class="space-y-1.5 pr-1">
                        <span
                            class="inline-flex items-center gap-1 text-[10px] font-medium text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full">
                            <span>الخطوة 1 / 2</span>
                        </span>
                        <h3 class="text-lg font-bold text-slate-900">أبلغ عن مشكلة</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">ساعدنا في تحديد المشكلة عبر اختيار الصورة
                            ونوع البلاغ. بإمكانك متابعة خطوات إضافية إذا لزم الأمر.</p>
                    </div>
                    <div class="space-y-3.5">
                        <template x-if="images.length">
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-[13px] font-semibold text-gray-800">اختر الصورة المتأثرة</span>
                                    <span class="text-[10px] font-medium"
                                        :class="selectedImageId ? 'text-green-600' : 'text-slate-400'"
                                        x-text="selectedImageId ? 'صورة محددة' : 'لم تحدد صورة بعد'"></span>
                                </div>
                                <div class="flex gap-2 overflow-x-auto pb-1 -mx-0.5 px-0.5">
                                    <template x-for="img in images" :key="img.id">
                                        <button type="button"
                                            class="relative flex-shrink-0 w-[72px] h-[90px] rounded-2xl border-2 overflow-hidden transition focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/70"
                                            @click="selectImage(img.id)"
                                            :class="selectedImageId===img.id ? 'border-indigo-600 shadow-lg shadow-indigo-200/60' : 'border-slate-200 bg-slate-50/80'">
                                            <img :src="img.url" :alt="img.label"
                                                class="w-full h-full object-cover pointer-events-none">
                                            <div class="absolute inset-0 bg-indigo-600/25 flex items-center justify-center text-xs font-semibold text-white"
                                                x-show="selectedImageId===img.id" x-transition>محددة</div>
                                            <span
                                                class="absolute bottom-1 inset-x-1 bg-black/55 text-white text-[10px] leading-none rounded px-1.5 py-1"
                                                x-text="img.label"></span>
                                        </button>
                                    </template>
                                </div>
                                <p class="text-[10px] text-slate-400 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-info"></i>
                                    اختيار الصورة يساعد الفريق على تحديد المشكلة بشكل أسرع.
                                </p>
                            </div>
                        </template>
                        <div class="space-y-1.5">
                            <template x-for="t in types" :key="t.value">
                                <label
                                    class="flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 cursor-pointer transition border border-transparent bg-slate-50 hover:bg-slate-100"
                                    :class="selectedType===t.value ? 'ring-2 ring-indigo-600 bg-white border-indigo-200 shadow-sm' : ''">
                                    <div class="text-[12px] font-medium text-slate-800" x-text="t.label"></div>
                                    <input type="checkbox" class="rounded h-5 w-5 text-indigo-600 focus:ring-indigo-500"
                                        :checked="selectedType===t.value" @change="toggleType(t.value)">
                                </label>
                            </template>
                        </div>
                    </div>
                    <div class="pt-1.5">
                        <button type="button" @click="goNext()"
                            :disabled="busy || !selectedType || (images.length && !selectedImageId)"
                            class="w-full h-11 rounded-full bg-indigo-900 text-white text-sm font-semibold flex items-center justify-center gap-2 disabled:opacity-40 disabled:cursor-not-allowed">
                            <span x-show="!busy" class="flex items-center gap-1">
                                <span
                                    x-text="selectedType==='rights_violation' ? 'التالي (التفاصيل)' : 'إرسال البلاغ'"></span>
                                <i class="fa-solid fa-arrow-right-long text-[11px]"
                                    x-show="selectedType==='rights_violation'"></i>
                            </span>
                            <span x-show="busy" class="flex items-center gap-2"><i class="fa-solid fa-spinner fa-spin"></i>
                                جارٍ</span>
                        </button>
                        <p class="text-[10px] text-slate-400 text-center mt-2" x-show="selectedType==='rights_violation'">
                            بعد الضغط على التالي، ستنتقل للخطوة الثانية لإضافة تفاصيل أكثر عن الانتهاك.
                        </p>
                        <p class="text-[10px] text-slate-400 text-center mt-2"
                            x-show="selectedType && selectedType!=='rights_violation'">
                            سيتم إرسال البلاغ مباشرة بعد الضغط على زر <strong class="font-semibold">إرسال البلاغ</strong>.
                        </p>
                        <p class="text-xs text-center text-red-600 mt-3" x-show="error" x-text="error"></p>
                    </div>
                </div>
            </template>
            <!-- Step Details for Rights Violation -->
            <template x-if="step===2">
                <div
                    class="relative z-10 bg-white rounded-[22px] shadow-[0_16px_40px_rgba(15,23,42,0.14)] w-full max-w-[320px] max-h-[85vh] p-4 sm:p-5 space-y-4 overflow-y-auto">
                    <button type="button" class="absolute top-3 left-3 text-slate-400 hover:text-slate-600 transition"
                        @click.stop.prevent="close()"><i class="fa-solid fa-xmark text-lg"></i></button>
                    <div class="space-y-1.5 pr-1">
                        <span
                            class="inline-flex items-center gap-1 text-[10px] font-medium text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full">
                            <span>الخطوة 2 / 2</span>
                        </span>
                        <h3 class="text-lg font-bold text-slate-900">تفاصيل إضافية</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">نحتاج وصفاً مختصراً لسبب انتهاك الحقوق حتى
                            يتمكن الفريق من التحقق سريعاً.</p>
                    </div>
                    <div class="space-y-2.5">
                        <label class="block text-[11px] font-semibold text-slate-700">ينتهك حقوقي</label>
                        <textarea x-model="details" rows="4"
                            class="w-full rounded-2xl border border-slate-200 focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-400 text-sm p-3 resize-none"
                            placeholder="مثال: تم استخدام هذه الصورة دون إذني أو تضمين رابط للملف الأصلي..."></textarea>
                        <div class="flex items-start gap-2 text-[10px] text-slate-400">
                            <i class="fa-regular fa-lightbulb pt-0.5"></i>
                            <span>كلما كانت التفاصيل أوضح، كلما كان التعامل مع البلاغ أسرع.</span>
                        </div>
                        <p class="text-[11px] text-red-600" x-show="error" x-text="error"></p>
                    </div>
                    <div class="flex gap-3 pt-1.5">
                        <button type="button" @click="step=1"
                            class="flex-1 h-10 rounded-full border border-slate-200 text-slate-700 text-sm font-medium hover:bg-slate-50">رجوع</button>
                        <button type="button" @click="submit()" :disabled="busy || !details.trim()"
                            class="flex-1 h-10 rounded-full bg-indigo-900 text-white text-sm font-semibold disabled:opacity-40">إرسال
                            البلاغ</button>
                    </div>
                </div>
            </template>
            <!-- Done -->
            <template x-if="step===3">
                <div
                    class="relative z-10 bg-white rounded-[22px] shadow-[0_16px_40px_rgba(15,23,42,0.14)] w-full max-w-[300px] max-h-[80vh] p-5 space-y-4 text-center overflow-y-auto">
                    <button type="button" class="absolute top-3 left-3 text-slate-400 hover:text-slate-600 transition"
                        @click.stop.prevent="close()"><i class="fa-solid fa-xmark text-base"></i></button>
                    <div
                        class="mx-auto w-12 h-12 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-flag-checkered"></i>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-base font-bold text-slate-900">تم إرسال البلاغ</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">شكرًا لإبلاغك. سيقوم فريق المراجعة بالتواصل
                            عند
                            الحاجة أو اتخاذ الإجراء المناسب.</p>
                    </div>
                    <button type="button" @click="close()"
                        class="w-full h-10 rounded-full bg-indigo-900 text-white text-sm font-semibold">إغلاق</button>
                </div>
            </template>
        </div>
    @endauth
</x-layout>