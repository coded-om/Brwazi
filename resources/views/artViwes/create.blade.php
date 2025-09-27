<x-layout>
    @if(auth()->check() && auth()->user()->isBanned())
        @push('styles')
            <style>
                /* Move notify down when banner is visible */
                .brw-notify-wrapper { top: calc(5.5rem + 3.25rem); }
                @media (max-width: 640px) { .brw-notify-wrapper { top: calc(4.5rem + 3.25rem); } }
            </style>
        @endpush
        <div class="fixed inset-x-0 z-50" style="top: 5.5rem;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="rounded-md bg-amber-50 border border-amber-200 text-amber-800 px-3 py-2 sm:px-4 sm:py-3 text-sm flex items-center justify-between gap-3 shadow">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <span>حسابك محظور، يرجى التواصل مع الدعم.</span>
                    </div>
                    <a href="{{ url('/support') }}" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-amber-600 hover:bg-amber-700 text-white">
                        <i class="fa-solid fa-life-ring text-xs"></i>
                        <span class="text-xs sm:text-sm">اتصل بالدعم</span>
                    </a>
                </div>
            </div>
        </div>
        <!-- Spacer to avoid overlap with fixed banner -->
        <div class="h-12 sm:h-14"></div>
    @endif
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14" x-data="artworkForm()"
        x-init="initOld(); isBanned = ($el.getAttribute('data-banned') === '1')"
        data-banned="{{ auth()->check() && auth()->user()->isBanned() ? '1' : '0' }}"
        data-old-dimensions="{{ old('dimensions') }}"
        data-old-tags='@json(old('tags', []))'
        data-old-sale="{{ old('sale_mode') }}"
        data-old-offers="{{ old('allow_offers') }}">
        <div class="mb-6 flex items-center text-sm text-indigo-600 gap-2">
            <a href="{{ route('user.dashboard') }}" class="hover:underline">الحساب الشخصي</a>
            <span>/</span>
            <span class="text-gray-500">رفع عمل فني جديد</span>
        </div>
        <h1 class="text-3xl font-bold text-center mb-2 text-indigo-900">رفع عمل فني جديد</h1>
        <p class="text-center text-gray-500 mb-10">شارك إبداعك، وارفعه كمسودة أو انشره فوراً.</p>
        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg text-sm">
                <ul class="list-disc pr-5 space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- اختيار نوع العمل -->
        <section class="mb-8  border-b">
            <h2 class="my-3">نوع العمل</h2>
            <div class="bg-gray-200 rounded-lg p-6">
                <div
                    class=" p-1 flex items-center gap-3 sm:justify-center sm:gap-6 overflow-x-auto snap-x snap-mandatory [-ms-overflow-style:'none'] [scrollbar-width:'none'] [&::-webkit-scrollbar]:hidden">
                    <button type="button" @click="selectType('art')"
                        :class="selectedType==='art' ? 'ring-2 ring-indigo-500' : 'hover:shadow'"
                        class="group w-36 sm:w-40 h-36 rounded-sm bg-white border flex flex-col items-center justify-center gap-3 transition shrink-0 snap-center">
                        <i class="fa-solid fa-paintbrush text-3xl text-[#130f41] group-hover:scale-105 transition"></i>
                        <div class="text-sm text-gray-700">فن (صورة/لوحة)</div>
                    </button>
                    <!-- 3D (غير متاح) -->
                    <button type="button" @click="selectType('3d')"
                        class="relative w-36 sm:w-40 h-36 rounded-sm bg-white border flex flex-col items-center justify-center gap-3 opacity-60 shrink-0 snap-center">
                        <i class="fa-solid fa-cubes text-3xl text-[#130f41]"></i>
                        <div class="text-sm text-gray-700">3D</div>
                        <span
                            class="absolute top-2 left-2 text-[11px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded">غير
                            متاح</span>
                    </button>
                    <!-- Vector (غير متاح) -->
                    <button type="button" @click="selectType('vector')"
                        class="relative w-36 sm:w-40 h-36 rounded-sm bg-white border flex flex-col items-center justify-center gap-3 opacity-60 shrink-0 snap-center">
                        <i class="fa-regular fa-image text-3xl text-[#130f41]"></i>
                        <div class="text-sm text-gray-700">Vector</div>
                        <span
                            class="absolute top-2 left-2 text-[11px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded">غير
                            متاح</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- section type of sell  -->
        <section x-show="selectedType==='art'" x-cloak>
            <div class="space-y-4">
                <label class="block text-sm font-medium text-gray-700">نوع العمل <i
                        class="fa-solid fa-layer-group text-[#130f41]"></i></label>
                <div @drop.prevent="handleDrop($event)" @dragover.prevent
                    class="border-2 border-dashed rounded-sm p-6 text-center bg-white flex flex-col items-center gap-4"
                    :class="{'border-indigo-400':dragging}" @dragenter="dragging=true" @dragleave="dragging=false">
                    <div class="text-gray-500" x-show="!images.length">
                        <i class="fas fa-cloud-upload-alt text-3xl mb-2 text-[#130f41]"></i>
                        <p class="text-sm">اسحب الصور هنا أو
                            @if(auth()->check() && auth()->user()->isBanned())
                                <span class="text-indigo-600 font-medium cursor-not-allowed opacity-60"
                                      @click="window.notify?.warning?.('تم تعطيل الرفع لحسابك. يرجى التواصل مع قسم الدعم.');">اختر ملفات</span>
                            @else
                                <span class="text-indigo-600 font-medium cursor-pointer"
                                      @click="$refs.fileInput.click()">اختر ملفات</span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-400 mt-1">مسموح حتى 6 صور - JPEG/PNG/WebP <span
                                class=" text-red-500">- حجم 4MB</span></p>
                    </div>
                    <input x-ref="fileInput" type="file" accept="image/*" multiple class="hidden" @change="uploadFiles($event)"
                        @if(auth()->check() && auth()->user()->isBanned()) disabled @endif>
                    <div class="grid grid-cols-3 gap-3 w-full" x-show="images.length">
                        <template x-for="(img,i) in images" :key="img.path">
                            <div class="relative group rounded-lg overflow-hidden border"
                                :class="{'ring-2 ring-indigo-500': primaryImage===img.path}">
                                <img :src="img.url" class="h-28 w-full object-cover">
                                <button type="button" @click="removeImage(img.path)"
                                    class="absolute top-1 left-1 bg-black/50 text-white rounded-full h-6 w-6 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition">×</button>
                                <button type="button" @click="primaryImage=img.path"
                                    class="absolute bottom-1 right-1 backdrop-blur px-2 py-0.5 rounded text-[10px] font-medium"
                                    :class="primaryImage===img.path ? 'bg-yellow-400 text-yellow-900' : 'bg-white/80'"
                                    x-text="primaryImage===img.path?'أساسي':'تعيين'"></button>
                            </div>
                        </template>
                        @if(auth()->check() && auth()->user()->isBanned())
                            <button type="button" x-show="images.length<6" disabled
                                class="opacity-60 cursor-not-allowed h-28 border-2 border-dashed rounded-lg flex items-center justify-center text-gray-400 transition">
                                <i class="fas fa-plus text-[#130f41]"></i>
                            </button>
                        @else
                            <button type="button" @click="$refs.fileInput.click()" x-show="images.length<6"
                                class="h-28 border-2 border-dashed rounded-lg flex items-center justify-center text-gray-400 hover:text-indigo-600 hover:border-indigo-400 transition">
                                <i class="fas fa-plus text-[#130f41]"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </section>
        <section class="lg:col-span-2">
            <div class="pt-2 w-full">
                <div class="flex items-center gap-2 text-indigo-900 mb-3">
                    <span
                        class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-fuchsia-700 text-white">
                        <i class="fa-solid fa-dollar-sign text-xs leading-none"></i>
                    </span>
                    <h3 class="font-semibold">خيارات البيع والسعر </h3>

                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 w-full">
                    <!-- عرض فقط -->
                    <button type="button" @click="selectSale('display')"
                        :class="saleMode==='display' ? 'ring-2 ring-indigo-500' : 'hover:shadow'"
                        class="group w-full h-full rounded-lg bg-white border p-4 text-right flex flex-col justify-between transition">
                        <div class="flex items-start justify-between gap-2">
                            <i class="fa-regular fa-eye text-2xl text-cyan-500"></i>
                            <i class="fa-solid fa-circle-info text-sm text-indigo-400"></i>
                        </div>
                        <div class="mt-4">
                            <div class="font-medium text-gray-800">عرض فقط</div>
                            <div class="text-xs text-gray-500 mt-1">عرض العمل في المعرض دون إمكانية البيع
                            </div>
                        </div>
                        <div class="mt-3">
                            <input type="checkbox" class="accent-indigo-600" :checked="saleMode==='display'" readonly>
                        </div>
                    </button>

                    <!-- سعر ثابت -->
                    <button type="button" @click="selectSale('fixed')"
                        :class="saleMode==='fixed' ? 'ring-2 ring-indigo-500' : 'hover:shadow'"
                        class="group w-full h-full rounded-lg bg-white border p-4 text-right flex flex-col justify-between transition">
                        <div class="flex items-start justify-between gap-2">
                            <i class="fa-solid fa-tag text-2xl text-yellow-500"></i>
                            <i class="fa-solid fa-circle-info text-sm text-indigo-400"></i>
                        </div>
                        <div class="mt-4">
                            <div class="font-medium text-gray-800">سعر ثابت</div>
                            <div class="text-xs text-gray-500 mt-1">بيع العمل بسعر محدد مسبقاً</div>
                        </div>
                        <div class="mt-3">
                            <input type="checkbox" class="accent-indigo-600" :checked="saleMode==='fixed'" readonly>
                        </div>
                    </button>

                    <!-- مزاد علني -->
                    <button type="button" @click="selectSale('auction')"
                        :class="saleMode==='auction' ? 'ring-2 ring-indigo-500' : 'hover:shadow'"
                        class="group w-full h-full rounded-lg bg-white border p-4 text-right flex flex-col justify-between transition">
                        <div class="flex items-start justify-between gap-2">
                            <i class="fa-solid fa-gavel text-2xl text-red-500"></i>
                            <i class="fa-solid fa-circle-info text-sm text-indigo-400"></i>
                        </div>
                        <div class="mt-4">
                            <div class="font-medium text-gray-800">مزاد علني</div>
                            <div class="text-xs text-gray-500 mt-1">طرح العمل في مزاد علني</div>
                        </div>
                        <div class="mt-3">
                            <input type="checkbox" class="accent-indigo-600" :checked="saleMode==='auction'" readonly>
                        </div>
                    </button>

                    <!-- قابل للتفاوض -->
                    <button type="button" @click="toggleOffers()"
                        :class="allowOffers ? 'ring-2 ring-indigo-500' : 'hover:shadow'"
                        class="group w-full h-full rounded-lg bg-white border p-4 text-right flex flex-col justify-between transition"
                        :disabled="saleMode!=='fixed'" :aria-disabled="saleMode!=='fixed'">
                        <div class="flex items-start justify-between gap-2">
                            <i class="fa-solid fa-handshake text-2xl text-violet-500"></i>
                            <i class="fa-solid fa-circle-info text-sm text-indigo-400"></i>
                        </div>
                        <div class="mt-4">
                            <div class="font-medium text-gray-800">قابل للتفاوض</div>
                            <div class="text-xs text-gray-500 mt-1"
                                x-text="saleMode==='fixed' ? 'السماح للمشترين بتقديم عروض' : 'مفعّل مع سعر ثابت فقط'">
                            </div>
                        </div>
                        <div class="mt-3">
                            <input type="checkbox" class="accent-indigo-600" :checked="allowOffers"
                                :disabled="saleMode!=='fixed'">
                        </div>
                    </button>
                </div>
            </div>
        </section>
        <section>
            <form id="artwork-main-form" action="{{ route('art.store') }}" method="POST" class="space-y-10"
                @submit="submitting=true">
                @csrf
                <input type="hidden" name="type" :value="selectedType">
                <!-- Hidden dynamic inputs: each image path as its own array element -->
                <div class="hidden">
                    <template x-for="img in images" :key="'hidden-'+img.path">
                        <input type="hidden" name="uploaded_images[]" :value="img.path">
                    </template>
                </div>
                <input type="hidden" name="primary_image" :value="primaryImage">
                <!-- Selling options hidden fields for backend -->
                <input type="hidden" name="sale_mode" :value="saleMode">
                <input type="hidden" name="allow_offers" :value="allowOffers ? 1 : 0">
                <!-- Fixed price details (edition type + copy subtypes) -->
                <input type="hidden" name="edition_type" :value="fixedEdition">
                <input type="hidden" name="copy_digital" :value="fixedCopyDigital ? 1 : 0">
                <input type="hidden" name="copy_printed" :value="fixedCopyPrinted ? 1 : 0">
                <!-- Auction details -->
                <input type="hidden" name="auction_start_price" :value="auctionStartPrice">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <!-- data section  -->
                    <section class="space-y-6 lg:col-span-2 p-4 my-5">
                        <!-- عنوان فرعي -->
                        <div class="flex items-center gap-2 text-indigo-900">
                            <span
                                class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-fuchsia-700 text-white">
                                <i class="fa-solid fa-exclamation text-xs leading-none"></i>
                            </span>
                            <h3 class="font-semibold">المعلومات الأساسية</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- عنوان العمل الفني -->
                            <div>
                                <label class="mb-1 text-sm font-medium text-gray-700 flex items-center gap-1">
                                    <i class="fa-solid fa-pen-nib text-[#130f41]"></i>
                                    <span>عنوان العمل الفني <span class="text-red-500">*</span></span>
                                </label>
                                <input x-model="form.title" name="title" type="text" autocomplete="off" maxlength="120"
                                    class="w-full rounded-sm border border-gray-300/70 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base placeholder:text-gray-300"
                                    placeholder="مثال: غروب الصحراء" required>
                                <p class="mt-1 text-xs text-gray-400">عنوان واضح وجذاب يساعد على اكتشاف عملك.</p>
                            </div>

                            <!-- نوع العمل الفني (يستخدم التصنيف الحالي) -->
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
                                <p class="mt-1 text-xs text-gray-400">اختر التصنيف الأقرب لطبيعة العمل.</p>
                            </div>

                            <!-- نوع الوسيط المستخدم (اختياري، للواجهة فقط حالياً) -->
                            <div>
                                <label class="mb-1 text-sm font-medium text-gray-700 flex items-center gap-1">
                                    <i class="fa-solid fa-palette text-[#130f41]"></i>
                                    <span>نوع الوسيط المستخدم</span>
                                </label>
                                <select name="medium"
                                    class="w-full rounded-sm border border-gray-300/70 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base">
                                    <option value="">اختر الوسيط</option>
                                    @isset($mediums)
                                        @foreach($mediums as $mSlug => $mName)
                                            <option value="{{ $mSlug }}">{{ $mName }}</option>
                                        @endforeach
                                    @endisset
                                </select>
                                <p class="mt-1 text-xs text-gray-400">اختياري: التقنية/الخامة المستخدمة.</p>
                            </div>

                            <!-- سنة الإنتاج -->
                            <div>
                                <label class="mb-1 text-sm font-medium text-gray-700 flex items-center gap-1">
                                    <i class="fa-regular fa-calendar-days text-[#130f41]"></i>
                                    <span>سنة الإنتاج</span>
                                </label>
                                <input x-model="form.year" name="year" type="number" min="1900" max="{{ date('Y') }}"
                                    step="1" inputmode="numeric" pattern="\\d*" autocomplete="off"
                                    class="w-full rounded-sm border border-gray-300/70 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base"
                                    placeholder="{{ date('Y') }}">
                            </div>

                            <!-- الأبعاد -->
                            <div>
                                <label class="mb-1 text-sm font-medium text-gray-700 flex items-center gap-1">
                                    <i class="fa-solid fa-ruler-combined text-[#130f41]"></i>
                                    <span>الأبعاد (سم)</span>
                                </label>
                                <div class="flex items-center gap-2">
                                    <input x-model="dimsW" @input="updateDimensions()" type="number" min="1" max="9999"
                                        step="1" inputmode="numeric" autocomplete="off"
                                        class="w-full rounded-sm border border-gray-300/70 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base"
                                        placeholder="العرض">
                                    <span class="text-gray-400 select-none">×</span>
                                    <input x-model="dimsH" @input="updateDimensions()" type="number" min="1" max="9999"
                                        step="1" inputmode="numeric" autocomplete="off"
                                        class="w-full rounded-sm border border-gray-300/70 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base"
                                        placeholder="الارتفاع">
                                </div>
                                <!-- keep backend compatibility -->
                                <input type="hidden" name="dimensions" :value="form.dimensions">
                                <p class="mt-1 text-xs text-gray-400">أدخل العرض والارتفاع بالسنتيمتر. سيتم تنسيقها كـ
                                    عرض×ارتفاع (مثال: 80×60).</p>
                            </div>

                            <!-- الوزن (اختياري، للواجهة فقط حالياً) -->
                            <div>
                                <label class="mb-1 text-sm font-medium text-gray-700 flex items-center gap-1">
                                    <i class="fa-solid fa-weight-hanging text-[#130f41]"></i>
                                    <span>الوزن (كغ)</span>
                                </label>
                                <input name="weight" type="number" step="0.1" min="0" inputmode="decimal"
                                    autocomplete="off"
                                    class="w-full rounded-sm border border-gray-300/70 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base"
                                    placeholder="2.5">
                                <p class="mt-1 text-xs text-gray-400">اختياري: الوزن التقريبي بالكيلوغرام.</p>
                            </div>

                            <!-- السعر: يظهر فقط مع سعر ثابت -->
                            <div x-show="saleMode==='fixed'">
                                <label class="mb-1 text-sm font-medium text-gray-700 flex items-center gap-1">
                                    <i class="fa-solid fa-tags text-[#130f41]"></i>
                                    <span>السعر (بالريال العُماني ر.ع)</span>
                                </label>
                                <input x-model="form.price" name="price" type="number" step="0.001" min="0"
                                    :disabled="saleMode!=='fixed'" :required="saleMode==='fixed'" inputmode="decimal"
                                    autocomplete="off"
                                    class="w-full rounded-sm border border-gray-300/70 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base"
                                    placeholder="150.000">
                                <p class="mt-1 text-xs text-gray-400">يظهر عند اختيار "سعر ثابت" فقط.</p>
                            </div>
                        </div>
                        <div>
                            <label class="mb-1 text-sm font-medium text-gray-700 flex items-center gap-1">
                                <i class="fa-solid fa-align-right text-[#130f41]"></i>
                                <span>وصف العمل الفني <span class="text-red-500">*</span></span>
                            </label>
                            <textarea x-model="form.description" name="description" rows="6" maxlength="1000"
                                class="w-full rounded-sm border border-gray-300/70 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base"
                                placeholder="اكتب وصفاً مفصلاً عن عملك، الفكرة وراءه، والتقنيات المستخدمة..."
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
                    <!-- type of sell  -->

                </div>
                <div class="flex flex-wrap gap-4 items-center">
                    <button name="action" value="draft" type="submit" :disabled="submitting"
                        @if(auth()->check() && auth()->user()->isBanned()) disabled @endif
                        :class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                        class="px-5 py-3 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium">حفظ
                        كمسودة</button>
                    <button name="action" value="publish" type="submit" :disabled="submitting"
                        @if(auth()->check() && auth()->user()->isBanned()) disabled @endif
                        :class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                        class="px-6 py-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-medium">نشر
                        الآن</button>
                    @if(auth()->check() && auth()->user()->isBanned())
                        <button type="button" disabled
                            :class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                            class="px-5 py-3 rounded-lg bg-white border text-gray-700 text-sm font-medium">معاينة</button>
                    @else
                        <button type="button" @click="openPreview()" :disabled="submitting"
                            :class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                            class="px-5 py-3 rounded-lg bg-white border hover:bg-gray-50 text-gray-700 text-sm font-medium">معاينة</button>
                    @endif
                    <a href="{{ route('user.dashboard') }}"
                        class="px-5 py-3 rounded-lg bg-white border text-sm text-gray-600 hover:bg-gray-50">إلغاء</a>
                </div>
            </form>
        </section>

        <!-- إشعار للأنواع غير المدعومة -->
        <section x-show="selectedType!=='art'" x-cloak>
            <div class="mt-8 p-6 bg-amber-50 border border-amber-200 text-amber-800 rounded-sm text-sm">
                هذا النوع غير متاح حالياً. يرجى اختيار "فن" للاستمرار برفع الصور.
            </div>
        </section>

        <!-- Preview Modal -->
        <div x-show="previewOpen" x-transition
            class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4">
            <div class="bg-white rounded-sm shadow-xl max-w-xl w-full overflow-hidden" @click.away="previewOpen=false">
                <div class="flex items-center justify-between px-5 py-3 border-b">
                    <h3 class="font-semibold text-gray-800 text-sm">معاينة العمل</h3>
                    <button @click="previewOpen=false" class="text-gray-400 hover:text-gray-600">×</button>
                </div>
                <div class="p-5"
                    x-html="previewHtml || '<p class=\'text-center text-gray-400 text-sm\'>...تحميل المعاينة</p>'">
                </div>
            </div>
        </div>

        <!-- Fixed Price Details Modal -->
        <div x-show="fixedModalOpen" x-transition class="fixed inset-0 z-40 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="closeFixedModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full overflow-hidden">
                <div class="px-6 py-5 border-b flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span
                            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-100 text-yellow-700">
                            <i class="fa-solid fa-tag text-sm"></i>
                        </span>
                        <div>
                            <div class="font-semibold text-gray-800">سعر ثابت</div>
                            <div class="text-xs text-gray-500">بيع العمل بسعر محدد مسبقاً</div>
                        </div>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600 text-xl" @click="closeFixedModal()">×</button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Original -->
                        <button type="button" @click="fixedEdition='original'"
                            :class="fixedEdition==='original' ? 'ring-2 ring-indigo-500 bg-indigo-50' : 'hover:shadow'"
                            class="w-full text-right bg-white border rounded-lg p-4 transition">
                            <div class="font-medium text-gray-800">النسخة الأصلية</div>
                            <div class="text-xs text-gray-500 mt-1">شراء الملكية الفكرية</div>
                            <div class="mt-3">
                                <input type="checkbox" class="accent-indigo-600" :checked="fixedEdition==='original'"
                                    readonly>
                            </div>
                        </button>
                        <!-- Copy -->
                        <button type="button" @click="fixedEdition='copy'"
                            :class="fixedEdition==='copy' ? 'ring-2 ring-indigo-500 bg-indigo-50' : 'hover:shadow'"
                            class="w-full text-right bg-white border rounded-lg p-4 transition">
                            <div class="font-medium text-gray-800">نسخة</div>
                            <div class="text-xs text-gray-500 mt-1">ليس العمل الأصلي بل نسخة طبق الأصل</div>
                            <div class="mt-3">
                                <input type="checkbox" class="accent-indigo-600" :checked="fixedEdition==='copy'"
                                    readonly>
                            </div>
                        </button>
                    </div>

                    <!-- Copy sub-options -->
                    <div x-show="fixedEdition==='copy'" x-cloak class="space-y-2 pr-1">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" class="accent-indigo-600" x-model="fixedCopyDigital">
                            <span class="font-medium text-gray-800">نسخة رقمية</span>
                            <span class="text-xs text-gray-500">ليس العمل الأصلي بل نسخة طبق الأصل</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" class="accent-indigo-600" x-model="fixedCopyPrinted">
                            <span class="font-medium text-gray-800">مطبوعة</span>
                            <span class="text-xs text-gray-500">ليس العمل الأصلي بل نسخة طبق الأصل</span>
                        </label>
                    </div>

                    <!-- Price input inside modal to focus user -->
                    <div class="pt-2">
                        <input x-model="form.price" type="number" step="0.001" min="0" inputmode="decimal"
                            class="w-full rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base"
                            placeholder="السعر بالريال العُماني">
                    </div>
                </div>
                <div class="px-6 py-4 border-t bg-gray-50 flex items-center justify-end gap-3">
                    <button type="button" class="px-4 py-2 rounded-md bg-white border hover:bg-gray-100 text-gray-700"
                        @click="closeFixedModal()">إلغاء</button>
                    <button type="button" class="px-5 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 text-white"
                        @click="applyFixedModal()">تطبيق</button>
                </div>
            </div>
        </div>

        <!-- Auction Details Modal -->
        <div x-show="auctionModalOpen" x-transition class="fixed inset-0 z-40 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" @click="closeAuctionModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full overflow-hidden">
                <div class="px-6 py-5 border-b flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span
                            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-rose-100 text-rose-700">
                            <i class="fa-solid fa-gavel text-sm"></i>
                        </span>
                        <div>
                            <div class="font-semibold text-gray-800">مزاد علني</div>
                        </div>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600 text-xl" @click="closeAuctionModal()">×</button>
                </div>
                <div class="px-6 py-5 space-y-4 text-right">
                    <ol class="list-decimal pr-5 space-y-2 text-sm text-gray-700">
                        <li>
                            <span class="font-semibold">نسخة أصلية:</span>
                            <ul class="list-disc pr-5 text-gray-600 mt-1">
                                <li>يجب أن تكون جميع الأعمال المعروضة نسخة أصلية وغير مكررة أو مطبوعة بشكل تجاري.</li>
                                <li>يلتزم الفنان/العارض بتقديم شهادة أصالة موقعة منه، أو موثقة من جهة مختصة إذا لزم
                                    الأمر.</li>
                            </ul>
                        </li>
                        <li>
                            <span class="font-semibold">موافقة الإدارة:</span>
                            <ul class="list-disc pr-5 text-gray-600 mt-1">
                                <li>يتم إدراج أي عمل في المزاد بعد مراجعة التقييم الفني من منصة بروازي.</li>
                                <li>تحتفظ اللجنة بحق رفض أي عمل لا يتوافق مع المعايير الفنية أو التقييم الثقافي للمشروع.
                                </li>
                            </ul>2
                        </li>2
                        <li>2
                            <span class="font-semibold">الترشح عبر المعرض الرسمي:2</span>
                            <ul class="list-disc pr-5 text-gray-600 mt-1">2
                                <li>يشترط أن يكون العمل قد تم عرضه سابقًا في معرض 2برواز أو تم ترشيحه من قبل لجنة
                                    الاختيار.</li>2
                                <li>الأولوية للأعمال التي حظيت بتقييم عالٍ أو اهتمام ملحوظ من الجمهور أثناء العرض.</li>
                            </ul>
                        <z/li>
                    </ol>

                    <div class="pt-2">
                        <input x-model="auctionStartPrice" type="number" step="0.001" min="0" inputmode="decimal"
                            class="w-full rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 bg-white px-3 py-2.5 text-sm sm:text-base"
                            placeholder="سعر البداية بالريال العُماني">
                    </div>
                </div>
                <div class="px-6 py-4 border-t bg-gray-50 flex items-center justify-end gap-3">
                    <button type="button" class="px-4 py-2 rounded-md bg-white border hover:bg-gray-100 text-gray-700"
                        @click="closeAuctionModal()">إلغاء</button>
                    <button type="button" class="px-5 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 text-white"
                        @click="applyAuctionModal()">تطبيق</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function artworkForm() {
                return {
                    isBanned: false,
                    selectedType: 'art',
                    form: { title: '', category: '', year: '', dimensions: '', price: '', description: '' },
                    // split dimensions for better UX
                    dimsW: '',
                    dimsH: '',
                    // selling options
                    saleMode: 'display', // 'display' | 'fixed' | 'auction'
                    allowOffers: false,
                    // fixed price modal state
                    fixedModalOpen: false,
                    fixedEdition: 'original', // 'original' | 'copy'
                    fixedCopyDigital: false,
                    fixedCopyPrinted: false,
                    // auction modal state
                    auctionModalOpen: false,
                    auctionStartPrice: '',
                    images: [],
                    primaryImage: null,
                    dragging: false,
                    previewOpen: false,
                    previewHtml: '',
                    tags: [],
                    tagDraft: '',
                    selectType(t) { this.selectedType = t; },
                    warnBanned() {
                        try { window.notify?.warning?.('تم تعطيل الرفع لحسابك. يرجى التواصل مع قسم الدعم.'); } catch (e) {}
                        if (!window.notify?.warning) alert('تم تعطيل الرفع لحسابك. يرجى التواصل مع قسم الدعم.');
                    },
                    async uploadFiles(e) {
                        const files = Array.from(e.target.files || e.dataTransfer.files);
                        const MAX_MB = (window.CONFIG && window.CONFIG.maxUploadMb) ? window.CONFIG.maxUploadMb : 8; // backup default
                        for (const file of files) {
                            if (this.images.length >= 6) break;
                            // فحص نوع الملف والحجم قبل الرفع
                            if (!file.type.startsWith('image/')) { continue; }
                            if (file.size > MAX_MB * 1024 * 1024) { continue; }
                            const fd = new FormData();
                            fd.append('image', file);
                            fd.append('_token', '{{ csrf_token() }}');
                            const res = await fetch("{{ route('art.upload-image') }}", { method: 'POST', body: fd });
                            const data = await res.json();
                            if (data.success) {
                                this.images.push({ path: data.path, url: data.url });
                                if (!this.primaryImage) this.primaryImage = data.path;
                            }
                        }
                        if (e.target && 'value' in e.target) { e.target.value = ''; }
                    },
                    handleDrop(ev) {
                        this.dragging = false; this.uploadFiles(ev);
                    },
                    removeImage(path) {
                        this.images = this.images.filter(i => i.path !== path);
                        if (this.primaryImage === path) {
                            this.primaryImage = this.images[0]?.path || null;
                        }
                    },
                    async openPreview() {
                        if (!this.images.length) return alert('أضف صورة أولاً');
                        this.previewOpen = true; this.previewHtml = '';
                        const payload = new FormData();
                        // ensure composed dimensions kept in sync
                        this.updateDimensions();
                        Object.entries(this.form).forEach(([k, v]) => payload.append(k, v));
                        payload.append('primary_image', this.primaryImage || '');
                        this.images.forEach(i => payload.append('images[]', i.url));
                        this.tags.forEach(t => payload.append('tags[]', t));
                        payload.append('_token', '{{ csrf_token() }}');
                        const res = await fetch("{{ route('art.preview') }}", { method: 'POST', body: payload });
                        const data = await res.json();
                        this.previewHtml = data.html;
                    },
                    updateDimensions() {
                        const w = (this.dimsW || '').toString().trim();
                        const h = (this.dimsH || '').toString().trim();
                        if (w && h) {
                            this.form.dimensions = `${w}x${h}`;
                        } else {
                            this.form.dimensions = '';
                        }
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
                    removeTag: function(i) { this.tags.splice(i, 1); },
                    focusTagInput() { this.$refs.tagInput.focus(); },
                    addTagExternal(t) { this.tagDraft = t; this.commitTag(); },
                    initOld() {
                        // populate dimensions from old input if validation failed previously
                        const dims = this.$el.getAttribute('data-old-dimensions') || '';
                        if (typeof dims === 'string' && /^\d{1,4}x\d{1,4}$/.test(dims)) {
                            const [w, h] = dims.split('x');
                            this.dimsW = w; this.dimsH = h; this.form.dimensions = dims;
                        }
                        // restore tags (custom user tags not from DB) after validation errors
                        const oldTagsRaw = this.$el.getAttribute('data-old-tags') || '[]';
                        let oldTags = [];
                        try { oldTags = JSON.parse(oldTagsRaw); } catch (e) { oldTags = []; }
                        if (Array.isArray(oldTags) && oldTags.length) {
                            this.tags = oldTags
                                .map(t => String(t)
                                    .replace(/^#+/, '')
                                    .trim()
                                    .toLowerCase()
                                    .replace(/\s+/g, '')
                                )
                                .filter((t, i, arr) => t.length >= 2 && arr.indexOf(t) === i)
                                .slice(0, 10);
                        }
                        // restore sale fields
                        const oldSale = this.$el.getAttribute('data-old-sale') || '';
                        const oldOffers = this.$el.getAttribute('data-old-offers') || '';
                        if (oldSale && ['display', 'fixed', 'auction'].includes(oldSale)) this.saleMode = oldSale;
                        this.allowOffers = oldOffers == 1 || oldOffers === '1' || oldOffers === true;
                    },
                    selectSale(mode) {
                        this.saleMode = mode;
                        if (mode === 'fixed') {
                            // open details modal on first switch to fixed
                            this.fixedModalOpen = true;
                            // clear auction fields when switching to fixed
                            this.auctionStartPrice = '';
                        } else if (mode === 'auction') {
                            this.auctionModalOpen = true;
                            // clear fixed price and offers when switching to auction
                            this.form.price = '';
                            this.allowOffers = false;
                        } else {
                            // display only: clear fixed price and auction fields, disable offers
                            this.allowOffers = false;
                            this.form.price = '';
                            this.auctionStartPrice = '';
                        }
                    },
                    toggleOffers() {
                        if (this.saleMode === 'fixed') this.allowOffers = !this.allowOffers;
                    },
                    closeFixedModal() {
                        this.fixedModalOpen = false;
                    },
                    applyFixedModal() {
                        // basic guard: ensure edition chosen; defaults already set
                        this.fixedModalOpen = false;
                    },
                    closeAuctionModal() {
                        this.auctionModalOpen = false;
                    },
                    applyAuctionModal() {
                        this.auctionModalOpen = false;
                    }
                }
            }
        </script>
    @endpush
</x-layout>
