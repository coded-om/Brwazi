@props([
    'items' => collect(), // collection of models or urls
    'columns' => 4,
])

@php
    $cols = max(1, (int) $columns);
    // Distribute items round-robin into $cols buckets
    $buckets = array_fill(0, $cols, []);
    $i = 0;
    foreach (($items ?? collect()) as $item) {
        $buckets[$i % $cols][] = $item;
        $i++;
    }

    $getUrl = function($item) {
        if (is_string($item)) return $item;
        if (is_object($item)) {
            if (isset($item->primary_image_url)) return $item->primary_image_url;
            if (method_exists($item, 'getAttribute')) return $item->getAttribute('primary_image_url');
        }
        return null;
    };
    $getAlt = function($item) {
        if (is_object($item) && isset($item->title)) return $item->title;
        return 'artwork';
    };
    $getTitle = function($item) {
        if (is_object($item) && isset($item->title)) return $item->title;
        return null;
    };
    $isArtwork = function($item) {
        return is_object($item) && isset($item->id);
    };
    $isForSale = function($item) {
        if (!is_object($item)) return false;
        $mode = $item->sale_mode ?? null;
        return $mode === 'fixed' || $mode === 'auction';
    };
    $priceText = function($item) {
        if (!is_object($item)) return null;
        $mode = $item->sale_mode ?? null;
        if ($mode === 'fixed' && isset($item->price) && $item->price !== null) {
            $num = is_numeric($item->price) ? number_format((float)$item->price, 0, '.', ',') : $item->price;
            return ['label' => 'ريال', 'value' => $num];
        }
        if ($mode === 'auction' && isset($item->auction_start_price) && $item->auction_start_price !== null) {
            $num = is_numeric($item->auction_start_price) ? number_format((float)$item->auction_start_price, 0, '.', ',') : $item->auction_start_price;
            return ['label' => 'مزاد', 'value' => $num];
        }
        return null;
    };
@endphp

@php
    // Optional preload: build a map of user_id => isVerified (single batched query) to avoid N+1 when relation 'user' isn't eager-loaded.
    $verifiedMap = [];
    try {
        $ids = [];
        foreach (($items ?? collect()) as $it) {
            if (!is_object($it)) continue;
            $uid = $it->user_id ?? (method_exists($it, 'getAttribute') ? $it->getAttribute('user_id') : null);
            if ($uid) $ids[] = (int) $uid;
        }
        $ids = array_values(array_unique(array_filter($ids)));
        if ($ids) {
            $statuses = \App\Models\User::whereIn('id', $ids)->pluck('status', 'id')->all();
            foreach ($statuses as $uid => $status) {
                $verifiedMap[(int)$uid] = in_array((int)$status, [\App\Models\User::STATUS_VERIFIED, \App\Models\User::STATUS_PREMIUM], true);
            }
        }
    } catch (\Throwable $e) {
        $verifiedMap = [];
    }
@endphp

@php
    // Map number to tailwind grid-cols-*
    $gridCols = match($cols) {
        1 => 'grid-cols-1',
        2 => 'grid-cols-2',
        3 => 'grid-cols-3',
        4 => 'grid-cols-4',
        5 => 'grid-cols-5',
        6 => 'grid-cols-6',
        default => 'grid-cols-4',
    };
    $lgGridCols = 'lg:' . $gridCols; // apply chosen columns from lg and up; mobile gets 2, md gets 3
    // Global card index to create a subtle stagger for AOS
    $cardIndex = 0;
    $currentTab = request()->query('tab');
    $showFavoritesLikeBar = $currentTab === 'favorites';
@endphp
<div class="grid gap-3 sm:gap-4 grid-cols-2 md:grid-cols-3 {{ $lgGridCols }} js-masonry-grid" data-cols="{{ $cols }}">
    @foreach($buckets as $column)
    <div class="flex flex-col gap-4 js-masonry-col">
            @foreach($column as $it)
                @php $url = $getUrl($it); @endphp
                @if($url)
                    @php
                        $title = $getTitle($it);
                        $sale = $priceText($it);
                        $liked = (is_object($it) && method_exists($it, 'likedBy')) ? $it->likedBy(auth()->user()) : false;
                        $isDraft = is_object($it) && isset($it->status) && $it->status === \App\Models\Artwork::STATUS_DRAFT;
                        // Stagger delay in ms (cap at 480ms) for smoother reveal
                        $aosDelay = min(($cardIndex % 12) * 40, 480);
                        $cardIndex++;
                    @endphp
                    <a href="{{ $isArtwork($it) ? route('art.show', $it) : '#' }}" class="group masonry-card block relative overflow-hidden rounded-xl bg-gray-100 shadow-sm {{ $showFavoritesLikeBar ? 'favorite-mode' : '' }}" data-aos="fade-up" data-aos-delay="{{ $aosDelay }}">
                        <img src="{{ $url }}"
                             alt="{{ $getAlt($it) }}"
                             class="lazy-media w-full h-auto object-cover transform transition-transform duration-300 group-hover:scale-[1.01] blur-sm"
                             loading="lazy"
                             decoding="async"
                             fetchpriority="{{ $cardIndex < 4 ? 'high' : 'low' }}"
                             onload="this.classList.add('is-loaded'); this.classList.remove('blur-sm');" />

                        @if($isArtwork($it))
                            @php
                                $authorVerified = false;
                                if (is_object($it) && method_exists($it, 'relationLoaded') && $it->relationLoaded('user')) {
                                    $u = $it->getRelation('user');
                                    $authorVerified = $u && method_exists($u, 'isVerified') && $u->isVerified();
                                } else {
                                    $uid = $it->user_id ?? (method_exists($it, 'getAttribute') ? $it->getAttribute('user_id') : null);
                                    if ($uid && isset($verifiedMap[(int)$uid])) {
                                        $authorVerified = $verifiedMap[(int)$uid];
                                    }
                                }
                            @endphp
                            @if($authorVerified)
                                <div class="absolute top-1 left-1 z-20 h-8 w-8 rounded-full bg-black/5 backdrop-blur-sm flex items-center justify-center">
                                    <img src="{{ asset('imgs/icons-color/verifiid.svg') }}" alt="موثّق" class="h-8 w-8 drop-shadow-md" />
                                </div>
                            @endif
                            @if($isDraft)
                                <div class="absolute bottom-1 left-1 z-20 px-2 py-0.5 rounded-full bg-amber-500/90 text-white text-[11px] font-semibold shadow">
                                    مسودة
                                </div>
                            @endif
                            <!-- Like area (button + count) -->
                            <div class="like-bar">
                                <button type="button"
                                    class="like-btn js-like-btn"
                                    title="إعجاب"
                                    data-artwork-id="{{ $it->id }}"
                                    data-liked="{{ $liked ? 1 : 0 }}"
                                    data-like-url="{{ route('art.like', $it) }}"
                                    data-unlike-url="{{ route('art.unlike', $it) }}"
                                    data-token="{{ csrf_token() }}">
                                    <i class="{{ $liked ? 'fa-solid text-rose-500' : 'fa-regular text-white' }} fa-heart"></i>
                                </button>
                                <span class="like-count js-like-count">{{ (int)($it->likes_count ?? 0) }}</span>
                            </div>
                        @endif

                        @if($title || $sale)
                            <!-- Bottom gradient overlay for text readability -->
                            <div class="absolute inset-x-0 bottom-0 h-20 md:h-24 bg-gradient-to-t from-black/70 group-hover:from-black/80 to-transparent transition-colors duration-300 pointer-events-none"></div>
                            <div class="absolute inset-x-0 bottom-0 p-2.5 md:p-3 text-white">
                                <div class="flex items-end justify-between gap-3">
                                    @if($sale)
                                        <div class="flex items-baseline gap-1 drop-shadow opacity-80 group-hover:opacity-100 transition-opacity duration-300 ease-out">
                                            <span class="text-white/90 text-sm md:text-base">{{ $sale['label'] }}</span>
                                            <span class="text-white font-extrabold text-xl md:text-2xl leading-none">{{ $sale['value'] }}</span>
                                        </div>
                                    @else
                                        <span></span>
                                    @endif
                                    @if($title)
                                        <div class="ml-2 text-right drop-shadow opacity-80 group-hover:opacity-100 transition-opacity duration-300 ease-out">
                                            <div class="font-bold text-base md:text-lg leading-tight">{{ $title }}</div>
                                            @if(is_object($it) && ($it->sale_mode ?? null) === 'auction')
                                                <div class="text-xs text-white/90">مزاد</div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </a>
                @endif
            @endforeach
        </div>
    @endforeach
    @if(empty($buckets) || $i === 0)
        <div class="col-span-full text-center text-gray-500 py-8">لا توجد عناصر للعرض</div>
    @endif

</div>

@push('scripts')
    @once
    <script>
    (function(){
        const onLoad = (img)=>{
            img.classList.add('is-loaded');
        };
        const init = () => {
            const imgs = document.querySelectorAll('img.lazy-media');
            imgs.forEach(img => {
                if (img.complete) {
                    onLoad(img);
                } else {
                    img.addEventListener('load', () => onLoad(img), { once: true });
                    img.addEventListener('error', () => onLoad(img), { once: true });
                }
            });
        };
        if (document.readyState === 'loading'){
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();

    document.addEventListener('click', async function(e){
            const btn = e.target.closest('.js-like-btn');
            if(!btn) return;
            e.preventDefault();
            e.stopPropagation();
            const artworkId = btn.dataset.artworkId;
            const liked = btn.dataset.liked === '1';
            const url = liked ? btn.dataset.unlikeUrl : btn.dataset.likeUrl;
            const method = liked ? 'DELETE' : 'POST';
            const token = btn.dataset.token;
            try {
                const res = await fetch(url, {
                    method,
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                });
                if(res.status === 401){
                    window.location.href = "{{ route('login') }}";
                    return;
                }
                if(!res.ok){ throw new Error('request failed'); }
                const data = await res.json();
                // toggle UI
                btn.dataset.liked = liked ? '0' : '1';
                const icon = btn.querySelector('i');
                if(icon){
                    if(liked){
                        icon.classList.remove('fa-solid','text-rose-500');
                        icon.classList.add('fa-regular','text-white');
                    } else {
                        icon.classList.remove('fa-regular','text-white');
                        icon.classList.add('fa-solid','text-rose-500');
                    }
                }
                const countEl = btn.parentElement?.querySelector('.js-like-count');
                if(countEl && data && typeof data.likes_count !== 'undefined'){
                    countEl.textContent = data.likes_count;
                }
            } catch (err) {
                console.error('like/unlike error', err);
            }
        });
    </script>
    @endonce
@endpush

@push('styles')
    <style>
    /* Lazy fade-in for images */
    img.lazy-media{ opacity: 0; transform: translateY(4px) scale(1.01); transition: opacity .5s ease, transform .5s ease; }
    img.lazy-media.is-loaded{ opacity: 1; transform: none; }
        /* Scoped styles for masonry card */
        .masonry-card{
            position: relative;
        }
        /* Smooth inner shadow around edges */
        .masonry-card::after{
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            pointer-events: none;
            box-shadow:
                inset 0 10px 20px -14px rgba(0,0,0,.28),
                inset 0 -10px 20px -14px rgba(0,0,0,.22),
                inset 10px 0 20px -14px rgba(0,0,0,.20),
                inset -10px 0 20px -14px rgba(0,0,0,.20);
            opacity: .55;
            transition: opacity .3s ease;
        }
        .masonry-card:hover::after{ opacity: .75; }
        .masonry-card .like-bar{
            position: absolute;
            top: .5rem;
            right: .5rem;
            z-index: 10;
            display: inline-flex;
            align-items: center;
            gap: .1rem;
            backdrop-filter: blur(6px);
            background: rgba(0,0,0,.20);
            border-radius: 9999px;
            padding: .06rem .18rem;
            opacity: 0;
            pointer-events: none;
            transform: translateY(-2px);
            transition: opacity .2s ease, transform .2s ease;
        }
        .masonry-card:hover .like-bar{ opacity: 1; pointer-events: auto; transform: translateY(0); }
        /* In favorites tab, keep the like bar visible */
        .masonry-card.favorite-mode .like-bar{ opacity: 1; pointer-events: auto; transform: translateY(0); }
        .masonry-card .like-btn{
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 9999px;
            background: rgba(255,255,255,.22);
            transition: background-color .2s ease;
        }
        .masonry-card .like-btn:hover{ background: rgba(255,255,255,.4); }
        .masonry-card .like-btn i{ font-size: 11px; text-shadow: 0 1px 1px rgba(0,0,0,.6); }
        .masonry-card .like-count{
            min-width: 12px;
            height: 12px;
            padding: 0 .2rem;
            border-radius: 9999px;
            background: rgba(0,0,0,.22);
            color: #fff;
            font-size: 9px;
            line-height: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            opacity: .6;
            transition: opacity .2s ease;
        }
        .masonry-card:hover .like-count{ opacity: .9; }
    </style>
@endpush
