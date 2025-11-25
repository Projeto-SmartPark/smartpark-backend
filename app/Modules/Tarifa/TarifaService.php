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

    /**
     * Desativar todas as tarifas de um estacionamento, exceto a especificada
     */
    public function desativarTarifasDoEstacionamento(int $estacionamentoId, ?int $exceto = null): void
    {
        $query = Tarifa::where('estacionamento_id', $estacionamentoId)
            ->where('ativa', 'S');
        
        if ($exceto) {
            $query->where('id_tarifa', '!=', $exceto);
        }
        
        $query->update(['ativa' => 'N']);
    }
}
