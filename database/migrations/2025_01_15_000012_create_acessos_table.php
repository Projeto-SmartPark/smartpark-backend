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
        Schema::create('acessos', function (Blueprint $table) {
            $table->id('id_acesso');
            $table->date('data');
            $table->time('hora_inicio');
            $table->time('hora_fim')->nullable();
            $table->decimal('valor_total', 8, 2)->nullable();
            $table->unsignedBigInteger('veiculo_id');
            $table->unsignedBigInteger('vaga_id');
            $table->unsignedBigInteger('cliente_id');

            $table->foreign('veiculo_id')
                  ->references('id_veiculo')
                  ->on('veiculos')
                  ->onDelete('cascade');

            $table->foreign('vaga_id')
                  ->references('id_vaga')
                  ->on('vagas')
                  ->onDelete('cascade');

            $table->foreign('cliente_id')
                  ->references('id_cliente')
                  ->on('clientes')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acessos');
    }
};
