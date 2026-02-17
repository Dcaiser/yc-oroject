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
        Schema::table('pos_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('pos_transactions', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            }

            // Ensure money columns are big integers if they exist
            if (Schema::hasColumn('pos_transactions', 'expense_amount')) {
                $table->unsignedBigInteger('expense_amount')->default(0)->change();
            }
            if (Schema::hasColumn('pos_transactions', 'discount')) {
                $table->unsignedBigInteger('discount')->default(0)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('pos_transactions', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });
    }
};
