<?php

namespace App\Modules\Telefone;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class TelefoneService
{
    /**
     * Listar todos os telefones
     */
    public function listarTelefones()
    {
        return Telefone::all();
    }

    /**
     * Criar novo telefone
     * 
     * @param array $dados
     * @return Telefone
     */
    public function criarTelefone(array $dados): Telefone
    {
        return Telefone::create($dados);
    }

    /**
     * Buscar telefone por ID
     * 
     * @param int $id
     * @return Telefone
     * @throws ModelNotFoundException
     */
    public function buscarTelefonePorId(int $id): Telefone
    {
        return Telefone::findOrFail($id);
    }

    /**
     * Atualizar telefone
     * 
     * @param int $id
     * @param array $dados
     * @return Telefone
     * @throws ModelNotFoundException
     */
    public function atualizarTelefone(int $id, array $dados): Telefone
    {
        $telefone = $this->buscarTelefonePorId($id);
        $telefone->update($dados);
        return $telefone;
    }

    /**
     * Deletar telefone
     * 
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function deletarTelefone(int $id): bool
    {
        $telefone = $this->buscarTelefonePorId($id);
        return $telefone->delete();
    }

    /**
     * Criar e vincular telefones a um estacionamento
     * 
     * @param int $estacionamentoId
     * @param array $telefones
     * @return array
     */
    public function vincularTelefonesAoEstacionamento(int $estacionamentoId, array $telefones): array
    {
        $telefonesIds = [];

        foreach ($telefones as $telefoneData) {
            $telefone = $this->criarTelefone($telefoneData);
            $telefonesIds[] = $telefone->id_telefone;

            // Vincular na tabela de relacionamento
            DB::table('estacionamento_telefones')->insert([
                'id_estacionamento' => $estacionamentoId,
                'id_telefone' => $telefone->id_telefone
            ]);
        }

        return $telefonesIds;
    }

    /**
     * Atualizar telefones de um estacionamento
     * 
     * @param int $estacionamentoId
     * @param array $telefones
     * @return array
     */
    public function atualizarTelefonesDoEstacionamento(int $estacionamentoId, array $telefones): array
    {
        // Remove vínculos antigos
        $telefonesAntigos = DB::table('estacionamento_telefones')
            ->where('id_estacionamento', $estacionamentoId)
            ->pluck('id_telefone')
            ->toArray();

        // Deleta os telefones antigos e vínculos
        DB::table('estacionamento_telefones')
            ->where('id_estacionamento', $estacionamentoId)
            ->delete();

        foreach ($telefonesAntigos as $telefoneId) {
            Telefone::where('id_telefone', $telefoneId)->delete();
        }

        // Cria e vincula novos telefones
        return $this->vincularTelefonesAoEstacionamento($estacionamentoId, $telefones);
    }

    /**
     * Buscar telefones de um estacionamento
     * 
     * @param int $estacionamentoId
     * @return array
     */
    public function buscarTelefonesDoEstacionamento(int $estacionamentoId): array
    {
        return DB::table('telefones')
            ->join('estacionamento_telefones', 'telefones.id_telefone', '=', 'estacionamento_telefones.id_telefone')
            ->where('estacionamento_telefones.id_estacionamento', $estacionamentoId)
            ->select('telefones.*')
            ->get()
            ->toArray();
    }

    /**
     * Deletar telefones de um estacionamento
     * 
     * @param int $estacionamentoId
     * @return bool
     */
    public function deletarTelefonesDoEstacionamento(int $estacionamentoId): bool
    {
        $telefonesIds = DB::table('estacionamento_telefones')
            ->where('id_estacionamento', $estacionamentoId)
            ->pluck('id_telefone')
            ->toArray();

        // Remove vínculos
        DB::table('estacionamento_telefones')
            ->where('id_estacionamento', $estacionamentoId)
            ->delete();

        // Remove telefones
        foreach ($telefonesIds as $telefoneId) {
            Telefone::where('id_telefone', $telefoneId)->delete();
        }

        return true;
    }
}
