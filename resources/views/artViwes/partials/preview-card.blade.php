<div class="rounded-xl overflow-hidden border shadow bg-white">
    <div class="aspect-[4/3] bg-gray-100 relative">
        @if(!empty($data['images']))
            <img src="{{ $data['images'][0] }}" class="absolute inset-0 w-full h-full object-cover" alt="preview">
        @else
            <div class="flex items-center justify-center h-full text-gray-400 text-sm">لا توجد صورة</div>
        @endif
    </div>
    <div class="p-4 space-y-2">
        <h4 class="font-bold text-gray-800 text-lg">{{ $data['title'] ?? 'عنوان غير محدد' }}</h4>
        <p class="text-xs text-indigo-600 font-medium">
            {{ \App\Models\Artwork::categories()[$data['category']] ?? 'تصنيف' }}
        </p>
        <p class="text-sm text-gray-600 line-clamp-3">{{ $data['description'] ?? '' }}</p>
        @if(!empty($data['tags']))
            <div class="flex flex-wrap gap-1 pt-1">
                @foreach((array) $data['tags'] as $tg)
                    <span class="text-[11px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded">#{{ $tg }}</span>
                @endforeach
            </div>
        @endif
        <div class="flex items-center gap-4 text-xs text-gray-500 pt-2">
            @if(!empty($data['year']))<span><i class="fa-regular fa-calendar"></i> {{ $data['year'] }}</span>@endif
            @if(!empty($data['dimensions']))<span><i class="fa-solid fa-up-down-left-right"></i>
            {{ $data['dimensions'] }}</span>@endif
            @if(!empty($data['price']))<span
                class="text-emerald-600 font-semibold text-sm">{{ number_format((float) $data['price'], 2) }}
            ر.ع</span>@endif
        </div>
    </div>
</div>