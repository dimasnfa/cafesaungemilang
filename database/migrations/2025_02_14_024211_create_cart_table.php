<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
public function up()
{
    Schema::create('cart', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('menu_id');
        $table->unsignedBigInteger('meja_id')->nullable(); // NULL jika takeaway
        $table->string('nama_pelanggan')->nullable(); // Wajib diisi untuk takeaway
        $table->string('nomor_wa')->nullable(); // Wajib diisi untuk takeaway
        $table->unsignedInteger('qty'); // Tidak boleh negatif
        $table->string('jenis_pesanan'); // dinein / takeaway
        $table->date('tanggal_pesanan')->nullable(); // Khusus takeaway
        $table->time('waktu_pesanan')->nullable(); // Khusus takeaway
        $table->timestamps();

        // Foreign Keys
        $table->foreign('menu_id')->references('id')->on('menu')->onDelete('cascade');
        $table->foreign('meja_id')->references('id')->on('meja')->onDelete('set null');
    });
}

public function down()
{
    Schema::dropIfExists('cart');
}
};
