<?php

namespace App\Modules\Usuarios\Services;

use App\Modules\Usuarios\Models\Cliente;
use App\Modules\Usuarios\Models\Gestor;
use Illuminate\Support\Facades\DB;
use Throwable;

class UsuarioService
{
    /**
     * Lista todos os usuários (clientes e gestores)
     */
    public function listarTodos(): array
    {
        return [
            'clientes' => Cliente::all(),
            'gestores' => Gestor::all(),
        ];
    }

    /**
     * Cria um novo usuário (cliente ou gestor)
     * 
     * @param array $dados
     * @return array
     * @throws \Exception
     */
    public function criar(array $dados): array
    {
        // Verifica se email já existe
        $this->validarEmailUnico($dados['email'], $dados['perfil']);

        DB::beginTransaction();

        try {
            // Cria usuário base
            $usuarioId = $this->criarUsuarioBase($dados['perfil']);

            // Cria o tipo específico (Cliente ou Gestor)
            $this->criarTipoEspecifico($dados, $usuarioId);

            DB::commit();
            
            return [
                'message' => 'Usuário criado com sucesso.',
                'id_usuario' => $usuarioId
            ];

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Busca um usuário por ID
     * 
     * @param int $id
     * @return array|null
     */
    public function buscarPorId(int $id): ?array
    {
        $dadosUsuario = DB::table('usuarios')->where('id_usuario', $id)->first();

        if (!$dadosUsuario) {
            return null;
        }

        return $this->montarDadosUsuario($dadosUsuario);
    }

    /**
     * Remove um usuário
     * 
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function remover(int $id): bool
    {
        $dadosUsuario = DB::table('usuarios')->where('id_usuario', $id)->first();

        if (!$dadosUsuario) {
            throw new \Exception('Usuário não encontrado.');
        }

        DB::beginTransaction();

        try {
            $this->removerTipoEspecifico($dadosUsuario->perfil, $id);
            DB::table('usuarios')->where('id_usuario', $id)->delete();

            DB::commit();
            return true;

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Valida se o email já está cadastrado
     * 
     * @param string $email
     * @param string $perfil
     * @throws \Exception
     */
    private function validarEmailUnico(string $email, string $perfil): void
    {
        if ($perfil === 'C') {
            if (Cliente::where('email', $email)->exists()) {
                throw new \Exception('Já existe um cliente com este email.');
            }
        } else {
            if (Gestor::where('email', $email)->exists()) {
                throw new \Exception('Já existe um gestor com este email.');
            }
        }
    }

    /**
     * Cria o registro base na tabela usuarios
     * 
     * @param string $perfil
     * @return int
     */
    private function criarUsuarioBase(string $perfil): int
    {
        return DB::table('usuarios')->insertGetId(['perfil' => $perfil]);
    }

    /**
     * Cria o tipo específico (Cliente ou Gestor)
     * 
     * @param array $dados
     * @param int $usuarioId
     */
    private function criarTipoEspecifico(array $dados, int $usuarioId): void
    {
        if ($dados['perfil'] === 'C') {
            Cliente::create([
                'id_cliente' => $usuarioId,
                'nome' => $dados['nome'],
                'email' => $dados['email'],
                'senha' => $dados['senha'],
            ]);
        } else {
            Gestor::create([
                'id_gestor' => $usuarioId,
                'nome' => $dados['nome'],
                'email' => $dados['email'],
                'senha' => $dados['senha'],
                'cnpj' => $dados['cnpj'] ?? '',
            ]);
        }
    }

    /**
     * Monta os dados completos do usuário
     * 
     * @param object $dadosUsuario
     * @return array
     */
    private function montarDadosUsuario(object $dadosUsuario): array
    {
        $dados = null;

        if ($dadosUsuario->perfil === 'C') {
            $dados = Cliente::where('id_cliente', $dadosUsuario->id_usuario)->first();
        } elseif ($dadosUsuario->perfil === 'G') {
            $dados = Gestor::where('id_gestor', $dadosUsuario->id_usuario)->first();
        }

        return [
            'id_usuario' => $dadosUsuario->id_usuario,
            'perfil' => $dadosUsuario->perfil,
            'dados' => $dados
        ];
    }

    /**
     * Remove o tipo específico (Cliente ou Gestor)
     * 
     * @param string $perfil
     * @param int $usuarioId
     */
    private function removerTipoEspecifico(string $perfil, int $usuarioId): void
    {
        if ($perfil === 'C') {
            Cliente::where('id_cliente', $usuarioId)->delete();
        } elseif ($perfil === 'G') {
            Gestor::where('id_gestor', $usuarioId)->delete();
        }
    }
}
