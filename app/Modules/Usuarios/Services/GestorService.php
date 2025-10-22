<?php

namespace App\Modules\Usuarios\Services;

use App\Modules\Usuarios\Models\Gestor;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GestorService
{
    /**
     * Lista todos os gestores
     */
    public function listarTodos()
    {
        return Gestor::all();
    }

    /**
     * Busca um gestor por ID
     * 
     * @param int $id
     * @return Gestor
     * @throws ModelNotFoundException
     */
    public function buscarPorId(int $id): Gestor
    {
        return Gestor::findOrFail($id);
    }

    /**
     * Atualiza os dados de um gestor
     * 
     * @param int $id
     * @param array $dados
     * @return Gestor
     * @throws \Exception
     */
    public function atualizar(int $id, array $dados): Gestor
    {
        $gestor = $this->buscarPorId($id);

        // Valida se o email não está sendo usado por outro gestor
        $this->validarEmailUnico($dados['email'], $id);

        $gestor->update($dados);

        return $gestor;
    }

    /**
     * Remove um gestor
     * 
     * @param int $id
     * @return bool
     */
    public function remover(int $id): bool
    {
        return Gestor::destroy($id) > 0;
    }

    /**
     * Valida se o email já está sendo usado por outro gestor
     * 
     * @param string $email
     * @param int $idExcluir
     * @throws \Exception
     */
    private function validarEmailUnico(string $email, int $idExcluir): void
    {
        $existe = Gestor::where('email', $email)
            ->where('id_gestor', '!=', $idExcluir)
            ->exists();

        if ($existe) {
            throw new \Exception('Já existe outro gestor com este email.');
        }
    }
}
