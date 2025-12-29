<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('pos_transactions', function (Blueprint $table) {
        $table->string('payment_method')->default('cash')->after('payment_received');
        $table->string('bank_name')->nullable()->after('payment_method');
        $table->string('account_number')->nullable()->after('bank_name');
        $table->text('shipping_address')->nullable()->after('note');
        $table->integer('expense_amount')->default(0)->after('tip');
        $table->integer('discount')->default(0)->after('expense_amount');
        $table->decimal('discount_percent', 5, 2)->default(0)->after('discount');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_transactions', function (Blueprint $table) {
            //
        });
    }
};
