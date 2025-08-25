<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rekomendasi_menu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_id'); 
            $table->json('recommended_menu_ids');  
            $table->timestamps();

            $table->foreign('menu_id')->references('id')->on('menu')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rekomendasi_menu');
    }
};
