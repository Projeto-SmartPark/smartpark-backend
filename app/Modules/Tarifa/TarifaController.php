<?php

namespace App\Modules\Tarifa;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * @OA\Tag(
 *     name="Tarifas",
 *     description="Operações de gerenciamento de tarifas de estacionamento"
 * )
 */
class TarifaController extends Controller
{
    private TarifaService $tarifaService;

    public function __construct(TarifaService $tarifaService)
    {
        $this->tarifaService = $tarifaService;
    }

    /**
     * @OA\Get(
     *     path="/tarifas",
     *     summary="Listar todas as tarifas",
     *     tags={"Tarifas"},
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tarifas retornada com sucesso",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(
     *
     *                 @OA\Property(property="id_tarifa", type="integer", example=1),
     *                 @OA\Property(property="nome", type="string", example="Tarifa Horária"),
     *                 @OA\Property(property="valor", type="number", format="float", example=5.50),
     *                 @OA\Property(property="tipo", type="string", example="hora"),
     *                 @OA\Property(property="estacionamento_id", type="integer", example=1),
     *                 @OA\Property(
     *                     property="estacionamento",
     *                     type="object",
     *                     @OA\Property(property="id_estacionamento", type="integer", example=1),
     *                     @OA\Property(property="nome", type="string", example="SmartPark Centro")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        try {
            $tarifas = $this->tarifaService->listarTarifas();

            return response()->json($tarifas, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao listar tarifas'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/tarifas",
     *     summary="Criar nova tarifa",
     *     tags={"Tarifas"},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"nome", "valor", "tipo", "estacionamento_id"},
     *
     *             @OA\Property(property="nome", type="string", example="Tarifa Diária", maxLength=100),
     *             @OA\Property(property="valor", type="number", format="float", example=45.00),
     *             @OA\Property(property="tipo", type="string", enum={"segundo", "minuto", "hora", "diaria", "mensal"}, example="diaria"),
     *             @OA\Property(property="estacionamento_id", type="integer", example=1)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Tarifa criada com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="id_tarifa", type="integer", example=1),
     *             @OA\Property(property="nome", type="string", example="Tarifa Diária"),
     *             @OA\Property(property="valor", type="number", format="float", example=45.00),
     *             @OA\Property(property="tipo", type="string", example="diaria"),
     *             @OA\Property(property="estacionamento_id", type="integer", example=1)
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
            'nome' => 'required|string|max:100',
            'valor' => 'required|numeric|min:0',
            'tipo' => 'required|in:segundo,minuto,hora,diaria,mensal',
            'estacionamento_id' => 'required|exists:estacionamentos,id_estacionamento',
            'ativa' => 'sometimes|in:S,N',
        ], [
            'nome.required' => 'O nome da tarifa é obrigatório',
            'nome.string' => 'O nome da tarifa deve ser texto',
            'nome.max' => 'O nome da tarifa deve ter no máximo 100 caracteres',
            'valor.required' => 'O valor da tarifa é obrigatório',
            'valor.numeric' => 'O valor da tarifa deve ser numérico',
            'valor.min' => 'O valor da tarifa deve ser no mínimo 0',
            'tipo.required' => 'O tipo da tarifa é obrigatório',
            'tipo.in' => 'O tipo da tarifa deve ser: segundo, minuto, hora, diaria ou mensal',
            'estacionamento_id.required' => 'O estacionamento é obrigatório',
            'estacionamento_id.exists' => 'Estacionamento não encontrado',
            'ativa.in' => 'O campo ativa deve ser S ou N',
        ]);

        try {
            $tarifa = $this->tarifaService->criarTarifa($request->all());

            return response()->json([
                'message' => 'Tarifa criada com sucesso.',
                'data' => $tarifa,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao criar tarifa.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/tarifas/{id}",
     *     summary="Buscar tarifa por ID",
     *     tags={"Tarifas"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da tarifa",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Tarifa encontrada",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="id_tarifa", type="integer", example=1),
     *             @OA\Property(property="nome", type="string", example="Tarifa Horária"),
     *             @OA\Property(property="valor", type="number", format="float", example=5.50),
     *             @OA\Property(property="tipo", type="string", example="hora"),
     *             @OA\Property(property="estacionamento_id", type="integer", example=1),
     *             @OA\Property(
     *                 property="estacionamento",
     *                 type="object",
     *                 @OA\Property(property="id_estacionamento", type="integer", example=1),
     *                 @OA\Property(property="nome", type="string", example="SmartPark Centro")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Tarifa não encontrada"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $tarifa = $this->tarifaService->buscarTarifaPorId($id);

            return response()->json($tarifa, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Tarifa não encontrada'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao buscar tarifa'], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/tarifas/{id}",
     *     summary="Atualizar tarifa",
     *     tags={"Tarifas"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da tarifa",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="nome", type="string", example="Tarifa Horária", maxLength=100),
     *             @OA\Property(property="valor", type="number", format="float", example=6.00),
     *             @OA\Property(property="tipo", type="string", enum={"segundo", "minuto", "hora", "diaria", "mensal"}, example="hora"),
     *             @OA\Property(property="estacionamento_id", type="integer", example=1)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Tarifa atualizada com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="id_tarifa", type="integer", example=1),
     *             @OA\Property(property="nome", type="string", example="Tarifa Horária"),
     *             @OA\Property(property="valor", type="number", format="float", example=6.00),
     *             @OA\Property(property="tipo", type="string", example="hora"),
     *             @OA\Property(property="estacionamento_id", type="integer", example=1)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Tarifa não encontrada"
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
            'nome' => 'sometimes|string|max:100',
            'valor' => 'sometimes|numeric|min:0',
            'tipo' => 'sometimes|in:segundo,minuto,hora,diaria,mensal',
            'estacionamento_id' => 'sometimes|exists:estacionamentos,id_estacionamento',
            'ativa' => 'sometimes|in:S,N',
        ], [
            'nome.string' => 'O nome da tarifa deve ser texto',
            'nome.max' => 'O nome da tarifa deve ter no máximo 100 caracteres',
            'valor.numeric' => 'O valor da tarifa deve ser numérico',
            'valor.min' => 'O valor da tarifa deve ser no mínimo 0',
            'tipo.in' => 'O tipo da tarifa deve ser: segundo, minuto, hora, diaria ou mensal',
            'estacionamento_id.exists' => 'Estacionamento não encontrado',
            'ativa.in' => 'O campo ativa deve ser S ou N',
        ]);

        try {
            $tarifa = $this->tarifaService->atualizarTarifa($id, $request->all());

            return response()->json([
                'message' => 'Tarifa atualizada com sucesso.',
                'data' => $tarifa,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Tarifa não encontrada.',
                'message' => 'A tarifa com o ID informado não existe.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao atualizar tarifa.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/tarifas/{id}",
     *     summary="Deletar tarifa",
     *     tags={"Tarifas"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID da tarifa",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Tarifa deletada com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Tarifa deletada com sucesso")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Tarifa não encontrada"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->tarifaService->deletarTarifa($id);

            return response()->json(['message' => 'Tarifa deletada com sucesso'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Tarifa não encontrada'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao deletar tarifa'], 500);
        }
    }
}
