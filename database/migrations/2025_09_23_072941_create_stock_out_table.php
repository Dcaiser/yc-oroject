<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_out', function (Blueprint $table) {
            $table ->id();
            $table ->string('product_name');
            $table ->string('customer_name');
            $table ->string('customer_type');
            $table ->integer('stock_qty');
            $table ->string('satuan');
            $table ->decimal('prices');
            $table ->decimal('shipping_cost')->default(0);
            $table ->decimal('total_price');
            $table ->decimal('payment_received')->default(0);
            $table ->text('note')->nullable(0);
            $table ->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_out', function (Blueprint $table) {
            //
        });
    }
};
