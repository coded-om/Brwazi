@props([
    'artists' => collect(),
    'selected' => null,
])

<div class="bg-white/70 backdrop-blur rounded-2xl border border-gray-200 p-4 sticky top-24">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-base font-bold text-indigo-900">الفنانون</h3>
        @if($selected)
            <a href="{{ route('art.index') }}" class="text-xs text-indigo-700 hover:underline">عرض الكل</a>
        @endif
    </div>

    <div class="max-h-[420px] overflow-y-auto pr-1 custom-scroll">
        <ul class="space-y-3">
            @forelse($artists as $a)
                @php $isActive = (int)($selected->id ?? 0) === (int)$a->id; @endphp
                <li>
                    <a href="{{ route('art.index', array_filter(array_merge(request()->query(), ['artist' => $a->id, 'page' => null]))) }}"
                    class="flex items-center gap-3 rounded-xl p-2 transition {{ $isActive ? 'bg-indigo-50 ring-1 ring-indigo-200' : 'hover:bg-gray-50' }}">
                        <div class="h-10 w-10 rounded-full overflow-hidden ring-1 ring-gray-200 flex-shrink-0">
                            <img src="{{ $a->profile_image_url ?? asset('imgs/default-avatar.png') }}" alt="{{ $a->full_name ?? ($a->fname.' '.$a->lname) }}" class="h-full w-full object-cover">
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-sm font-semibold text-gray-900 truncate">{{ $a->full_name ?? trim(($a->fname ?? '').' '.($a->lname ?? '')) }}</div>
                            <div class="text-xs text-gray-500 flex items-center gap-3">
                                <span class="flex items-center gap-1"><i class="fa-solid fa-heart text-rose-500"></i>{{ (int)($a->total_likes ?? 0) }}</span>
                                <span class="text-gray-300">•</span>
                                <span class="flex items-center gap-1"><i class="fa-regular fa-image text-indigo-500"></i>{{ (int)($a->artworks_count ?? 0) }}</span>
                            </div>
                        </div>
                    </a>
                </li>
            @empty
                <li class="text-sm text-gray-500">لا يوجد فنانون بعد.</li>
            @endforelse
        </ul>
    </div>
</div>

@once
    <style>
        /* Consistent thin, rounded scrollbar for overflow lists */
        .custom-scroll { scrollbar-width: thin; scrollbar-color: #94a3b8 transparent; }
        .custom-scroll::-webkit-scrollbar { width: 8px; height: 8px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb {
            background-color: #cbd5e1; /* slate-300 */
            border-radius: 9999px;
            border: 2px solid transparent;
            background-clip: content-box;
        }
        .custom-scroll:hover::-webkit-scrollbar-thumb { background-color: #94a3b8; /* slate-400 */ }
    </style>
@endonce
