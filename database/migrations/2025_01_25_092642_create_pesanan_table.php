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
            $table->enum('metode_pembayaran', ['cash', 'qris'])->nullable();
              //  TAMBAHAN: Field untuk tracking Midtrans order ID
            $table->string('midtrans_order_id')->nullable()->unique();
            $table->timestamps();
            
            //  TAMBAHAN: Index untuk performa
            $table->index(['status_pesanan', 'metode_pembayaran']);
            $table->index(['tanggal_pesanan', 'waktu_pesanan']);
            $table->index('midtrans_order_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pesanan');
    }
};