<?php

namespace App\Modules\Usuarios\Controllers;

use App\Modules\Usuarios\Services\UsuarioService;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Throwable;

class UsuariosController extends Controller
{
    private UsuarioService $usuarioService;

    public function __construct(UsuarioService $usuarioService)
    {
        $this->usuarioService = $usuarioService;
    }
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
    public function index(): JsonResponse
    {
        $usuarios = $this->usuarioService->listarTodos();
        return response()->json($usuarios);
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
     *             @OA\Property(property="error", type="string", example="Dados inválidos."),
     *             @OA\Property(property="message", type="string", example="Os dados fornecidos não são válidos."),
     *             @OA\Property(property="errors", type="object", example={"email": {"O campo email é obrigatório."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro no servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erro no servidor."),
     *             @OA\Property(property="message", type="string", example="Ocorreu um erro inesperado ao processar a requisição.")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $dados = $request->validate([
                'perfil' => 'required|in:C,G',
                'nome'   => 'required|string|max:100',
                'email'  => 'required|email|max:100',
                'senha'  => 'required|string|max:100',
                'cnpj'   => 'nullable|string|max:20'
            ]);

            $resultado = $this->usuarioService->criar($dados);
            
            return response()->json($resultado, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Dados inválidos.',
                'message' => 'Os dados fornecidos não são válidos.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Email já cadastrado.',
                'message' => $e->getMessage()
            ], 409);
        } catch (Throwable $e) {
            return response()->json([
                'error' => 'Erro no servidor.',
                'message' => $e->getMessage()
            ], 500);
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
     *             @OA\Property(property="error", type="string", example="Usuário não encontrado."),
     *             @OA\Property(property="message", type="string", example="O usuário com o ID informado não existe.")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        $usuario = $this->usuarioService->buscarPorId($id);

        if (!$usuario) {
            return response()->json([
                'error' => 'Usuário não encontrado.',
                'message' => 'O usuário com o ID informado não existe.'
            ], 404);
        }

        return response()->json($usuario);
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
     *             @OA\Property(property="error", type="string", example="Erro ao remover usuário."),
     *             @OA\Property(property="message", type="string", example="Ocorreu um erro inesperado ao remover o usuário.")
     *         )
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->usuarioService->remover($id);
            
            return response()->json([
                'message' => 'Usuário removido com sucesso.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Usuário não encontrado.',
                'message' => $e->getMessage()
            ], 404);
        } catch (Throwable $e) {
            return response()->json([
                'error' => 'Erro ao remover usuário.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
