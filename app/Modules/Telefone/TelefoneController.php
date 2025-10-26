<?php

namespace App\Modules\Telefone;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TelefoneController extends Controller
{
    private TelefoneService $telefoneService;

    public function __construct(TelefoneService $telefoneService)
    {
        $this->telefoneService = $telefoneService;
    }

    /**
     * @OA\Get(
     *     path="/telefones",
     *     tags={"Telefones"},
     *     summary="Lista todos os telefones",
     *     description="Retorna uma lista com todos os telefones cadastrados",
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista retornada com sucesso",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(
     *
     *                 @OA\Property(property="id_telefone", type="integer", example=1),
     *                 @OA\Property(property="ddd", type="string", example="11"),
     *                 @OA\Property(property="numero", type="string", example="987654321")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $telefones = $this->telefoneService->listarTelefones();

        return response()->json($telefones, 200);
    }

    /**
     * @OA\Post(
     *     path="/telefones",
     *     tags={"Telefones"},
     *     summary="Cria um novo telefone",
     *     description="Cadastra um novo telefone no sistema (também é criado automaticamente ao criar estacionamento)",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"ddd", "numero"},
     *
     *             @OA\Property(property="ddd", type="string", maxLength=2, example="11", description="Código DDD (2 dígitos)"),
     *             @OA\Property(property="numero", type="string", maxLength=9, example="987654321", description="Número do telefone (até 9 dígitos)")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Telefone criado com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Telefone criado com sucesso."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id_telefone", type="integer", example=1),
     *                 @OA\Property(property="ddd", type="string", example="11"),
     *                 @OA\Property(property="numero", type="string", example="987654321")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Dados inválidos",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Os dados fornecidos são inválidos."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="ddd",
     *                     type="array",
     *
     *                     @OA\Items(type="string", example="O campo DDD é obrigatório.")
     *                 ),
     *
     *                 @OA\Property(
     *                     property="numero",
     *                     type="array",
     *
     *                     @OA\Items(type="string", example="O campo número é obrigatório.")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erro no servidor",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Erro ao criar telefone."),
     *             @OA\Property(property="message", type="string", example="Detalhes do erro.")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ddd' => 'required|string|size:2',
            'numero' => 'required|string|max:9',
        ], [
            'ddd.required' => 'O campo DDD é obrigatório.',
            'ddd.string' => 'O campo DDD deve ser um texto.',
            'ddd.size' => 'O campo DDD deve ter exatamente 2 caracteres.',
            'numero.required' => 'O campo número é obrigatório.',
            'numero.string' => 'O campo número deve ser um texto.',
            'numero.max' => 'O campo número não pode ter mais de 9 caracteres.',
        ]);

        try {
            $telefone = $this->telefoneService->criarTelefone($validated);

            return response()->json([
                'message' => 'Telefone criado com sucesso.',
                'data' => $telefone,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao criar telefone.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/telefones/{id}",
     *     tags={"Telefones"},
     *     summary="Exibe um telefone específico",
     *     description="Retorna os dados de um telefone pelo ID",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do telefone",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Dados do telefone retornados com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="id_telefone", type="integer", example=1),
     *             @OA\Property(property="ddd", type="string", example="11"),
     *             @OA\Property(property="numero", type="string", example="987654321")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Telefone não encontrado",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Telefone não encontrado."),
     *             @OA\Property(property="message", type="string", example="O telefone com o ID informado não existe.")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $telefone = $this->telefoneService->buscarTelefonePorId($id);

            return response()->json($telefone, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Telefone não encontrado.',
                'message' => 'O telefone com o ID informado não existe.',
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/telefones/{id}",
     *     tags={"Telefones"},
     *     summary="Atualiza um telefone",
     *     description="Atualiza os dados de um telefone existente",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do telefone",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"ddd", "numero"},
     *
     *             @OA\Property(property="ddd", type="string", maxLength=2, example="11"),
     *             @OA\Property(property="numero", type="string", maxLength=9, example="987654321")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Telefone atualizado com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Telefone atualizado com sucesso."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Telefone não encontrado",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Telefone não encontrado."),
     *             @OA\Property(property="message", type="string", example="O telefone com o ID informado não existe.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Dados inválidos",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'ddd' => 'required|string|size:2',
            'numero' => 'required|string|max:9',
        ], [
            'ddd.required' => 'O campo DDD é obrigatório.',
            'ddd.string' => 'O campo DDD deve ser um texto.',
            'ddd.size' => 'O campo DDD deve ter exatamente 2 caracteres.',
            'numero.required' => 'O campo número é obrigatório.',
            'numero.string' => 'O campo número deve ser um texto.',
            'numero.max' => 'O campo número não pode ter mais de 9 caracteres.',
        ]);

        try {
            $telefone = $this->telefoneService->atualizarTelefone($id, $validated);

            return response()->json([
                'message' => 'Telefone atualizado com sucesso.',
                'data' => $telefone,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Telefone não encontrado.',
                'message' => 'O telefone com o ID informado não existe.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao atualizar telefone.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/telefones/{id}",
     *     tags={"Telefones"},
     *     summary="Remove um telefone",
     *     description="Deleta um telefone do sistema",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do telefone",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Telefone deletado com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Telefone deletado com sucesso.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Telefone não encontrado",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Telefone não encontrado."),
     *             @OA\Property(property="message", type="string", example="O telefone com o ID informado não existe.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erro no servidor",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Erro ao deletar telefone."),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->telefoneService->deletarTelefone($id);

            return response()->json([
                'message' => 'Telefone deletado com sucesso.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Telefone não encontrado.',
                'message' => 'O telefone com o ID informado não existe.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao deletar telefone.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
