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
     */
    public function criarReserva(array $dados): Reserva
    {
        return Reserva::create($dados);
    }

    /**
     * Buscar reserva por ID
     *
     * @throws ModelNotFoundException
     */
    public function buscarReservaPorId(int $id): Reserva
    {
        return Reserva::with(['cliente', 'veiculo', 'vaga'])->findOrFail($id);
    }

    /**
     * Atualizar reserva
     *
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
     * @throws ModelNotFoundException
     */
    public function deletarReserva(int $id): bool
    {
        $reserva = $this->buscarReservaPorId($id);

        return $reserva->delete();
    }
}
