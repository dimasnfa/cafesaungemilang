<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('meja', function (Blueprint $table) {
            $table->id();
            $table->integer('nomor_meja');
            $table->enum('tipe_meja', ['lesehan', 'meja cafe']);
            $table->enum('lantai', ['1', '2']);
            $table->enum('status', ['tersedia', 'terisi', 'dibersihkan'])->default('tersedia');
            $table->string('qr_code')->nullable(); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('meja');
    }
};
