<?php

namespace App\Modules\Usuarios\Services;

use App\Modules\Usuarios\Models\Cliente;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClienteService
{
    /**
     * Lista todos os clientes
     */
    public function listarTodos()
    {
        return Cliente::all();
    }

    /**
     * Busca um cliente por ID
     * 
     * @param int $id
     * @return Cliente
     * @throws ModelNotFoundException
     */
    public function buscarPorId(int $id): Cliente
    {
        return Cliente::findOrFail($id);
    }

    /**
     * Atualiza os dados de um cliente
     * 
     * @param int $id
     * @param array $dados
     * @return Cliente
     * @throws \Exception
     */
    public function atualizar(int $id, array $dados): Cliente
    {
        $cliente = $this->buscarPorId($id);

        // Valida se o email não está sendo usado por outro cliente
        $this->validarEmailUnico($dados['email'], $id);

        $cliente->update($dados);

        return $cliente;
    }

    /**
     * Remove um cliente
     * 
     * @param int $id
     * @return bool
     */
    public function remover(int $id): bool
    {
        return Cliente::destroy($id) > 0;
    }

    /**
     * Valida se o email já está sendo usado por outro cliente
     * 
     * @param string $email
     * @param int $idExcluir
     * @throws \Exception
     */
    private function validarEmailUnico(string $email, int $idExcluir): void
    {
        $existe = Cliente::where('email', $email)
            ->where('id_cliente', '!=', $idExcluir)
            ->exists();

        if ($existe) {
            throw new \Exception('Já existe outro cliente com este email.');
        }
    }
}
