<?php

namespace App\Modules\Estacionamento;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Modules\Telefone\TelefoneService;
use Illuminate\Support\Facades\DB;
use Throwable;

class EstacionamentoService
{
    private TelefoneService $telefoneService;

    public function __construct(TelefoneService $telefoneService)
    {
        $this->telefoneService = $telefoneService;
    }

    /**
     * Listar todos os estacionamentos
     */
    public function listarEstacionamentos()
    {
        return Estacionamento::with('telefones')->get();
    }

    /**
     * Criar novo estacionamento
     * 
     * @param array $dados
     * @param array $telefones
     * @return Estacionamento
     * @throws \Exception
     */
    public function criarEstacionamento(array $dados, array $telefones): Estacionamento
    {
        DB::beginTransaction();

        try {
            // Cria estacionamento
            $estacionamento = Estacionamento::create($dados);

            // Vincula telefones
            if (!empty($telefones)) {
                $this->telefoneService->vincularTelefonesAoEstacionamento(
                    $estacionamento->id_estacionamento,
                    $telefones
                );
            }

            DB::commit();
            return $estacionamento->load('telefones');

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
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
        return Estacionamento::with('telefones')->findOrFail($id);
    }

    /**
     * Atualizar estacionamento
     * 
     * @param int $id
     * @param array $dados
     * @param array $telefones
     * @return Estacionamento
     * @throws ModelNotFoundException
     */
    public function atualizarEstacionamento(int $id, array $dados, array $telefones): Estacionamento
    {
        DB::beginTransaction();

        try {
            $estacionamento = Estacionamento::findOrFail($id);
            $estacionamento->update($dados);

            // Atualiza telefones
            if (!empty($telefones)) {
                $this->telefoneService->atualizarTelefonesDoEstacionamento($id, $telefones);
            }

            DB::commit();
            return $estacionamento->load('telefones');

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
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
        DB::beginTransaction();

        try {
            $estacionamento = Estacionamento::findOrFail($id);

            // Deleta telefones vinculados
            $this->telefoneService->deletarTelefonesDoEstacionamento($id);

            // Deleta estacionamento
            $result = $estacionamento->delete();

            DB::commit();
            return $result;

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
