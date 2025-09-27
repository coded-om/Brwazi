<x-layout>
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1 rounded-2xl border p-6 space-y-6">
                <div>
                    <h2 class="font-bold mb-3">تفاصيل الطلب</h2>
                    <div class="text-sm text-gray-600 space-y-1">
                        <div>التاريخ: {{ $order->created_at->format('d - F - Y') }}</div>
                        <div>الإسم: {{ $order->customer_name ?? $order->user->fname }}</div>
                        <div>العنوان: {{ $order->customer_city ?? '-' }}</div>
                        <div>رقم الهاتف: {{ $order->customer_phone ?? '-' }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="px-2 py-1 rounded-full bg-gray-100">الدفع: {{ $order->payment_status }}</span>
                    <span class="px-2 py-1 rounded-full bg-gray-100">الشحن:
                        {{ $order->fulfillment_status ?: $order->status }}</span>
                </div>
                <div>
                    <h2 class="font-bold mb-3">تفاصيل السعر</h2>
                    <div class="text-sm space-y-1">
                        <div class="flex justify-between">
                            <span>السعر</span><span>{{ number_format($order->subtotal, 3) }} ريال</span>
                        </div>
                        <div class="flex justify-between">
                            <span>الرسوم</span><span>{{ number_format($order->shipping_fee, 3) }} ريال</span>
                        </div>
                        <div class="flex justify-between font-bold">
                            <span>الإجمالي</span><span>{{ number_format($order->total, 3) }} ريال</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    @if($order->invoice_pdf_path)
                        <a href="{{ route('orders.invoice', $order) }}" class="text-indigo-700 hover:underline">تحميل
                            الفاتورة</a>
                    @endif
                    @if($order->tracking_number && $order->shipping_carrier)
                        <a href="https://www.google.com/search?q={{ urlencode($order->shipping_carrier . ' ' . $order->tracking_number) }}"
                            target="_blank" class="text-indigo-700 hover:underline">تتبع الشحنة</a>
                    @endif
                </div>
                <div
                    class="h-20 bg-[repeating-linear-gradient(90deg,black,black_3px,transparent_3px,transparent_6px)] rounded-md">
                </div>
                <div class="text-xs text-center text-gray-500">{{ $order->order_no }}</div>
            </div>
            <div class="lg:col-span-2 rounded-2xl border p-6 space-y-4">
                @foreach($order->items as $it)
                    <div class="flex items-center gap-4">
                        <img src="{{ $it->image_url }}" class="w-24 h-24 rounded-md object-cover" />
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold">{{ $it->title }}</div>
                            <div class="text-sm text-gray-500">{{ $it->artist_name }}</div>
                        </div>
                        <div class="font-bold">{{ number_format($it->price, 3) }} ريال</div>
                    </div>
                @endforeach

                @php
                    $user = auth()->user();
                @endphp

                {{-- Seller can add shipping if paid and not yet shipped --}}
                @if($user && (int) $order->seller_id === (int) $user->id && $order->payment_status === \App\Models\Order::PAYMENT_PAID && $order->fulfillment_status !== \App\Models\Order::FULFILLMENT_SHIPPED)
                    <div class="mt-6 border-t pt-6">
                        <h3 class="font-bold mb-3">إضافة معلومات الشحن</h3>
                        <form method="post" action="{{ route('orders.shipping', $order) }}"
                            class="grid sm:grid-cols-2 gap-3">
                            @csrf
                            <input name="shipping_carrier" placeholder="شركة الشحن" class="border rounded-lg px-3 py-2"
                                required />
                            <input name="tracking_number" placeholder="رقم التتبع" class="border rounded-lg px-3 py-2"
                                required />
                            <div class="sm:col-span-2">
                                <button class="px-4 py-2 rounded-xl bg-indigo-950 text-white">تأكيد الشحن</button>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- Buyer can confirm delivered if shipped --}}
                @if($user && (int) $order->buyer_id === (int) $user->id && $order->fulfillment_status === \App\Models\Order::FULFILLMENT_SHIPPED)
                    <div class="mt-6 border-t pt-6">
                        <h3 class="font-bold mb-3">تأكيد الاستلام</h3>
                        <form method="post" action="{{ route('orders.delivered', $order) }}">
                            @csrf
                            <button class="px-4 py-2 rounded-xl bg-green-600 text-white">تم الاستلام</button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout>
