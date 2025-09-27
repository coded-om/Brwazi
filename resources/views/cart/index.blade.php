<x-layout>
    <div class="max-w-6xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-extrabold text-indigo-950 mb-6">سلة التسوق</h1>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-4">
                @forelse($items as $it)
                    <div class="rounded-2xl border p-4 flex items-center gap-4">
                        <div class="w-20 h-20 rounded-md overflow-hidden bg-gray-100">
                            <img src="{{ $it['image'] }}" class="w-full h-full object-cover" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-indigo-950 line-clamp-1">{{ $it['title'] }}</div>
                            <div class="text-sm text-gray-500">{{ $it['artist'] }}</div>
                            <div class="mt-2 flex items-center gap-3">
                                <form method="post" action="{{ route('cart.update') }}" class="flex items-center gap-2">
                                    @csrf
                                    <input type="hidden" name="items[{{ $it['id'] }}][id]" value="{{ $it['id'] }}" />
                                    <button name="items[{{ $it['id'] }}][qty]" value="{{ max(0, $it['qty'] - 1) }}"
                                        class="px-3 py-1 rounded-full border">-</button>
                                    <span class="w-6 text-center">{{ $it['qty'] }}</span>
                                    <button name="items[{{ $it['id'] }}][qty]" value="{{ min(99, $it['qty'] + 1) }}"
                                        class="px-3 py-1 rounded-full border">+</button>
                                </form>
                            </div>
                        </div>
                        <div class="text-indigo-900 font-bold">{{ number_format($it['price'], 3) }} ريال</div>
                        <form method="post" action="{{ route('cart.remove', $it['id']) }}" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700" title="حذف" aria-label="حذف">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed p-10 text-center text-gray-500">السلة فارغة</div>
                @endforelse
            </div>
            <div>
                <div class="rounded-2xl border p-6 sticky top-4">
                    <h2 class="font-bold mb-4">ملخص الطلب</h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span>المجموع
                                الفرعي</span><span>{{ number_format($subtotal, 3) }} ريال</span></div>
                        <div class="flex justify-between text-rose-600"><span>الخصم (20%)</span><span>-
                                {{ number_format($discount, 3) }} ريال</span></div>
                        <div class="flex justify-between"><span>الشحن</span><span>{{ number_format($shipping, 3) }}
                                ريال</span></div>
                        <hr>
                        <div class="flex justify-between font-extrabold text-indigo-950 text-lg">
                            <span>الإجمالي</span><span>{{ number_format($total, 3) }} ريال</span>
                        </div>
                    </div>
                    @if(count($items) > 0)
                        <form method="post" action="{{ route('checkout.process') }}" class="mt-6">
                            @csrf
                            <button class="w-full py-3 rounded-2xl bg-indigo-950 text-white font-semibold">إتمام
                                الشراء</button>
                        </form>
                    @else
                        <div class="mt-6 text-center text-gray-500">أضف منتجات للسلة لإتمام الشراء</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout>
