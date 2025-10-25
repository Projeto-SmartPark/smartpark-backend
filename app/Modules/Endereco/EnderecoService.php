<?php

namespace App\Modules\Endereco;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class EnderecoService
{
    /**
     * Listar todos os endereços
     */
    public function listarEnderecos()
    {
        return Endereco::all();
    }

    /**
     * Criar novo endereço
     * 
     * @param array $dados
     * @return Endereco
     */
    public function criarEndereco(array $dados): Endereco
    {
        return Endereco::create($dados);
    }

    /**
     * Buscar endereço por ID
     * 
     * @param int $id
     * @return Endereco
     * @throws ModelNotFoundException
     */
    public function buscarEnderecoPorId(int $id): Endereco
    {
        return Endereco::findOrFail($id);
    }

    /**
     * Atualizar endereço
     * 
     * @param int $id
     * @param array $dados
     * @return Endereco
     * @throws ModelNotFoundException
     */
    public function atualizarEndereco(int $id, array $dados): Endereco
    {
        $endereco = $this->buscarEnderecoPorId($id);
        $endereco->update($dados);
        return $endereco;
    }

    /**
     * Deletar endereço
     * 
     * @param int $id
     * @return bool
     * @throws ModelNotFoundException
     */
    public function deletarEndereco(int $id): bool
    {
        $endereco = $this->buscarEnderecoPorId($id);
        return $endereco->delete();
    }
}
