<?php

namespace App\Modules\Vaga;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\Rule;

class VagaController extends Controller
{
    private VagaService $vagaService;

    public function __construct(VagaService $vagaService)
    {
        $this->vagaService = $vagaService;
    }

    /**
     * @OA\Get(
     *     path="/vagas",
     *     tags={"Vagas"},
     *     summary="Lista todas as vagas",
     *     description="Retorna uma lista com todas as vagas cadastradas",
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
     *                 @OA\Property(property="id_vaga", type="integer", example=1),
     *                 @OA\Property(property="identificacao", type="string", example="A-101"),
     *                 @OA\Property(property="tipo", type="string", example="carro"),
     *                 @OA\Property(property="disponivel", type="string", example="S"),
     *                 @OA\Property(property="estacionamento_id", type="integer", example=1)
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $vagas = $this->vagaService->listarVagas();

        return response()->json($vagas, 200);
    }

    /**
     * @OA\Get(
     *     path="/vagas/estacionamento/{estacionamentoId}",
     *     tags={"Vagas"},
     *     summary="Lista vagas de um estacionamento",
     *     description="Retorna todas as vagas de um estacionamento específico",
     *
     *     @OA\Parameter(
     *         name="estacionamentoId",
     *         in="path",
     *         description="ID do estacionamento",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Lista retornada com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id_vaga", type="integer", example=1),
     *                 @OA\Property(property="identificacao", type="string", example="A-101"),
     *                 @OA\Property(property="tipo", type="string", example="carro"),
     *                 @OA\Property(property="disponivel", type="string", example="S"),
     *                 @OA\Property(property="estacionamento_id", type="integer", example=1)
     *             )
     *         )
     *     )
     * )
     */
    public function listarPorEstacionamento(int $estacionamentoId): JsonResponse
    {
        $vagas = $this->vagaService->listarVagasPorEstacionamento($estacionamentoId);

        return response()->json($vagas, 200);
    }

    /**
     * @OA\Post(
     *     path="/vagas",
     *     tags={"Vagas"},
     *     summary="Cria uma nova vaga",
     *     description="Cadastra uma nova vaga no sistema",
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"identificacao", "tipo", "estacionamento_id"},
     *
     *             @OA\Property(property="identificacao", type="string", maxLength=20, example="A-101", description="Identificação única da vaga"),
     *             @OA\Property(property="tipo", type="string", enum={"carro", "moto", "deficiente", "idoso", "eletrico", "outro"}, example="carro"),
     *             @OA\Property(property="disponivel", type="string", enum={"S", "N"}, example="S", description="S=Disponível, N=Ocupada"),
     *             @OA\Property(property="estacionamento_id", type="integer", example=1)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Vaga criada com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Vaga criada com sucesso."),
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
     *             @OA\Property(property="error", type="string", example="Erro ao criar vaga."),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'identificacao' => [
                'required',
                'string',
                'max:20',
                Rule::unique('vagas', 'identificacao')
                    ->where('estacionamento_id', $request->estacionamento_id)
            ],
            'tipo' => 'required|in:carro,moto,deficiente,idoso,eletrico,outro',
            'disponivel' => 'nullable|in:S,N',
            'estacionamento_id' => 'required|integer|exists:estacionamentos,id_estacionamento',
        ], [
            'identificacao.required' => 'O campo identificação é obrigatório.',
            'identificacao.string' => 'O campo identificação deve ser um texto.',
            'identificacao.max' => 'O campo identificação não pode ter mais de 20 caracteres.',
            'identificacao.unique' => 'Já existe uma vaga com esta identificação neste estacionamento.',
            'tipo.required' => 'O campo tipo é obrigatório.',
            'tipo.in' => 'O campo tipo deve ser: carro, moto, deficiente, idoso, eletrico ou outro.',
            'disponivel.in' => 'O campo disponível deve ser S (Sim) ou N (Não).',
            'estacionamento_id.required' => 'O campo estacionamento é obrigatório.',
            'estacionamento_id.integer' => 'O campo estacionamento deve ser um número inteiro.',
            'estacionamento_id.exists' => 'O estacionamento informado não existe.',
        ]);

        try {
            $vaga = $this->vagaService->criarVaga($validated);

            return response()->json([
                'message' => 'Vaga criada com sucesso.',
                'data' => $vaga,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao criar vaga.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/vagas/{id}",
     *     tags={"Vagas"},
     *     summary="Exibe uma vaga específica",
     *     description="Retorna os dados de uma vaga pelo ID",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da vaga",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Vaga encontrada",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="id_vaga", type="integer", example=1),
     *             @OA\Property(property="identificacao", type="string", example="A-101"),
     *             @OA\Property(property="tipo", type="string", example="carro"),
     *             @OA\Property(property="disponivel", type="string", example="S"),
     *             @OA\Property(property="estacionamento_id", type="integer", example=1)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Vaga não encontrada",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Vaga não encontrada."),
     *             @OA\Property(property="message", type="string", example="A vaga com o ID informado não existe.")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $vaga = $this->vagaService->buscarVagaPorId($id);

            return response()->json($vaga, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Vaga não encontrada.',
                'message' => 'A vaga com o ID informado não existe.',
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/vagas/{id}",
     *     tags={"Vagas"},
     *     summary="Atualiza uma vaga",
     *     description="Atualiza os dados de uma vaga existente",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da vaga",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"identificacao", "tipo", "estacionamento_id"},
     *
     *             @OA\Property(property="identificacao", type="string", maxLength=20, example="A-102"),
     *             @OA\Property(property="tipo", type="string", enum={"carro", "moto", "deficiente", "idoso", "eletrico", "outro"}, example="moto"),
     *             @OA\Property(property="disponivel", type="string", enum={"S", "N"}, example="N"),
     *             @OA\Property(property="estacionamento_id", type="integer", example=1)
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Vaga atualizada com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Vaga atualizada com sucesso."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Vaga não encontrada",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Vaga não encontrada."),
     *             @OA\Property(property="message", type="string", example="A vaga com o ID informado não existe.")
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
     *             @OA\Property(property="error", type="string", example="Erro ao atualizar vaga."),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'identificacao' => [
                'required',
                'string',
                'max:20',
                Rule::unique('vagas', 'identificacao')
                    ->where('estacionamento_id', $request->estacionamento_id)
                    ->ignore($id, 'id_vaga')
            ],
            'tipo' => 'required|in:carro,moto,deficiente,idoso,eletrico,outro',
            'disponivel' => 'nullable|in:S,N',
            'estacionamento_id' => 'required|integer|exists:estacionamentos,id_estacionamento',
        ], [
            'identificacao.required' => 'O campo identificação é obrigatório.',
            'identificacao.string' => 'O campo identificação deve ser um texto.',
            'identificacao.max' => 'O campo identificação não pode ter mais de 20 caracteres.',
            'identificacao.unique' => 'Já existe uma vaga com esta identificação neste estacionamento.',
            'tipo.required' => 'O campo tipo é obrigatório.',
            'tipo.in' => 'O campo tipo deve ser: carro, moto, deficiente, idoso, eletrico ou outro.',
            'disponivel.in' => 'O campo disponível deve ser S (Sim) ou N (Não).',
            'estacionamento_id.required' => 'O campo estacionamento é obrigatório.',
            'estacionamento_id.integer' => 'O campo estacionamento deve ser um número inteiro.',
            'estacionamento_id.exists' => 'O estacionamento informado não existe.',
        ]);

        try {
            $vaga = $this->vagaService->atualizarVaga($id, $validated);

            return response()->json([
                'message' => 'Vaga atualizada com sucesso.',
                'data' => $vaga,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Vaga não encontrada.',
                'message' => 'A vaga com o ID informado não existe.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao atualizar vaga.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/vagas/{id}",
     *     tags={"Vagas"},
     *     summary="Remove uma vaga",
     *     description="Deleta uma vaga do sistema",
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID da vaga",
     *         required=true,
     *
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Vaga removida com sucesso",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string", example="Vaga removida com sucesso.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Vaga não encontrada",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Vaga não encontrada."),
     *             @OA\Property(property="message", type="string", example="A vaga com o ID informado não existe.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao remover",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="error", type="string", example="Erro ao remover vaga."),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->vagaService->deletarVaga($id);

            return response()->json([
                'message' => 'Vaga removida com sucesso.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Vaga não encontrada.',
                'message' => 'A vaga com o ID informado não existe.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao remover vaga.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
