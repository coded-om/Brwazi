<x-layout>
    <section class="max-w-3xl mx-auto px-4 py-12">
        <div class="mb-10 text-center space-y-3">
            <h1 class="text-3xl font-bold text-indigo-950">اقتراح ورشة جديدة</h1>
            <p class="text-slate-500 text-sm">يمكن للأعضاء الموثقين اقتراح ورشات، وسيقوم فريق الإدارة بمراجعتها والموافقة عليها قبل ظهورها للجميع.</p>
        </div>

        @if(! auth()->user() || ! auth()->user()->isVerified())
            <div class="rounded-3xl bg-white p-8 shadow-sm ring-1 ring-rose-200/50 border border-rose-100 text-center">
                <h2 class="text-xl font-semibold text-rose-600 mb-3">هذه الميزة متاحة للأعضاء الموثقين فقط</h2>
                <p class="text-slate-600 text-sm mb-6">قم بطلب التوثيق أولاً للاستفادة من إمكانية اقتراح ورشات جديدة.</p>
                <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 rounded-full bg-slate-800 text-white px-6 py-3 text-sm font-semibold hover:bg-slate-900">العودة للوحة التحكم</a>
            </div>
        @else
            <form action="{{ route('workshops.submit.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                <div class="rounded-3xl bg-white shadow-sm ring-1 ring-slate-100 p-8 space-y-8">
                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-sm font-medium text-indigo-900">عنوان الورشة *</label>
                            <input name="title" value="{{ old('title') }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100" placeholder="مثال: أساسيات الرسم الزيتي" />
                            @error('title') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-indigo-900">اسم المقدم *</label>
                            <input name="presenter_name" value="{{ old('presenter_name', auth()->user()->full_name ?? '') }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100" />
                            @error('presenter_name') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-indigo-900">نوع الفن</label>
                            <input name="art_type" value="{{ old('art_type') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100" />
                            @error('art_type') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-indigo-900">موعد البداية *</label>
                            <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" required class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100" />
                            @error('starts_at') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-indigo-900">المدة (دقائق)</label>
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100" placeholder="مثال: 120" />
                            @error('duration_minutes') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="text-sm font-medium text-indigo-900">الموقع / المنصة</label>
                            <input name="location" value="{{ old('location') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100" placeholder="مثال: جدة - مركز الفن الحديث أو عبر الزوم" />
                            @error('location') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-indigo-900">وصف مختصر</label>
                        <textarea name="short_description" rows="4" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100" placeholder="لمحة عن محتوى الورشة وهدفها">{{ old('short_description') }}</textarea>
                        @error('short_description') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-6">
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-indigo-900">رابط التسجيل الخارجي (اختياري)</label>
                                <input name="external_apply_url" value="{{ old('external_apply_url') }}" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100" placeholder="https://..." />
                                @error('external_apply_url') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-indigo-900">صورة الغلاف</label>
                                <input type="file" name="cover_image" accept="image/*" class="block w-full text-sm" />
                                @error('cover_image') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-indigo-900">نبذة عن المقدم</label>
                            <textarea name="presenter_bio" rows="3" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-100" placeholder="خبرة المقدم ومجال تخصصه">{{ old('presenter_bio') }}</textarea>
                            @error('presenter_bio') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-indigo-900">صورة المقدم (اختياري)</label>
                            <input type="file" name="presenter_avatar" accept="image/*" class="block w-full text-sm" />
                            @error('presenter_avatar') <p class="text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <a href="{{ route('workshops.index') }}" class="text-sm text-slate-500 hover:text-slate-700">إلغاء والعودة</a>
                    <button class="rounded-2xl bg-indigo-600 px-8 py-3 text-sm font-semibold text-white hover:bg-indigo-700">إرسال للمراجعة</button>
                </div>
            </form>
        @endif
    </section>
</x-layout>
