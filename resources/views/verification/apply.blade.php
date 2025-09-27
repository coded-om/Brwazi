<x-layout>
    <div class="max-w-5xl mx-auto px-4 py-8" dir="rtl">
        <div class="bg-white/90 backdrop-blur border border-gray-200 rounded-2xl shadow-sm p-6 sm:p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">التسجيل للعضوية</h1>
                <p class="text-sm text-gray-500 mt-1">يرجى اتباع المراحل التالية لإكمال طلب العضوية.</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-red-700 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Steps bar -->
            <div id="stepsBar" class="flex gap-3 overflow-x-auto pb-2 mb-6">
                <div class="step-card min-w-[220px] flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 border-purple-500 bg-purple-50" data-step="0">
                    <div class="index w-9 h-9 rounded-full grid place-items-center text-sm font-extrabold border border-purple-200 bg-purple-50 text-purple-700">1</div>
                    <div>
                        <div class="title text-sm font-bold text-gray-900">اختيار النوع</div>
                        <div class="sub text-xs text-gray-500">استمارة العضوية</div>
                    </div>
                </div>
                <div class="step-card min-w-[220px] flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3" data-step="1">
                    <div class="index w-9 h-9 rounded-full grid place-items-center text-sm font-extrabold border border-purple-200 bg-purple-50 text-purple-700">2</div>
                    <div>
                        <div class="title text-sm font-bold text-gray-900">الشروط والمتطلبات</div>
                        <div class="sub text-xs text-gray-500">حسب النوع المختار</div>
                    </div>
                </div>
                <div class="step-card min-w-[220px] flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3" data-step="2">
                    <div class="index w-9 h-9 rounded-full grid place-items-center text-sm font-extrabold border border-purple-200 bg-purple-50 text-purple-700">3</div>
                    <div>
                        <div class="title text-sm font-bold text-gray-900">البيانات الشخصية</div>
                        <div class="sub text-xs text-gray-500">معلومات المتقدم</div>
                    </div>
                </div>
                <div class="step-card min-w-[220px] flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3" data-step="3">
                    <div class="index w-9 h-9 rounded-full grid place-items-center text-sm font-extrabold border border-purple-200 bg-purple-50 text-purple-700">4</div>
                    <div>
                        <div class="title text-sm font-bold text-gray-900">الملفات المطلوبة</div>
                        <div class="sub text-xs text-gray-500">تحميل المرفقات</div>
                    </div>
                </div>
                <div class="step-card min-w-[220px] flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3" data-step="4">
                    <div class="index w-9 h-9 rounded-full grid place-items-center text-sm font-extrabold border border-purple-200 bg-purple-50 text-purple-700">5</div>
                    <div>
                        <div class="title text-sm font-bold text-gray-900">مراجعة وإرسال</div>
                        <div class="sub text-xs text-gray-500">تأكيد الطلب</div>
                    </div>
                </div>
            </div>

            <div id="verify-config"
                @foreach($forms as $type => $form)
                    data-{{ $type }}-min="{{ (int) ($form->works_min ?? 1) }}"
                    data-{{ $type }}-max="{{ (int) ($form->works_max ?? 1) }}"
                @endforeach
                data-post-max-mb="{{ (int) ($uploadMaxMb ?? 40) }}" class="hidden"></div>

            <form id="membershipForm" action="{{ route('verification.store') }}" method="post"
                enctype="multipart/form-data" novalidate>
                @csrf

                <!-- Step 0: نوع الاستمارة -->
                <section class="step-section block" data-step="0">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">١) اختيار النوع — إستمارات تسجيل العضويات</h3>
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <label class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-4 py-2 bg-white hover:bg-purple-50 cursor-pointer">
                                <input type="radio" name="formType" value="visual" class="text-purple-600 focus:ring-purple-500" required>
                                <span class="text-sm font-medium">الفنون التشكيلية والرقمية</span>
                            </label>
                            <label class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-4 py-2 bg-white hover:bg-purple-50 cursor-pointer">
                                <input type="radio" name="formType" value="photo" class="text-purple-600 focus:ring-purple-500">
                                <span class="text-sm font-medium">التصوير الضوئي</span>
                            </label>
                        </div>
                        <p id="formTypeError" class="hidden text-sm text-red-600">الرجاء اختيار نوع الاستمارة.</p>
                    </div>
                </section>

                <!-- Step 1: الشروط -->
                <section class="step-section hidden" data-step="1">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">٢) الشروط والمتطلبات</h3>
                    <div id="terms-visual" class="rounded-xl border border-dashed border-gray-200 bg-white p-4 text-sm leading-7 space-y-3">
                        <div>
                            <strong>الشروط:</strong>
                            <ul class="list-disc pr-5 mt-1 space-y-1 text-gray-700">
                                <li>أن يكون المتقدم عماني الجنسية أو مقيماً في سلطنة عمان.</li>
                                <li>أن يكون لديه أعمال فنية أصلية من إنتاجه.</li>
                                <li>الالتزام بالمعايير الأخلاقية والفنية.</li>
                            </ul>
                        </div>
                        <div>
                            <strong>المرفقات المطلوبة:</strong>
                            <ul class="list-disc pr-5 mt-1 space-y-1 text-gray-700">
                                <li>صورة من البطاقة الشخصية أو جواز السفر.</li>
                                <li>صورة شخصية حديثة.</li>
                                <li>نماذج من الأعمال الفنية (صور).</li>
                            </ul>
                        </div>
                        <label class="flex items-start gap-2 text-sm text-gray-700">
                            <input type="checkbox" id="agree-visual" class="mt-1 text-purple-600 focus:ring-purple-500">
                            <span>أوافق على جميع الشروط المذكورة أعلاه</span>
                        </label>
                        <p id="agree-visual-error" class="hidden text-sm text-red-600">الرجاء الموافقة على الشروط للمتابعة.</p>
                    </div>
                    <div id="terms-photo" class="rounded-xl border border-dashed border-gray-200 bg-white p-4 text-sm leading-7 space-y-3 hidden">
                        <div>
                            <strong>الشروط:</strong>
                            <ul class="list-disc pr-5 mt-1 space-y-1 text-gray-700">
                                <li>أن يكون المتقدم عماني الجنسية أو مقيماً في سلطنة عمان.</li>
                                <li>أن يكون لديه أعمال تصوير ضوئي أصلية من إنتاجه.</li>
                                <li>الالتزام بالمعايير الأخلاقية والفنية.</li>
                            </ul>
                        </div>
                        <div>
                            <strong>المرفقات المطلوبة:</strong>
                            <ul class="list-disc pr-5 mt-1 space-y-1 text-gray-700">
                                <li>صورة من البطاقة الشخصية أو جواز السفر.</li>
                                <li>صورة شخصية حديثة.</li>
                                <li>نماذج من الأعمال التصويرية (صور).</li>
                            </ul>
                        </div>
                        <label class="flex items-start gap-2 text-sm text-gray-700">
                            <input type="checkbox" id="agree-photo" class="mt-1 text-purple-600 focus:ring-purple-500">
                            <span>أوافق على جميع الشروط المذكورة أعلاه</span>
                        </label>
                        <p id="agree-photo-error" class="hidden text-sm text-red-600">الرجاء الموافقة على الشروط للمتابعة.</p>
                    </div>
                </section>

                <!-- Step 2: البيانات الشخصية -->
                <section class="step-section hidden" data-step="2">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">٣) البيانات الشخصية</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-1">الإسم الثلاثي والقبيلة <span
                                    class="text-red-600">*</span></label>
                            <input type="text" name="fullName" required
                                class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-1">تاريخ الميلاد <span
                                    class="text-red-600">*</span></label>
                            <input type="date" name="birthDate" required
                                class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-800 mb-1">الجنس <span
                                    class="text-red-600">*</span></label>
                            <div class="flex flex-wrap items-center gap-2">
                                <label
                                    class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-4 py-2 bg-white cursor-pointer">
                                    <input type="radio" name="gender" value="ذكر" required
                                        class="text-purple-600 focus:ring-purple-500">
                                    <span class="text-sm">ذكر</span>
                                </label>
                                <label
                                    class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-4 py-2 bg-white cursor-pointer">
                                    <input type="radio" name="gender" value="أنثى"
                                        class="text-purple-600 focus:ring-purple-500">
                                    <span class="text-sm">أنثى</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-1">المؤهل الدراسي <span
                                    class="text-red-600">*</span></label>
                            <input type="text" name="education" required
                                class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-1">العنوان الدائم <span
                                    class="text-red-600">*</span></label>
                            <input type="text" name="address" required
                                class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-1">رقم الهاتف <span
                                    class="text-red-600">*</span></label>
                            <input type="tel" name="phone" pattern="^[0-9+\-\s]{6,}$" required
                                class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                                placeholder="مثال: 91234567">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-800 mb-1">البريد الإلكتروني <span
                                    class="text-red-600">*</span></label>
                            <input type="email" name="email" required
                                class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500"
                                placeholder="you@example.com">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-800 mb-2">التخصص الفني (إمكانية اختيار
                                أكثر من واحد) <span class="text-red-600">*</span></label>
                            <div class="flex flex-wrap items-center gap-2">
                                @php($specs = ['الرسم والتصوير', 'النحت والخزف', 'الأعمال التركيبية وفنون الميديا', 'الخط العربي والتشكيلات الحروفية', 'التصميم الجرافيكي', 'الكاريكاتير', 'الطباعة بالحفر', 'أخرى'])
                                @foreach($specs as $spec)
                                    <label
                                        class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-4 py-2 bg-white cursor-pointer">
                                        <input type="checkbox" name="specialties[]" value="{{ $spec }}"
                                            class="text-purple-600 focus:ring-purple-500">
                                        <span class="text-sm">{{ $spec }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <p id="specialtiesError" class="hidden text-sm text-red-600 mt-1">الرجاء اختيار تخصص واحد
                                على الأقل.</p>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-800 mb-1">الجنسية</label>
                            <div class="flex flex-wrap items-center gap-2">
                                <label
                                    class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-4 py-2 bg-white cursor-pointer">
                                    <input type="radio" name="nationality" value="عماني" checked
                                        class="text-purple-600 focus:ring-purple-500">
                                    <span class="text-sm">عماني</span>
                                </label>
                                <label
                                    class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-4 py-2 bg-white cursor-pointer">
                                    <input type="radio" name="nationality" value="غير عماني"
                                        class="text-purple-600 focus:ring-purple-500">
                                    <span class="text-sm">غير عماني</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Step 3: الملفات -->
                <section class="step-section hidden" data-step="3">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">٤) تحميل الملفات المطلوبة</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <label class="block text-sm font-semibold text-gray-800 mb-1">نسخة من البطاقة الشخصية أو
                                جواز السفر <span class="text-red-600">*</span></label>
                            <input type="file" name="idFile" accept=".pdf,.doc,.docx,image/*" required
                                class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" />
                            <p class="text-xs text-gray-500 mt-1">PDF أو document أو image. حتى 10 MB</p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <label class="block text-sm font-semibold text-gray-800 mb-1">صورة شخصية <span
                                    class="text-red-600">*</span></label>
                            <input type="file" name="avatarFile" accept=".pdf,.doc,.docx,image/*" required
                                class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" />
                            <p class="text-xs text-gray-500 mt-1">PDF أو document أو image. حتى 10 MB</p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <label class="block text-sm font-semibold text-gray-800 mb-1">معلومات فنية عن المشاركات
                                السابقة (السيرة الفنية)</label>
                            <input type="file" name="cvFile" accept=".pdf,.doc,.docx,image/*"
                                class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" />
                            <p class="text-xs text-gray-500 mt-1">اختياري حتى 10MB.</p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-white p-4">
                            <label id="worksLabel" class="block text-sm font-semibold text-gray-800 mb-1">تحميل نماذج من
                                الأعمال الفنية (صور) <span class="text-red-600">*</span></label>
                            <input type="file" name="works[]" id="worksFiles" accept="image/*" multiple
                                class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" />
                            <p id="worksNote" class="text-xs text-gray-500 mt-1"></p>
                            <p id="postSizeError" class="hidden text-sm text-red-600 mt-1"></p>
                            @error('works')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </section>

                <!-- Step 4: المراجعة والإرسال -->
                <section class="step-section hidden" data-step="4">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">٥) مراجعة وإرسال</h3>
                    <div id="reviewBox"
                        class="rounded-xl border border-dashed border-gray-200 bg-white p-4 text-sm text-gray-700">
                        سيتم عرض ملخص لمدخلاتك قبل الإرسال.
                    </div>
                </section>

                <!-- Footer actions -->
                <div class="mt-6 flex flex-wrap gap-3">
                    <button type="button" id="prevBtn"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        السابق
                    </button>
                    <div class="ms-auto flex gap-2">
                        <button type="button" id="saveDraftBtn" title="حفظ محلي"
                            class="inline-flex items-center justify-center rounded-xl bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                            حفظ مؤقت
                        </button>
                        <button type="button" id="nextBtn"
                            class="inline-flex items-center justify-center rounded-xl bg-gradient-to-l from-purple-700 to-indigo-700 px-5 py-2 text-sm font-semibold text-white shadow hover:opacity-95">
                            التالي
                        </button>
                        <button type="submit" id="submitBtn"
                            class="hidden inline-flex items-center justify-center rounded-xl bg-gradient-to-l from-purple-700 to-indigo-700 px-5 py-2 text-sm font-semibold text-white shadow hover:opacity-95">
                            إرسال
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const steps = [...document.querySelectorAll('.step-section')];
            const stepCards = [...document.querySelectorAll('.step-card')];
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');
            const saveDraftBtn = document.getElementById('saveDraftBtn');
            const form = document.getElementById('membershipForm');

            const formTypeError = document.getElementById('formTypeError');
            const termsVisual = document.getElementById('terms-visual');
            const termsPhoto = document.getElementById('terms-photo');
            const agreeVisual = document.getElementById('agree-visual');
            const agreePhoto = document.getElementById('agree-photo');
            const agreeVisualError = document.getElementById('agree-visual-error');
            const agreePhotoError = document.getElementById('agree-photo-error');
            const worksLabel = document.getElementById('worksLabel');
            const worksNote = document.getElementById('worksNote');
            const worksFiles = document.getElementById('worksFiles');
            const specialtiesError = document.getElementById('specialtiesError');
            const reviewBox = document.getElementById('reviewBox');

            let current = 0;

            function setStep(i) {
                current = i;
                steps.forEach((s, idx) => s.classList.toggle('hidden', idx !== i));
                steps.forEach((s, idx) => s.classList.toggle('block', idx === i));
                stepCards.forEach((card, idx) => {
                    card.classList.toggle('border-purple-500', idx === i);
                    card.classList.toggle('bg-purple-50', idx === i);
                });
                prevBtn.disabled = i === 0;
                nextBtn.classList.toggle('hidden', i === steps.length - 1);
                submitBtn.classList.toggle('hidden', i !== steps.length - 1);
            }

            function getFormType() {
                const el = form.querySelector('input[name="formType"]:checked');
                return el ? el.value : null;
            }

            function validateStep0() {
                const type = getFormType();
                formTypeError.classList.toggle('hidden', !!type);
                return !!type;
            }

            const cfgEl = document.getElementById('verify-config');
            const limits = {
                visual: {
                    min: parseInt(cfgEl?.dataset.visualMin || '5', 10),
                    max: parseInt(cfgEl?.dataset.visualMax || '10', 10),
                },
                photo: {
                    min: parseInt(cfgEl?.dataset.photoMin || '10', 10),
                    max: parseInt(cfgEl?.dataset.photoMax || '10', 10),
                },
            };
            const MAX_POST_MB = parseInt(cfgEl?.dataset.postMaxMb || '40', 10);
            const MAX_POST_BYTES = MAX_POST_MB * 1024 * 1024;

            function showTermsForType(type) {
                if (type === 'visual') {
                    termsVisual.classList.remove('hidden');
                    termsPhoto.classList.add('hidden');
                } else if (type === 'photo') {
                    termsPhoto.classList.remove('hidden');
                    termsVisual.classList.add('hidden');
                }
                if (type && limits[type]) {
                    const { min, max } = limits[type];
                    worksNote.textContent = (min === max)
                        ? `العدد المطلوب ${min} صورة بالضبط، كل صورة حتى 10MB.`
                        : `الحد الأدنى ${min} والحد الأقصى ${max} صور، كل صورة حتى 10MB.`;
                }
            }

            function validateStep1() {
                const type = getFormType();
                if (type === 'visual') {
                    const ok = !!agreeVisual.checked;
                    agreeVisualError.classList.toggle('hidden', ok);
                    return ok;
                }
                if (type === 'photo') {
                    const ok = !!agreePhoto.checked;
                    agreePhotoError.classList.toggle('hidden', ok);
                    return ok;
                }
                return false;
            }

            function validateStep2() {
                let ok = true;
                ['fullName', 'birthDate', 'education', 'address', 'phone', 'email'].forEach(name => {
                    const el = form.querySelector(`[name="${name}"]`);
                    if (!el || !el.value) ok = false;
                });
                const gender = form.querySelector('input[name="gender"]:checked');
                if (!gender) ok = false;
                const anySpec = form.querySelectorAll('input[name="specialties[]"]:checked').length > 0;
                specialtiesError.classList.toggle('hidden', anySpec);
                return ok && anySpec;
            }

            function validateStep3() {
                const idFile = form.querySelector('input[name="idFile"]');
                const avatarFile = form.querySelector('input[name="avatarFile"]');
                if (!idFile.files.length || !avatarFile.files.length) return false;
                const type = getFormType();
                const count = worksFiles.files.length;
                if (type && limits[type]) {
                    const { min, max } = limits[type];
                    const countOk = count >= min && count <= max;
                    if (!countOk) return false;
                }
                // Total size preflight against MAX_POST_BYTES
                const postSizeError = document.getElementById('postSizeError');
                let total = 0;
                [idFile, avatarFile, form.querySelector('input[name="cvFile"]')].forEach(input => {
                    if (input && input.files && input.files[0]) total += input.files[0].size || 0;
                });
                if (worksFiles && worksFiles.files) {
                    for (const f of worksFiles.files) total += f.size || 0;
                }
                const ok = total <= MAX_POST_BYTES;
                postSizeError.classList.toggle('hidden', ok);
                if (!ok) {
                    const mb = (total / (1024 * 1024)).toFixed(1);
                    postSizeError.textContent = `إجمالي المرفوع (${mb} MB) يتجاوز الحد (${MAX_POST_MB} MB). قلل عدد/حجم الملفات.`;
                }
                return ok;
            }

            function fillReview() {
                const fd = new FormData(form);
                const lines = [];
                lines.push(`نوع الاستمارة: ${getFormType() === 'visual' ? 'الفنون التشكيلية والرقمية' : 'التصوير الضوئي'}`);
                lines.push(`الاسم: ${fd.get('fullName') || ''}`);
                lines.push(`تاريخ الميلاد: ${fd.get('birthDate') || ''}`);
                lines.push(`الجنس: ${fd.get('gender') || ''}`);
                lines.push(`المؤهل: ${fd.get('education') || ''}`);
                lines.push(`العنوان: ${fd.get('address') || ''}`);
                lines.push(`الهاتف: ${fd.get('phone') || ''}`);
                lines.push(`البريد: ${fd.get('email') || ''}`);
                lines.push(`الجنسية: ${fd.get('nationality') || ''}`);
                const specs = fd.getAll('specialties[]');
                lines.push(`التخصصات: ${specs.join(', ')}`);
                lines.push(`عدد الأعمال: ${worksFiles.files.length}`);
                reviewBox.textContent = lines.join(' | ');
            }

            form.addEventListener('change', (e) => {
                if (e.target.name === 'formType') {
                    const type = getFormType();
                    showTermsForType(type);
                }
            });

            prevBtn.addEventListener('click', () => setStep(Math.max(0, current - 1)));

            nextBtn.addEventListener('click', () => {
                if (current === 0 && !validateStep0()) return;
                if (current === 1 && !validateStep1()) return;
                if (current === 2 && !validateStep2()) return;
                if (current === 3 && !validateStep3()) return;
                const next = Math.min(steps.length - 1, current + 1);
                if (next === 4) fillReview();
                setStep(next);
            });

            // Prevent submit if step3 invalid due to size
            form.addEventListener('submit', (e) => {
                if (!validateStep3()) e.preventDefault();
            });

            // Optional: save small draft to localStorage
            saveDraftBtn.addEventListener('click', () => {
                try {
                    const data = {
                        formType: getFormType(),
                        fullName: form.fullName?.value || '',
                        birthDate: form.birthDate?.value || '',
                        gender: form.querySelector('input[name="gender"]:checked')?.value || '',
                        education: form.education?.value || '',
                        address: form.address?.value || '',
                        phone: form.phone?.value || '',
                        email: form.email?.value || '',
                        nationality: form.querySelector('input[name="nationality"]:checked')?.value || 'عماني',
                        specialties: [...form.querySelectorAll('input[name="specialties[]"]:checked')].map(x => x.value)
                    };
                    localStorage.setItem('verifyDraft', JSON.stringify(data));
                    alert('تم الحفظ مؤقتاً في هذا الجهاز.');
                } catch { }
            });

            // Restore draft if available
            (function restore() {
                try {
                    const raw = localStorage.getItem('verifyDraft');
                    if (!raw) return;
                    const d = JSON.parse(raw);
                    if (d.formType) form.querySelector(`input[name="formType"][value="${d.formType}"]`)?.click();
                    ['fullName', 'birthDate', 'education', 'address', 'phone', 'email'].forEach(k => { if (d[k]) form[k].value = d[k]; });
                    if (d.gender) form.querySelector(`input[name="gender"][value="${d.gender}"]`)?.click();
                    if (d.nationality) form.querySelector(`input[name="nationality"][value="${d.nationality}"]`)?.click();
                    if (Array.isArray(d.specialties)) d.specialties.forEach(v => form.querySelector(`input[name="specialties[]"][value="${v}"]`)?.click());
                } catch { }
            })();

            // Initialize
            setStep(0);
        })();
    </script>
</x-layout>
