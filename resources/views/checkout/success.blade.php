<x-layout>
    <div class="max-w-3xl mx-auto px-4 py-10">
        <div class="text-center mb-8">
            <div class="mx-auto w-16 h-16 rounded-full bg-green-100 flex items-center justify-center text-3xl">✔</div>
            <h1 class="mt-4 text-3xl font-extrabold text-indigo-950">تم الدفع بنجاح!</h1>
            <p class="text-gray-500 mt-2">شكراً لك، تم إتمام عملية الدفع وتم تأكيد طلبك.</p>
        </div>

        <div class="bg-white rounded-2xl border p-6">
            <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                <div class="text-gray-500">رقم العملية</div>
                <div class="font-semibold">{{ $order->order_no }}</div>
                <div class="text-gray-500">المبلغ المدفوع</div>
                <div class="font-semibold">{{ number_format($order->total, 3) }} ر.ع/ريال</div>
                <div class="text-gray-500">طريقة الدفع</div>
                <div class="font-semibold">Bank Transfer / Thawani</div>
                <div class="text-gray-500">تاريخ العملية</div>
                <div class="font-semibold">{{ $order->created_at->format('d/m/Y') }}</div>
                <div class="text-gray-500">المنتج</div>
                <div class="font-semibold">{{ $order->items->pluck('title')->join('، ') }}</div>
            </div>
        </div>

        <h2 class="mt-10 mb-4 text-center font-bold text-indigo-950">حالة الطلب</h2>
        <div class="flex flex-col items-end gap-6">
            <div class="flex items-center gap-3"><span
                    class="w-6 h-6 rounded-full bg-green-500 text-white flex items-center justify-center">✔</span>
                <span>تم تأكيد الطلب والدفع</span>
            </div>
            <div class="flex items-center gap-3"><span
                    class="w-6 h-6 rounded-full bg-yellow-400 text-white flex items-center justify-center">⏳</span>
                <span>جاري تحضير الطلب</span>
            </div>
            <div class="flex items-center gap-3"><span
                    class="w-6 h-6 rounded-full bg-gray-300 text-white flex items-center justify-center">🚚</span>
                <span>تم الشحن</span>
            </div>
            <div class="flex items-center gap-3"><span
                    class="w-6 h-6 rounded-full bg-gray-300 text-white flex items-center justify-center">📦</span>
                <span>تم التسليم</span>
            </div>
        </div>

        <div class="mt-10 flex items-center justify-between">
            @if($order->invoice_pdf_path)
                <a href="{{ route('orders.invoice', $order) }}" class="px-6 py-3 rounded-2xl border">تحميل الفاتورة</a>
            @else
                <span class="px-6 py-3 rounded-2xl border text-gray-400 cursor-not-allowed">الفاتورة قيد التجهيز</span>
            @endif

            @if($order->tracking_number && $order->shipping_carrier)
                <a href="https://www.google.com/search?q={{ urlencode($order->shipping_carrier . ' ' . $order->tracking_number) }}"
                    target="_blank" class="px-6 py-3 rounded-2xl bg-indigo-950 text-white">تتبع الشحنة</a>
            @else
                <span class="px-6 py-3 rounded-2xl bg-gray-200 text-gray-600">سيتم تفعيل التتبع بعد الشحن</span>
            @endif
        </div>
    </div>
</x-layout>
