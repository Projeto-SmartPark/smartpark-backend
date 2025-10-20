<?php

namespace App\Modules\Usuarios;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class UsuariosController extends Controller
{
    // Lista todos os usuários (clientes e gestores)
    public function index()
    {
        return response()->json([
            'clientes' => Cliente::all(),
            'gestores' => Gestor::all(),
        ]);
    }

    // Cadastra um novo usuário (cliente ou gestor)
    public function store(Request $request)
    {
        // Validação básica dos dados recebidos
        $dados = $request->validate([
            'perfil' => 'required|in:C,G',
            'nome'   => 'required|string|max:100',
            'email'  => 'required|email|max:100',
            'senha'  => 'required|string|max:100',
            'cnpj'   => 'nullable|string|max:20'
        ]);

        // Verifica se o email já existe conforme o perfil
        if ($dados['perfil'] === 'C') {
            $emailExiste = Cliente::where('email', $dados['email'])->exists();
            if ($emailExiste) {
                return response()->json([
                    'error' => 'Email já cadastrado.',
                    'message' => 'Já existe um cliente com este email.'
                ], 409);
            }
        } else {
            $emailExiste = Gestor::where('email', $dados['email'])->exists();
            if ($emailExiste) {
                return response()->json([
                    'error' => 'Email já cadastrado.',
                    'message' => 'Já existe um gestor com este email.'
                ], 409);
            }
        }

        DB::beginTransaction();

        try {
            // Cria o registro genérico na tabela usuarios
            $usuario = DB::table('usuarios')->insertGetId([
                'perfil' => $dados['perfil']
            ]);

            // Cria o tipo de usuário conforme o perfil informado
            if ($dados['perfil'] === 'C') {
                Cliente::create([
                    'nome' => $dados['nome'],
                    'email' => $dados['email'],
                    'senha' => $dados['senha'],
                    'usuario_id' => $usuario,
                ]);
            } 
            
            if ($dados['perfil'] === 'G') {
                Gestor::create([
                    'nome' => $dados['nome'],
                    'email' => $dados['email'],
                    'senha' => $dados['senha'],
                    'cnpj' => $dados['cnpj'] ?? '',
                    'usuario_id' => $usuario,
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Usuário criado com sucesso.'], 201);

        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Exibe os dados de um usuário específico
    public function show($id)
    {
        $dadosUsuario = DB::table('usuarios')->where('id_usuario', $id)->first();

        if (!$dadosUsuario) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        // Se for cliente
        if ($dadosUsuario->perfil === 'C') {
            $cliente = Cliente::where('usuario_id', $id)->first();
            return response()->json([
                'id_usuario' => $dadosUsuario->id_usuario,
                'perfil' => 'C',
                'dados' => $cliente
            ]);
        }

        // Se for gestor
        if ($dadosUsuario->perfil === 'G') {
            $gestor = Gestor::where('usuario_id', $id)->first();
            return response()->json([
                'id_usuario' => $dadosUsuario->id_usuario,
                'perfil' => 'G',
                'dados' => $gestor
            ]);
        }

        // Se não for nenhum tipo válido
        return response()->json(['message' => 'Tipo de usuário inválido.'], 400);
    }


    // Remove um usuário e seus registros vinculados
    public function destroy($id)
    {
        try {
            // Busca o usuário na tabela usuarios
            $dadosUsuario = DB::table('usuarios')->where('id_usuario', $id)->first();

            if (!$dadosUsuario) {
                return response()->json([
                    'error' => 'Usuário não encontrado.',
                    'message' => 'O usuário com o ID informado não existe.'
                ], 404);
            }

            DB::beginTransaction();

            // Remove o cliente ou gestor conforme o perfil
            if ($dadosUsuario->perfil === 'C') {
                Cliente::where('usuario_id', $id)->delete();
            } 
            
            if ($dadosUsuario->perfil === 'G') {
                Gestor::where('usuario_id', $id)->delete();
            }

            // Remove o registro da tabela usuarios
            DB::table('usuarios')->where('id_usuario', $id)->delete();

            DB::commit();
            return response()->json(['message' => 'Usuário removido com sucesso.']);

        } catch (Throwable $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Erro ao remover usuário.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
