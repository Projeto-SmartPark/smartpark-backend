<?php

namespace App\Modules\Veiculo;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VeiculoController extends Controller
{
    private VeiculoService $veiculoService;

    public function __construct(VeiculoService $veiculoService)
    {
        $this->veiculoService = $veiculoService;
    }

    /**
     * @OA\Get(
     *     path="/veiculos",
     *     tags={"Veículos"},
     *     summary="Lista todos os veículos",
     *     description="Retorna uma lista com todos os veículos cadastrados",
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
     *                 @OA\Property(property="id_veiculo", type="integer", example=1),
     *                 @OA\Property(property="placa", type="string", example="ABC1234"),
     *                 @OA\Property(property="cliente_id", type="integer", example=1)
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $veiculos = $this->veiculoService->listarVeiculos();

        return response()->json($veiculos, 200);
    }

    /**
     * @OA\Get(
     *     path="/veiculos/cliente",
     *     tags={"Veículos"},
     *     summary="Lista veículos do cliente autenticado",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Lista de veículos do cliente")
     * )
     */
    public function listarPorCliente(Request $request): JsonResponse
    {
        try {
            $usuario = $request->usuario;
            $veiculos = $this->veiculoService->listarVeiculosPorCliente($usuario['id']);
            return response()->json($veiculos, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao listar veículos.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/veiculos",
     *     tags={"Veículos"},
     *     summary="Cria um novo veículo",
     *     description="Cadastra um novo veículo no sistema",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"placa", "cliente_id"},
     *
     *             @OA\Property(property="placa", type="string", maxLength=10, example="ABC1234", description="Placa do veículo"),
     *             @OA\Property(property="cliente_id", type="integer", example=1)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Veículo criado com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Veículo criado com sucesso."),
     *             @OA\Property(property="data", type="object")
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
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erro no servidor",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Erro ao criar veículo."),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'placa' => 'required|string|max:10|unique:veiculos,placa',
            'cliente_id' => 'required|integer|exists:clientes,id_cliente',
        ], [
            'placa.required' => 'O campo placa é obrigatório.',
            'placa.string' => 'O campo placa deve ser um texto.',
            'placa.max' => 'O campo placa não pode ter mais de 10 caracteres.',
            'placa.unique' => 'Já existe um veículo com esta placa.',
            'cliente_id.required' => 'O campo cliente é obrigatório.',
            'cliente_id.integer' => 'O campo cliente deve ser um número inteiro.',
            'cliente_id.exists' => 'O cliente informado não existe.',
        ]);

        try {
            $veiculo = $this->veiculoService->criarVeiculo($validated);

            return response()->json([
                'message' => 'Veículo criado com sucesso.',
                'data' => $veiculo,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao criar veículo.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/veiculos/{id}",
     *     tags={"Veículos"},
     *     summary="Exibe um veículo específico",
     *     description="Retorna os dados de um veículo pelo ID",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do veículo",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Veículo encontrado",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="id_veiculo", type="integer", example=1),
     *             @OA\Property(property="placa", type="string", example="ABC1234"),
     *             @OA\Property(property="cliente_id", type="integer", example=1)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Veículo não encontrado",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Veículo não encontrado."),
     *             @OA\Property(property="message", type="string", example="O veículo com o ID informado não existe.")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $veiculo = $this->veiculoService->buscarVeiculoPorId($id);

            return response()->json($veiculo, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Veículo não encontrado.',
                'message' => 'O veículo com o ID informado não existe.',
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/veiculos/{id}",
     *     tags={"Veículos"},
     *     summary="Atualiza um veículo",
     *     description="Atualiza os dados de um veículo existente",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do veículo",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"placa", "cliente_id"},
     *
     *             @OA\Property(property="placa", type="string", maxLength=10, example="XYZ5678"),
     *             @OA\Property(property="cliente_id", type="integer", example=1)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Veículo atualizado com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Veículo atualizado com sucesso."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Veículo não encontrado",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Veículo não encontrado."),
     *             @OA\Property(property="message", type="string", example="O veículo com o ID informado não existe.")
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
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erro no servidor",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Erro ao atualizar veículo."),
     *             @OA\Property(property="message", type="string")
         )
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'placa' => 'required|string|max:10|unique:veiculos,placa,'.$id.',id_veiculo',
            'cliente_id' => 'required|integer|exists:clientes,id_cliente',
        ], [
            'placa.required' => 'O campo placa é obrigatório.',
            'placa.string' => 'O campo placa deve ser um texto.',
            'placa.max' => 'O campo placa não pode ter mais de 10 caracteres.',
            'placa.unique' => 'Já existe um veículo com esta placa.',
            'cliente_id.required' => 'O campo cliente é obrigatório.',
            'cliente_id.integer' => 'O campo cliente deve ser um número inteiro.',
            'cliente_id.exists' => 'O cliente informado não existe.',
        ]);

        try {
            $veiculo = $this->veiculoService->atualizarVeiculo($id, $validated);

            return response()->json([
                'message' => 'Veículo atualizado com sucesso.',
                'data' => $veiculo,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Veículo não encontrado.',
                'message' => 'O veículo com o ID informado não existe.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao atualizar veículo.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/veiculos/{id}",
     *     tags={"Veículos"},
     *     summary="Remove um veículo",
     *     description="Deleta um veículo do sistema",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do veículo",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Veículo removido com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Veículo removido com sucesso.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Veículo não encontrado",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Veículo não encontrado."),
     *             @OA\Property(property="message", type="string", example="O veículo com o ID informado não existe.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao remover",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Erro ao remover veículo."),
     *             @OA\Property(property="message", type="string")
         )
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->veiculoService->deletarVeiculo($id);

            return response()->json([
                'message' => 'Veículo removido com sucesso.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Veículo não encontrado.',
                'message' => 'O veículo com o ID informado não existe.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao remover veículo.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
