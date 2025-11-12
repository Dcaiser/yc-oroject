<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('suppliers')) {
            return;
        }

        Schema::table('suppliers', function (Blueprint $table) {
            if (! Schema::hasColumn('suppliers', 'supplier_code')) {
                $table->string('supplier_code')->nullable()->after('id');
            }

            if (! Schema::hasColumn('suppliers', 'contact_person')) {
                $table->string('contact_person')->nullable()->after('name');
            }

            if (! Schema::hasColumn('suppliers', 'phone')) {
                $table->string('phone')->nullable()->after('contact_person');
            }

            if (! Schema::hasColumn('suppliers', 'email')) {
                $table->string('email')->nullable()->after('phone');
            }

            if (! Schema::hasColumn('suppliers', 'address')) {
                $table->string('address')->nullable()->after('email');
            }

            if (! Schema::hasColumn('suppliers', 'npwp')) {
                $table->string('npwp')->nullable()->after('address');
            }
        });

        if (! Schema::hasColumn('suppliers', 'supplier_code')) {
            return;
        }

        $existing = DB::table('suppliers')
            ->select('id', 'supplier_code')
            ->orderBy('id')
            ->get();

        $counter = 1;
        foreach ($existing as $supplier) {
            if (! empty($supplier->supplier_code)) {
                continue;
            }

            $code = sprintf('SUP%03d', $counter++);
            while (DB::table('suppliers')->where('supplier_code', $code)->exists()) {
                $code = sprintf('SUP%03d', $counter++);
            }

            DB::table('suppliers')
                ->where('id', $supplier->id)
                ->update(['supplier_code' => $code]);
        }

        if (Schema::hasColumn('suppliers', 'supplier_code')) {
            $database = DB::getDatabaseName();
            $indexExists = DB::table('information_schema.statistics')
                ->where('table_schema', $database)
                ->where('table_name', 'suppliers')
                ->where('index_name', 'suppliers_supplier_code_unique')
                ->exists();

            if (! $indexExists) {
                Schema::table('suppliers', function (Blueprint $table) {
                    $table->unique('supplier_code');
                });
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('suppliers')) {
            return;
        }

        if (Schema::hasColumn('suppliers', 'supplier_code')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->dropColumn('supplier_code');
            });
        }
    }
};
