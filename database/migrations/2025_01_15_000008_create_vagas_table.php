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
        Schema::create('vagas', function (Blueprint $table) {
            $table->id('id_vaga');
            $table->string('identificacao', 20);
            $table->enum('tipo', ['carro', 'moto', 'deficiente', 'idoso', 'eletrico', 'outro']);
            $table->enum('disponivel', ['S', 'N'])->default('S');
            $table->unsignedBigInteger('estacionamento_id');

            $table->foreign('estacionamento_id')
                ->references('id_estacionamento')
                ->on('estacionamentos')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unique(['estacionamento_id', 'identificacao'], 'uq_identificacao_estacionamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vagas');
    }
};
