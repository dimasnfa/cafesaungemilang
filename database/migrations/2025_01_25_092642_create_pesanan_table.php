<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meja_id')->nullable(); // NULL untuk takeaway
            $table->string('nama_pelanggan')->nullable(); // Wajib diisi untuk takeaway
            $table->string('nomor_wa')->nullable(); // Wajib diisi untuk takeaway
            $table->integer('total_harga');
            $table->string('status_pesanan')->default('pending');
            $table->string('jenis_pesanan')->nullable(); // Otomatis diisi
            $table->date('tanggal_pesanan');
            $table->time('waktu_pesanan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pesanan');
    }
};
