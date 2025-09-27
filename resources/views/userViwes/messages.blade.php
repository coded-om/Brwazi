@php($user = auth()->user())
<x-layout>
    <div class="min-h-screen bg-gray-50 py-10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow p-8">
                <div class="flex items-center justify-between mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fa-solid fa-comments text-indigo-600"></i>
                        المراسلات
                        @if($user->unread_messages_count > 0)
                            <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                {{ $user->unread_messages_count }}
                            </span>
                        @endif
                    </h1>
                    <div class="flex items-center gap-4">
                        <button id="newMessageBtn"
                            class="text-sm text-indigo-600 hover:text-indigo-800 flex items-center gap-1 px-3 py-2 border border-indigo-200 rounded-lg hover:bg-indigo-50">
                            <i class="fa-solid fa-plus"></i> محادثة جديدة
                        </button>
                        <a href="{{ route('user.dashboard') }}"
                            class="text-sm text-gray-600 hover:text-gray-800 flex items-center gap-1">
                            <i class="fa-solid fa-gauge-high"></i> العودة للوحة التحكم
                        </a>
                    </div>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- قائمة المحادثات -->
                    <div class="md:col-span-1 space-y-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-sm font-semibold text-gray-700">المحادثات</h2>
                        </div>

                        <div class="space-y-3 max-h-[600px] overflow-y-auto">
                            @forelse($conversations as $conversation)
                                <a href="{{ route('user.conversation', $conversation->id) }}"
                                    class="block p-4 rounded-lg border hover:bg-gray-50 transition
                                                      @if($conversation->unreadCount > 0) bg-indigo-50 border-indigo-200 @else bg-white border-gray-200 @endif">
                                    <div class="flex items-center gap-3">
                                        <!-- صورة المستخدم الآخر -->
                                        <div class="relative">
                                            <div
                                                class="h-12 w-12 rounded-full overflow-hidden bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center">
                                                @if($conversation->otherUser->ProfileImage)
                                                    <img src="{{ asset('storage/' . $conversation->otherUser->ProfileImage) }}"
                                                        alt="{{ $conversation->otherUser->full_name }}"
                                                        class="h-full w-full object-cover">
                                                @else
                                                    <i class="fas fa-user text-white text-lg"></i>
                                                @endif
                                            </div>
                                            @if($conversation->unreadCount > 0)
                                                <span
                                                    class="absolute -top-1 -right-1 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full font-bold">
                                                    {{ $conversation->unreadCount }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- معلومات المحادثة -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    {{ $conversation->otherUser->full_name }}
                                                </p>
                                                @if($conversation->lastMessage)
                                                    <span class="text-xs text-gray-500">
                                                        {{ $conversation->lastMessage->created_at->diffForHumans() }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if($conversation->otherUser->tagline)
                                                <p class="text-xs text-gray-500 truncate">
                                                    {{ $conversation->otherUser->tagline }}
                                                </p>
                                            @endif

                                            @if($conversation->lastMessage)
                                                <p class="text-xs text-gray-600 truncate mt-1">
                                                    @if($conversation->lastMessage->type === 'image')
                                                        <i class="fa-solid fa-image"></i> صورة
                                                    @elseif($conversation->lastMessage->type === 'artwork_request')
                                                        <i class="fa-solid fa-palette"></i> طلب لوحة
                                                    @else
                                                        {{ Str::limit($conversation->lastMessage->content, 30) }}
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center py-8 text-gray-500">
                                    <i class="fa-solid fa-comments text-3xl mb-3"></i>
                                    <p class="text-sm">لا توجد محادثات بعد</p>
                                    <p class="text-xs">ابدأ محادثة جديدة للتواصل مع الفنانين</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- منطقة الرسائل -->
                    <div class="md:col-span-2 flex flex-col h-[600px]">
                        <div class="flex-1 overflow-y-auto space-y-6 pr-2 border rounded-lg bg-gray-50 p-6">
                            <div class="text-center py-16 text-gray-400">
                                <i class="fa-solid fa-message text-4xl mb-4"></i>
                                <p class="text-sm">اختر محادثة من القائمة لعرض الرسائل</p>
                                <p class="text-xs mt-2">أو ابدأ محادثة جديدة مع فنان</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- نافذة محادثة جديدة -->
    <div id="newMessageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">محادثة جديدة</h3>
                    <button id="closeModalBtn" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>

                <form id="startConversationForm" action="{{ route('user.conversation.start') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">البحث عن مستخدم</label>
                            <input type="text" id="userSearch" placeholder="ابحث بالاسم أو البريد الإلكتروني..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <div id="searchResults" class="mt-2 max-h-40 overflow-y-auto hidden"></div>
                        </div>

                        <input type="hidden" name="user_id" id="selectedUserId" required>
                        <div id="selectedUserInfo" class="hidden p-3 bg-gray-50 rounded-lg"></div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" id="cancelBtn"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800">إلغاء</button>
                        <button type="submit" id="startBtn" disabled
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            بدء المحادثة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // فتح نافذة محادثة جديدة
        document.getElementById('newMessageBtn').addEventListener('click', function () {
            document.getElementById('newMessageModal').classList.remove('hidden');
        });

        // إغلاق النافذة
        function closeModal() {
            document.getElementById('newMessageModal').classList.add('hidden');
            document.getElementById('userSearch').value = '';
            document.getElementById('searchResults').innerHTML = '';
            document.getElementById('searchResults').classList.add('hidden');
            document.getElementById('selectedUserInfo').classList.add('hidden');
            document.getElementById('selectedUserId').value = '';
            document.getElementById('startBtn').disabled = true;
        }

        document.getElementById('closeModalBtn').addEventListener('click', closeModal);
        document.getElementById('cancelBtn').addEventListener('click', closeModal);

        // البحث عن المستخدمين
        let searchTimeout;
        document.getElementById('userSearch').addEventListener('input', function () {
            const query = this.value.trim();

            if (query.length < 2) {
                document.getElementById('searchResults').classList.add('hidden');
                return;
            }

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetch(`{{ route('user.search-users') }}?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        const users = Array.isArray(data) ? data : (data?.users || []);
                        const resultsDiv = document.getElementById('searchResults');
                        resultsDiv.innerHTML = '';

                        if (!users || users.length === 0) {
                            resultsDiv.innerHTML = '<p class="text-sm text-gray-500 p-2">لا توجد نتائج</p>';
                        } else {
                            users.forEach(user => {
                                const userDiv = document.createElement('div');
                                userDiv.className = 'flex items-center gap-3 p-2 hover:bg-gray-100 cursor-pointer rounded';
                                userDiv.onclick = () => selectUser(user);

                                userDiv.innerHTML = `
                                    <div class="h-10 w-10 rounded-full overflow-hidden bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center">
                                        ${user.ProfileImage ?
                                        `<img src="/storage/${user.ProfileImage}" alt="${(user.fname || '')} ${(user.lname || '')}" class="h-full w-full object-cover">` :
                                        '<i class="fas fa-user text-white"></i>'
                                    }
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium">${(user.fname || '').trim()} ${(user.lname || '').trim()}</p>
                                        <p class="text-xs text-gray-500">${user.tagline || 'بدون تخصص'}</p>
                                    </div>
                                `;

                                resultsDiv.appendChild(userDiv);
                            });
                        }

                        resultsDiv.classList.remove('hidden');
                    })
                    .catch(console.error);
            }, 300);
        });

        // اختيار مستخدم
        function selectUser(user) {
            document.getElementById('selectedUserId').value = user.id;
            document.getElementById('userSearch').value = `${user.fname} ${user.lname}`;
            document.getElementById('searchResults').classList.add('hidden');

            const infoDiv = document.getElementById('selectedUserInfo');
            infoDiv.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="h-12 w-12 rounded-full overflow-hidden bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center">
                        ${user.ProfileImage ?
                    `<img src="/storage/${user.ProfileImage}" alt="${user.fname} ${user.lname}" class="h-full w-full object-cover">` :
                    '<i class="fas fa-user text-white"></i>'
                }
                    </div>
                    <div>
                        <p class="font-medium">${user.fname} ${user.lname}</p>
                        <p class="text-sm text-gray-600">${user.tagline || 'بدون تخصص'}</p>
                    </div>
                </div>
            `;
            infoDiv.classList.remove('hidden');
            document.getElementById('startBtn').disabled = false;
        }

        // إرسال بدء المحادثة عبر AJAX لانتقال تلقائي
        document.getElementById('startConversationForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const form = e.currentTarget;
            const userId = document.getElementById('selectedUserId').value;
            if (!userId) return;
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ user_id: userId })
            });
            if (res.ok) {
                const data = await res.json();
                if (data && data.conversation_id) {
                    window.location.href = `{{ route('user.conversation', ['conversation' => '___ID___']) }}`.replace('___ID___', data.conversation_id);
                } else {
                    // fallback to normal submit
                    form.submit();
                }
            } else {
                form.submit();
            }
        });
    </script>
</x-layout>