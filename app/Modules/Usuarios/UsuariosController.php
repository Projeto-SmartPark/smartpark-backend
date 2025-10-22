<?php

namespace App\Modules\Usuarios;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class UsuariosController extends Controller
{
    /**
     * @OA\Get(
     *     path="/usuarios",
     *     tags={"Usuários"},
     *     summary="Lista todos os usuários",
     *     description="Retorna uma lista com todos os clientes e gestores cadastrados",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuários retornada com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="clientes", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="gestores", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index()
    {
        return response()->json([
            'clientes' => Cliente::all(),
            'gestores' => Gestor::all(),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/usuarios",
     *     tags={"Usuários"},
     *     summary="Cadastra um novo usuário",
     *     description="Cria um novo cliente ou gestor no sistema",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"perfil", "nome", "email", "senha"},
     *             @OA\Property(property="perfil", type="string", enum={"C", "G"}, example="C", description="C = Cliente, G = Gestor"),
     *             @OA\Property(property="nome", type="string", maxLength=100, example="João Silva"),
     *             @OA\Property(property="email", type="string", format="email", maxLength=100, example="joao@exemplo.com"),
     *             @OA\Property(property="senha", type="string", maxLength=100, example="senha123"),
     *             @OA\Property(property="cnpj", type="string", maxLength=20, example="12345678000190", description="Apenas números. Obrigatório apenas para gestores")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuário criado com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Email já cadastrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Email já cadastrado."),
     *             @OA\Property(property="message", type="string", example="Já existe um cliente com este email.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro no servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/usuarios/{id}",
     *     tags={"Usuários"},
     *     summary="Exibe um usuário específico",
     *     description="Retorna os dados detalhados de um usuário (cliente ou gestor)",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dados do usuário retornados com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id_usuario", type="integer", example=1),
     *             @OA\Property(property="perfil", type="string", example="C"),
     *             @OA\Property(property="dados", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuário não encontrado.")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/usuarios/{id}",
     *     tags={"Usuários"},
     *     summary="Remove um usuário",
     *     description="Deleta um usuário e todos os seus dados vinculados (cliente ou gestor)",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do usuário",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuário removido com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuário não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Usuário não encontrado."),
     *             @OA\Property(property="message", type="string", example="O usuário com o ID informado não existe.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao remover usuário",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
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
