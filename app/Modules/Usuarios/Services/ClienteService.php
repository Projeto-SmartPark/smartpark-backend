<?php

namespace App\Modules\Usuarios\Services;

use App\Modules\Usuarios\Models\Cliente;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Throwable;

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
     * Cria um novo cliente
     * 
     * @param array $dados
     * @return Cliente
     * @throws \Exception
     */
    public function criarCliente(array $dados): Cliente
    {
        // Valida se o email já está cadastrado
        $this->validarEmailUnico($dados['email']);

        DB::beginTransaction();

        try {
            // 1. Cria usuário base
            $usuarioId = DB::table('usuarios')->insertGetId(['perfil' => 'C']);
            
            // 2. Cria cliente
            $cliente = Cliente::create([
                'id_cliente' => $usuarioId,
                'nome' => $dados['nome'],
                'email' => $dados['email'],
                'senha' => $dados['senha'],
            ]);
            
            DB::commit();
            return $cliente;

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
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
     * @param int|null $idExcluir
     * @throws \Exception
     */
    private function validarEmailUnico(string $email, ?int $idExcluir = null): void
    {
        $query = Cliente::where('email', $email);
        
        if ($idExcluir !== null) {
            $query->where('id_cliente', '!=', $idExcluir);
        }

        if ($query->exists()) {
            throw new \Exception($idExcluir === null 
                ? 'Já existe um cliente com este email.' 
                : 'Já existe outro cliente com este email.');
        }
    }
}
