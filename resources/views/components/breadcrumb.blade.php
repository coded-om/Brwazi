@props([
    'items' => [], // each: ['label' => 'النص', 'url' => '/path', 'current' => bool]
])
@if(count($items) > 0)
<nav class="max-w-6xl mx-auto px-4 sm:px-6 pt-6 text-sm" aria-label="breadcrumb" dir="rtl">
    <ol class="flex flex-wrap items-center text-gray-400 gap-1">
        @foreach($items as $i => $it)
            @if(!$loop->first)
                <li class="text-gray-400 mx-1">›</li>
            @endif
            <li>
                @if(!($it['current'] ?? false) && !empty($it['url']))
                    <a href="{{ $it['url'] }}" class="hover:text-indigo-700 hover:underline transition">{{ $it['label'] }}</a>
                @else
                    <span class="text-gray-500 font-medium">{{ $it['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif
