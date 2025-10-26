<?php

namespace App\Modules\Estacionamento;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Modules\Telefone\TelefoneService;
use App\Modules\Endereco\EnderecoService;
use Illuminate\Support\Facades\DB;
use Throwable;

class EstacionamentoService
{
    private TelefoneService $telefoneService;
    private EnderecoService $enderecoService;

    public function __construct(TelefoneService $telefoneService, EnderecoService $enderecoService)
    {
        $this->telefoneService = $telefoneService;
        $this->enderecoService = $enderecoService;
    }

    /**
     * Listar todos os estacionamentos
     */
    public function listarEstacionamentos()
    {
        return Estacionamento::with(['telefones', 'endereco'])->get();
    }

    /**
     * Criar novo estacionamento
     * 
     * @param array $dados
     * @param array $dadosEndereco
     * @param array $telefones
     * @return Estacionamento
     * @throws \Exception
     */
    public function criarEstacionamento(array $dados, array $dadosEndereco, array $telefones): Estacionamento
    {
        DB::beginTransaction();

        try {
            // 1. Cria endereço
            $endereco = $this->enderecoService->criarEndereco($dadosEndereco);
            
            // 2. Adiciona endereco_id aos dados do estacionamento
            $dados['endereco_id'] = $endereco->id_endereco;
            
            // 3. Cria estacionamento
            $estacionamento = Estacionamento::create($dados);

            // 4. Vincula telefones
            if (!empty($telefones)) {
                $this->telefoneService->vincularTelefonesAoEstacionamento(
                    $estacionamento->id_estacionamento,
                    $telefones
                );
            }

            DB::commit();
            return $estacionamento->load(['telefones', 'endereco']);

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
        return Estacionamento::with(['telefones', 'endereco'])->findOrFail($id);
    }

    /**
     * Atualizar estacionamento
     * 
     * @param int $id
     * @param array $dados
     * @param array $dadosEndereco
     * @param array $telefones
     * @return Estacionamento
     * @throws ModelNotFoundException
     */
    public function atualizarEstacionamento(int $id, array $dados, array $dadosEndereco, array $telefones): Estacionamento
    {
        DB::beginTransaction();

        try {
            $estacionamento = Estacionamento::findOrFail($id);
            
            // 1. Atualiza endereço
            if (!empty($dadosEndereco)) {
                $this->enderecoService->atualizarEndereco($estacionamento->endereco_id, $dadosEndereco);
            }
            
            // 2. Atualiza estacionamento (sem endereco_id nos dados)
            $estacionamento->update($dados);

            // 3. Atualiza telefones
            if (!empty($telefones)) {
                $this->telefoneService->atualizarTelefonesDoEstacionamento($id, $telefones);
            }

            DB::commit();
            return $estacionamento->load(['telefones', 'endereco']);

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
