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
        Schema::create('tarifas', function (Blueprint $table) {
            $table->id('id_tarifa');
            $table->string('nome', 100);
            $table->decimal('valor', 7, 2);
            $table->enum('tipo', ['segundo', 'minuto', 'hora', 'diaria', 'mensal']);
            $table->unsignedBigInteger('estacionamento_id');

            $table->foreign('estacionamento_id')
                ->references('id_estacionamento')
                ->on('estacionamentos')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarifas');
    }
};
