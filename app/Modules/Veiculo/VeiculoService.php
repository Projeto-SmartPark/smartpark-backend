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
     */
    public function criarVeiculo(array $dados): Veiculo
    {
        return Veiculo::create($dados);
    }

    /**
     * Buscar veículo por ID
     *
     * @throws ModelNotFoundException
     */
    public function buscarVeiculoPorId(int $id): Veiculo
    {
        return Veiculo::with('cliente')->findOrFail($id);
    }

    /**
     * Atualizar veículo
     *
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
     * @throws ModelNotFoundException
     */
    public function deletarVeiculo(int $id): bool
    {
        $veiculo = $this->buscarVeiculoPorId($id);

        return $veiculo->delete();
    }
}
