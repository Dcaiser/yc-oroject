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
            $table->index('created_at');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->index('expense_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_transactions', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['expense_date']);
        });
    }
};
