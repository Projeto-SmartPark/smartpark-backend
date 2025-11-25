<?php

namespace App\Modules\Acesso;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * @OA\Tag(
 *     name="Acessos",
 *     description="Operações de gerenciamento de acessos ao estacionamento"
 * )
 */
class AcessoController extends Controller
{
    private AcessoService $acessoService;

    public function __construct(AcessoService $acessoService)
    {
        $this->acessoService = $acessoService;
    }

    /**
     * @OA\Get(
     *     path="/acessos",
     *     summary="Listar todos os acessos",
     *     tags={"Acessos"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de acessos retornada com sucesso",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(
     *
     *                 @OA\Property(property="id_acesso", type="integer", example=1),
     *                 @OA\Property(property="data", type="string", format="date", example="2025-01-15"),
     *                 @OA\Property(property="hora_inicio", type="string", format="time", example="08:30:00"),
     *                 @OA\Property(property="hora_fim", type="string", format="time", example="12:45:00"),
     *                 @OA\Property(property="valor_total", type="number", format="float", example=22.50),
     *                 @OA\Property(property="veiculo_id", type="integer", example=1),
     *                 @OA\Property(property="vaga_id", type="integer", example=5),
     *                 @OA\Property(property="cliente_id", type="integer", example=3),
     *                 @OA\Property(
     *                     property="veiculo",
     *                     type="object",
     *                     @OA\Property(property="id_veiculo", type="integer", example=1),
     *                     @OA\Property(property="placa", type="string", example="ABC1D23")
     *                 ),
     *                 @OA\Property(
     *                     property="vaga",
     *                     type="object",
     *                     @OA\Property(property="id_vaga", type="integer", example=5),
     *                     @OA\Property(property="numero", type="integer", example=15)
     *                 ),
     *                 @OA\Property(
     *                     property="cliente",
     *                     type="object",
     *                     @OA\Property(property="id_cliente", type="integer", example=3),
     *                     @OA\Property(property="usuario_id", type="integer", example=3)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $acessos = $this->acessoService->listarAcessos();

            return response()->json($acessos, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao listar acessos'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/acessos/cliente",
     *     summary="Listar acessos do cliente autenticado",
     *     tags={"Acessos"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de acessos do cliente retornada com sucesso"
     *     )
     * )
     */
    public function acessosCliente(Request $request): JsonResponse
    {
        try {
            // Pega o usuário do middleware auth.microservico (como array)
            $usuario = $request->input('usuario');
            $clienteId = $usuario['id'];
            $acessos = $this->acessoService->listarAcessosPorCliente($clienteId);

            return response()->json($acessos, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao listar acessos',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/acessos",
     *     summary="Criar novo acesso",
     *     tags={"Acessos"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"data", "hora_inicio", "veiculo_id", "vaga_id", "cliente_id"},
     *
     *             @OA\Property(property="data", type="string", format="date", example="2025-01-15"),
     *             @OA\Property(property="hora_inicio", type="string", format="time", example="08:30:00"),
     *             @OA\Property(property="hora_fim", type="string", format="time", example="12:45:00"),
     *             @OA\Property(property="valor_total", type="number", format="float", example=22.50),
     *             @OA\Property(property="veiculo_id", type="integer", example=1),
     *             @OA\Property(property="vaga_id", type="integer", example=5),
     *             @OA\Property(property="cliente_id", type="integer", example=3)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Acesso criado com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="id_acesso", type="integer", example=1),
     *             @OA\Property(property="data", type="string", format="date", example="2025-01-15"),
     *             @OA\Property(property="hora_inicio", type="string", format="time", example="08:30:00"),
     *             @OA\Property(property="hora_fim", type="string", format="time", example="12:45:00"),
     *             @OA\Property(property="valor_total", type="number", format="float", example=22.50),
     *             @OA\Property(property="veiculo_id", type="integer", example=1),
     *             @OA\Property(property="vaga_id", type="integer", example=5),
     *             @OA\Property(property="cliente_id", type="integer", example=3)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'data' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i:s',
            'hora_fim' => 'nullable|date_format:H:i:s',
            'valor_total' => 'nullable|numeric|min:0',
            'veiculo_id' => 'required|exists:veiculos,id_veiculo',
            'vaga_id' => 'required|exists:vagas,id_vaga',
            'cliente_id' => 'required|exists:clientes,id_cliente',
            'tarifa_id' => 'required|exists:tarifas,id_tarifa',
        ], [
            'data.required' => 'A data do acesso é obrigatória',
            'data.date' => 'A data do acesso deve estar no formato válido',
            'hora_inicio.required' => 'A hora de início é obrigatória',
            'hora_inicio.date_format' => 'A hora de início deve estar no formato HH:MM:SS',
            'hora_fim.date_format' => 'A hora de fim deve estar no formato HH:MM:SS',
            'valor_total.numeric' => 'O valor total deve ser numérico',
            'valor_total.min' => 'O valor total deve ser no mínimo 0',
            'veiculo_id.required' => 'O veículo é obrigatório',
            'veiculo_id.exists' => 'Veículo não encontrado',
            'vaga_id.required' => 'A vaga é obrigatória',
            'vaga_id.exists' => 'Vaga não encontrada',
            'cliente_id.required' => 'O cliente é obrigatório',
            'cliente_id.exists' => 'Cliente não encontrado',
            'tarifa_id.required' => 'A tarifa é obrigatória',
            'tarifa_id.exists' => 'Tarifa não encontrada',
        ]);

        try {
            $acesso = $this->acessoService->criarAcesso($request->all());

            return response()->json([
                'message' => 'Acesso criado com sucesso.',
                'data' => $acesso,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao criar acesso.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/acessos/{id}",
     *     summary="Buscar acesso por ID",
     *     tags={"Acessos"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do acesso",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Acesso encontrado",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="id_acesso", type="integer", example=1),
     *             @OA\Property(property="data", type="string", format="date", example="2025-01-15"),
     *             @OA\Property(property="hora_inicio", type="string", format="time", example="08:30:00"),
     *             @OA\Property(property="hora_fim", type="string", format="time", example="12:45:00"),
     *             @OA\Property(property="valor_total", type="number", format="float", example=22.50),
     *             @OA\Property(property="veiculo_id", type="integer", example=1),
     *             @OA\Property(property="vaga_id", type="integer", example=5),
     *             @OA\Property(property="cliente_id", type="integer", example=3),
     *             @OA\Property(
     *                 property="veiculo",
     *                 type="object",
     *                 @OA\Property(property="id_veiculo", type="integer", example=1),
     *                 @OA\Property(property="placa", type="string", example="ABC1D23")
     *             ),
     *             @OA\Property(
     *                 property="vaga",
     *                 type="object",
     *                 @OA\Property(property="id_vaga", type="integer", example=5),
     *                 @OA\Property(property="numero", type="integer", example=15)
     *             ),
     *             @OA\Property(
     *                 property="cliente",
     *                 type="object",
     *                 @OA\Property(property="id_cliente", type="integer", example=3),
     *                 @OA\Property(property="usuario_id", type="integer", example=3)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Acesso não encontrado"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $acesso = $this->acessoService->buscarAcessoPorId($id);

            return response()->json($acesso, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Acesso não encontrado'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao buscar acesso'], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/acessos/{id}",
     *     summary="Atualizar acesso",
     *     tags={"Acessos"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do acesso",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="data", type="string", format="date", example="2025-01-15"),
     *             @OA\Property(property="hora_inicio", type="string", format="time", example="08:30:00"),
     *             @OA\Property(property="hora_fim", type="string", format="time", example="13:00:00"),
     *             @OA\Property(property="valor_total", type="number", format="float", example=25.00),
     *             @OA\Property(property="veiculo_id", type="integer", example=1),
     *             @OA\Property(property="vaga_id", type="integer", example=5),
     *             @OA\Property(property="cliente_id", type="integer", example=3)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Acesso atualizado com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="id_acesso", type="integer", example=1),
     *             @OA\Property(property="data", type="string", format="date", example="2025-01-15"),
     *             @OA\Property(property="hora_inicio", type="string", format="time", example="08:30:00"),
     *             @OA\Property(property="hora_fim", type="string", format="time", example="13:00:00"),
     *             @OA\Property(property="valor_total", type="number", format="float", example=25.00),
     *             @OA\Property(property="veiculo_id", type="integer", example=1),
     *             @OA\Property(property="vaga_id", type="integer", example=5),
     *             @OA\Property(property="cliente_id", type="integer", example=3)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Acesso não encontrado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erro de validação"
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'data' => 'sometimes|date',
            'hora_inicio' => 'sometimes|date_format:H:i:s',
            'hora_fim' => 'nullable|date_format:H:i:s',
            'valor_total' => 'nullable|numeric|min:0',
            'veiculo_id' => 'sometimes|exists:veiculos,id_veiculo',
            'vaga_id' => 'sometimes|exists:vagas,id_vaga',
            'cliente_id' => 'sometimes|exists:clientes,id_cliente',
            'tarifa_id' => 'sometimes|exists:tarifas,id_tarifa',
        ], [
            'data.date' => 'A data do acesso deve estar no formato válido',
            'hora_inicio.date_format' => 'A hora de início deve estar no formato HH:MM:SS',
            'hora_fim.date_format' => 'A hora de fim deve estar no formato HH:MM:SS',
            'valor_total.numeric' => 'O valor total deve ser numérico',
            'valor_total.min' => 'O valor total deve ser no mínimo 0',
            'veiculo_id.exists' => 'Veículo não encontrado',
            'vaga_id.exists' => 'Vaga não encontrada',
            'cliente_id.exists' => 'Cliente não encontrado',
            'tarifa_id.exists' => 'Tarifa não encontrada',
        ]);

        try {
            $acesso = $this->acessoService->atualizarAcesso($id, $request->all());

            return response()->json([
                'message' => 'Acesso atualizado com sucesso.',
                'data' => $acesso,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Acesso não encontrado.',
                'message' => 'O acesso com o ID informado não existe.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao atualizar acesso.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/acessos/{id}",
     *     summary="Deletar acesso",
     *     tags={"Acessos"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do acesso",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Acesso deletado com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Acesso deletado com sucesso")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Acesso não encontrado"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->acessoService->deletarAcesso($id);

            return response()->json(['message' => 'Acesso deletado com sucesso'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Acesso não encontrado'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao deletar acesso'], 500);
        }
    }
}
