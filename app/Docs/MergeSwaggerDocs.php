<?php

namespace App\Docs;

/**
 * Classe responsável por unir (mesclar) a documentação Swagger
 * do SmartPark Backend e do SmartPark Auth em um único arquivo JSON.
 */
class MergeSwaggerDocs
{
    /**
     * Gera a documentação unificada (Auth + Backend).
     *
     * @throws \Exception Caso algum arquivo JSON não seja encontrado ou esteja inválido.
     */
    public static function gerarDocumentacaoUnificada(): void
    {
        // Caminhos absolutos dos arquivos JSON de origem
        $caminhoJsonBackend = storage_path('api-docs/api-docs.json');
        $caminhoJsonAuth = base_path('../smartpark-auth/storage/api-docs/api-docs.json');

        // Caminho de saída (arquivo final mesclado)
        $caminhoJsonFinal = storage_path('api-docs/swaggerMesclado.json');

        // --- Validação da existência dos arquivos ---
        if (! file_exists($caminhoJsonAuth)) {
            throw new \Exception("❌ Arquivo de documentação do AUTH não encontrado em: {$caminhoJsonAuth}");
        }

        if (! file_exists($caminhoJsonBackend)) {
            throw new \Exception("❌ Arquivo de documentação do BACKEND não encontrado em: {$caminhoJsonBackend}");
        }

        // --- Leitura dos arquivos JSON ---
        $documentacaoAuth = json_decode(file_get_contents($caminhoJsonAuth), true);
        $documentacaoBackend = json_decode(file_get_contents($caminhoJsonBackend), true);

        if (! $documentacaoAuth || ! $documentacaoBackend) {
            throw new \Exception('❌ Erro ao decodificar um dos arquivos JSON (Auth ou Backend).');
        }

        // --- Combinação das rotas (paths) com servidores automáticos ---
        $rotasCombinadas = [];

        // Rotas do AUTH → servidor 9000
        foreach ($documentacaoAuth['paths'] ?? [] as $rota => $definicao) {
            // ✅ Garante que o prefixo /api exista nas rotas do AUTH
            $rotaCorrigida = str_starts_with($rota, '/api/') ? $rota : '/api'.$rota;

            foreach ($definicao as &$metodo) {
                $metodo['servers'] = [[
                    'url' => env('AUTH_SERVICE_URL', 'http://127.0.0.1:9000/api'),
                    'description' => 'Serviço de Autenticação (JWT)',
                ]];
            }

            $rotasCombinadas[$rotaCorrigida] = $definicao;
        }

        // Rotas do BACKEND → servidor 8000
        foreach ($documentacaoBackend['paths'] ?? [] as $rota => $definicao) {
            // ✅ Garante que o prefixo /api exista também no backend
            $rotaCorrigida = str_starts_with($rota, '/api/') ? $rota : '/api'.$rota;

            foreach ($definicao as &$metodo) {
                $metodo['servers'] = [[
                    'url' => env('APP_URL', 'http://127.0.0.1:8000/api'),
                    'description' => 'SmartPark Backend (API principal)',
                ]];
            }

            $rotasCombinadas[$rotaCorrigida] = $definicao;
        }

        // --- Combinação das tags (sem sobrescrever) ---
        $todasAsTags = [];
        $origensTags = [
            $documentacaoAuth['tags'] ?? [],
            $documentacaoBackend['tags'] ?? [],
        ];

        foreach ($origensTags as $lista) {
            foreach ($lista as $tag) {
                $nome = $tag['name'] ?? null;
                if ($nome && ! collect($todasAsTags)->contains(fn ($t) => $t['name'] === $nome)) {
                    $todasAsTags[] = $tag;
                }
            }
        }

        // Reordena para garantir que “Autenticação” e “Usuários” fiquem no topo
        usort($todasAsTags, function ($tagA, $tagB) {
            $prioridades = [
                'Autenticação' => 1,
                'Usuários' => 2,
            ];

            $ordemA = $prioridades[$tagA['name']] ?? 99;
            $ordemB = $prioridades[$tagB['name']] ?? 99;

            return $ordemA <=> $ordemB;
        });

        // --- Combinação dos components (schemas) ---
        $schemasCombinados = array_merge(
            $documentacaoAuth['components']['schemas'] ?? [],
            $documentacaoBackend['components']['schemas'] ?? []
        );

        // --- Montagem da documentação final ---
        $documentacaoFinal = $documentacaoBackend;
        $documentacaoFinal['paths'] = $rotasCombinadas;
        $documentacaoFinal['tags'] = $todasAsTags;
        $documentacaoFinal['components']['schemas'] = $schemasCombinados;

        // --- Define os servidores base globais ---
        $documentacaoFinal['servers'] = [
            [
                'url' => env('APP_URL', 'http://127.0.0.1:8000/api'),
                'description' => 'SmartPark Backend (API principal)',
            ],
            [
                'url' => env('AUTH_SERVICE_URL', 'http://127.0.0.1:9000/api'),
                'description' => 'Serviço de Autenticação (JWT)',
            ],
        ];

        // --- Geração do arquivo final ---
        file_put_contents(
            $caminhoJsonFinal,
            json_encode($documentacaoFinal, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );

        echo "\n✅ Documentação unificada gerada com sucesso!\n";
        echo "💡 As rotas do AUTH usam automaticamente a porta 9000.\n";
        echo "💡 As rotas do BACKEND usam automaticamente a porta 8000.\n";
    }
}
