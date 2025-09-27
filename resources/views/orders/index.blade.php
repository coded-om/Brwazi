<x-layout>
    <div class="max-w-6xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-extrabold text-indigo-950 mb-6">الطلبات</h1>
        <div class="grid gap-4">
            @foreach($orders as $order)
                <div class="rounded-2xl border p-4 flex items-center gap-4 justify-between">
                    <a class="flex-1 min-w-0 flex items-center gap-4" href="{{ route('orders.show', $order) }}">
                        <div class="font-semibold">{{ $order->order_no }}</div>
                        <div class="text-gray-500 truncate">{{ $order->items->pluck('title')->join('، ') }}</div>
                    </a>
                    <div class="font-bold whitespace-nowrap">{{ number_format($order->total, 3) }} ريال</div>
                    <div class="text-xs px-2 py-1 rounded-full bg-gray-100">{{ $order->payment_status }} /
                        {{ $order->fulfillment_status ?: $order->status }}</div>
                    @if($order->invoice_pdf_path)
                        <a href="{{ route('orders.invoice', $order) }}" class="text-indigo-700 hover:underline">الفاتورة</a>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    </div>
</x-layout>
