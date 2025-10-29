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
        Schema::create('enderecos', function (Blueprint $table) {
            $table->id('id_endereco');
            $table->string('cep', 8);
            $table->char('estado', 2);
            $table->string('cidade', 80);
            $table->string('bairro', 80);
            $table->string('numero', 10);
            $table->string('logradouro', 120);
            $table->string('complemento', 100)->nullable();
            $table->string('ponto_referencia', 100)->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enderecos');
    }
};
