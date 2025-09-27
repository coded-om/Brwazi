<x-layout>
    <section class="min-h-[calc(100vh-64px)] bg-[#0f102a] py-6">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center mb-4 text-white">
                <h1 class="text-xl md:text-2xl font-bold">معرض برواز - عرض تفاعلي</h1>
                <a href="{{ route('artbrwaz.index') }}"
                    class="inline-flex items-center gap-2 text-sm md:text-base px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 transition-colors">
                    <i class="fa-solid fa-arrow-right"></i>
                    <span>رجوع</span>
                </a>
            </div>

            <div class="relative w-full rounded-xl overflow-hidden shadow-2xl ring-1 ring-white/10">
                <!-- 16:9 responsive wrapper -->
                <div class="relative w-full" style="padding-top: 56.25%">
                    @php($settings = \App\Models\Gallery3DSetting::current())
                    <iframe
                        src="{{ $settings->exhibit_url ?: 'https://www.artsteps.com/embed/68c5668548bbdfa0b611ff85/560/315' }}"
                        class="absolute inset-0 w-full h-full border-0"
                        allow="fullscreen; xr-spatial-tracking; accelerometer; magnetometer; gyroscope;" allowfullscreen
                        loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="معرض برواز التفاعلي">
                    </iframe>
                </div>
            </div>

            <p class="text-white/70 mt-3 text-sm">نصيحة: لتجربة أفضل، استخدم وضع ملء الشاشة أو جهاز بشاشة كبيرة.</p>
        </div>
    </section>
</x-layout>