<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migration untuk menambahkan field dan index Midtrans tanpa merusak data existing
     */
    public function up(): void
    {
        // ✅ UPDATE TABEL PESANAN
        Schema::table('pesanan', function (Blueprint $table) {
            // Tambah field untuk tracking Midtrans order ID
            $table->string('midtrans_order_id')->nullable()->unique()->after('metode_pembayaran');
            
            // Tambah index untuk performa
            $table->index(['status_pesanan', 'metode_pembayaran'], 'idx_pesanan_status_metode');
            $table->index(['tanggal_pesanan', 'waktu_pesanan'], 'idx_pesanan_tanggal_waktu');
            $table->index('midtrans_order_id', 'idx_pesanan_midtrans_order');
        });

        // ✅ UPDATE TABEL PEMBAYARAN  
        Schema::table('pembayaran', function (Blueprint $table) {
            // Tambah field untuk tracking Midtrans
            $table->string('midtrans_transaction_id')->nullable()->after('waktu_pesanan');
            $table->string('midtrans_payment_type')->nullable()->after('midtrans_transaction_id');
            $table->timestamp('settlement_time')->nullable()->after('midtrans_payment_type');
            
            // Tambah index untuk performa dan pooling
            $table->index(['status_pembayaran', 'metode_pembayaran'], 'idx_pembayaran_status_metode');
            $table->index(['order_id', 'metode_pembayaran'], 'idx_pembayaran_order_metode');
            $table->index('midtrans_transaction_id', 'idx_pembayaran_midtrans_trx');
            $table->index(['tanggal_pesanan', 'waktu_pesanan'], 'idx_pembayaran_tanggal_waktu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ✅ ROLLBACK TABEL PESANAN
        Schema::table('pesanan', function (Blueprint $table) {
            // Drop index dulu
            $table->dropIndex('idx_pesanan_status_metode');
            $table->dropIndex('idx_pesanan_tanggal_waktu');
            $table->dropIndex('idx_pesanan_midtrans_order');
            
            // Drop column
            $table->dropColumn('midtrans_order_id');
        });

        // ✅ ROLLBACK TABEL PEMBAYARAN
        Schema::table('pembayaran', function (Blueprint $table) {
            // Drop index dulu
            $table->dropIndex('idx_pembayaran_status_metode');
            $table->dropIndex('idx_pembayaran_order_metode');
            $table->dropIndex('idx_pembayaran_midtrans_trx');
            $table->dropIndex('idx_pembayaran_tanggal_waktu');
            
            // Drop columns
            $table->dropColumn([
                'midtrans_transaction_id',
                'midtrans_payment_type', 
                'settlement_time'
            ]);
        });
    }
};