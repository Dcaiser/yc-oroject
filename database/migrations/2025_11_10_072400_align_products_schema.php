<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->after('name');
            }

            if (! Schema::hasColumn('products', 'price')) {
                $table->decimal('price', 15, 2)->default(0)->after('sku');
            }

            if (! Schema::hasColumn('products', 'stock_quantity')) {
                $table->integer('stock_quantity')->default(0)->after('price');
            }

            if (! Schema::hasColumn('products', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('stock_quantity');
            }

            if (! Schema::hasColumn('products', 'supplier_id')) {
                $table->unsignedBigInteger('supplier_id')->nullable()->after('category_id');
            }

            if (! Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable()->after('supplier_id');
            }

            if (! Schema::hasColumn('products', 'satuan')) {
                $table->string('satuan')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        // Intentionally left blank to avoid dropping columns that may be required by existing data.
    }
};
