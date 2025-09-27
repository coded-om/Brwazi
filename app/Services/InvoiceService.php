<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    public function generate(Order $order): string
    {
        $order->loadMissing('items', 'buyer', 'seller');
        $pdf = Pdf::loadView('invoices.show', [
            'order' => $order,
        ]);

        $dir = 'invoices/' . now()->format('Y/m/d');
        $filename = ($order->invoice_number ?: 'INV-' . $order->id) . '.pdf';
        $path = $dir . '/' . $filename;
        Storage::put($path, $pdf->output());
        return $path;
    }
}
