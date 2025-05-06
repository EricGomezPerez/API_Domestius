<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->integer('edat')->nullable();
            $table->string('especie');
            $table->string('raça')->nullable();
            $table->text('descripcio')->nullable();
            $table->string('estat')->default('disponible');
            $table->string('imatge')->nullable();
            $table->foreignId('usuari_id')->nullable()->constrained('usuaris')->onDelete('set null');
            $table->foreignId('publicacio_id')->nullable()->constrained('publicacions')->onDelete('set null');
            $table->foreignId('geolocalitzacio_id')->nullable()->constrained('geolocalitzacions')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('animals');
    }
};
