<?php

namespace App\Modules\Usuarios;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Throwable;

class ClienteController extends Controller
{
    // Lista todos os clientes
    public function index()
    {
        return Cliente::all();
    }

    // Exibe um cliente específico
    public function show($id)
    {
        return Cliente::findOrFail($id);
    }

    // Atualiza dados de um cliente com validação de email único
    public function update(Request $request, $id)
    {
        try {
            // Validação dos dados
            $dados = $request->validate([
                'nome'  => 'required|string|max:100',
                'email' => 'required|email|max:100',
                'senha' => 'required|string|max:100',
            ]);

            $cliente = Cliente::findOrFail($id);

            // Verifica se o email já existe em outro cliente
            $emailExiste = Cliente::where('email', $dados['email'])
                ->where('id_cliente', '!=', $id)
                ->exists();

            if ($emailExiste) {
                return response()->json([
                    'error' => 'Email já cadastrado.',
                    'message' => 'Já existe outro cliente com este email.'
                ], 409);
            }

            $cliente->update($dados);

            return response()->json(['message' => 'Cliente atualizado com sucesso.']);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Cliente não encontrado.',
                'message' => 'O cliente com o ID informado não existe.'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Dados inválidos.',
                'message' => 'Os dados fornecidos não são válidos.',
                'errors' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'error' => 'Erro ao atualizar cliente.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Remove um cliente (implementar soft delete)
    public function destroy($id)
    {
        Cliente::destroy($id);
        return response()->json(['message' => 'Cliente removido com sucesso.']);
    }
}
