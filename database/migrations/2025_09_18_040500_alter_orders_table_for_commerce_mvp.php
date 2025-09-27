<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'buyer_id')) {
                $table->foreignId('buyer_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('orders', 'seller_id')) {
                $table->foreignId('seller_id')->nullable()->after('buyer_id')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('orders', 'platform_fee')) {
                $table->decimal('platform_fee', 12, 3)->default(0)->after('shipping_fee');
            }
            if (!Schema::hasColumn('orders', 'shipping_cost')) {
                $table->decimal('shipping_cost', 12, 3)->default(0)->after('platform_fee');
            }

            if (!Schema::hasColumn('orders', 'fulfillment_status')) {
                $table->string('fulfillment_status')->default('unfulfilled')->after('payment_status');
            }
            if (!Schema::hasColumn('orders', 'payment_provider')) {
                $table->string('payment_provider')->nullable()->after('payment_status');
            }

            if (!Schema::hasColumn('orders', 'shipping_address')) {
                $table->json('shipping_address')->nullable()->after('customer_city');
            }
            if (!Schema::hasColumn('orders', 'shipping_carrier')) {
                $table->string('shipping_carrier')->nullable()->after('shipping_address');
            }
            if (!Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('shipping_carrier');
            }
            if (!Schema::hasColumn('orders', 'shipped_at')) {
                $table->timestamp('shipped_at')->nullable()->after('tracking_number');
            }
            if (!Schema::hasColumn('orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            }

            if (!Schema::hasColumn('orders', 'invoice_number')) {
                $table->string('invoice_number')->nullable()->unique()->after('delivered_at');
            }
            if (!Schema::hasColumn('orders', 'invoice_pdf_path')) {
                $table->string('invoice_pdf_path')->nullable()->after('invoice_number');
            }

            if (!Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable()->after('invoice_pdf_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'notes'))
                $table->dropColumn('notes');
            if (Schema::hasColumn('orders', 'invoice_pdf_path'))
                $table->dropColumn('invoice_pdf_path');
            if (Schema::hasColumn('orders', 'invoice_number'))
                $table->dropUnique(['invoice_number']);
            if (Schema::hasColumn('orders', 'invoice_number'))
                $table->dropColumn('invoice_number');
            if (Schema::hasColumn('orders', 'delivered_at'))
                $table->dropColumn('delivered_at');
            if (Schema::hasColumn('orders', 'shipped_at'))
                $table->dropColumn('shipped_at');
            if (Schema::hasColumn('orders', 'tracking_number'))
                $table->dropColumn('tracking_number');
            if (Schema::hasColumn('orders', 'shipping_carrier'))
                $table->dropColumn('shipping_carrier');
            if (Schema::hasColumn('orders', 'shipping_address'))
                $table->dropColumn('shipping_address');
            if (Schema::hasColumn('orders', 'payment_provider'))
                $table->dropColumn('payment_provider');
            if (Schema::hasColumn('orders', 'fulfillment_status'))
                $table->dropColumn('fulfillment_status');
            if (Schema::hasColumn('orders', 'shipping_cost'))
                $table->dropColumn('shipping_cost');
            if (Schema::hasColumn('orders', 'platform_fee'))
                $table->dropColumn('platform_fee');
            if (Schema::hasColumn('orders', 'seller_id'))
                $table->dropConstrainedForeignId('seller_id');
            if (Schema::hasColumn('orders', 'buyer_id'))
                $table->dropConstrainedForeignId('buyer_id');
        });
    }
};
