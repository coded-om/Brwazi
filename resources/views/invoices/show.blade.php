@php
    /** @var \App\Models\Order $order */
    $buyer = $order->buyer;
    $seller = $order->seller;
@endphp
<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
        }

        .flex {
            display: flex;
        }

        .between {
            justify-content: space-between;
        }

        .center {
            align-items: center;
        }

        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            color: #4c1d95;
        }

        .section {
            margin-bottom: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #eee;
            padding: 8px;
            vertical-align: top;
        }

        th {
            background: #f6f5ff;
            text-align: right;
        }

        .muted {
            color: #555;
        }

        .logo {
            height: 36px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .small {
            font-size: 11px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="flex between center">
            <div class="flex center" style="gap: 12px;">
                <img class="logo" src="{{ public_path('logo.svg') }}" alt="logo">
                <div>
                    <div class="title">فاتورة</div>
                    <div class="muted small">{{ config('app.name') }} — {{ config('app.url') }}</div>
                </div>
            </div>
            <div class="small">
                <div><strong>رقم الطلب:</strong> {{ $order->order_no }}</div>
                <div><strong>رقم الفاتورة:</strong> {{ $order->invoice_number ?? '—' }}</div>
                <div><strong>التاريخ:</strong> {{ now()->format('Y-m-d') }}</div>
            </div>
        </div>
    </div>

    <div class="section grid">
        <div>
            <strong>بيانات التاجر (البائع)</strong>
            <div class="small">
                {{ $seller?->full_name ?? ($seller?->name ?? '—') }}<br>
                {{ $seller?->email ?? '—' }}
            </div>
        </div>
        <div>
            <strong>بيانات المشتري</strong>
            <div class="small">
                {{ $buyer?->full_name ?? ($buyer?->name ?? '—') }}<br>
                {{ $buyer?->email ?? '—' }}
            </div>
        </div>
    </div>
    <div class="section">
        <table>
            <thead>
                <tr>
                    <th>العنصر</th>
                    <th>الكمية</th>
                    <th>السعر</th>
                    <th>الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $it)
                    <tr>
                        <td>{{ $it->title_snapshot ?? $it->title }}</td>
                        <td>{{ $it->quantity }}</td>
                        <td>{{ number_format((float) ($it->price_snapshot ?? $it->price), 2) }}</td>
                        <td>{{ number_format((float) ($it->quantity * ($it->price_snapshot ?? $it->price)), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="section" style="max-width: 360px; margin-left: auto;">
        <table>
            <tr>
                <th>المجموع</th>
                <td>{{ number_format((float) $order->subtotal, 3) }}</td>
            </tr>
            <tr>
                <th>الخصم</th>
                <td>{{ number_format((float) $order->discount, 3) }}</td>
            </tr>
            <tr>
                <th>الشحن</th>
                <td>{{ number_format((float) ($order->shipping_cost ?? $order->shipping_fee), 3) }}</td>
            </tr>
            <tr>
                <th>رسوم المنصة</th>
                <td>{{ number_format((float) ($order->platform_fee ?? 0), 3) }}</td>
            </tr>
            <tr>
                <th>الإجمالي</th>
                <td>{{ number_format((float) $order->total, 3) }}</td>
            </tr>
        </table>
    </div>
</body>

</html>