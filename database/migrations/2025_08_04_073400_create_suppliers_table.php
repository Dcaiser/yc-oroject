<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration (buat tabel suppliers).
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_code')->unique(); // kode unik supplier
            $table->string('name');                    // nama supplier
            $table->string('contact_person')->nullable(); // orang yang bisa dihubungi
            $table->string('phone')->nullable();       // nomor telepon
            $table->string('email')->nullable();       // email supplier
            $table->string('address')->nullable();     // alamat
            $table->string('npwp')->nullable();        // nomor NPWP (opsional)
            $table->timestamps();
        });
    }

    /**
     * Rollback (hapus tabel suppliers).
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
