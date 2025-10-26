<?php

namespace App\Modules\Veiculo;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class VeiculoService
{
    /**
     * Listar todos os veículos
     */
    public function listarVeiculos()
    {
        return Veiculo::with('cliente')->get();
    }

    /**
     * Criar novo veículo
     * 
     * @param array $dados
     * @return Veiculo
     */
    public function criarVeiculo(array $dados): Veiculo
    {
        return Veiculo::create($dados);
    }

    /**
     * Buscar veículo por ID
     * 
     * @param int $id
     * @return Veiculo
     * @throws ModelNotFoundException
     */
    public function buscarVeiculoPorId(int $id): Veiculo
    {
        return Veiculo::with('cliente')->findOrFail($id);
    }

    /**
     * Atualizar veículo
     * 
     * @param int $id
     * @param array $dados
     * @return Veiculo
     * @throws ModelNotFoundException
     */
    public function atualizarVeiculo(int $id, array $dados): Veiculo
    {
        $veiculo = $this->buscarVeiculoPorId($id);
        $veiculo->update($dados);
        return $veiculo;
    }

    /**
     * Deletar veículo
     * 
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function deletarVeiculo(int $id): bool
    {
        $veiculo = $this->buscarVeiculoPorId($id);
        return $veiculo->delete();
    }
}
