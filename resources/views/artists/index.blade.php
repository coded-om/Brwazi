@php
    $title = 'قسم الفنانين';
@endphp
<x-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-indigo-900 flex items-center gap-2">
                    {{ $title }}
                    <img src="{{ asset('imgs/icons-color/eye-category.svg') }}" alt="Icon"
                        class="inline-block w-7 h-7" />
                </h1>
                <nav class="mt-2 text-sm text-gray-500" aria-label="breadcrumb">
                    <ol class="inline-flex items-center gap-2">
                        <li><a href="{{ route('home') }}" class="hover:text-indigo-700">الرئيسية</a></li>
                        <li>/</li>
                        <li class="text-gray-700">{{ $title }}</li>
                    </ol>
                </nav>
            </div>
            <form method="GET" action="{{ route('artists.index') }}" class="flex items-center gap-2">
                <div class="relative">
                    <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="ابحث عن فنان"
                        class="w-56 sm:w-72 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100" />
                    @if(!empty($q))
                        <a href="{{ route('artists.index') }}"
                            class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-gray-500 hover:text-indigo-600">مسح</a>
                    @endif
                </div>
                <select name="sort"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100"
                    onchange="this.form.submit()">
                    <option value="latest" {{ ($sort ?? 'latest') === 'latest' ? 'selected' : '' }}>الأحدث</option>
                    <option value="popular" {{ ($sort ?? 'latest') === 'popular' ? 'selected' : '' }}>الأكثر نشاطًا
                    </option>
                </select>
                <button type="submit"
                    class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm">
                    بحث
                </button>
            </form>
        </div>

        @if($artists->count())
            <div class="grid gap-6 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
                @foreach($artists as $a)
                    @php
                        $name = $a->full_name;
                        $avatar = $a->profile_image_url;
                    @endphp
                    <a href="{{ route('art.index', ['artist' => $a->id]) }}"
                        class="block group rounded-2xl overflow-hidden bg-white shadow-sm ring-1 ring-gray-200">
                        <div class="h-44 bg-gray-100">
                            <img src="{{ $avatar }}" alt="{{ $name }}"
                                 class="w-full h-full object-cover transition duration-500 ease-out blur-sm group-hover:blur-[2px]"
                                 loading="lazy" decoding="async"
                                 onload="this.classList.remove('blur-sm');" />
                        </div>
                        <div class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full overflow-hidden ring-1 ring-gray-200">
                                    <img src="{{ $avatar }}" alt="{{ $name }}" loading="lazy"
                                         class="h-full w-full object-cover transition duration-500 ease-out blur-sm"
                                         onload="this.classList.remove('blur-sm');" />
                                </div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900 truncate">{{ $name }}</div>
                                    <div class="text-xs text-gray-500">أعمال منشورة:
                                        {{ (int) ($a->artworks_published_count ?? 0) }}</div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $artists->links() }}
            </div>
        @else
            <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center text-gray-500">
                لا يوجد فنانون لعرضهم الآن.
            </div>
        @endif
    </div>
</x-layout>