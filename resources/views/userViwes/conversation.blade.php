@php($user = auth()->user())
<x-layout>
    <div class="min-h-screen bg-gray-50 py-10" data-conversation-id="{{ $conversation->id }}"
        data-current-user-id="{{ $user->id }}">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow overflow-hidden">
                <!-- رأس المحادثة -->
                <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-violet-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <a href="{{ route('user.messages') }}" class="text-gray-600 hover:text-gray-800">
                                <i class="fa-solid fa-arrow-right text-lg"></i>
                            </a>

                            <!-- صورة وبيانات المستخدم الآخر -->
                            <div class="flex items-center gap-3">
                                <div
                                    class="h-12 w-12 rounded-full overflow-hidden bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center">
                                    @if($otherUser->ProfileImage)
                                        <img src="{{ asset('storage/' . $otherUser->ProfileImage) }}"
                                            alt="{{ $otherUser->full_name }}" class="h-full w-full object-cover">
                                    @else
                                        <i class="fas fa-user text-white text-lg"></i>
                                    @endif
                                </div>
                                <div>
                                    <h2 class="font-semibold text-gray-900">{{ $otherUser->full_name }}</h2>
                                    <p class="text-sm text-gray-600">{{ $otherUser->tagline ?: 'فنان' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- أزرار إضافية -->
                        <div class="flex items-center gap-2">
                            <button id="artworkRequestBtn"
                                class="px-3 py-2 text-sm bg-violet-600 text-white rounded-lg hover:bg-violet-700 flex items-center gap-2">
                                <i class="fa-solid fa-palette"></i>
                                طلب لوحة
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex">
                    <!-- منطقة الرسائل -->
                    <div class="flex-1 flex flex-col h-[600px]">
                        <!-- الرسائل -->
                        <div id="messagesContainer"
                            class="flex-1 overflow-y-auto px-8 py-6 space-y-4 messages-container">
                            @forelse($messages as $message)
                            <div class="flex {{ $message->sender_id === $user->id ? 'justify-end' : 'justify-start' }} mb-4 message-item"
                                data-message-id="{{ $message->id }}">
                                <div class="max-w-xs lg:max-w-md">
                                    <!-- رسالة نصية أو صورة -->
                                    @if($message->type === 'text' || $message->type === 'image')
                                        <div
                                            class=" py-1 px-2 rounded-md {{ $message->sender_id === $user->id ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-900' }}">
                                            @if($message->image_path)
                                                <img src="{{ asset('storage/' . $message->image_path) }}" alt="صورة مرفقة"
                                                    class="rounded-lg mb-2 max-w-full cursor-pointer"
                                                    data-url="{{ asset('storage/' . $message->image_path) }}"
                                                    onclick="openImageModal(this.dataset.url)">
                                            @endif

                                            @if($message->content)
                                                <p class="text-sm">{{ $message->content }}</p>
                                            @endif

                                            <div
                                                class="flex items-center justify-between mt-2 text-[8px] {{ $message->sender_id === $user->id ? 'text-indigo-200' : 'text-gray-500' }}">
                                                <span>{{ $message->created_at->format('H:i') }}</span>
                                                @if($message->sender_id === $user->id)
                                                    <span>{{ $message->is_read ? 'تم القراءة' : 'تم الإرسال' }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- طلب لوحة -->
                                    @elseif($message->type === 'artwork_request' && $message->artworkRequest)
                                    @php($request = $message->artworkRequest)
                                    <div class="p-3 border-2 border-violet-200 bg-violet-50 rounded-lg">
                                        <div class="flex items-center gap-2 mb-3">
                                            <i class="fa-solid fa-palette text-violet-600"></i>
                                            <span class="font-semibold text-violet-800">طلب لوحة فنية</span>
                                            <span class="px-2 py-1 text-xs rounded-full {{ $request->status_color }}">
                                                <i class="{{ $request->status_icon }}"></i>
                                                {{ $request->status_label }}
                                            </span>
                                        </div>

                                        <h4 class="font-semibold text-gray-900 mb-2">{{ $request->title }}</h4>
                                        <p class="text-sm text-gray-700 mb-3">{{ $request->description }}</p>

                                        <div class="grid grid-cols-2 gap-4 text-xs text-gray-600 mb-3">
                                            @if($request->budget)
                                                <div>
                                                    <i class="fa-solid fa-dollar-sign text-green-600"></i>
                                                    الميزانية: ${{ number_format($request->budget, 2) }}
                                                </div>
                                            @endif
                                            @if($request->deadline)
                                                <div>
                                                    <i class="fa-solid fa-calendar text-orange-600"></i>
                                                    التسليم: {{ $request->deadline->format('Y-m-d') }}
                                                </div>
                                            @endif
                                        </div>

                                        @if($request->hasReferenceImages())
                                            <div class="mb-3">
                                                <p class="text-xs text-gray-600 mb-2">صور مرجعية:</p>
                                                <div class="flex gap-2">
                                                    @foreach($request->reference_image_urls as $imageUrl)
                                                        <img src="{{ $imageUrl }}" alt="مرجع"
                                                            class="h-16 w-16 object-cover rounded cursor-pointer"
                                                            data-url="{{ $imageUrl }}"
                                                            onclick="openImageModal(this.dataset.url)">
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if($request->artist_notes)
                                            <div class="p-2 bg-white rounded border-r-4 border-violet-400 mb-3">
                                                <p class="text-xs text-gray-600 mb-1">ملاحظات الفنان:</p>
                                                <p class="text-sm">{{ $request->artist_notes }}</p>
                                            </div>
                                        @endif

                                        <!-- أزرار الرد للفنان -->
                                        @if($request->artist_id === $user->id && $request->status === 'pending')
                                            <div class="flex gap-2 mt-3">
                                                <form method="POST"
                                                    action="{{ route('user.artwork-request.respond', $request) }}"
                                                    class="inline">
                                                    @csrf
                                                    <input type="hidden" name="response" value="accepted">
                                                    <button type="submit"
                                                        class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                        <i class="fa-solid fa-check"></i> قبول
                                                    </button>
                                                </form>
                                                <form method="POST"
                                                    action="{{ route('user.artwork-request.respond', $request) }}"
                                                    class="inline">
                                                    @csrf
                                                    <input type="hidden" name="response" value="rejected">
                                                    <button type="submit"
                                                        class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                                                        <i class="fa-solid fa-times"></i> رفض
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                        <div class="text-xs text-gray-500 mt-2">
                                            {{ $message->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    @endif

                                    <!-- اسم المرسل للرسائل الواردة -->
                                    @if($message->sender_id !== $user->id)
                                        <p class="text-xs text-gray-500 mt-1">{{ $message->sender->fname }}</p>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-16 text-gray-400">
                                <i class="fa-solid fa-message text-4xl mb-4"></i>
                                <p class="text-sm">لا توجد رسائل بعد</p>
                                <p class="text-xs">ابدأ المحادثة بإرسال رسالة</p>
                            </div>
                            @endforelse
                        </div>

                        <!-- منطقة إرسال الرسائل -->
                        <div class="px-8 py-6 border-t border-gray-200 bg-gray-50">
                            @if(session('success'))
                                <div class="mb-3 px-3 py-2 rounded bg-green-50 text-green-700 text-sm">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if($errors->any())
                                <div class="mb-3 px-3 py-2 rounded bg-red-50 text-red-700 text-sm">
                                    {{ $errors->first() }}
                                </div>
                            @endif
                            <form id="messageForm" method="POST"
                                action="{{ route('user.message.send', $conversation) }}" enctype="multipart/form-data"
                                class="flex items-end gap-4">
                                @csrf
                                <div class="flex-1">
                                    <div class="flex items-end gap-2">
                                        <textarea name="content" rows="2" placeholder="اكتب رسالتك..."
                                            class="flex-1 rounded-lg border border-gray-300 px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                                            required></textarea>

                                        <!-- زر إرفاق صورة -->
                                        <label for="messageImage"
                                            class="cursor-pointer p-3 text-gray-500 hover:text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                                            <i class="fa-solid fa-image"></i>
                                        </label>
                                        <input type="file" id="messageImage" name="image" accept="image/*"
                                            class="hidden">
                                    </div>

                                    <!-- معاينة الصورة المحددة -->
                                    <div id="imagePreview" class="hidden mt-2">
                                        <div class="flex items-center gap-2 p-2 bg-gray-100 rounded">
                                            <img id="previewImg" src="" alt="معاينة"
                                                class="h-12 w-12 object-cover rounded">
                                            <span id="fileName" class="text-sm text-gray-600"></span>
                                            <button type="button" id="removeImage"
                                                class="text-red-500 hover:text-red-700 ml-auto">
                                                <i class="fa-solid fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="h-12 w-12 rounded-full bg-indigo-600 text-white flex items-center justify-center shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <i class="fa-solid fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- نافذة طلب لوحة -->
    <div id="artworkModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50"
        data-reopen="{{ (old('title') || old('description') || old('budget') || old('deadline')) ? '1' : '0' }}">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold">طلب لوحة من {{ $otherUser->full_name }}</h3>
                        <button id="closeArtworkModal" class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('user.artwork-request.send', $conversation) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">عنوان اللوحة *</label>
                                <input type="text" name="title" required value="{{ old('title') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">وصف اللوحة المطلوبة
                                    *</label>
                                <textarea name="description" rows="4" required
                                    placeholder="اكتب وصفاً تفصيلياً للوحة التي تريدها..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none">{{ old('description') }}</textarea>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">الميزانية المقترحة
                                        (دولار) — اختياري</label>
                                    <input type="number" name="budget" min="0" step="0.01" value="{{ old('budget') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ التسليم
                                        المرغوب — اختياري</label>
                                    <input type="date" name="deadline" value="{{ old('deadline') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">صور مرجعية
                                    (اختيارية)</label>
                                <input type="file" name="reference_images[]" multiple accept="image/*"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="text-xs text-gray-500 mt-1">اختياري: حتى 5 صور مرجعية (كل صورة 2MB كحد أقصى)
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" id="cancelArtworkBtn"
                                class="px-4 py-2 text-gray-600 hover:text-gray-800">إلغاء</button>
                            <button type="submit"
                                class="px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700">
                                <i class="fa-solid fa-palette"></i>
                                إرسال الطلب
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- نافذة عرض الصور -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50" onclick="closeImageModal()">
        <div class="flex items-center justify-center min-h-screen p-4">
            <img id="modalImage" src="" alt="صورة" class="max-w-full max-h-full object-contain">
        </div>
    </div>

    <script>
        // التمرير التلقائي لآخر رسالة
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('messagesContainer');
            container.scrollTop = container.scrollHeight;

            // إعادة فتح نافذة طلب اللوحة إذا كان هناك مدخلات قديمة (فشل تحقق)
            const artworkModalEl = document.getElementById('artworkModal');
            const shouldReopenArtwork = artworkModalEl && artworkModalEl.dataset.reopen === '1';
            if (shouldReopenArtwork) {
                document.getElementById('artworkModal').classList.remove('hidden');
            }
        });

        // فتح نافذة طلب لوحة
        document.getElementById('artworkRequestBtn').addEventListener('click', function () {
            document.getElementById('artworkModal').classList.remove('hidden');
        });

        // إغلاق نافذة طلب لوحة
        function closeArtworkModal() {
            document.getElementById('artworkModal').classList.add('hidden');
        }
        document.getElementById('closeArtworkModal').addEventListener('click', closeArtworkModal);
        document.getElementById('cancelArtworkBtn').addEventListener('click', closeArtworkModal);

        // معاينة الصورة المرفقة
        document.getElementById('messageImage').addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const preview = document.getElementById('imagePreview');
                const img = document.getElementById('previewImg');
                const fileName = document.getElementById('fileName');

                fileName.textContent = file.name;
                img.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
            }
        });

        // إزالة الصورة المحددة
        document.getElementById('removeImage').addEventListener('click', function () {
            document.getElementById('messageImage').value = '';
            document.getElementById('imagePreview').classList.add('hidden');
        });

        // فتح صورة في نافذة منبثقة
        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        // إغلاق نافذة الصور
        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }
    </script>
</x-layout>