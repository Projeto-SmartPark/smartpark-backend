<?php

namespace App\Modules\Reserva;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReservaService
{
    /**
     * Listar todas as reservas
     */
    public function listarReservas()
    {
        return Reserva::with(['cliente', 'veiculo', 'vaga'])->get();
    }

    /**
     * Criar nova reserva
     * 
     * @param array $dados
     * @return Reserva
     */
    public function criarReserva(array $dados): Reserva
    {
        return Reserva::create($dados);
    }

    /**
     * Buscar reserva por ID
     * 
     * @param int $id
     * @return Reserva
     * @throws ModelNotFoundException
     */
    public function buscarReservaPorId(int $id): Reserva
    {
        return Reserva::with(['cliente', 'veiculo', 'vaga'])->findOrFail($id);
    }

    /**
     * Atualizar reserva
     * 
     * @param int $id
     * @param array $dados
     * @return Reserva
     * @throws ModelNotFoundException
     */
    public function atualizarReserva(int $id, array $dados): Reserva
    {
        $reserva = $this->buscarReservaPorId($id);
        $reserva->update($dados);
        return $reserva;
    }

    /**
     * Deletar reserva
     * 
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function deletarReserva(int $id): bool
    {
        $reserva = $this->buscarReservaPorId($id);
        return $reserva->delete();
    }
}
