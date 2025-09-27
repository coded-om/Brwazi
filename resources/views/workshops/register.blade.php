<x-layout>
    <section class="max-w-4xl mx-auto px-4 py-12">
        <div class="mb-10 flex flex-col gap-3 text-center">
            <a href="{{ route('workshops.index') }}"
                class="mx-auto inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-700">
                <i class="fa-solid fa-arrow-right"></i>
                العودة إلى قائمة الورشات
            </a>
            <h1 class="text-3xl font-bold text-indigo-950">التسجيل في ورشة: {{ $workshop->title }}</h1>
            <p class="text-slate-500">
                قدم بياناتك للانضمام إلى الورشة، وسيتواصل معك فريقنا لتأكيد الحضور وإرسال التفاصيل.
            </p>
        </div>

        <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-100">
            <div class="grid gap-6 md:grid-cols-[1.1fr_0.9fr]">
                <div class="p-8">
                    @if($workshop->external_apply_url)
                        <div class="space-y-6">
                            <div class="rounded-2xl bg-emerald-50 p-6 border border-emerald-100">
                                <h2 class="font-semibold text-emerald-700 mb-2">التسجيل لهذه الورشة خارجي</h2>
                                <p class="text-sm text-emerald-700/80 mb-4">التسجيل يتم عبر رابط خارجي يوفره مقدم الورشة.</p>
                                <a href="{{ $workshop->external_apply_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-700">
                                    الانتقال لصفحة التسجيل
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                            </div>
                            <a href="{{ route('workshops.index') }}" class="text-sm text-slate-500 hover:text-slate-700">عودة إلى الورشات</a>
                        </div>
                    @else
                    <form action="{{ route('workshops.register.store', $workshop) }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-medium text-indigo-900">الاسم الكامل</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $prefill['name'] ?? '') }}"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                                placeholder="مثال: سارة العبدلي" required>
                            @error('name')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-indigo-900">البريد
                                الإلكتروني</label>
                            <input id="email" name="email" type="email"
                                value="{{ old('email', $prefill['email'] ?? '') }}"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                                placeholder="example@email.com" required>
                            @error('email')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="phone" class="block text-sm font-medium text-indigo-900">رقم التواصل
                                (اختياري)</label>
                            <input id="phone" name="phone" type="text"
                                value="{{ old('phone', $prefill['phone'] ?? '') }}"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                                placeholder="05xxxxxxxx">
                            @error('phone')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="notes" class="block text-sm font-medium text-indigo-900">رسالة أو ملاحظات
                                إضافية</label>
                            <textarea id="notes" name="notes" rows="4"
                                class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100"
                                placeholder="أخبرنا عن توقعاتك أو أي استفسارات لديك">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-xs text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full rounded-2xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                            إرسال طلب المشاركة
                        </button>
                    </form>
                    @endif
                </div>

                <aside class="relative flex flex-col gap-5 bg-indigo-950 p-8 text-white">
                    <div class="space-y-3">
                        <h2 class="text-lg font-semibold">معلومات الورشة</h2>
                        <p class="text-sm text-indigo-100">{{ $workshop->short_description }}</p>
                    </div>

                    <ul class="space-y-4 text-sm text-indigo-100">
                        <li class="flex items-center gap-3">
                            <i class="fa-regular fa-calendar text-indigo-300"></i>
                            <span>{{ $workshop->starts_at->translatedFormat('l d F Y') }}</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fa-regular fa-clock text-indigo-300"></i>
                            <span>{{ $workshop->starts_at->format('H:i') }} @if($workshop->duration_label) - لمدة
                            {{ $workshop->duration_label }} @endif</span>
                        </li>
                        @if($workshop->location)
                            <li class="flex items-center gap-3">
                                <i class="fa-solid fa-location-dot text-indigo-300"></i>
                                <span>{{ $workshop->location }}</span>
                            </li>
                        @endif
                        <li class="flex items-center gap-3">
                            @if($workshop->presenter_avatar_path)
                                <img src="{{ asset('storage/' . ltrim($workshop->presenter_avatar_path, '/')) }}" alt="{{ $workshop->presenter_name }}" class="h-9 w-9 rounded-full object-cover ring-2 ring-indigo-700/40">
                            @else
                                <i class="fa-regular fa-user text-indigo-300"></i>
                            @endif
                            <span>يقدمها: {{ $workshop->presenter_name }}</span>
                        </li>
                        @if($workshop->art_type)
                            <li class="flex items-center gap-3">
                                <i class="fa-solid fa-palette text-indigo-300"></i>
                                <span>نوع الفن: {{ $workshop->art_type }}</span>
                            </li>
                        @endif
                        @if($workshop->presenter_bio)
                            <li class="flex items-start gap-3">
                                <i class="fa-solid fa-id-card text-indigo-300 mt-0.5"></i>
                                <span class="leading-relaxed">{{ Str::limit($workshop->presenter_bio, 180) }}</span>
                            </li>
                        @endif
                    </ul>

                    @if(!$workshop->external_apply_url)
                        <div class="mt-auto rounded-2xl bg-white/10 p-4 text-xs leading-relaxed text-indigo-100/80">
                            بالضغط على زر الإرسال، فأنت توافق على تواصل فريقنا معك لتأكيد الحضور وإرسال التفاصيل.
                        </div>
                    @else
                        <div class="mt-auto rounded-2xl bg-white/10 p-4 text-xs leading-relaxed text-indigo-100/80">
                            التسجيل يتم عبر منصة خارجية. أي استفسار إضافي يمكنك مراسلتنا.
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </section>
</x-layout>