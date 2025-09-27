<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Order;
use App\Notifications\OrderPaidBuyer;
use App\Notifications\OrderPaidSeller;
use Illuminate\Support\Str;

class OrderPaidService
{
    public function __construct(private InvoiceService $invoices)
    {
    }

    /**
     * Handle post-payment tasks: generate invoice, notify parties, and open conversation.
     */
    public function handle(Order $order): void
    {
        // Generate invoice number if missing
        if (!$order->invoice_number) {
            $order->invoice_number = 'INV-' . now()->format('ymd') . '-' . Str::upper(Str::random(6));
        }

        // Generate and store invoice PDF exactly as requested
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.show', ['order' => $order])->setPaper('a4');
        $storePath = 'invoices/' . $order->invoice_number . '.pdf';
        \Illuminate\Support\Facades\Storage::put($storePath, $pdf->output());
        $order->invoice_pdf_path = $storePath;

        $order->save();

        // Notify buyer and seller about successful payment
        try {
            if ($order->buyer) {
                $order->buyer->notify(new OrderPaidBuyer($order));
            }
            if ($order->seller) {
                $order->seller->notify(new OrderPaidSeller($order));
            }
        } catch (\Throwable $e) {
            // ignore notification failures in MVP
        }

        // Open conversation between buyer and seller if both exist
        if ($order->buyer_id && $order->seller_id) {
            $conv = Conversation::createOrGet($order->buyer_id, $order->seller_id);
            $invoiceUrl = url('/orders/' . $order->id . '/invoice');
            $msgBody = "تم إنشاء طلبك رقم {$order->order_no}.\nهذا رابط الفاتورة: {$invoiceUrl}\nسيتم إضافة رقم التتبع هنا عند الشحن.";
            Message::create([
                'conversation_id' => $conv->id,
                'sender_id' => $order->seller_id, // يمكن لاحقاً استخدام مستخدم نظام
                'content' => $msgBody,
                'type' => 'text',
            ]);
            $conv->updateLastMessageTime();
        }

        // Send email with link or attachment (placeholder minimal):
        try {
            if (class_exists(\Illuminate\Support\Facades\Mail::class)) {
                $buyer = $order->buyer;
                $seller = $order->seller;
                $downloadUrl = url('/orders/' . $order->id . '/invoice');
                $invoicePath = $order->invoice_pdf_path;
                \Illuminate\Support\Facades\Mail::raw(
                    "تم دفع طلبك رقم {$order->order_no}. يمكنك تنزيل الفاتورة: {$downloadUrl}",
                    function ($message) use ($buyer, $invoicePath) {
                        if ($buyer?->email)
                            $message->to($buyer->email);
                        $message->subject('فاتورة طلبك');
                        $fullPath = storage_path('app/' . $invoicePath);
                        if (is_file($fullPath))
                            $message->attach($fullPath);
                    }
                );
                if ($seller?->email) {
                    \Illuminate\Support\Facades\Mail::raw(
                        "تم دفع طلب رقم {$order->order_no}. تم إرفاق نسخة من الفاتورة.",
                        function ($message) use ($seller, $invoicePath) {
                            $message->to($seller->email)->subject('تم دفع طلبك');
                            $fullPath = storage_path('app/' . $invoicePath);
                            if (is_file($fullPath))
                                $message->attach($fullPath);
                        }
                    );
                }
            }
        } catch (\Throwable $e) {
            // Ignore mail errors for MVP
        }

        if (function_exists('notify')) {
            try {
                notify()->success('تم إصدار الفاتورة وإرسال إشعار للطرفين وفتح المحادثة');
            } catch (\Throwable $e) {
            }
        }
    }
}
