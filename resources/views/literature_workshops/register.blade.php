<x-layout>
    <section class="max-w-4xl mx-auto px-4 py-12">
        <div class="mb-10 flex flex-col gap-3 text-center">
            <a href="{{ route('literature_workshops.index') }}" class="mx-auto inline-flex items-center gap-2 text-sm text-violet-600 hover:text-violet-700"><i class="fa-solid fa-arrow-right"></i> العودة إلى قائمة الورشات الأدبية</a>
            <h1 class="text-3xl font-bold text-violet-950">التسجيل في ورشة: {{ $workshop->title }}</h1>
            <p class="text-slate-500">أدخل بياناتك للانضمام إلى الورشة الأدبية. سيتم التواصل معك لتأكيد التفاصيل.</p>
        </div>
        <div class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-100">
            <div class="grid gap-6 md:grid-cols-[1.1fr_0.9fr]">
                <div class="p-8">
                    @if($workshop->external_apply_url)
                        <div class="space-y-6">
                            <div class="rounded-2xl bg-emerald-50 p-6 border border-emerald-100">
                                <h2 class="font-semibold text-emerald-700 mb-2">التسجيل خارجي</h2>
                                <p class="text-sm text-emerald-700/80 mb-4">التسجيل يتم عبر رابط خارجي يوفره مقدم الورشة.</p>
                                <a href="{{ $workshop->external_apply_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-700">الانتقال للرابط <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                            </div>
                            <a href="{{ route('literature_workshops.index') }}" class="text-sm text-slate-500 hover:text-slate-700">عودة</a>
                        </div>
                    @else
                        @if(isset($alreadyRegistered) && $alreadyRegistered)
                            <div class="space-y-6">
                                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-6 text-center">
                                    <div class="flex items-center justify-center gap-2 text-emerald-700 font-semibold text-sm mb-2"><i class="fa-solid fa-circle-check"></i> تم التسجيل مسبقاً</div>
                                    <p class="text-xs text-emerald-700/80 mb-4">طلبك موجود بالفعل.</p>
                                    <a href="{{ route('literature_workshops.index') }}" class="inline-flex items-center gap-2 text-violet-700 text-xs font-medium"><i class="fa-solid fa-arrow-right"></i> العودة</a>
                                </div>
                            </div>
                        @else
                            @php
                                $currentRegistrations = $workshop->registrations()->count();
                                $capacity = $workshop->capacity;
                                $isFull = $capacity && $currentRegistrations >= $capacity;
                                $percent = $capacity ? min(100, round(($currentRegistrations / max(1,$capacity)) * 100)) : null;
                            @endphp
                            <div class="space-y-5 mb-8">
                                @if($capacity)
                                    @php
                                        $containerClasses = 'rounded-2xl border p-4 ' . ($isFull ? 'border-rose-300 bg-rose-50' : 'border-slate-200 bg-slate-50');
                                        $titleClasses = 'font-medium ' . ($isFull ? 'text-rose-700' : 'text-slate-700');
                                    @endphp
                                    <div class="{{ $containerClasses }}">
                                        <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
                                            <div class="{{ $titleClasses }}">السعة: <span class="font-semibold">{{ $currentRegistrations }} / {{ $capacity }}</span></div>
                                            @if(!$isFull)
                                                <div class="text-xs text-slate-500">تبقى <span class="font-semibold text-violet-600">{{ $capacity - $currentRegistrations }}</span> مقعد</div>
                                            @else
                                                <div class="text-xs font-semibold text-rose-600 flex items-center gap-1"><i class="fa-solid fa-circle-exclamation"></i> اكتمل العدد</div>
                                            @endif
                                        </div>
                                        <div class="mt-3 space-y-1">
                                            <progress value="{{ $currentRegistrations }}" max="{{ $capacity }}" class="w-full h-2 overflow-hidden rounded bg-white/70 ring-1 ring-slate-200 [accent-color:theme(colors.violet.600)]"></progress>
                                            <div class="text-[11px] tracking-tight text-slate-500 text-center">{{ $percent }}%</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @if($isFull)
                                <div class="rounded-2xl bg-rose-50 border border-rose-200 p-6 text-center space-y-3">
                                    <div class="text-rose-600 font-semibold flex items-center justify-center gap-2 text-sm"><i class="fa-solid fa-lock"></i> التسجيل مغلق</div>
                                    <p class="text-xs text-rose-600/80">يمكنك متابعة ورشات أخرى.</p>
                                    <a href="{{ route('literature_workshops.index') }}" class="inline-flex items-center gap-2 text-violet-700 text-xs font-medium"><i class="fa-solid fa-arrow-right"></i> العودة</a>
                                </div>
                            @else
                                <form action="{{ route('literature_workshops.register.store', $workshop) }}" method="POST" class="space-y-6">
                                    @csrf
                                    <div class="rounded-xl bg-violet-50 border border-violet-100 p-4 text-xs text-violet-700 flex items-center gap-2"><i class="fa-solid fa-circle-info text-violet-500"></i> تأكد من صحة بياناتك.</div>
                                    <div class="space-y-2">
                                        <label for="name" class="block text-sm font-medium text-violet-900">الاسم الكامل</label>
                                        <input id="name" name="name" type="text" value="{{ old('name', $prefill['name'] ?? '') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-100" required>
                                        @error('name')<p class="text-xs text-rose-500">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label for="email" class="block text-sm font-medium text-violet-900">البريد الإلكتروني</label>
                                        <input id="email" name="email" type="email" value="{{ old('email', $prefill['email'] ?? '') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-100" required>
                                        @error('email')<p class="text-xs text-rose-500">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label for="phone" class="block text-sm font-medium text-violet-900">رقم الجوال *</label>
                                        <input id="phone" name="phone" type="text" value="{{ old('phone', $prefill['phone'] ?? '') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-100" required>
                                        @error('phone')<p class="text-xs text-rose-500">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label for="whatsapp_phone" class="block text-sm font-medium text-violet-900">رقم واتساب (اختياري)</label>
                                        <input id="whatsapp_phone" name="whatsapp_phone" type="text" value="{{ old('whatsapp_phone') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-100">
                                        @error('whatsapp_phone')<p class="text-xs text-rose-500">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="space-y-2">
                                        <label for="notes" class="block text-sm font-medium text-violet-900">ملاحظات</label>
                                        <textarea id="notes" name="notes" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-violet-400 focus:outline-none focus:ring-2 focus:ring-violet-100">{{ old('notes') }}</textarea>
                                        @error('notes')<p class="text-xs text-rose-500">{{ $message }}</p>@enderror
                                    </div>
                                    <button type="submit" class="w-full rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white focus:outline-none focus:ring-4 focus:ring-emerald-200">إرسال طلب المشاركة</button>
                                </form>
                            @endif
                        @endif
                    @endif
                </div>
                <aside class="relative flex flex-col gap-5 bg-violet-950 p-8 text-white">
                    <div class="space-y-3">
                        <h2 class="text-lg font-semibold">معلومات الورشة</h2>
                        <p class="text-sm text-violet-100">{{ $workshop->short_description }}</p>
                    </div>
                    <ul class="space-y-4 text-sm text-violet-100">
                        <li class="flex items-center gap-3"><i class="fa-regular fa-calendar text-violet-300"></i><span>{{ $workshop->starts_at->translatedFormat('l d F Y') }}</span></li>
                        <li class="flex items-center gap-3"><i class="fa-regular fa-clock text-violet-300"></i><span>{{ $workshop->starts_at->format('H:i') }} @if($workshop->duration_label) - لمدة {{ $workshop->duration_label }} @endif</span></li>
                        @if($workshop->location)
                            <li class="flex items-center gap-3"><i class="fa-solid fa-location-dot text-violet-300"></i><span>{{ $workshop->location }}</span></li>
                        @endif
                        <li class="flex items-center gap-3">
                            @if($workshop->presenter_avatar_path)
                                <img src="{{ asset('storage/' . ltrim($workshop->presenter_avatar_path, '/')) }}" alt="{{ $workshop->presenter_name }}" class="h-9 w-9 rounded-full object-cover ring-2 ring-violet-700/40">
                            @else
                                <i class="fa-regular fa-user text-violet-300"></i>
                            @endif
                            <span>يقدمها: {{ $workshop->presenter_name }}</span>
                        </li>
                        @if($workshop->genre)
                            <li class="flex items-center gap-3"><i class="fa-solid fa-feather text-violet-300"></i><span>التصنيف: {{ $workshop->genre }}</span></li>
                        @endif
                        @if($workshop->presenter_bio)
                            <li class="flex items-start gap-3"><i class="fa-solid fa-id-card text-violet-300 mt-0.5"></i><span class="leading-relaxed">{{ Str::limit($workshop->presenter_bio, 180) }}</span></li>
                        @endif
                        @php $currentRegistrationsAside = $workshop->registrations()->count(); @endphp
                        @if($workshop->capacity)
                            <li class="flex items-center gap-3"><i class="fa-solid fa-users-line text-violet-300"></i><span>السعة: {{ $currentRegistrationsAside }} / {{ $workshop->capacity }} @if($currentRegistrationsAside >= $workshop->capacity)<span class="ml-2 inline-flex items-center gap-1 rounded-full bg-rose-600/20 px-2 py-0.5 text-[10px] font-medium text-rose-200">ممتلئة</span>@elseif($workshop->capacity - $currentRegistrationsAside <= 3)<span class="ml-2 inline-flex items-center gap-1 rounded-full bg-amber-500/20 px-2 py-0.5 text-[10px] font-medium text-amber-100">تبقى {{ $workshop->capacity - $currentRegistrationsAside }}</span>@endif</span></li>
                        @endif
                    </ul>
                    @if(!$workshop->external_apply_url)
                        <div class="mt-auto rounded-2xl bg-white/10 p-4 text-xs leading-relaxed text-violet-100/80">بالضغط على الإرسال، توافق على تواصلنا معك لاحقاً.</div>
                    @else
                        <div class="mt-auto rounded-2xl bg-white/10 p-4 text-xs leading-relaxed text-violet-100/80">التسجيل خارجي. لأي استفسار تواصل معنا.</div>
                    @endif
                </aside>
            </div>
        </div>
    </section>
</x-layout>
