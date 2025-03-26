<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('interaccions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuari_id')->constrained('usuaris')->onDelete('cascade');
            $table->string('accio');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('interaccions');
        Schema::dropIfExists('tipus_interaccions');
    }
};
