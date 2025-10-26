<?php

namespace App\Modules\Reserva;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ReservaController extends Controller
{
    private ReservaService $reservaService;

    public function __construct(ReservaService $reservaService)
    {
        $this->reservaService = $reservaService;
    }

    /**
     * @OA\Get(
     *     path="/reservas",
     *     tags={"Reservas"},
     *     summary="Lista todas as reservas",
     *     description="Retorna uma lista com todas as reservas cadastradas",
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
     *                 @OA\Property(property="id_reserva", type="integer", example=1),
     *                 @OA\Property(property="data", type="string", format="date", example="2025-10-26"),
     *                 @OA\Property(property="hora_inicio", type="string", format="time", example="14:00:00"),
     *                 @OA\Property(property="hora_fim", type="string", format="time", example="16:00:00"),
     *                 @OA\Property(property="status", type="string", example="ativa"),
     *                 @OA\Property(property="cliente_id", type="integer", example=1),
     *                 @OA\Property(property="veiculo_id", type="integer", example=1),
     *                 @OA\Property(property="vaga_id", type="integer", example=1)
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $reservas = $this->reservaService->listarReservas();

        return response()->json($reservas, 200);
    }

    /**
     * @OA\Post(
     *     path="/reservas",
     *     tags={"Reservas"},
     *     summary="Cria uma nova reserva",
     *     description="Cadastra uma nova reserva no sistema",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"data", "hora_inicio", "hora_fim", "cliente_id", "veiculo_id", "vaga_id"},
     *
     *             @OA\Property(property="data", type="string", format="date", example="2025-10-26"),
     *             @OA\Property(property="hora_inicio", type="string", format="time", example="14:00:00"),
     *             @OA\Property(property="hora_fim", type="string", format="time", example="16:00:00"),
     *             @OA\Property(property="status", type="string", enum={"ativa", "cancelada", "concluida", "expirada"}, example="ativa"),
     *             @OA\Property(property="cliente_id", type="integer", example=1),
     *             @OA\Property(property="veiculo_id", type="integer", example=1),
     *             @OA\Property(property="vaga_id", type="integer", example=1)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Reserva criada com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Reserva criada com sucesso."),
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
     *             @OA\Property(property="error", type="string", example="Erro ao criar reserva."),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'data' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i:s',
            'hora_fim' => 'required|date_format:H:i:s',
            'status' => 'nullable|in:ativa,cancelada,concluida,expirada',
            'cliente_id' => 'required|integer|exists:clientes,id_cliente',
            'veiculo_id' => 'required|integer|exists:veiculos,id_veiculo',
            'vaga_id' => 'required|integer|exists:vagas,id_vaga',
        ], [
            'data.required' => 'O campo data é obrigatório.',
            'data.date' => 'O campo data deve ser uma data válida.',
            'hora_inicio.required' => 'O campo hora de início é obrigatório.',
            'hora_inicio.date_format' => 'O campo hora de início deve estar no formato HH:MM:SS.',
            'hora_fim.required' => 'O campo hora de fim é obrigatório.',
            'hora_fim.date_format' => 'O campo hora de fim deve estar no formato HH:MM:SS.',
            'status.in' => 'O campo status deve ser: ativa, cancelada, concluida ou expirada.',
            'cliente_id.required' => 'O campo cliente é obrigatório.',
            'cliente_id.integer' => 'O campo cliente deve ser um número inteiro.',
            'cliente_id.exists' => 'O cliente informado não existe.',
            'veiculo_id.required' => 'O campo veículo é obrigatório.',
            'veiculo_id.integer' => 'O campo veículo deve ser um número inteiro.',
            'veiculo_id.exists' => 'O veículo informado não existe.',
            'vaga_id.required' => 'O campo vaga é obrigatório.',
            'vaga_id.integer' => 'O campo vaga deve ser um número inteiro.',
            'vaga_id.exists' => 'A vaga informada não existe.',
        ]);

        try {
            $reserva = $this->reservaService->criarReserva($validated);

            return response()->json([
                'message' => 'Reserva criada com sucesso.',
                'data' => $reserva,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao criar reserva.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/reservas/{id}",
     *     tags={"Reservas"},
     *     summary="Exibe uma reserva específica",
     *     description="Retorna os dados de uma reserva pelo ID",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da reserva",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Reserva encontrada",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="id_reserva", type="integer", example=1),
     *             @OA\Property(property="data", type="string", format="date", example="2025-10-26"),
     *             @OA\Property(property="hora_inicio", type="string", format="time", example="14:00:00"),
     *             @OA\Property(property="hora_fim", type="string", format="time", example="16:00:00"),
     *             @OA\Property(property="status", type="string", example="ativa"),
     *             @OA\Property(property="cliente_id", type="integer", example=1),
     *             @OA\Property(property="veiculo_id", type="integer", example=1),
     *             @OA\Property(property="vaga_id", type="integer", example=1)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Reserva não encontrada",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Reserva não encontrada."),
     *             @OA\Property(property="message", type="string", example="A reserva com o ID informado não existe.")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $reserva = $this->reservaService->buscarReservaPorId($id);

            return response()->json($reserva, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Reserva não encontrada.',
                'message' => 'A reserva com o ID informado não existe.',
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/reservas/{id}",
     *     tags={"Reservas"},
     *     summary="Atualiza uma reserva",
     *     description="Atualiza os dados de uma reserva existente",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da reserva",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"data", "hora_inicio", "hora_fim", "cliente_id", "veiculo_id", "vaga_id"},
     *
     *             @OA\Property(property="data", type="string", format="date", example="2025-10-27"),
     *             @OA\Property(property="hora_inicio", type="string", format="time", example="15:00:00"),
     *             @OA\Property(property="hora_fim", type="string", format="time", example="17:00:00"),
     *             @OA\Property(property="status", type="string", enum={"ativa", "cancelada", "concluida", "expirada"}, example="concluida"),
     *             @OA\Property(property="cliente_id", type="integer", example=1),
     *             @OA\Property(property="veiculo_id", type="integer", example=1),
     *             @OA\Property(property="vaga_id", type="integer", example=1)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Reserva atualizada com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Reserva atualizada com sucesso."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Reserva não encontrada",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Reserva não encontrada."),
     *             @OA\Property(property="message", type="string", example="A reserva com o ID informado não existe.")
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
     *             @OA\Property(property="error", type="string", example="Erro ao atualizar reserva."),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'data' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i:s',
            'hora_fim' => 'required|date_format:H:i:s',
            'status' => 'nullable|in:ativa,cancelada,concluida,expirada',
            'cliente_id' => 'required|integer|exists:clientes,id_cliente',
            'veiculo_id' => 'required|integer|exists:veiculos,id_veiculo',
            'vaga_id' => 'required|integer|exists:vagas,id_vaga',
        ], [
            'data.required' => 'O campo data é obrigatório.',
            'data.date' => 'O campo data deve ser uma data válida.',
            'hora_inicio.required' => 'O campo hora de início é obrigatório.',
            'hora_inicio.date_format' => 'O campo hora de início deve estar no formato HH:MM:SS.',
            'hora_fim.required' => 'O campo hora de fim é obrigatório.',
            'hora_fim.date_format' => 'O campo hora de fim deve estar no formato HH:MM:SS.',
            'status.in' => 'O campo status deve ser: ativa, cancelada, concluida ou expirada.',
            'cliente_id.required' => 'O campo cliente é obrigatório.',
            'cliente_id.integer' => 'O campo cliente deve ser um número inteiro.',
            'cliente_id.exists' => 'O cliente informado não existe.',
            'veiculo_id.required' => 'O campo veículo é obrigatório.',
            'veiculo_id.integer' => 'O campo veículo deve ser um número inteiro.',
            'veiculo_id.exists' => 'O veículo informado não existe.',
            'vaga_id.required' => 'O campo vaga é obrigatório.',
            'vaga_id.integer' => 'O campo vaga deve ser um número inteiro.',
            'vaga_id.exists' => 'A vaga informada não existe.',
        ]);

        try {
            $reserva = $this->reservaService->atualizarReserva($id, $validated);

            return response()->json([
                'message' => 'Reserva atualizada com sucesso.',
                'data' => $reserva,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Reserva não encontrada.',
                'message' => 'A reserva com o ID informado não existe.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao atualizar reserva.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/reservas/{id}",
     *     tags={"Reservas"},
     *     summary="Remove uma reserva",
     *     description="Deleta uma reserva do sistema",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da reserva",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Reserva removida com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Reserva removida com sucesso.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Reserva não encontrada",
     *
     *         @OA\JsonContent(
             @OA\Property(property="error", type="string", example="Reserva não encontrada."),
     *             @OA\Property(property="message", type="string", example="A reserva com o ID informado não existe.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao remover",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Erro ao remover reserva."),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->reservaService->deletarReserva($id);

            return response()->json([
                'message' => 'Reserva removida com sucesso.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Reserva não encontrada.',
                'message' => 'A reserva com o ID informado não existe.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao remover reserva.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
