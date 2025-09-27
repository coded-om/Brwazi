<x-layout>
    <section class="max-w-6xl mx-auto px-4 py-12">
        <header class="text-center mb-12">
            <span
                class="inline-flex items-center gap-2 px-4 py-1 rounded-full bg-violet-100 text-violet-700 text-sm font-medium">
                <i class="fa-solid fa-feather"></i>
                فعاليات أدبية
            </span>
            <h1 class="mt-4 text-3xl md:text-4xl font-bold text-violet-950">الورشات الأدبية</h1>
            <p class="mt-3 text-slate-600 max-w-2xl mx-auto">
                استكشف أحدث الورشات الأدبية والتدريبية في مجالات الكتابة، الشعر، التحرير، والسرد القصصي.
            </p>
        </header>
        @if($workshops->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <h2 class="text-xl font-semibold text-slate-700">لا توجد ورشات أدبية حالياً</h2>
                <p class="mt-3 text-slate-500">تابع لاحقاً لمزيد من الفعاليات الأدبية.</p>
            </div>
        @else
            <div class="grid gap-7 md:grid-cols-2 xl:grid-cols-3">
                @php
                    $userRegisteredLookup = isset($userRegisteredIds) ? array_flip($userRegisteredIds) : [];
                @endphp
                @foreach($workshops as $workshop)
                    @php
                        $soon = $workshop->starts_at->isFuture() && $workshop->starts_at->diffInDays() <= 30;
                        $avatarRaw = $workshop->presenter_avatar_path;
                        $avatarUrl = $avatarRaw ? (preg_match('/^https?:/i', $avatarRaw) ? $avatarRaw : asset('storage/' . ltrim($avatarRaw, '/'))) : null;
                        $isRegistered = isset($userRegisteredLookup[$workshop->id]);
                        $capacity = $workshop->capacity;
                        $count = $workshop->registrations_count ?? 0;
                        $full = $capacity && $count >= $capacity;
                    @endphp
                    <article
                        class="group flex h-full flex-col overflow-hidden rounded-3xl bg-white shadow-md ring-1 ring-slate-100 transition hover:shadow-xl @if($isRegistered) border border-emerald-300 ring-emerald-200/70 bg-gradient-to-b from-emerald-50 to-white @endif">
                        <div
                            class="relative h-48 w-full overflow-hidden bg-gradient-to-br from-violet-200 via-fuchsia-200 to-pink-200 flex items-center justify-center">
                            <div
                                class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_center,white,transparent_70%)]">
                            </div>
                            <i class="fa-solid fa-feather text-violet-600 text-5xl"></i>
                            @if($soon)
                                <span
                                    class="absolute top-3 left-3 inline-flex items-center gap-1 rounded-full bg-amber-500/95 px-3 py-1 text-xs font-medium text-white shadow-sm">
                                    <i class="fa-regular fa-clock"></i> قريباً
                                </span>
                            @endif
                            @if($isRegistered)
                                <span
                                    class="absolute top-3 right-3 inline-flex items-center gap-1 rounded-full bg-emerald-600 px-3 py-1 text-xs font-semibold text-white shadow ring-2 ring-white/40">
                                    <i class="fa-solid fa-circle-check"></i> مسجل
                                </span>
                            @elseif($full)
                                <span
                                    class="absolute top-3 right-3 inline-flex items-center gap-1 rounded-full bg-rose-600/95 px-3 py-1 text-xs font-medium text-white shadow-sm">
                                    <i class="fa-solid fa-lock"></i> ممتلئة
                                </span>
                            @endif
                            @if($avatarUrl)
                                <div class="absolute -bottom-6 inset-x-0 flex justify-center">
                                    <img src="{{ $avatarUrl }}" alt="{{ $workshop->presenter_name }}"
                                        class="h-16 w-16 rounded-xl object-cover ring-4 ring-white shadow-md" loading="lazy">
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col p-6 @if($avatarUrl) pt-12 @endif">
                            <header class="mb-4 space-y-2 text-center">
                                <h2 class="text-lg font-bold leading-snug text-slate-900">{{ $workshop->title }}</h2>
                                @if($workshop->short_description)
                                    <p class="text-sm leading-relaxed text-slate-600 line-clamp-3">
                                        “{{ Str::limit(trim($workshop->short_description, '"'), 110) }}”</p>
                                @endif
                            </header>
                            <div class="mt-auto space-y-5">
                                <div class="flex flex-wrap items-center justify-center gap-4 text-[13px] text-slate-500">
                                    <span class="inline-flex items-center gap-1"><i
                                            class="fa-regular fa-calendar"></i>{{ $workshop->starts_at->translatedFormat('d F Y') }}</span>
                                    <span class="inline-flex items-center gap-1"><i
                                            class="fa-regular fa-clock"></i>{{ $workshop->starts_at->format('H:i') }}</span>
                                    @if($workshop->duration_label)
                                        <span class="inline-flex items-center gap-1"><i
                                                class="fa-solid fa-hourglass-half"></i>{{ $workshop->duration_label }}</span>
                                    @endif
                                </div>
                                <hr class="border-slate-200">
                                <div class="grid grid-cols-2 gap-4 text-center text-sm">
                                    <div class="space-y-1">
                                        <div class="text-[11px] font-medium tracking-wide text-slate-400">سعر المشاركة</div>
                                        <div class="font-bold text-emerald-600">مجاناً</div>
                                    </div>
                                    <div class="space-y-1">
                                        <div class="text-[11px] font-medium tracking-wide text-slate-400">التصنيف</div>
                                        <div class="font-semibold text-slate-700">{{ $workshop->genre ?? 'عام' }}</div>
                                    </div>
                                </div>
                                <div class="pt-3">
                                    @if($workshop->external_apply_url)
                                        <a href="{{ $workshop->external_apply_url }}" target="_blank" rel="noopener"
                                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">المزيد
                                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i></a>
                                    @else
                                        <a href="{{ route('literature_workshops.register', $workshop) }}"
                                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-violet-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-violet-700">المزيد
                                            <i class="fa-solid fa-arrow-left text-xs"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</x-layout>