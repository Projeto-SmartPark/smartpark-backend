<?php

namespace App\Modules\Vaga;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class VagaService
{
    /**
     * Listar todas as vagas
     */
    public function listarVagas()
    {
        return Vaga::with('estacionamento')->get();
    }

    /**
     * Criar nova vaga
     */
    public function criarVaga(array $dados): Vaga
    {
        return Vaga::create($dados);
    }

    /**
     * Buscar vaga por ID
     *
     * @throws ModelNotFoundException
     */
    public function buscarVagaPorId(int $id): Vaga
    {
        return Vaga::with('estacionamento')->findOrFail($id);
    }

    /**
     * Atualizar vaga
     *
     * @throws ModelNotFoundException
     */
    public function atualizarVaga(int $id, array $dados): Vaga
    {
        $vaga = $this->buscarVagaPorId($id);
        $vaga->update($dados);

        return $vaga;
    }

    /**
     * Deletar vaga
     *
     * @throws ModelNotFoundException
     */
    public function deletarVaga(int $id): bool
    {
        $vaga = $this->buscarVagaPorId($id);

        return $vaga->delete();
    }
}
