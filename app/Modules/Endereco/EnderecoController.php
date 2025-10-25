<?php

namespace App\Modules\Endereco;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;
use Throwable;

class EnderecoController extends Controller
{
    private EnderecoService $enderecoService;

    public function __construct(EnderecoService $enderecoService)
    {
        $this->enderecoService = $enderecoService;
    }

    /**
     * @OA\Get(
     *     path="/enderecos",
     *     summary="Listar todos os endereços",
     *     tags={"Endereços"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de endereços retornada com sucesso",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="cep", type="string", example="12345678"),
     *                 @OA\Property(property="estado", type="string", example="SP"),
     *                 @OA\Property(property="cidade", type="string", example="São Paulo"),
     *                 @OA\Property(property="bairro", type="string", example="Centro"),
     *                 @OA\Property(property="numero", type="string", example="123"),
     *                 @OA\Property(property="logradouro", type="string", example="Rua das Flores"),
     *                 @OA\Property(property="complemento", type="string", example="Apto 101"),
     *                 @OA\Property(property="ponto_referencia", type="string", example="Próximo ao mercado"),
     *                 @OA\Property(property="latitude", type="number", format="float", example=-23.550520),
     *                 @OA\Property(property="longitude", type="number", format="float", example=-46.633308)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erro ao buscar endereços")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $enderecos = $this->enderecoService->listarEnderecos();
            return response()->json($enderecos, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao buscar endereços'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/enderecos",
     *     summary="Criar novo endereço",
     *     tags={"Endereços"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"cep", "estado", "cidade", "bairro", "numero", "logradouro"},
     *             @OA\Property(property="cep", type="string", maxLength=8, example="12345678"),
     *             @OA\Property(property="estado", type="string", maxLength=2, example="SP"),
     *             @OA\Property(property="cidade", type="string", maxLength=80, example="São Paulo"),
     *             @OA\Property(property="bairro", type="string", maxLength=80, example="Centro"),
     *             @OA\Property(property="numero", type="string", maxLength=10, example="123"),
     *             @OA\Property(property="logradouro", type="string", maxLength=120, example="Rua das Flores"),
     *             @OA\Property(property="complemento", type="string", maxLength=100, example="Apto 101"),
     *             @OA\Property(property="ponto_referencia", type="string", maxLength=100, example="Próximo ao mercado"),
     *             @OA\Property(property="latitude", type="number", format="float", example=-23.550520),
     *             @OA\Property(property="longitude", type="number", format="float", example=-46.633308)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Endereço criado com sucesso",
     *         @OA\JsonContent(
                 @OA\Property(property="id", type="integer", example=1),
                 @OA\Property(property="cep", type="string", example="12345678"),
                 @OA\Property(property="estado", type="string", example="SP"),
     *             @OA\Property(property="cidade", type="string", example="São Paulo"),
     *             @OA\Property(property="bairro", type="string", example="Centro"),
     *             @OA\Property(property="numero", type="string", example="123"),
     *             @OA\Property(property="logradouro", type="string", example="Rua das Flores"),
     *             @OA\Property(property="complemento", type="string", example="Apto 101"),
     *             @OA\Property(property="ponto_referencia", type="string", example="Próximo ao mercado"),
     *             @OA\Property(property="latitude", type="number", format="float", example=-23.550520),
     *             @OA\Property(property="longitude", type="number", format="float", example=-46.633308)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dados inválidos"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="cep", type="array", @OA\Items(type="string", example="O campo cep é obrigatório.")),
     *                 @OA\Property(property="estado", type="array", @OA\Items(type="string", example="O campo estado é obrigatório."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erro ao criar endereço")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cep' => 'required|string|size:8',
            'estado' => 'required|string|size:2',
            'cidade' => 'required|string|max:80',
            'bairro' => 'required|string|max:80',
            'numero' => 'required|string|max:10',
            'logradouro' => 'required|string|max:120',
            'complemento' => 'nullable|string|max:100',
            'ponto_referencia' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ], [
            'cep.required' => 'O campo cep é obrigatório.',
            'cep.size' => 'O campo cep deve ter 8 caracteres.',
            'estado.required' => 'O campo estado é obrigatório.',
            'estado.size' => 'O campo estado deve ter 2 caracteres.',
            'cidade.required' => 'O campo cidade é obrigatório.',
            'bairro.required' => 'O campo bairro é obrigatório.',
            'numero.required' => 'O campo numero é obrigatório.',
            'logradouro.required' => 'O campo logradouro é obrigatório.',
        ]);

        try {
            $endereco = $this->enderecoService->criarEndereco($validated);
            return response()->json($endereco, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao criar endereço'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/enderecos/{id}",
     *     summary="Buscar endereço por ID",
     *     tags={"Endereços"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Endereço encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="cep", type="string", example="12345678"),
     *             @OA\Property(property="estado", type="string", example="SP"),
     *             @OA\Property(property="cidade", type="string", example="São Paulo"),
     *             @OA\Property(property="bairro", type="string", example="Centro"),
     *             @OA\Property(property="numero", type="string", example="123"),
     *             @OA\Property(property="logradouro", type="string", example="Rua das Flores"),
     *             @OA\Property(property="complemento", type="string", example="Apto 101"),
     *             @OA\Property(property="ponto_referencia", type="string", example="Próximo ao mercado"),
     *             @OA\Property(property="latitude", type="number", format="float", example=-23.550520),
     *             @OA\Property(property="longitude", type="number", format="float", example=-46.633308)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Endereço não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Endereço não encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erro ao buscar endereço")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $endereco = $this->enderecoService->buscarEnderecoPorId($id);
            return response()->json($endereco, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Endereço não encontrado'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao buscar endereço'], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/enderecos/{id}",
     *     summary="Atualizar endereço",
     *     tags={"Endereços"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="cep", type="string", example="12345678"),
     *             @OA\Property(property="estado", type="string", maxLength=50, example="SP"),
     *             @OA\Property(property="cidade", type="string", maxLength=100, example="São Paulo"),
     *             @OA\Property(property="bairro", type="string", maxLength=100, example="Centro"),
     *             @OA\Property(property="numero", type="string", maxLength=10, example="123"),
     *             @OA\Property(property="logradouro", type="string", maxLength=100, example="Rua das Flores"),
     *             @OA\Property(property="complemento", type="string", maxLength=100, example="Apto 101"),
     *             @OA\Property(property="ponto_referencia", type="string", maxLength=100, example="Próximo ao mercado"),
     *             @OA\Property(property="latitude", type="number", format="float", example=-23.550520),
     *             @OA\Property(property="longitude", type="number", format="float", example=-46.633308)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Endereço atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="cep", type="string", example="12345678"),
     *             @OA\Property(property="estado", type="string", example="SP"),
     *             @OA\Property(property="cidade", type="string", example="São Paulo"),
     *             @OA\Property(property="bairro", type="string", example="Centro"),
     *             @OA\Property(property="numero", type="string", example="123"),
     *             @OA\Property(property="logradouro", type="string", example="Rua das Flores"),
     *             @OA\Property(property="complemento", type="string", example="Apto 101"),
     *             @OA\Property(property="ponto_referencia", type="string", example="Próximo ao mercado"),
     *             @OA\Property(property="latitude", type="number", format="float", example=-23.550520),
     *             @OA\Property(property="longitude", type="number", format="float", example=-46.633308)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Endereço não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Endereço não encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Dados inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dados inválidos"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="cep", type="array", @OA\Items(type="string", example="O campo cep deve ter no máximo 15 caracteres.")),
     *                 @OA\Property(property="estado", type="array", @OA\Items(type="string", example="O campo estado deve ter no máximo 50 caracteres."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erro ao atualizar endereço")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'cep' => 'sometimes|required|string|size:8',
            'estado' => 'sometimes|required|string|size:2',
            'cidade' => 'sometimes|required|string|max:80',
            'bairro' => 'sometimes|required|string|max:80',
            'numero' => 'sometimes|required|string|max:10',
            'logradouro' => 'sometimes|required|string|max:120',
            'complemento' => 'nullable|string|max:100',
            'ponto_referencia' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        try {
            $endereco = $this->enderecoService->atualizarEndereco($id, $validated);
            return response()->json($endereco, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Endereço não encontrado'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao atualizar endereço'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/enderecos/{id}",
     *     summary="Deletar endereço",
     *     tags={"Endereços"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Endereço deletado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Endereço deletado com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Endereço não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Endereço não encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro do servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erro ao deletar endereço")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->enderecoService->deletarEndereco($id);
            return response()->json(['message' => 'Endereço deletado com sucesso'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Endereço não encontrado'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Erro ao deletar endereço'], 500);
        }
    }
}
