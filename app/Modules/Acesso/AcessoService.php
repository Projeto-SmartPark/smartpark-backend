<?php

namespace App\Modules\Acesso;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class AcessoService
{
    /**
     * Listar todos os acessos
     */
    public function listarAcessos()
    {
        return Acesso::with(['veiculo', 'vaga', 'cliente'])->get();
    }

    /**
     * Criar novo acesso
     * 
     * @param array $dados
     * @return Acesso
     */
    public function criarAcesso(array $dados): Acesso
    {
        return Acesso::create($dados);
    }

    /**
     * Buscar acesso por ID
     * 
     * @param int $id
     * @return Acesso
     * @throws ModelNotFoundException
     */
    public function buscarAcessoPorId(int $id): Acesso
    {
        return Acesso::with(['veiculo', 'vaga', 'cliente'])->findOrFail($id);
    }

    /**
     * Atualizar acesso
     * 
     * @param int $id
     * @param array $dados
     * @return Acesso
     * @throws ModelNotFoundException
     */
    public function atualizarAcesso(int $id, array $dados): Acesso
    {
        $acesso = $this->buscarAcessoPorId($id);
        $acesso->update($dados);
        return $acesso;
    }

    /**
     * Deletar acesso
     * 
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function deletarAcesso(int $id): bool
    {
        $acesso = $this->buscarAcessoPorId($id);
        return $acesso->delete();
    }
}
