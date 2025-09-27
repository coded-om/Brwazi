<x-layout>
    <div class="max-w-3xl mx-auto py-10 px-4">
        <h1 class="text-2xl font-bold text-indigo-700 mb-6">طلب مزاد</h1>
        <form method="POST" action="{{ route('user.auctions.request.store') }}" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">اختر اللوحة</label>
                <select name="artwork_id" class="w-full border rounded p-2" required>
                    <option value="" disabled selected>— اختر —</option>
                    @foreach($artworks as $art)
                        <option value="{{ $art->id }}">#{{ $art->id }} — {{ $art->title }}</option>
                    @endforeach
                </select>
                @error('artwork_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">سعر البدء</label>
                <input name="desired_start_price" type="number" min="0" step="0.01" class="w-full border rounded p-2"
                    required />
                @error('desired_start_price')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">موعد مقترح (اختياري)</label>
                    <input name="suggested_start_at" type="datetime-local" class="w-full border rounded p-2" />
                    @error('suggested_start_at')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">مدة بالدقائق (اختياري)</label>
                    <input name="suggested_duration" type="number" min="5" max="10080"
                        class="w-full border rounded p-2" />
                    @error('suggested_duration')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">ملاحظات (اختياري)</label>
                <textarea name="admin_notes" rows="3" class="w-full border rounded p-2"></textarea>
                @error('admin_notes')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-3">
                <button class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">إرسال الطلب</button>
                <a href="{{ route('user.dashboard') }}" class="text-gray-600">إلغاء</a>
            </div>
        </form>
    </div>
</x-layout>