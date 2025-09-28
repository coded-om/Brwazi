<x-layout>
    <!-- Predefine exhibitionsPage BEFORE Alpine processes x-data to avoid ReferenceError -->
    <script>
        window.exhibitionsPage = function () {
            return {
                mode: localStorage.getItem('exhMode') || 'grid',
                exhibitions: [],
                map: null,
                markers: [],
                loading: true,
                init() {
                    try {
                        const payload = JSON.parse(document.getElementById('exhibitions-data')?.textContent || '{"data":[],"meta":{}}');
                        this.exhibitions = payload.data || [];
                        this.meta = payload.meta || {};
                    } catch (e) { this.exhibitions = []; }
                    // Auto switch to map only if user has not chosen before
                    if (!localStorage.getItem('exhMode') && this.exhibitions.some(e => e.lat && e.lng)) { this.mode = 'map'; }
                    this.$nextTick(() => { if (this.mode === 'map') this.initMap(); });
                    // unified external toggle (overwrites any previous)
                    window.__exhToggle = (m) => {
                        this.mode = m;
                        localStorage.setItem('exhMode', m);
                        this.$nextTick(() => { if (m === 'map' && !this.map) this.initMap(); });
                    };
                    setTimeout(()=>{this.loading=false}, 150); // tiny delay for nicer skeleton fade
                },
                meta: {},
                loadingMore:false,
                loadMore() {
                    if(!this.meta?.next_page_url || this.loadingMore) return;
                    this.loadingMore = true;
                    fetch(this.meta.next_page_url, { headers: { 'Accept': 'application/json' }})
                        .then(r=>r.json())
                        .then(json => {
                            if(Array.isArray(json.data)) {
                                this.exhibitions.push(...json.data);
                                this.meta = json.meta || this.meta;
                                if(this.mode==='map' && this.map) { // add new markers
                                    json.data.filter(e=>e.lat && e.lng).forEach(e=>{
                                        const m = L.marker([e.lat,e.lng]).addTo(this.map);
                                        m.bindPopup(`<div class='space-y-1'><a href='${e.url}' class='font-semibold text-indigo-700 hover:underline'>${e.title}</a>${e.short ? `<div class=\"text-xs text-slate-500 line-clamp-3\">${e.short}</div>` : ''}</div>`);
                                        this.markers.push(m);
                                    });
                                }
                            }
                        })
                        .catch(()=>{})
                        .finally(()=>{this.loadingMore=false});
                },
                initMap() {
                    if (!window.L) {
                        // Leaflet not yet available (network latency) – retry briefly
                        let attempts = 0;
                        const retry = setInterval(() => {
                            if (window.L || attempts > 30) { clearInterval(retry); if (window.L) this.initMap(); }
                            attempts++;
                        }, 100);
                        return;
                    }
                    if (this.map) return; // idempotent
                    this.map = L.map('exhibitions-map', { scrollWheelZoom: true }).setView([21.4735, 55.9754], 6);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(this.map);
                    this.exhibitions.filter(e => e.lat && e.lng).forEach(e => {
                        const m = L.marker([e.lat, e.lng]).addTo(this.map);
                        m.bindPopup(`<div class='space-y-1'>
                                <a href='${e.url}' class='font-semibold text-indigo-700 hover:underline'>${e.title}</a>
                                ${e.short ? `<div class="text-xs text-slate-500 line-clamp-3">${e.short}</div>` : ''}
                            </div>`);
                        this.markers.push(m);
                    });
                    if (this.markers.length) {
                        const g = L.featureGroup(this.markers);
                        this.map.fitBounds(g.getBounds().pad(0.2));
                    }
                },
                formatDate(iso) {
                    try { const d = new Date(iso); return d.toLocaleDateString('ar-EG', { month: 'short', day: 'numeric' }); } catch (e) { return ''; }
                }
            }
        }
    </script>
    <section class="max-w-7xl mx-auto px-4 md:px-6 py-6 md:py-8" x-data="{openFilters:false}">
        <!-- Breadcrumb -->
        <nav class="text-[13px] text-slate-500 mb-5 flex flex-wrap gap-1" aria-label="breadcrumb">
            <a href="/" class="hover:text-indigo-600 transition">الرئيسية</a>
            <span class="opacity-50">/</span>
            <span class="text-slate-700 font-medium">قسم المعارض الواقعية</span>
        </nav>

        <!-- Hero / Header -->
        <div class="relative overflow-hidden rounded-3xl mb-8 ring-1 ring-slate-200 bg-gradient-to-br from-indigo-600/15 via-indigo-500/10 to-pink-400/10 dark:from-indigo-700/25 dark:via-indigo-600/20 dark:to-fuchsia-600/10">
            <div class="absolute inset-0 pointer-events-none opacity-40 mix-blend-multiply bg-[url('https://tile.openstreetmap.org/6/34/24.png')] bg-cover bg-center"></div>
            <div class="relative px-6 py-10 md:py-14 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-3 max-w-2xl">
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-indigo-950 flex items-center gap-3">
                        <span>قسم المعارض الواقعية</span>
                        <span class="text-2xl md:text-3xl">�️</span>
                    </h1>
                    <p class="text-sm md:text-[15px] leading-relaxed text-slate-600">استكشف مواقع المعارض وعناوينها جغرافيًا، ثم انتقل لقراءة التفاصيل والصور.</p>
                    <div class="flex flex-wrap gap-2 text-[11px] text-slate-500">
                        <span class="px-2 py-1 bg-white/70 backdrop-blur rounded-full ring-1 ring-slate-200">عرض تفاعلي</span>
                        <span class="px-2 py-1 bg-white/70 backdrop-blur rounded-full ring-1 ring-slate-200">خرائط مباشرة</span>
                        <span class="px-2 py-1 bg-white/70 backdrop-blur rounded-full ring-1 ring-slate-200">تصفية لاحقاً</span>
                    </div>
                </div>
                <!-- Mode Toggle / Actions -->
                <div class="flex items-center gap-3" x-data="exhToggleLocal = { get mode(){return localStorage.getItem('exhMode')||'grid'}, setMode(m){ localStorage.setItem('exhMode',m); window.__exhToggle(m);} }" x-init="window.addEventListener('storage',()=>{ $refs.gridBtn?.dispatchEvent(new Event('refresh')); })">
                    <div class="inline-flex bg-white shadow-sm ring-1 ring-slate-200 rounded-xl overflow-hidden">
                        <button x-ref="gridBtn" @click="exhToggleLocal.setMode('grid')" :aria-pressed="(exhToggleLocal.mode==='grid').toString()" class="ui-toggle-btn" :class="exhToggleLocal.mode==='grid' ? 'is-active' : ''" title="عرض الشبكة" type="button">
                            <i class="fa-solid fa-grip"></i>
                        </button>
                        <button @click="exhToggleLocal.setMode('map')" :aria-pressed="(exhToggleLocal.mode==='map').toString()" class="ui-toggle-btn" :class="exhToggleLocal.mode==='map' ? 'is-active' : ''" title="عرض الخريطة" type="button">
                            <i class="fa-solid fa-map"></i>
                        </button>
                    </div>
                    <button type="button" @click="openFilters=true" class="hidden md:inline-flex items-center gap-2 text-sm font-medium px-4 h-11 rounded-xl bg-white ring-1 ring-slate-200 hover:bg-slate-50 transition">
                        <i class="fa-solid fa-filter text-indigo-500"></i><span>فرز / تصفية</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Map / Grid Wrapper -->
        <div x-data="exhibitionsPage()" x-init="init()" class="space-y-8">
            <template x-if="mode==='map'">
                <div class="h-[560px] rounded-3xl overflow-hidden ring-1 ring-slate-200 shadow-sm relative" id="exhibitions-map">
                    <div x-show="loading" class="absolute inset-0 bg-white/70 backdrop-blur flex items-center justify-center text-sm text-slate-500">جارٍ تحميل الخريطة...</div>
                </div>
            </template>

            <template x-if="mode==='grid'">
                <div class="min-h-[200px]" x-cloak>
                    <!-- Loading skeleton -->
                    <template x-if="loading">
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 animate-pulse">
                            <template x-for="i in 8" :key="i">
                                <div class="rounded-2xl bg-white ring-1 ring-slate-200 overflow-hidden">
                                    <div class="h-40 bg-slate-200"></div>
                                    <div class="p-4 space-y-3">
                                        <div class="h-3 bg-slate-200 rounded w-3/4"></div>
                                        <div class="h-2 bg-slate-200 rounded w-full"></div>
                                        <div class="h-2 bg-slate-200 rounded w-2/3"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <!-- Empty state -->
                    <template x-if="!loading && exhibitions.length===0">
                        <div class="flex flex-col items-center justify-center py-16 text-center rounded-3xl ring-1 ring-slate-200 bg-white">
                            <div class="w-16 h-16 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl mb-4"><i class="fa-regular fa-image"></i></div>
                            <h2 class="font-semibold text-slate-700 mb-2">لا توجد معارض منشورة حالياً</h2>
                            <p class="text-sm text-slate-500 mb-5">عند إضافة أول معرض سيتم عرضه هنا في الشبكة أو على الخريطة.</p>
                            <button @click="window.__exhToggle('map')" class="px-5 h-11 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition">اعرض الخريطة</button>
                        </div>
                    </template>
                    <!-- Grid -->
                    <template x-if="!loading && exhibitions.length">
                        <div>
                          <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                            <template x-for="ex in exhibitions" :key="ex.id">
                                <a :href="ex.url"
                                   class="group flex flex-col rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 hover:shadow-lg transition overflow-hidden relative">
                                    <div class="relative h-40 bg-slate-100 overflow-hidden">
                                        <img :src="ex.cover" :alt="'صورة معرض ' + ex.title" class="w-full h-full object-cover group-hover:scale-105 transition duration-500 ease-out" loading="lazy">
                                        <div class="absolute top-2 right-2 flex flex-col gap-2">
                                            <span x-show="ex.starts_at" class="px-2 py-1 rounded-md text-[10px] font-medium bg-indigo-600/90 text-white backdrop-blur" x-text="formatDate(ex.starts_at)"></span>
                                        </div>
                                        <span x-show="ex.city" class="absolute left-2 top-2 z-[2] px-2 py-1 rounded-md text-[10px] font-medium bg-white/80 backdrop-blur ring-1 ring-slate-200 text-slate-600 flex items-center gap-1"><i class="fa-solid fa-location-dot text-indigo-400"></i><span x-text="ex.city"></span></span>
                                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/55 to-transparent px-3 pt-10 pb-2">
                                            <h3 class="text-white text-sm font-semibold line-clamp-1" x-text="ex.title"></h3>
                                        </div>
                                    </div>
                                    <div class="p-4 space-y-2">
                                        <p class="text-xs text-slate-500 line-clamp-2" x-text="ex.short || ''"></p>
                                        <div class="flex items-center justify-end text-[11px] text-slate-500 gap-3">
                                            <span class="flex items-center gap-1" x-show="ex.lat && ex.lng"><i class="fa-solid fa-location-crosshairs text-indigo-400"></i>موقع</span>
                                            <span class="opacity-60">#<span x-text="ex.id"></span></span>
                                        </div>
                                    </div>
                                </a>
                            </template>
                          </div>
                          <div class="mt-10 flex justify-center" x-show="meta && meta.next_page_url">
                            <button @click="loadMore" :disabled="loadingMore" class="px-6 h-12 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
                                <span x-show="!loadingMore">تحميل المزيد</span>
                                <span x-show="loadingMore" class="flex items-center gap-2"><span class="w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span> جاري التحميل</span>
                            </button>
                          </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </section>

    @push('styles')
        <!-- Correct SRI hashes for Leaflet 1.9.4 -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <style>
            .leaflet-container {
                font-family: inherit;
            }

            /* keep typography consistent */
            [x-cloak] { display: none !important; }
            .ui-toggle-btn { width:3rem; height:3rem; display:flex; align-items:center; justify-content:center; color:#64748b; position:relative; transition:color .2s, background-color .2s; }
            .ui-toggle-btn:hover { color:#4f46e5; }
            .ui-toggle-btn.is-active { background:#4f46e5; color:#fff; }
            .ui-toggle-btn:not(.is-active) { background:#ffffff; }
            .ui-toggle-btn + .ui-toggle-btn { border-right:1px solid #e2e8f0; }
            .dark .ui-toggle-btn.is-active { background:#6366f1; }
            .dark .ui-toggle-btn:not(.is-active) { background:#1e293b; color:#cbd5e1; }
            .dark .ui-toggle-btn:not(.is-active):hover { color:#ffffff; }
            .dark .ui-toggle-btn + .ui-toggle-btn { border-right:1px solid #334155; }
        </style>
    @endpush

    @push('scripts')
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
            <script id="exhibitions-data" type="application/json">{!! json_encode([
                'data' => $exhibitions->getCollection()->map(fn($e) => [
                    'id' => $e->id,
                    'title' => $e->title,
                    'slug' => $e->slug,
                    'short' => $e->short_description,
                    'lat' => $e->latitude,
                    'lng' => $e->longitude,
                    'cover' => $e->cover_image_path ? asset_url($e->cover_image_path, 'imgs/pic/Book.png') : asset('imgs/pic/Book.png'),
                    'url' => route('exhibitions.show', $e),
                    'city' => $e->city,
                    'starts_at' => optional($e->starts_at)->toIso8601String(),
                ]),
                'meta' => [
                    'current_page' => $exhibitions->currentPage(),
                    'last_page' => $exhibitions->lastPage(),
                    'per_page' => $exhibitions->perPage(),
                    'total' => $exhibitions->total(),
                    'next_page_url' => $exhibitions->nextPageUrl(),
                    'prev_page_url' => $exhibitions->previousPageUrl(),
                ]
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
            <!-- exhibitionsPage definition moved to top for early availability -->
    @endpush
</x-layout>
