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
        Schema::create('activities', function (Blueprint $table) {
           $table->id();
        $table->string('user')->nullable();     // siapa yang melakukan
        $table->string('action');              // aksi apa (tambah, edit, hapus)
        $table->string('model')->nullable();   // model/table apa
        $table->unsignedBigInteger('record_id')->nullable(); // id data terkait
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
