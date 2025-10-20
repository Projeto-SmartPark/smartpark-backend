<?php

namespace App\Modules\Usuarios;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Throwable;

class GestorController extends Controller
{
    // Lista todos os gestores
    public function index()
    {
        return Gestor::all();
    }

    // Exibe um gestor específico
    public function show($id)
    {
        return Gestor::findOrFail($id);
    }

    // Atualiza dados de um gestor com validação de email único
    public function update(Request $request, $id)
    {
        try {
            // Validação dos dados
            $dados = $request->validate([
                'nome'  => 'required|string|max:100',
                'email' => 'required|email|max:100',
                'senha' => 'required|string|max:100',
                'cnpj'  => 'required|string|max:20',
            ]);

            $gestor = Gestor::findOrFail($id);

            // Verifica se o email já existe em outro gestor
            $emailExiste = Gestor::where('email', $dados['email'])
                ->where('id_gestor', '!=', $id)
                ->exists();

            if ($emailExiste) {
                return response()->json([
                    'error' => 'Email já cadastrado.',
                    'message' => 'Já existe outro gestor com este email.'
                ], 409);
            }

            $gestor->update($dados);

            return response()->json(['message' => 'Gestor atualizado com sucesso.']);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Gestor não encontrado.',
                'message' => 'O gestor com o ID informado não existe.'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Dados inválidos.',
                'message' => 'Os dados fornecidos não são válidos.',
                'errors' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'error' => 'Erro ao atualizar gestor.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Remove um gestor (implementar soft delete)
    public function destroy($id)
    {
        Gestor::destroy($id);
        return response()->json(['message' => 'Gestor removido com sucesso.']);
    }
}
