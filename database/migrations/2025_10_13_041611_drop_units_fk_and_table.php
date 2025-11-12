<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUnitsFkAndTable extends Migration
{
    public function up()
    {
        // Hapus foreign key di products jika ada
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'satuan')) {
            Schema::table('products', function (Blueprint $table) {
                // coba drop FK dengan nama default
                try {
                    $table->dropForeign(['satuan']);
                } catch (\Exception $e) {
                    // jika gagal karena nama FK berbeda, kita abaikan - nanti bisa drop manual
                }
                // jika ingin juga menghapus kolom satuan, uncomment:
                // $table->dropColumn('satuan');
            });
        }

        // lalu drop tabel units jika ada
        Schema::dropIfExists('units');
    }

    public function down()
    {
        // Jika perlu rollback, buat ulang tabel units (sederhana)
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('conversion_to_base')->default(1);
            $table->timestamps();
        });

        // Anda bisa menambahkan restore FK di sini bila diperlukan
    }
}
