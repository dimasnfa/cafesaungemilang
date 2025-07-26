<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('detailpesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanan')->onDelete('cascade'); // Relasi dengan pesanan
            $table->foreignId('menu_id')->constrained('menu')->onDelete('cascade'); // Relasi dengan menu
            $table->integer('jumlah')->unsigned(); // Jumlah menu yang dipesan
            $table->decimal('subtotal', 10, 2); // Subtotal harga
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('detailpesanan');
    }
};
