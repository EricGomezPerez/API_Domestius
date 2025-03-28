<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('publicacions', function (Blueprint $table) {
            $table->id();
            $table->string('tipus'); // Perdut o Adopció
            $table->date('data');
            $table->text('detalls');
            $table->foreignId('usuari_id')->constrained('usuaris')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('interaccions');
        Schema::dropIfExists('publicacions');
    }
};
