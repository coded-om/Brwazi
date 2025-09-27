<x-layout>
    <section class="max-w-6xl mx-auto px-4 py-12">
        <header class="text-center mb-12">
            <span
                class="inline-flex items-center gap-2 px-4 py-1 rounded-full bg-indigo-100 text-indigo-700 text-sm font-medium">
                <i class="fa-solid fa-brush"></i>
                فعاليات قادمة
            </span>
            <h1 class="mt-4 text-3xl md:text-4xl font-bold text-indigo-950">ورشات بروزاي الفنية</h1>
            <p class="mt-3 text-slate-600 max-w-2xl mx-auto">
                اكتشف أجدد الورشات الإبداعية التي يقدمها أفضل الفنانين. اختر ورشتك المفضلة، اطلع على التفاصيل،
                وسجل مشاركتك بخطوة واحدة.
            </p>
        </header>

        @if($workshops->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center">
                <h2 class="text-xl font-semibold text-slate-700">لا توجد ورشات متاحة الآن</h2>
                <p class="mt-3 text-slate-500">تابعنا قريبًا لمعرفة الورشات الجديدة التي سيضيفها فريقنا.</p>
            </div>
        @else
            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach($workshops as $workshop)
                    <article
                        class="group h-full overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 transition hover:-translate-y-1 hover:shadow-lg">
                        @if($workshop->cover_image_path)
                            <div class="relative h-48 overflow-hidden">
                                <img src="{{ asset($workshop->cover_image_path) }}" alt="{{ $workshop->title }}"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            </div>
                        @endif

                        <div class="flex h-full flex-col gap-5 p-6">
                            <header class="flex flex-col gap-2">
                                <span
                                    class="inline-flex w-fit items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                                    <i class="fa-solid fa-palette"></i>
                                    {{ $workshop->art_type ?? 'فن متنوع' }}
                                </span>
                                <h2 class="text-xl font-semibold text-indigo-950">{{ $workshop->title }}</h2>
                                <div class="flex flex-wrap gap-3 text-sm text-slate-500 items-center">
                                    <span class="inline-flex items-center gap-2">
                                        @if($workshop->presenter_avatar_path)
                                            <img src="{{ asset('storage/' . ltrim($workshop->presenter_avatar_path, '/')) }}" alt="{{ $workshop->presenter_name }}" class="h-6 w-6 rounded-full object-cover">
                                        @else
                                            <i class="fa-regular fa-user"></i>
                                        @endif
                                        <span>{{ $workshop->presenter_name }}</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fa-regular fa-calendar"></i>
                                        {{ $workshop->starts_at->translatedFormat('d F Y') }}
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fa-regular fa-clock"></i>
                                        {{ $workshop->starts_at->format('H:i') }}
                                    </span>
                                    @if($workshop->duration_label)
                                        <span class="inline-flex items-center gap-1">
                                            <i class="fa-solid fa-hourglass-half"></i>
                                            {{ $workshop->duration_label }}
                                        </span>
                                    @endif
                                </div>
                            </header>

                            @if($workshop->short_description)
                                <p class="text-sm leading-relaxed text-slate-600">
                                    {{ \Illuminate\Support\Str::limit($workshop->short_description, 150) }}
                                </p>
                            @endif

                            <div class="mt-auto flex flex-wrap items-center justify-between gap-3">
                                @if($workshop->location)
                                    <span
                                        class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600">
                                        <i class="fa-solid fa-location-dot text-slate-400"></i>
                                        {{ $workshop->location }}
                                    </span>
                                @endif
                                @if($workshop->external_apply_url)
                                    <a href="{{ $workshop->external_apply_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                                        التسجيل الخارجي
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                @else
                                    <a href="{{ route('workshops.register', $workshop) }}"
                                        class="inline-flex items-center gap-2 rounded-full bg-indigo-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                        شارك الآن
                                        <i class="fa-solid fa-arrow-left"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</x-layout>
