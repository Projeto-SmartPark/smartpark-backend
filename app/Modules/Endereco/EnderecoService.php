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
     */
    public function criarEndereco(array $dados): Endereco
    {
        return Endereco::create($dados);
    }

    /**
     * Buscar endereço por ID
     *
     * @throws ModelNotFoundException
     */
    public function buscarEnderecoPorId(int $id): Endereco
    {
        return Endereco::findOrFail($id);
    }

    /**
     * Atualizar endereço
     *
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
     * @throws ModelNotFoundException
     */
    public function deletarEndereco(int $id): bool
    {
        $endereco = $this->buscarEnderecoPorId($id);

        return $endereco->delete();
    }
}
