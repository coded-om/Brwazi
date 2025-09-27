<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            if (!Schema::hasColumn('artworks', 'type')) {
                $table->string('type')->default('art')->after('category');
            }
            if (!Schema::hasColumn('artworks', 'medium')) {
                $table->string('medium')->nullable()->after('type');
            }
            if (!Schema::hasColumn('artworks', 'weight')) {
                $table->decimal('weight', 8, 2)->nullable()->after('dimensions');
            }
            if (!Schema::hasColumn('artworks', 'sale_mode')) {
                $table->string('sale_mode')->default('display')->after('price');
            }
            if (!Schema::hasColumn('artworks', 'allow_offers')) {
                $table->boolean('allow_offers')->default(false)->after('sale_mode');
            }
            if (!Schema::hasColumn('artworks', 'edition_type')) {
                $table->string('edition_type')->nullable()->after('allow_offers');
            }
            if (!Schema::hasColumn('artworks', 'copy_digital')) {
                $table->boolean('copy_digital')->default(false)->after('edition_type');
            }
            if (!Schema::hasColumn('artworks', 'copy_printed')) {
                $table->boolean('copy_printed')->default(false)->after('copy_digital');
            }
            if (!Schema::hasColumn('artworks', 'auction_start_price')) {
                $table->decimal('auction_start_price', 10, 2)->nullable()->after('copy_printed');
            }
        });
    }

    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            // Drop added columns in reverse order if they exist
            foreach ([
                'auction_start_price',
                'copy_printed',
                'copy_digital',
                'edition_type',
                'allow_offers',
                'sale_mode',
                'weight',
                'medium',
                'type'
            ] as $col) {
                if (Schema::hasColumn('artworks', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
