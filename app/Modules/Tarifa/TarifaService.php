<?php

namespace App\Modules\Tarifa;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class TarifaService
{
    /**
     * Listar todas as tarifas
     */
    public function listarTarifas()
    {
        return Tarifa::with('estacionamento')->get();
    }

    /**
     * Criar nova tarifa
     */
    public function criarTarifa(array $dados): Tarifa
    {
        return Tarifa::create($dados);
    }

    /**
     * Buscar tarifa por ID
     *
     * @throws ModelNotFoundException
     */
    public function buscarTarifaPorId(int $id): Tarifa
    {
        return Tarifa::with('estacionamento')->findOrFail($id);
    }

    /**
     * Atualizar tarifa
     *
     * @throws ModelNotFoundException
     */
    public function atualizarTarifa(int $id, array $dados): Tarifa
    {
        $tarifa = $this->buscarTarifaPorId($id);
        $tarifa->update($dados);

        return $tarifa;
    }

    /**
     * Deletar tarifa
     *
     * @throws ModelNotFoundException
     */
    public function deletarTarifa(int $id): bool
    {
        $tarifa = $this->buscarTarifaPorId($id);

        return $tarifa->delete();
    }
}
