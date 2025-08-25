<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->nullable()->constrained('pesanan')->nullOnDelete();
            $table->string('order_id')->unique();
            $table->decimal('total_bayar', 10, 2);
            $table->enum('metode_pembayaran', ['cash', 'qris']);
            $table->enum('status_pembayaran', ['pending', 'dibayar', 'gagal'] )->default('pending');
            $table->enum('jenis_pesanan', ['dinein', 'takeaway'])->default('dinein');
            $table->unsignedBigInteger('nomor_meja')->nullable(); // Nomor meja untuk dine-in
            $table->string('nama_pelanggan')->nullable(); // Untuk takeaway
            $table->string('nomor_wa')->nullable(); // Untuk takeaway
            $table->date('tanggal_pesanan')->nullable(); // Tanggal pesanan
            $table->time('waktu_pesanan')->nullable();  // Waktu pesanan
            
            $table->timestamp('settlement_time')->nullable();
            
            $table->timestamps();
            
            // ✅ TAMBAHAN: Index untuk performa dan pooling
            $table->index(['status_pembayaran', 'metode_pembayaran']);
            $table->index(['order_id', 'metode_pembayaran']);
            $table->index('midtrans_transaction_id');
            $table->index(['tanggal_pesanan', 'waktu_pesanan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
