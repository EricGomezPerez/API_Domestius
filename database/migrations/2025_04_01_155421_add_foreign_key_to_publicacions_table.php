<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Añadir restricción de clave foránea a la tabla publicacions
        Schema::table('publicacions', function (Blueprint $table) {
            $table->foreign('animal_id')->references('id')->on('animals')->onDelete('cascade');
        });
        
        // También puedes asegurarte de que la restricción en la tabla animals esté correcta
        // Si ya existe en la migración original de animals, no es necesario añadir esta parte
        /*
        Schema::table('animals', function (Blueprint $table) {
            $table->foreign('publicacio_id')->references('id')->on('publicacions')->onDelete('set null');
        });
        */
    }

    public function down()
    {
        // Eliminar las restricciones añadidas
        Schema::table('publicacions', function (Blueprint $table) {
            $table->dropForeign(['animal_id']);
        });
        
        /*
        Schema::table('animals', function (Blueprint $table) {
            $table->dropForeign(['publicacio_id']);
        });
        */
    }
};