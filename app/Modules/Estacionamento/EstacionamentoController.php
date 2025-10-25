<?php

namespace App\Modules\Estacionamento;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class EstacionamentoController extends Controller
{
    private EstacionamentoService $estacionamentoService;

    public function __construct(EstacionamentoService $estacionamentoService)
    {
        $this->estacionamentoService = $estacionamentoService;
    }

    /**
     * @OA\Get(
     *     path="/estacionamentos",
     *     tags={"Estacionamentos"},
     *     summary="Lista todos os estacionamentos",
     *     description="Retorna uma lista com todos os estacionamentos cadastrados",
     *     @OA\Response(
     *         response=200,
     *         description="Lista retornada com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id_estacionamento", type="integer", example=1),
     *                 @OA\Property(property="nome", type="string", example="Estacionamento Central"),
     *                 @OA\Property(property="capacidade", type="integer", example=100),
     *                 @OA\Property(property="hora_abertura", type="string", example="08:00:00"),
     *                 @OA\Property(property="hora_fechamento", type="string", example="22:00:00"),
     *                 @OA\Property(property="lotado", type="string", example="N"),
     *                 @OA\Property(property="gestor_id", type="integer", example=1),
     *                 @OA\Property(property="endereco_id", type="integer", example=1)
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $estacionamentos = $this->estacionamentoService->listarEstacionamentos();
        return response()->json($estacionamentos, 200);
    }

    /**
     * @OA\Post(
     *     path="/estacionamentos",
     *     tags={"Estacionamentos"},
     *     summary="Cria um novo estacionamento",
     *     description="Cadastra um novo estacionamento no sistema com telefones",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome", "capacidade", "hora_abertura", "hora_fechamento", "gestor_id", "endereco_id", "telefones"},
             @OA\Property(property="nome", type="string", maxLength=100, example="Estacionamento Central"),
             @OA\Property(property="capacidade", type="integer", example=100),
             @OA\Property(property="hora_abertura", type="string", format="time", example="08:00:00"),
             @OA\Property(property="hora_fechamento", type="string", format="time", example="22:00:00"),
             @OA\Property(property="lotado", type="string", enum={"S", "N"}, example="N", description="S=Sim, N=Não"),
             @OA\Property(property="gestor_id", type="integer", example=1),
             @OA\Property(property="endereco_id", type="integer", example=1),
             @OA\Property(
                 property="telefones",
                 type="array",
                 description="Array de telefones (mínimo 1, máximo 2)",
                 @OA\Items(
                     type="object",
                     required={"ddd", "numero"},
                     @OA\Property(property="ddd", type="string", maxLength=2, example="11"),
                     @OA\Property(property="numero", type="string", maxLength=9, example="987654321")
                 )
             )
         )
     ),
     @OA\Response(
         response=201,
         description="Estacionamento criado com sucesso",
         @OA\JsonContent(
             @OA\Property(property="message", type="string", example="Estacionamento criado com sucesso."),
             @OA\Property(property="data", type="object")
         )
     ),
     @OA\Response(
         response=422,
         description="Dados inválidos",
         @OA\JsonContent(
             @OA\Property(property="message", type="string"),
             @OA\Property(property="errors", type="object")
         )
     ),
     @OA\Response(
         response=500,
         description="Erro no servidor",
         @OA\JsonContent(
             @OA\Property(property="error", type="string", example="Erro ao criar estacionamento."),
             @OA\Property(property="message", type="string")
         )
     )
 )
 */
public function store(Request $request): JsonResponse
{
    $validated = $request->validate([
        'nome' => 'required|string|max:100',
        'capacidade' => 'required|integer|min:1',
        'hora_abertura' => 'required|date_format:H:i:s',
        'hora_fechamento' => 'required|date_format:H:i:s',
        'lotado' => 'nullable|in:S,N',
        'gestor_id' => 'required|integer|exists:gestores,id_gestor',
        'endereco_id' => 'required|integer|exists:enderecos,id_endereco',
        'telefones' => 'required|array|min:1|max:2',
        'telefones.*.ddd' => 'required|string|size:2',
        'telefones.*.numero' => 'required|string|max:9',
    ], [
        'nome.required' => 'O campo nome é obrigatório.',
        'nome.string' => 'O campo nome deve ser um texto.',
        'nome.max' => 'O campo nome não pode ter mais de 100 caracteres.',
        'capacidade.required' => 'O campo capacidade é obrigatório.',
        'capacidade.integer' => 'O campo capacidade deve ser um número inteiro.',
        'capacidade.min' => 'O campo capacidade deve ser no mínimo 1.',
        'hora_abertura.required' => 'O campo hora de abertura é obrigatório.',
        'hora_abertura.date_format' => 'O campo hora de abertura deve estar no formato HH:MM:SS.',
        'hora_fechamento.required' => 'O campo hora de fechamento é obrigatório.',
        'hora_fechamento.date_format' => 'O campo hora de fechamento deve estar no formato HH:MM:SS.',
        'lotado.in' => 'O campo lotado deve ser S (Sim) ou N (Não).',
        'gestor_id.required' => 'O campo gestor é obrigatório.',
        'gestor_id.integer' => 'O campo gestor deve ser um número inteiro.',
        'gestor_id.exists' => 'O gestor informado não existe.',
        'endereco_id.required' => 'O campo endereço é obrigatório.',
        'endereco_id.integer' => 'O campo endereço deve ser um número inteiro.',
        'endereco_id.exists' => 'O endereço informado não existe.',
        'telefones.required' => 'É obrigatório informar pelo menos um telefone.',
        'telefones.array' => 'Os telefones devem ser informados em formato de array.',
        'telefones.min' => 'É obrigatório informar pelo menos 1 telefone.',
        'telefones.max' => 'É permitido informar no máximo 2 telefones.',
        'telefones.*.ddd.required' => 'O DDD do telefone é obrigatório.',
        'telefones.*.ddd.string' => 'O DDD deve ser um texto.',
        'telefones.*.ddd.size' => 'O DDD deve ter exatamente 2 caracteres.',
        'telefones.*.numero.required' => 'O número do telefone é obrigatório.',
        'telefones.*.numero.string' => 'O número deve ser um texto.',
        'telefones.*.numero.max' => 'O número não pode ter mais de 9 caracteres.',
    ]);

    try {
        $telefones = $validated['telefones'];
        unset($validated['telefones']);
        
        $estacionamento = $this->estacionamentoService->criarEstacionamento($validated, $telefones);
        return response()->json([
            'message' => 'Estacionamento criado com sucesso.',
            'data' => $estacionamento
        ], 201);
    } catch (Exception $e) {
        return response()->json([
            'error' => 'Erro ao criar estacionamento.',
            'message' => $e->getMessage()
        ], 500);
    }
}

    /**
     * @OA\Get(
     *     path="/estacionamentos/{id}",
     *     tags={"Estacionamentos"},
     *     summary="Exibe um estacionamento específico",
     *     description="Retorna os dados de um estacionamento pelo ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do estacionamento",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estacionamento encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="id_estacionamento", type="integer", example=1),
     *             @OA\Property(property="nome", type="string", example="Estacionamento Central"),
     *             @OA\Property(property="capacidade", type="integer", example=100),
     *             @OA\Property(property="hora_abertura", type="string", example="08:00:00"),
     *             @OA\Property(property="hora_fechamento", type="string", example="22:00:00"),
     *             @OA\Property(property="lotado", type="string", example="N"),
     *             @OA\Property(property="gestor_id", type="integer", example=1),
     *             @OA\Property(property="endereco_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estacionamento não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Estacionamento não encontrado."),
     *             @OA\Property(property="message", type="string", example="O estacionamento com o ID informado não existe.")
     *         )
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $estacionamento = $this->estacionamentoService->buscarEstacionamentoPorId($id);
            return response()->json($estacionamento, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Estacionamento não encontrado.',
                'message' => 'O estacionamento com o ID informado não existe.'
            ], 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/estacionamentos/{id}",
     *     tags={"Estacionamentos"},
     *     summary="Atualiza um estacionamento",
     *     description="Atualiza os dados de um estacionamento existente",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do estacionamento",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome", "capacidade", "hora_abertura", "hora_fechamento", "gestor_id", "endereco_id", "telefones"},
     *             @OA\Property(property="nome", type="string", maxLength=100, example="Estacionamento Central"),
     *             @OA\Property(property="capacidade", type="integer", example=100),
     *             @OA\Property(property="hora_abertura", type="string", format="time", example="08:00:00"),
     *             @OA\Property(property="hora_fechamento", type="string", format="time", example="22:00:00"),
     *             @OA\Property(property="lotado", type="string", enum={"S", "N"}, example="N"),
     *             @OA\Property(property="gestor_id", type="integer", example=1),
     *             @OA\Property(property="endereco_id", type="integer", example=1),
     *             @OA\Property(
     *                 property="telefones",
     *                 type="array",
     *                 description="Array de telefones (mínimo 1, máximo 2)",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"ddd", "numero"},
     *                     @OA\Property(property="ddd", type="string", maxLength=2, example="11"),
     *                     @OA\Property(property="numero", type="string", maxLength=9, example="987654321")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estacionamento atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Estacionamento atualizado com sucesso."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estacionamento não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Estacionamento não encontrado."),
     *             @OA\Property(property="message", type="string", example="O estacionamento com o ID informado não existe.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro no servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erro ao atualizar estacionamento."),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'capacidade' => 'required|integer|min:1',
            'hora_abertura' => 'required|date_format:H:i:s',
            'hora_fechamento' => 'required|date_format:H:i:s',
            'lotado' => 'nullable|in:S,N',
            'gestor_id' => 'required|integer|exists:gestores,id_gestor',
            'endereco_id' => 'required|integer|exists:enderecos,id_endereco',
            'telefones' => 'required|array|min:1|max:2',
            'telefones.*.ddd' => 'required|string|size:2',
            'telefones.*.numero' => 'required|string|max:9',
        ], [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string' => 'O campo nome deve ser um texto.',
            'nome.max' => 'O campo nome não pode ter mais de 100 caracteres.',
            'capacidade.required' => 'O campo capacidade é obrigatório.',
            'capacidade.integer' => 'O campo capacidade deve ser um número inteiro.',
            'capacidade.min' => 'O campo capacidade deve ser no mínimo 1.',
            'hora_abertura.required' => 'O campo hora de abertura é obrigatório.',
            'hora_abertura.date_format' => 'O campo hora de abertura deve estar no formato HH:MM:SS.',
            'hora_fechamento.required' => 'O campo hora de fechamento é obrigatório.',
            'hora_fechamento.date_format' => 'O campo hora de fechamento deve estar no formato HH:MM:SS.',
            'lotado.in' => 'O campo lotado deve ser S (Sim) ou N (Não).',
            'gestor_id.required' => 'O campo gestor é obrigatório.',
            'gestor_id.integer' => 'O campo gestor deve ser um número inteiro.',
            'gestor_id.exists' => 'O gestor informado não existe.',
            'endereco_id.required' => 'O campo endereço é obrigatório.',
            'endereco_id.integer' => 'O campo endereço deve ser um número inteiro.',
            'endereco_id.exists' => 'O endereço informado não existe.',
            'telefones.required' => 'É obrigatório informar pelo menos um telefone.',
            'telefones.array' => 'Os telefones devem ser informados em formato de array.',
            'telefones.min' => 'É obrigatório informar pelo menos 1 telefone.',
            'telefones.max' => 'É permitido informar no máximo 2 telefones.',
            'telefones.*.ddd.required' => 'O DDD do telefone é obrigatório.',
            'telefones.*.ddd.string' => 'O DDD deve ser um texto.',
            'telefones.*.ddd.size' => 'O DDD deve ter exatamente 2 caracteres.',
            'telefones.*.numero.required' => 'O número do telefone é obrigatório.',
            'telefones.*.numero.string' => 'O número deve ser um texto.',
            'telefones.*.numero.max' => 'O número não pode ter mais de 9 caracteres.',
        ]);

        try {
            $telefones = $validated['telefones'];
            unset($validated['telefones']);
            
            $estacionamento = $this->estacionamentoService->atualizarEstacionamento($id, $validated, $telefones);
            return response()->json([
                'message' => 'Estacionamento atualizado com sucesso.',
                'data' => $estacionamento
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Estacionamento não encontrado.',
                'message' => 'O estacionamento com o ID informado não existe.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao atualizar estacionamento.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/estacionamentos/{id}",
     *     tags={"Estacionamentos"},
     *     summary="Remove um estacionamento",
     *     description="Deleta um estacionamento do sistema",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do estacionamento",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Estacionamento removido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Estacionamento removido com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Estacionamento não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Estacionamento não encontrado."),
     *             @OA\Property(property="message", type="string", example="O estacionamento com o ID informado não existe.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao remover",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erro ao remover estacionamento."),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->estacionamentoService->deletarEstacionamento($id);
            return response()->json([
                'message' => 'Estacionamento removido com sucesso.'
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Estacionamento não encontrado.',
                'message' => 'O estacionamento com o ID informado não existe.'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao remover estacionamento.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
