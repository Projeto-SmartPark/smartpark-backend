<?php

namespace App\Modules\Estacionamento;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class EstacionamentoService
{
    /**
     * Listar todos os estacionamentos
     */
    public function listarEstacionamentos()
    {
        return Estacionamento::all();
    }

    /**
     * Criar novo estacionamento
     * 
     * @param array $dados
     * @return Estacionamento
     */
    public function criarEstacionamento(array $dados): Estacionamento
    {
        return Estacionamento::create($dados);
    }

    /**
     * Buscar estacionamento por ID
     * 
     * @param int $id
     * @return Estacionamento
     * @throws ModelNotFoundException
     */
    public function buscarEstacionamentoPorId(int $id): Estacionamento
    {
        return Estacionamento::findOrFail($id);
    }

    /**
     * Atualizar estacionamento
     * 
     * @param int $id
     * @param array $dados
     * @return Estacionamento
     * @throws ModelNotFoundException
     */
    public function atualizarEstacionamento(int $id, array $dados): Estacionamento
    {
        $estacionamento = $this->buscarEstacionamentoPorId($id);
        $estacionamento->update($dados);
        return $estacionamento;
    }

    /**
     * Deletar estacionamento
     * 
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function deletarEstacionamento(int $id): bool
    {
        $estacionamento = $this->buscarEstacionamentoPorId($id);
        return $estacionamento->delete();
    }
}
