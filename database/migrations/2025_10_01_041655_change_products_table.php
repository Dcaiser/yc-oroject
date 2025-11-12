<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('products') || ! Schema::hasColumn('products', 'satuan')) {
            return;
        }

        if (! Schema::hasTable('units')) {
            return;
        }

        $existingUnits = DB::table('products')
            ->whereNotNull('satuan')
            ->select('satuan')
            ->distinct()
            ->pluck('satuan');

        foreach ($existingUnits as $value) {
            if ($value === '') {
                continue;
            }

            if (is_numeric($value)) {
                $unitExists = DB::table('units')->where('id', (int) $value)->exists();

                if (! $unitExists) {
                    DB::table('units')->insert([
                        'id' => (int) $value,
                        'name' => 'Unit ' . $value,
                        'conversion_to_base' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                continue;
            }

            $unitId = DB::table('units')->where('name', $value)->value('id');

            if (! $unitId) {
                $unitId = DB::table('units')->insertGetId([
                    'name' => $value,
                    'conversion_to_base' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('products')
                ->where('satuan', $value)
                ->update(['satuan' => $unitId]);
        }

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('satuan')->nullable()->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('satuan')->references('id')->on('units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
