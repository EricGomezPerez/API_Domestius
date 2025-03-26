<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('missatges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('remitent_id')->constrained('usuaris')->onDelete('cascade');
            $table->foreignId('destinatari_id')->constrained('usuaris')->onDelete('cascade');
            $table->text('contingut');
            $table->timestamp('data')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('missatges');
    }
};
