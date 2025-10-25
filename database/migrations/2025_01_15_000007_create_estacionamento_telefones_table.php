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
        Schema::create('estacionamento_telefones', function (Blueprint $table) {
            $table->unsignedBigInteger('id_estacionamento');
            $table->unsignedBigInteger('id_telefone');
            
            $table->primary(['id_estacionamento', 'id_telefone']);
            
            $table->foreign('id_estacionamento')
                  ->references('id_estacionamento')
                  ->on('estacionamentos')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
                  
            $table->foreign('id_telefone')
                  ->references('id_telefone')
                  ->on('telefones')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estacionamento_telefones');
    }
};
