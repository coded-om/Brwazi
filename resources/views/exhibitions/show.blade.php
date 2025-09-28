<x-layout>
    <section class="max-w-6xl mx-auto px-4 md:px-6 py-10">
        <div class="mb-6 flex items-center gap-3 text-sm text-slate-500">
            <a href="{{ route('exhibitions.index') }}" class="hover:text-indigo-600 flex items-center gap-1"><i
                    class="fa-solid fa-arrow-right"></i> العودة</a>
            <span>/</span>
            <span class="text-slate-700 font-medium">{{ $exhibition->title }}</span>
        </div>
        <div class="grid md:grid-cols-[1.05fr_0.95fr] gap-10">
            <div class="space-y-8">
                <div class="rounded-3xl overflow-hidden ring-1 ring-slate-200 bg-slate-50">
                    <img src="{{ $exhibition->cover_image_path ? asset_url($exhibition->cover_image_path, 'imgs/pic/Book.png') : asset('imgs/pic/Book.png') }}"
                        alt="{{ $exhibition->title }}" class="w-full h-80 object-cover">
                </div>
                <div class="prose prose-slate max-w-none rtl:prose-p:leading-loose prose-p:text-slate-700">
                    @if($exhibition->description)
                        {!! nl2br(e($exhibition->description)) !!}
                    @else
                        <p class="text-slate-500 text-sm">لا توجد تفاصيل وصفية حالياً.</p>
                    @endif
                </div>
            </div>
            <aside class="space-y-6">
                <div class="rounded-2xl bg-white ring-1 ring-slate-200 p-6 space-y-5">
                    <h1 class="text-2xl font-bold text-indigo-950">{{ $exhibition->title }}</h1>
                    <ul class="space-y-3 text-sm text-slate-600">
                        @if($exhibition->city || $exhibition->country)
                            <li class="flex items-center gap-2"><i
                                    class="fa-solid fa-location-dot text-indigo-500"></i><span>{{ $exhibition->city }}
                                    {{ $exhibition->country ? '، ' . $exhibition->country : '' }}</span></li>
                        @endif
                        @if($exhibition->address)
                            <li class="flex items-center gap-2"><i
                                    class="fa-regular fa-map text-indigo-500"></i><span>{{ $exhibition->address }}</span>
                            </li>
                        @endif
                        @if($exhibition->starts_at)
                            <li class="flex items-center gap-2"><i
                                    class="fa-regular fa-calendar text-indigo-500"></i><span>البداية:
                                    {{ $exhibition->starts_at->translatedFormat('d F Y') }}</span></li>
                        @endif
                        @if($exhibition->ends_at)
                            <li class="flex items-center gap-2"><i
                                    class="fa-regular fa-calendar-check text-indigo-500"></i><span>النهاية:
                                    {{ $exhibition->ends_at->translatedFormat('d F Y') }}</span></li>
                        @endif
                        @if($exhibition->website_url)
                            <li class="flex items-center gap-2"><i class="fa-solid fa-earth-americas text-indigo-500"></i><a
                                    class="text-indigo-700 hover:underline" href="{{ $exhibition->website_url }}"
                                    target="_blank" rel="noopener">الموقع الرسمي</a></li>
                        @endif
                        @if($exhibition->contact_phone)
                            <li class="flex items-center gap-2"><i class="fa-solid fa-phone text-indigo-500"></i><span
                                    dir="ltr">{{ $exhibition->contact_phone }}</span></li>
                        @endif
                        @if($exhibition->contact_email)
                            <li class="flex items-center gap-2"><i class="fa-regular fa-envelope text-indigo-500"></i><a
                                    href="mailto:{{ $exhibition->contact_email }}"
                                    class="text-indigo-700 hover:underline">{{ $exhibition->contact_email }}</a></li>
                        @endif
                    </ul>
                    @if($exhibition->latitude && $exhibition->longitude)
                        <div class="h-56 rounded-xl overflow-hidden ring-1 ring-slate-200" id="mini-map"></div>
                    @endif
                </div>
            </aside>
        </div>
    </section>

    @if($exhibition->latitude && $exhibition->longitude)
        @push('styles')
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
                integrity="sha256-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44c=" crossorigin="" />
        @endpush
        @push('scripts')
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                integrity="sha256-VuZ8HsrwYJcCfevkpzxxEe7dBDEu4SU1mI7qp8v+Pqw=" crossorigin=""></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const lat = Number('{{ $exhibition->latitude }}') || null;
                    const lng = Number('{{ $exhibition->longitude }}') || null;
                    if (lat !== null && lng !== null) {
                        const map = L.map('mini-map', { zoomControl: false, attributionControl: false }).setView([lat, lng], 13);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
                        L.marker([lat, lng]).addTo(map);
                    }
                });
            </script>
        @endpush
    @endif
</x-layout>