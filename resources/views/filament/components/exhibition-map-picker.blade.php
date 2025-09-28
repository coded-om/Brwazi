<div x-data="exhibitionMapPicker()" x-init="init()" class="space-y-3">
    <div class="flex flex-wrap gap-3 items-center text-xs text-slate-600">
        <span class="font-medium">اضغط على الخريطة لتعبئة خطوط الطول و العرض أو استخدم زر موقعي.</span>
        <button type="button" @click="locate()"
            class="px-3 py-1.5 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 text-xs">موقعي الحالي</button>
        <button type="button" @click="copy()"
            class="px-3 py-1.5 rounded-md bg-slate-200 text-slate-700 hover:bg-slate-300 text-xs">نسخ
            الإحداثيات</button>
        <span x-show="copied" x-transition class="text-emerald-600 font-semibold">تم النسخ ✓</span>
    </div>
    <div id="exhibition-map-picker" class="w-full h-80 rounded-xl overflow-hidden ring-1 ring-slate-200"></div>
    <template x-if="lat && lng">
        <div class="text-xs text-slate-500">الإحداثيات الحالية: <span class="font-mono"
                x-text="lat.toFixed(6) + ', ' + lng.toFixed(6)"></span></div>
    </template>

    @once
        @push('styles')
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
                integrity="sha256-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44c=" crossorigin="" />
        @endpush
        @push('scripts')
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                integrity="sha256-VuZ8HsrwYJcCfevkpzxxEe7dBDEu4SU1mI7qp8v+Pqw=" crossorigin=""></script>
            <script>
                function exhibitionMapPicker() {
                    return {
                        map: null,
                        marker: null,
                        lat: null,
                        lng: null,
                        copied: false,
                        init() {
                            // read existing inputs (Filament names are data.latitude / data.longitude)
                            const latInput = document.querySelector('input[name="data.latitude"]');
                            const lngInput = document.querySelector('input[name="data.longitude"]');
                            const parseNum = v => { const n = parseFloat(v); return isNaN(n) ? null : n; };
                            this.lat = parseNum(latInput?.value);
                            this.lng = parseNum(lngInput?.value);
                            const center = (this.lat && this.lng) ? [this.lat, this.lng] : [21.4735, 55.9754]; // Oman approx
                            this.map = L.map('exhibition-map-picker').setView(center, this.lat ? 13 : 6);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(this.map);
                            if (this.lat && this.lng) {
                                this.marker = L.marker([this.lat, this.lng]).addTo(this.map);
                            }
                            this.map.on('click', (e) => {
                                this.setPoint(e.latlng.lat, e.latlng.lng, true);
                            });
                        },
                        setPoint(lat, lng, updateInputs = false) {
                            this.lat = lat; this.lng = lng; this.copied = false;
                            if (this.marker) { this.marker.setLatLng([lat, lng]); } else { this.marker = L.marker([lat, lng]).addTo(this.map); }
                            if (updateInputs) {
                                const latInput = document.querySelector('input[name="data.latitude"]');
                                const lngInput = document.querySelector('input[name="data.longitude"]');
                                if (latInput) { latInput.value = lat.toFixed(6); latInput.dispatchEvent(new Event('input')); }
                                if (lngInput) { lngInput.value = lng.toFixed(6); lngInput.dispatchEvent(new Event('input')); }
                            }
                        },
                        locate() {
                            if (!navigator.geolocation) { return; }
                            navigator.geolocation.getCurrentPosition(pos => {
                                const { latitude, longitude } = pos.coords;
                                this.map.setView([latitude, longitude], 14);
                                this.setPoint(latitude, longitude, true);
                            });
                        },
                        copy() {
                            if (this.lat === null || this.lng === null) return;
                            navigator.clipboard.writeText(this.lat.toFixed(6) + ',' + this.lng.toFixed(6)).then(() => {
                                this.copied = true;
                                setTimeout(() => this.copied = false, 2500);
                            });
                        }
                    }
                }
            </script>
        @endpush
    @endonce
</div>