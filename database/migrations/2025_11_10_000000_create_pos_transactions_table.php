<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            $table->uuid('reference')->unique();
            $table->string('customer_name')->nullable();
            $table->string('customer_type');
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('shipping_cost')->default(0);
            $table->unsignedBigInteger('tip')->default(0);
            $table->unsignedBigInteger('expense_amount')->default(0);
            $table->unsignedBigInteger('discount')->default(0);
            $table->float('discount_percent')->default(0);
            $table->unsignedBigInteger('grand_total')->default(0);
            $table->unsignedBigInteger('payment_received')->default(0);
            $table->string('payment_method')->default('cash');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->text('shipping_address')->nullable();
            $table->unsignedBigInteger('balance_due')->default(0);
            $table->unsignedBigInteger('change_due')->default(0);
            $table->string('status')->default('pending');
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_transactions');
    }
};
