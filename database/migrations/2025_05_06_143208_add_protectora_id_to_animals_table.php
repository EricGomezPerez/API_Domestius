<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('animals', function (Blueprint $table) {
            $table->unsignedBigInteger('protectora_id')->nullable();
            $table->foreign('protectora_id')->references('id')->on('protectores')->onDelete('set null');
            
            // Modificar la columna usuari_id para que sea nullable
            $table->unsignedBigInteger('usuari_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('animals', function (Blueprint $table) {
            $table->dropForeign(['protectora_id']);
            $table->dropColumn('protectora_id');
            
            // Revertir el cambio en usuari_id
            $table->unsignedBigInteger('usuari_id')->change();
        });
    }
};