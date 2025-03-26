<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('tipus_interaccions', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->timestamps();
        });

        Schema::table('interaccions', function (Blueprint $table) {
            $table->foreignId('tipus_interaccio_id')->constrained('tipus_interaccions')->onDelete('cascade');
        });
    }

    public function down()
    {
    }
};
