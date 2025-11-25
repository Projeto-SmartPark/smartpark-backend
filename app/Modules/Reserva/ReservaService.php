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
        return Reserva::with(['cliente', 'veiculo', 'vaga.estacionamento'])
            ->orderByRaw("CASE 
                WHEN status = 'ativa' THEN 1
                WHEN status = 'concluida' THEN 2
                ELSE 3
            END")
            ->orderBy('data', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->get();
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

    /**
     * Listar reservas por cliente
     */
    public function listarReservasPorCliente(int $clienteId)
    {
        return Reserva::with(['vaga.estacionamento.endereco'])
            ->where('cliente_id', $clienteId)
            ->orderByRaw("CASE 
                WHEN status = 'ativa' THEN 1
                WHEN status = 'concluida' THEN 2
                ELSE 3
            END")
            ->orderBy('data', 'desc')
            ->orderBy('hora_inicio', 'desc')
            ->get();
    }

    /**
     * Verificar disponibilidade de vaga
     */
    public function verificarDisponibilidade(int $vagaId, string $data, string $horaInicio, string $horaFim, ?int $reservaIdExcluir = null): bool
    {
        $query = Reserva::where('vaga_id', $vagaId)
            ->where('status', 'ativa')
            ->where('data', $data)
            ->where(function ($q) use ($horaInicio, $horaFim) {
                $q->where(function ($q2) use ($horaInicio, $horaFim) {
                    $q2->where('hora_inicio', '<=', $horaInicio)
                        ->where('hora_fim', '>', $horaInicio);
                })
                ->orWhere(function ($q2) use ($horaInicio, $horaFim) {
                    $q2->where('hora_inicio', '<', $horaFim)
                        ->where('hora_fim', '>=', $horaFim);
                })
                ->orWhere(function ($q2) use ($horaInicio, $horaFim) {
                    $q2->where('hora_inicio', '>=', $horaInicio)
                        ->where('hora_fim', '<=', $horaFim);
                });
            });

        if ($reservaIdExcluir) {
            $query->where('id_reserva', '!=', $reservaIdExcluir);
        }

        return $query->count() === 0;
    }

    /**
     * Cancelar reserva
     */
    public function cancelarReserva(int $id, int $clienteId): Reserva
    {
        $reserva = $this->buscarReservaPorId($id);

        if ($reserva->cliente_id != $clienteId) {
            throw new \Exception('Você não tem permissão para cancelar esta reserva');
        }

        if ($reserva->status !== 'ativa') {
            throw new \Exception('Esta reserva já foi cancelada ou concluída');
        }

        $reserva->status = 'cancelada';
        $reserva->save();

        // Liberar vaga
        if ($reserva->vaga) {
            $reserva->vaga->disponivel = 'S';
            $reserva->vaga->save();
        }

        return $reserva;
    }
}
