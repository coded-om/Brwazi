@props([
    'artists' => collect(),
    'selected' => null,
])

@if(($artists?->count() ?? 0) > 0)
<div class="mb-8 -mx-4 sm:-mx-6 lg:mx-0" data-aos="fade-up" data-aos-delay="50">
    <div class="px-4 sm:px-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-extrabold text-indigo-900 flex items-center gap-2">الفنانين <img
                    src="{{ asset('imgs/icons-color/eye-category.svg') }}" alt="Section Icon"
                    class="inline-block w-6 h-6 ml-1"></h3>
            @if($selected)
                <a href="{{ route('artists.index') }}" class="text-xs text-indigo-700 hover:underline">عرض الكل</a>
            @endif
        </div>

    <div class="relative" data-artists-slider>
        <div class="overflow-x-auto no-scrollbar scroll-smooth" data-scroller>
            <ul class="flex gap-5 snap-x snap-mandatory pb-2">
                @foreach($artists as $a)
                    @php
                        $isActive = (int)($selected->id ?? 0) === (int)$a->id;
                        try {
                            $preview = \App\Models\Artwork::query()
                                ->where('user_id', $a->id)
                                ->where('status', \App\Models\Artwork::STATUS_PUBLISHED)
                                ->where('images_count', '>', 0)
                                ->orderByDesc('published_at')
                                ->orderByDesc('likes_count')
                                ->first();
                        } catch (\Throwable $e) { $preview = null; }
                        $previewUrl = $preview?->primary_image_url ?? ($a->profile_image_url ?? asset('imgs/default-avatar.png'));
                        $name = $a->full_name ?? trim(($a->fname ?? '').' '.($a->lname ?? ''));
                    @endphp
                    <li class="flex-shrink-0 snap-start">
                        <a href="{{ route('art.index', array_filter(array_merge(request()->query(), ['artist' => $a->id, 'page' => null]))) }}" class="block group">
                            <div class="w-[280px] h-[170px] rounded-xl overflow-hidden shadow-sm ring-1 ring-gray-200 bg-gray-100">
                                <img src="{{ $previewUrl }}" alt="{{ $name }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.02]" loading="lazy" decoding="async" />
                            </div>
                            <div class="mt-2 flex items-center gap-3">
                                <div class="h-7 w-7 rounded-full overflow-hidden ring-1 ring-gray-200">
                                    <img src="{{ $a->profile_image_url ?? asset('imgs/default-avatar.png') }}" alt="{{ $name }}" class="h-full w-full object-cover">
                                </div>
                                <div class="text-sm font-semibold text-gray-900 truncate flex items-center gap-1">
                                    <span class="truncate">{{ $name }}</span>
                                    <span class="text-indigo-500"><img src="{{ asset('imgs/icons-color/flwor.svg') }}" alt="Section Icon" class="inline-block w-4 h-4 ml-1"></span>
                                </div>

                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    <!-- Right arrow -->
    <button type="button" data-arrow="right" aria-label="التالي"
        class="absolute inset-y-0 right-2 my-auto h-8 w-8 rounded-full bg-white/85 backdrop-blur text-gray-600 shadow-sm border border-gray-200 hover:bg-white hover:text-gray-800 transition z-20">
            <i class="fa-solid fa-chevron-right text-xs"></i>
        </button>
        <!-- Left arrow -->
    <button type="button" data-arrow="left" aria-label="السابق"
        class="absolute inset-y-0 left-2 my-auto h-8 w-8 rounded-full bg-white/85 backdrop-blur text-gray-600 shadow-sm border border-gray-200 hover:bg-white hover:text-gray-800 transition z-20">
            <i class="fa-solid fa-chevron-left text-xs"></i>
        </button>
    </div>
    </div>
    @push('styles')
        <style>
            /* Hide scrollbars cross-browser for the slider container */
            .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
            .no-scrollbar::-webkit-scrollbar { display: none; }
        </style>
    @endpush
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-artists-slider]').forEach(wrap => {
                    const scroller = wrap.querySelector('[data-scroller]');
                    const btnLeft = wrap.querySelector('[data-arrow="left"]');
                    const btnRight = wrap.querySelector('[data-arrow="right"]');
                    if (!scroller || !btnLeft || !btnRight) return;

                    const setDisabled = (btn, disabled) => {
                        btn.classList.toggle('opacity-40', disabled);
                        btn.classList.toggle('pointer-events-none', disabled);
                        btn.classList.toggle('cursor-default', disabled);
                    };
                    const update = () => {
                        const max = scroller.scrollWidth - scroller.clientWidth;
                        const x = scroller.scrollLeft;
                        const epsilon = 1; // be tolerant with sub-pixel values
                        const hasOverflow = max > epsilon;
                        // Always show arrows; disable them based on state
                        [btnLeft, btnRight].forEach(btn => btn.classList.remove('hidden'));
                        if (!hasOverflow) {
                            setDisabled(btnLeft, true);
                            setDisabled(btnRight, true);
                            return;
                        }
                        const canLeft = x > epsilon;
                        const canRight = x < (max - epsilon);
                        setDisabled(btnLeft, !canLeft);
                        setDisabled(btnRight, !canRight);
                    };
                    update();
                    scroller.addEventListener('scroll', update, { passive: true });
                    window.addEventListener('resize', () => requestAnimationFrame(update));

                    const step = () => Math.max(160, Math.floor(scroller.clientWidth * 0.8));
                    btnLeft.addEventListener('click', () => scroller.scrollBy({ left: -step(), behavior: 'smooth' }));
                    btnRight.addEventListener('click', () => scroller.scrollBy({ left: step(), behavior: 'smooth' }));
                });
            });
        </script>
    @endpush
</div>
@endif
