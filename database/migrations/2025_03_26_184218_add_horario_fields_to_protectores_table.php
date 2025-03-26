<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('protectores', function (Blueprint $table) {
            $table->time('horario_apertura')->nullable();
            $table->time('horario_cierre')->nullable();
        });
    }

    public function down()
    {
        Schema::table('protectores', function (Blueprint $table) {
            $table->dropColumn(['horario_apertura', 'horario_cierre']);
        });
    }
};