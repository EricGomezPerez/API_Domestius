<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('protectores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuari_id')->constrained('usuaris')->onDelete('cascade');
            $table->string('direccion');
            $table->string('telefono');
            $table->boolean('verificada')->default(false);
            $table->string('imatge')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('protectores');
    }
};
