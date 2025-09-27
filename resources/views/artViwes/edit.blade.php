<x-layout>

    @php
        $existingImages = ($artwork->images ?? collect())
            ->map(fn($i) => ['path' => $i->path, 'url' => asset('storage/' . $i->path)])
            ->values();
        $initial = [
            'saleMode' => $artwork->sale_mode,
            'allowOffers' => (bool) $artwork->allow_offers,
            'fixedEdition' => $artwork->edition_type ?? 'original',
            'fixedCopyDigital' => (bool) $artwork->copy_digital,
            'fixedCopyPrinted' => (bool) $artwork->copy_printed,
            'auctionStartPrice' => $artwork->auction_start_price,
            'existingImages' => $existingImages,
            'primaryImage' => optional($artwork->primary_image)->path,
            'tags' => ($artwork->tags ?? collect())->pluck('name')->map(fn($t) => strtolower($t))->values(),
            'form' => [
                'title' => $artwork->title,
                'category' => $artwork->category,
                'year' => $artwork->year,
                'dimensions' => $artwork->dimensions,
                'price' => $artwork->price,
                'description' => $artwork->description,
                'medium' => $artwork->medium,
                'weight' => $artwork->weight,
            ],
        ];
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14" x-data="artworkEditForm()"
        x-init='initFromServer($el.dataset.initial)' data-initial='@json($initial)'>
        <div class="mb-6 flex items-center text-sm text-indigo-600 gap-2">
            <a href="{{ route('user.dashboard') }}" class="hover:underline">الحساب الشخصي</a>
            <span>/</span>
            <span class="text-gray-500">تعديل العمل</span>
        </div>
        <h1 class="text-3xl font-bold text-center mb-2 text-indigo-900">تعديل العمل الفني</h1>
        <p class="text-center text-gray-500 mb-10">حدّث التفاصيل والصور ثم احفظ التغييرات.</p>
        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg text-sm">
                <ul class="list-disc pr-5 space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- صور العمل -->
        <section class="mb-8  border-b">
            <h2 class="my-3">صور العمل</h2>
            <div class="bg-gray-200 rounded-lg p-6">
                <div class="p-1">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 w-full">
                        <template x-for="(img,i) in existingImages" :key="img.path">
                            <div class="relative group rounded-lg overflow-hidden border"
                                :class="{'ring-2 ring-indigo-500': primaryImage===img.path}">
                                <img :src="img.url" class="h-36 w-full object-cover">
                                <button type="button" @click="primaryImage=img.path"
                                    class="absolute bottom-1 right-1 backdrop-blur px-2 py-0.5 rounded text-[10px] font-medium"
                                    :class="primaryImage===img.path ? 'bg-yellow-400 text-yellow-900' : 'bg-white/80'"
                                    x-text="primaryImage===img.path?'أساسي':'تعيين'"></button>
                            </div>
                        </template>
                        <!-- new uploads -->
                        <template x-for="(img,i) in newImages" :key="'new-'+img.path">
                            <div class="relative group rounded-lg overflow-hidden border"
                                :class="{'ring-2 ring-indigo-500': primaryImage===img.path}">
                                <img :src="img.url" class="h-36 w-full object-cover">
                                <button type="button" @click="primaryImage=img.path"
                                    class="absolute bottom-1 right-1 backdrop-blur px-2 py-0.5 rounded text-[10px] font-medium"
                                    :class="primaryImage===img.path ? 'bg-yellow-400 text-yellow-900' : 'bg-white/80'"
                                    x-text="primaryImage===img.path?'أساسي':'تعيين'"></button>
                            </div>
                        </template>
                        <button type="button" @click="$refs.fileInput.click()"
                            class="h-36 border-2 border-dashed rounded-lg flex items-center justify-center text-gray-400 hover:text-indigo-600 hover:border-indigo-400 transition">
                            <i class="fas fa-plus text-[#130f41]"></i>
                        </button>
                    </div>
                    <input x-ref="fileInput" type="file" accept="image/*" multiple class="hidden"
                        @change="uploadFiles($event)">
                </div>
            </div>
        </section>

        <section>
            <form id="artwork-edit-form" action="{{ route('art.update', $artwork) }}" method="POST" class="space-y-10">
                @csrf
                @method('PUT')
                <!-- Hidden dynamic inputs for any newly uploaded images -->
                <div class="hidden">
                    <template x-for="img in newImages" :key="'hidden-'+img.path">
                        <input type="hidden" name="uploaded_images[]" :value="img.path">
                    </template>
                </div>
                <input type="hidden" name="primary_image" :value="primaryImage">
                <input type="hidden" name="sale_mode" :value="saleMode">
                <input type="hidden" name="allow_offers" :value="allowOffers ? 1 : 0">
                <input type="hidden" name="edition_type" :value="fixedEdition">
                <input type="hidden" name="copy_digital" :value="fixedCopyDigital ? 1 : 0">
                <input type="hidden" name="copy_printed" :value="fixedCopyPrinted ? 1 : 0">
                <input type="hidden" name="auction_start_price" :value="auctionStartPrice">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <section class="space-y-6 lg:col-span-2 p-4 my-5">
                        <div class="flex items-center gap-2 text-indigo-900">
                            <span
                                class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-fuchsia-700 text-white">
                                <i class="fa-solid fa-exclamation text-xs leading-none"></i>
                            </span>
                            <h3 class="font-semibold">المعلومات الأساسية</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div>
                                <label class="mb-1 text-sm font-medium text-gray-700 flex items-center gap-1">
                                    <i class="fa-solid fa-pen-nib text-[#130f41]"></i>
                                    <span>عنوان العمل الفني <span class="text-red-500">*</span></span>
                                </label>
                                <input x-model="form.title" name="title" type="text" maxlength="120"
                                    class="w-full rounded-sm border border-gray-300/70 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base"
                                    required>
                            </div>
                            <div>
                                <label class="mb-1 text-sm font-medium text-gray-700 flex items-center gap-1">
                                    <i class="fa-solid fa-layer-group text-[#130f41]"></i>
                                    <span>نوع العمل الفني <span class="text-red-500">*</span></span>
                                </label>
                                <select x-model="form.category" name="category"
                                    class="w-full rounded-sm border border-gray-300/70 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base"
                                    required>
                                    <option value="">اختر نوع العمل</option>
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 text-sm font-medium text-gray-700 flex items-center gap-1">
                                    <i class="fa-solid fa-palette text-[#130f41]"></i>
                                    <span>نوع الوسيط المستخدم</span>
                                </label>
                                <select name="medium" x-model="form.medium"
                                    class="w-full rounded-sm border border-gray-300/70 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base">
                                    <option value="">اختر الوسيط</option>
                                    @isset($mediums)
                                        @foreach($mediums as $mSlug => $mName)
                                            <option value="{{ $mSlug }}">{{ $mName }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 text-sm font-medium text-gray-700 flex items-center gap-1">
                                    <i class="fa-regular fa-calendar-days text-[#130f41]"></i>
                                    <span>سنة الإنتاج</span>
                                </label>
                                <input x-model="form.year" name="year" type="number" min="1900" max="{{ date('Y') }}"
                                    step="1"
                                    class="w-full rounded-sm border border-gray-300/70 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base">
                            </div>
                            <div>
                                <label class="mb-1 text-sm font-medium text-gray-700 flex items-center gap-1">
                                    <i class="fa-solid fa-ruler-combined text-[#130f41]"></i>
                                    <span>الأبعاد (سم)</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <input x-model="dimsW" @input="updateDimensions()" type="number" min="1" max="9999"
                                        step="1"
                                        class="w-full rounded-sm border border-gray-300/70 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base"
                                        placeholder="العرض">
                                    <span class="text-gray-400 select-none">×</span>
                                    <input x-model="dimsH" @input="updateDimensions()" type="number" min="1" max="9999"
                                        step="1"
                                        class="w-full rounded-sm border border-gray-300/70 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base"
                                        placeholder="الارتفاع">
                                </div>
                                <input type="hidden" name="dimensions" :value="form.dimensions">
                            </div>
                            <div>
                                <label class="mb-1 text-sm font-medium text-gray-700 flex items-center gap-1">
                                    <i class="fa-solid fa-weight-hanging text-[#130f41]"></i>
                                    <span>الوزن (كغ)</span>
                                </label>
                                <input name="weight" x-model="form.weight" type="number" step="0.1" min="0"
                                    class="w-full rounded-sm border border-gray-300/70 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base"
                                    placeholder="2.5">
                            </div>
                            <div x-show="saleMode==='fixed'">
                                <label class="mb-1 text-sm font-medium text-gray-700 flex items-center gap-1">
                                    <i class="fa-solid fa-tags text-[#130f41]"></i>
                                    <span>السعر (بالريال العُماني ر.ع)</span>
                                </label>
                                <input x-model="form.price" name="price" type="number" step="0.001" min="0"
                                    :disabled="saleMode!=='fixed'" :required="saleMode==='fixed'" inputmode="decimal"
                                    class="w-full rounded-sm border border-gray-300/70 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base"
                                    placeholder="150.000">
                            </div>
                        </div>
                        <div>
                            <label class="mb-1 text-sm font-medium text-gray-700 flex items-center gap-1">
                                <i class="fa-solid fa-align-right text-[#130f41]"></i>
                                <span>وصف العمل الفني <span class="text-red-500">*</span></span>
                            </label>
                            <textarea x-model="form.description" name="description" rows="6" maxlength="1000"
                                class="w-full rounded-sm border border-gray-300/70 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base"
                                required></textarea>
                            <div class="mt-1 text-xs text-gray-400" x-text="(form.description?.length||0) + '/1000'">
                            </div>
                        </div>
                        <div>
                            <label class="mb-1 text-sm font-medium text-gray-700 flex items-center gap-1">
                                <i class="fa-solid fa-hashtag text-[#130f41]"></i>
                                <span>التصنيفات الفرعية</span>
                                <span class="text-xs text-gray-400">حتى 10</span>
                            </label>
                            <div class="w-full rounded-lg border border-gray-300 focus-within:ring-2 focus-within:ring-indigo-500 bg-white p-2 flex flex-wrap gap-2 text-sm"
                                @click="focusTagInput()">
                                <template x-for="(tag,i) in tags" :key="tag">
                                    <span
                                        class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded flex items-center gap-1">
                                        <span x-text="'#'+tag"></span>
                                        <button type="button" class="text-indigo-400 hover:text-red-500"
                                            @click="removeTag(i)">×</button>
                                    </span>
                                </template>
                                <input x-ref="tagInput" x-model="tagDraft" @keydown.enter.prevent="commitTag()"
                                    @keydown.space.prevent="commitTag()"
                                    @keydown=" if($event.key===','||$event.key==='،'||$event.key==='Tab'){ $event.preventDefault(); commitTag(); }"
                                    @blur="commitTag()" placeholder="أضف التصنيفات الفرعية..."
                                    class="flex-1 min-w-[90px] border-none focus:ring-0 text-sm placeholder:text-gray-300">
                            </div>
                            <template x-for="t in tags" :key="'hidden-tag-'+t">
                                <input type="hidden" name="tags[]" :value="t">
                            </template>
                            @isset($popularTags)
                                @if(isset($popularTags) && $popularTags instanceof \Illuminate\Support\Collection && $popularTags->count())
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                        <span class="text-gray-500">الأكثر شيوعاً:</span>
                                        @foreach($popularTags as $pt)
                                            <button type="button" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded"
                                                @click.prevent="addTagExternal('{{ $pt }}')">#{{ $pt }}</button>
                                        @endforeach
                                    </div>
                                @endif
                            @endisset
                        </div>
                    </section>
                </div>
                <div class="flex flex-wrap gap-4 items-center">
                    <button name="action" value="draft" type="submit"
                        class="px-5 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium">حفظ
                        مسودة</button>
                    <button name="action" value="publish" type="submit"
                        class="px-6 py-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium">حفظ
                        ونشر</button>
                    <a href="{{ route('art.show', $artwork) }}"
                        class="px-5 py-3 rounded-lg bg-white border text-sm text-gray-600 hover:bg-gray-50">إلغاء</a>
                </div>
            </form>
        </section>
    </div>

    @push('scripts')
        <script>
            function artworkEditForm() {
                return {
                    form: { title: '', category: '', year: '', dimensions: '', price: '', description: '', medium: '', weight: '' },
                    dimsW: '',
                    dimsH: '',
                    saleMode: 'display',
                    allowOffers: false,
                    fixedEdition: 'original',
                    fixedCopyDigital: false,
                    fixedCopyPrinted: false,
                    auctionStartPrice: '',
                    existingImages: [],
                    newImages: [],
                    primaryImage: null,
                    tags: [],
                    tagDraft: '',
                    async uploadFiles(e) {
                        const files = Array.from(e.target.files || e.dataTransfer?.files || []);
                        for (const file of files) {
                            const fd = new FormData();
                            fd.append('image', file);
                            fd.append('_token', '{{ csrf_token() }}');
                            const res = await fetch("{{ route('art.upload-image') }}", { method: 'POST', body: fd });
                            const data = await res.json();
                            if (data.success) {
                                this.newImages.push({ path: data.path, url: data.url });
                            }
                        }
                        e.target.value = '';
                    },
                    updateDimensions() {
                        const w = (this.dimsW || '').toString().trim();
                        const h = (this.dimsH || '').toString().trim();
                        this.form.dimensions = (w && h) ? `${w}x${h}` : '';
                    },
                    commitTag() {
                        if (!this.tagDraft) return;
                        let clean = this.tagDraft.replace(/^#+/, '').trim().toLowerCase();
                        clean = clean.replace(/\s+/g, '');
                        if (clean.length < 2) { this.tagDraft = ''; return; }
                        if (this.tags.includes(clean)) { this.tagDraft = ''; return; }
                        if (this.tags.length >= 10) { this.tagDraft = ''; return; }
                        this.tags.push(clean.substring(0, 20));
                        this.tagDraft = '';
                    },
                    removeTag(i) { this.tags.splice(i, 1); },
                    focusTagInput() { this.$refs.tagInput.focus(); },
                    addTagExternal(t) { this.tagDraft = t; this.commitTag(); },
                    initFromServer(initialJson) {
                        try {
                            const data = JSON.parse(initialJson || '{}');
                            this.saleMode = data.saleMode || 'display';
                            this.allowOffers = !!data.allowOffers;
                            this.fixedEdition = data.fixedEdition || 'original';
                            this.fixedCopyDigital = !!data.fixedCopyDigital;
                            this.fixedCopyPrinted = !!data.fixedCopyPrinted;
                            this.auctionStartPrice = data.auctionStartPrice ?? '';
                            this.existingImages = Array.isArray(data.existingImages) ? data.existingImages : [];
                            this.primaryImage = data.primaryImage || (this.existingImages[0]?.path || null);
                            this.tags = Array.isArray(data.tags) ? data.tags : [];
                            const f = data.form || {};
                            this.form.title = f.title || '';
                            this.form.category = f.category || '';
                            this.form.year = f.year || '';
                            this.form.medium = f.medium || '';
                            this.form.weight = f.weight || '';
                            this.form.price = f.price || '';
                            this.form.description = f.description || '';
                            const dims = f.dimensions || '';
                            if (dims && /^\d{1,4}x\d{1,4}$/.test(dims)) { const [w, h] = dims.split('x'); this.dimsW = w; this.dimsH = h; this.form.dimensions = dims; }
                        } catch (e) { console.error('Failed to init form', e); }
                    }
                }
            }
        </script>
    @endpush
</x-layout>