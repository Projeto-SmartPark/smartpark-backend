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
        Schema::create('estacionamentos', function (Blueprint $table) {
            $table->id('id_estacionamento');
            $table->string('nome', 100);
            $table->integer('capacidade');
            $table->time('hora_abertura');
            $table->time('hora_fechamento');
            $table->enum('lotado', ['S', 'N'])->default('N');
            $table->unsignedBigInteger('gestor_id');
            $table->unsignedBigInteger('endereco_id');
            
            $table->foreign('gestor_id')->references('id_gestor')->on('gestores')->onDelete('cascade');
            $table->foreign('endereco_id')->references('id_endereco')->on('enderecos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estacionamentos');
    }
};
