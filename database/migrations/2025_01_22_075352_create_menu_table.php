<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id(); // Primary key 'id'
            $table->string('nama_menu');
            $table->decimal('harga', 10, 2);
            $table->integer('stok');
            $table->unsignedBigInteger('id_kategori'); // Foreign key ke kategori
            $table->timestamps();

            // Foreign key ke tabel kategori
            $table->foreign('id_kategori')->references('id')->on('kategori')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('menu');
    }
};
