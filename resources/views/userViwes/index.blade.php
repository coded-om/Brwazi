@php
    $user = auth()->user();
    $displayName = $user->full_name ?? trim(($user->fname . ' ' . $user->lname)) ?: 'مستخدم';
    $bioParagraphs = $user->bio ? array_filter(preg_split('/\r\n|\n|\r/', trim($user->bio))) : [];
    $statusClass = match ($user->status) {
        \App\Models\User::STATUS_NORMAL => 'bg-gray-100 text-gray-600',
        \App\Models\User::STATUS_VERIFIED => 'bg-blue-100 text-blue-600',
        \App\Models\User::STATUS_PREMIUM => 'bg-amber-100 text-amber-600',
        \App\Models\User::STATUS_BANNED => 'bg-red-100 text-red-600',
        default => 'bg-gray-100 text-gray-600',
    };
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
    <div class="min-h-screen bg-gray-50 py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- بطاقة الملف الشخصي -->
            <section
                class="relative overflow-hidden rounded-[36px] bg-white shadow-xl ring-1 ring-slate-100 px-6 sm:px-12 py-10 mb-12">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-indigo-50 via-white to-transparent opacity-70 pointer-events-none">
                </div>
                <div class="relative grid items-center gap-12 lg:grid-cols-[minmax(0,1fr)_320px]">
                    <!-- المحتوى النصي -->
                    <div class="space-y-6 text-center lg:text-right">
                        <div class="space-y-4">
                            <span
                                class="inline-flex items-center gap-2 rounded-full bg-indigo-50 text-indigo-600 text-xs font-semibold px-4 py-1.5 shadow-sm">
                                <i class="fa-solid fa-user"></i>
                                <span>الملف الشخصي</span>
                            </span>
                            <h1 class="text-3xl sm:text-4xl font-extrabold text-[#8C3EB6] tracking-tight">
                                {{ $displayName }}
                            </h1>
                            <p
                                class="text-sm sm:text-base text-cyan-700 font-medium flex items-center justify-center lg:justify-start gap-2">
                                <i class="fas fa-sparkles text-cyan-500"></i>
                                {{ $user->tagline ?: 'لم يتم تحديد التخصص بعد' }}
                            </p>
                        </div>
                        <div
                            class="flex flex-wrap justify-center lg:justify-start gap-3 text-xs sm:text-sm font-medium">
                            <span
                                class="inline-flex items-center gap-2 rounded-full bg-indigo-50 text-indigo-700 px-4 py-1.5">
                                <i class="fas fa-id-badge text-xs"></i>
                                عضو منذ {{ $user->created_at?->diffForHumans() }}
                            </span>
                            @if($user->country)
                                <span
                                    class="inline-flex items-center gap-2 rounded-full bg-violet-50 text-violet-700 px-4 py-1.5">
                                    <i class="fas fa-location-dot text-xs"></i>{{ $user->country }}
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-2 rounded-full px-4 py-1.5 {{ $statusClass }}">
                                <i class="{{ $user->status_icon }} text-xs"></i>
                                {{ $user->status_label }}
                            </span>
                        </div>
                        <div
                            class="space-y-4 leading-relaxed text-gray-700 text-[15px] max-w-full lg:max-w-2xl xl:max-w-3xl mx-auto lg:mx-0">
                            @if(count($bioParagraphs))
                                @foreach($bioParagraphs as $para)
                                    <p class="break-words">{{ $para }}</p>
                                @endforeach
                            @else
                                <p class="text-gray-500 break-words">هذا النص أو العديد من النصوص هو مثال لنص يمكن أن
                                    يستبدل في نفس المساحة. يمكنك تعديل النبذة من زر تعديل الملف الشخصي.</p>
                                <p class="text-gray-500 break-words">أضف تفاصيل عن خبراتك، مجالات اهتمامك، وأي معلومات
                                    تعرّف بك بشكل أفضل.</p>
                            @endif
                        </div>
                    </div>

                    <!-- صورة المستخدم وسرعة الوصول -->
                    <div class="relative flex flex-col items-center gap-6">
                        <div class="relative">
                            <div
                                class="h-64 w-64 rounded-[32px] overflow-hidden bg-gradient-to-br from-indigo-500 to-violet-600 ring-4 ring-white shadow-2xl">
                                @if($user->ProfileImage)
                                    <img src="{{ asset('storage/' . $user->ProfileImage) }}" alt="صورة المستخدم"
                                        class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full flex items-center justify-center text-white/80">
                                        <i class="fas fa-user text-6xl"></i>
                                    </div>
                                @endif
                            </div>
                            @if($user->isVerified())
                                <img src="{{ asset('imgs/icons-color/verifiid.svg') }}" alt="موثّق"
                                    class="absolute -top-3 -right-3 h-9 w-9 drop-shadow-lg pointer-events-none select-none" />
                            @endif
                        </div>
                        <a href="{{ route('user.messages') }}"
                            class="relative inline-flex items-center gap-3 rounded-full bg-white/95 px-5 py-2.5 text-cyan-700 ring-1 ring-cyan-100 shadow-lg hover:text-cyan-900 hover:ring-cyan-200 transition">
                            <i class="fa-solid fa-comments text-base"></i>
                            <span class="text-sm font-semibold">المراسلات</span>
                            <span
                                class="inline-flex h-6 min-w-[1.75rem] items-center justify-center rounded-full bg-rose-500 text-white text-xs font-semibold shadow">
                                {{ $user->unread_messages_count ?: 0 }}
                            </span>
                        </a>
                    </div>
                </div>

                @php
                    // Prefer a dedicated sold-artworks page if defined; otherwise, fall back to messages
                    $hasUserSold = \Illuminate\Support\Facades\Route::has('user.sold');
                    $hasArtSold = \Illuminate\Support\Facades\Route::has('art.sold');
                    $soldUrl = $hasUserSold ? route('user.sold') : ($hasArtSold ? route('art.sold') : (\Illuminate\Support\Facades\Route::has('messages') ? route('messages') : url('/messages')));
                    $soldTitle = ($hasUserSold || $hasArtSold) ? 'الأعمال المباعة' : 'الرسائل';
                    $soldCount = $user->sold_artworks_count ?? $user->sold_count ?? null;
                @endphp

                <div class="relative mt-10">
                    <div class="flex flex-wrap justify-center lg:justify-end gap-3">
                        <a href="{{ route('user.profile') }}"
                            class="inline-flex items-center gap-2 rounded-full bg-indigo-600 text-white px-5 py-2.5 text-sm font-semibold shadow hover:bg-indigo-700 transition"
                            title="تعديل الملف الشخصي" aria-label="تعديل الملف الشخصي">
                            <i class="fa-solid fa-pen-to-square text-base"></i>
                            <span>تعديل الملف الشخصي</span>
                        </a>
                        @if(auth()->check() && auth()->user()->isBanned())
                            <a href="#"
                                onclick="(window.notify?.warning && window.notify.warning('تم تعطيل هذا الإجراء لحسابك. يرجى التواصل مع قسم الدعم.')) || alert('تم تعطيل هذا الإجراء لحسابك. يرجى التواصل مع قسم الدعم.'); return false;"
                                class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-gray-500 ring-1 ring-gray-200 cursor-not-allowed opacity-70"
                                title="محظور">
                                <img src="{{ asset('imgs/icons-color/auction-icon.svg') }}" alt="طلب مزاد" class="h-5 w-5">
                                <span>طلب مزاد</span>
                            </a>
                        @else
                            <a href="{{ route('user.auctions.request.create') }}"
                                class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-cyan-700 ring-1 ring-cyan-100 hover:ring-cyan-300 hover:text-cyan-900 transition"
                                title="طلب مزاد">
                                <img src="{{ asset('imgs/icons-color/auction-icon.svg') }}" alt="طلب مزاد" class="h-5 w-5">
                                <span>طلب مزاد</span>
                            </a>
                        @endif

                        @if(!$user->isVerified())
                            @if(method_exists($user, 'hasPendingVerification') && $user->hasPendingVerification())
                                <a href="{{ route('verification.apply') }}"
                                    class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-gray-500 ring-1 ring-gray-200 cursor-not-allowed"
                                    title="طلبك قيد المراجعة">
                                    <img src="{{ asset('imgs/icons-color/verifiid.svg') }}" alt="طلب التوثيق قيد المراجعة"
                                        class="h-5 w-5">
                                    <span>قيد المراجعة</span>
                                </a>
                            @else
                                <a href="{{ route('verification.apply') }}"
                                    class="inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-violet-700 ring-1 ring-violet-100 hover:ring-violet-300 hover:text-violet-900 transition"
                                    title="طلب التوثيق">
                                    <img src="{{ asset('imgs/icons-color/verifiid.svg') }}" alt="طلب التوثيق" class="h-5 w-5">
                                    <span>طلب التوثيق</span>
                                </a>
                            @endif
                        @endif

                        @if($user->isVerified())
                            <a href="{{ $soldUrl }}"
                                class="relative inline-flex items-center gap-2 rounded-full bg-white px-5 py-2.5 text-sm font-semibold text-emerald-700 ring-1 ring-emerald-100 hover:ring-emerald-300 hover:text-emerald-900 transition"
                                title="{{ $soldTitle }}">
                                <i class="fa-solid fa-bag-shopping text-base"></i>
                                <span>{{ $soldTitle }}</span>
                                @if(!is_null($soldCount))
                                    <span
                                        class="absolute -top-2 -right-2 inline-flex h-6 min-w-[1.5rem] items-center justify-center rounded-full bg-emerald-500 text-white text-xs font-semibold shadow">{{ (int) $soldCount }}</span>
                                @endif
                            </a>
                        @endif
                    </div>
                </div>

                <div class="absolute bottom-6 left-6 sm:left-8 z-30">
                    @if(auth()->check() && auth()->user()->isBanned())
                        <a href="#"
                            onclick="(window.notify?.warning && window.notify.warning('تم تعطيل الرفع لحسابك. يرجى التواصل مع قسم الدعم.')) || alert('تم تعطيل الرفع لحسابك. يرجى التواصل مع قسم الدعم.'); return false;"
                            class="h-14 w-14 rounded-full bg-indigo-500/60 text-white flex items-center justify-center shadow-xl focus:outline-none focus:ring-4 focus:ring-indigo-200 cursor-not-allowed"
                            title="محظور">
                            <i class="fas fa-plus text-xl"></i>
                        </a>
                    @else
                        <a href="{{ route('art.create') }}"
                            class="h-14 w-14 rounded-full bg-indigo-600 text-white flex items-center justify-center shadow-xl hover:bg-indigo-700 hover:scale-105 focus:outline-none focus:ring-4 focus:ring-indigo-300 transition-all duration-200 group"
                            title="إضافة عمل جديد">
                            <i class="fas fa-plus text-xl group-hover:rotate-90 transition-transform duration-200"></i>
                        </a>
                    @endif
                </div>
            </section>
            <!-- معرض الأعمال -->
            <section class="max-w-6xl mx-auto mb-24">
                <div class="flex items-center justify-between mb-6 pr-2">
                    <h2 class="text-2xl font-bold text-indigo-700">الأعمال</h2>
                    @php $tab = $tab ?? request('tab', 'all'); @endphp
                    <div class="flex gap-2 text-indigo-500" role="tablist" aria-label="فرز الأعمال">
                        <a href="{{ route('user.dashboard', ['tab' => 'all']) }}" role="tab"
                            aria-selected="{{ $tab === 'all' ? 'true' : 'false' }}"
                            class="h-9 w-9 flex items-center justify-center rounded-full hover:bg-indigo-50 {{ $tab === 'all' ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200' : '' }}"
                            title="المنشورة">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <a href="{{ route('user.dashboard', ['tab' => 'favorites']) }}" role="tab"
                            aria-selected="{{ $tab === 'favorites' ? 'true' : 'false' }}"
                            class="h-9 w-9 flex items-center justify-center rounded-full hover:bg-indigo-50 {{ $tab === 'favorites' ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200' : '' }}"
                            title="المفضلة">
                            <i class="fa-solid fa-heart"></i>
                        </a>
                        <a href="{{ route('user.dashboard', ['tab' => 'drafts']) }}" role="tab"
                            aria-selected="{{ $tab === 'drafts' ? 'true' : 'false' }}"
                            class="h-9 w-9 flex items-center justify-center rounded-full hover:bg-indigo-50 {{ $tab === 'drafts' ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200' : '' }}"
                            title="المسودات">
                            <i class="fa-solid fa-bookmark"></i>
                        </a>
                    </div>
                </div>
                @if(isset($artworks) && $artworks->count())
                    <x-masonry :items="$artworks" :columns="4" />
                @else
                    <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center text-gray-500">
                        لا توجد أعمال بعد. ابدأ بإضافة أول عمل لك من زر +
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-layout>

@push('styles')
    <style>
        /* no extra styles needed; masonry is server-rendered using 4 columns */
    </style>
@endpush