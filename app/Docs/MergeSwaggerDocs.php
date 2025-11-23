<?php

namespace App\Docs;

use Illuminate\Support\Facades\Artisan;

class MergeSwaggerDocs
{
    public static function gerarDocumentacaoUnificada(): void
    {
        echo "🚀 Iniciando geração da documentação Swagger...\n";

        $docsFile = config('l5-swagger.documentations.default.paths.docs_json', 'api-docs.json');

        // === Caminhos ===
        $backendJson = storage_path("api-docs/{$docsFile}");
        $finalJson   = $backendJson;

        // === 1. Baixa documentação do Auth via HTTP ===
        echo "🔐 Baixando documentação do Auth...\n";

        $authUrl = rtrim(env('AUTH_SERVICE_URL', 'http://smartpark-auth:8000/api'), '/') . '/docs';

        $authResponse = @file_get_contents($authUrl);
        if (! $authResponse) {
            throw new \Exception("❌ Não foi possível obter Swagger do Auth: {$authUrl}");
        }

        $auth = json_decode($authResponse, true);
        if (! $auth) {
            throw new \Exception("❌ JSON inválido recebido do Auth.");
        }

        // === 2. Gera documentação do Backend ===
        echo "📘 Gerando Swagger do Backend...\n";
        Artisan::call('config:clear');
        Artisan::call('l5-swagger:generate');

        if (! file_exists($backendJson)) {
            throw new \Exception("❌ Swagger do Backend não encontrado em {$backendJson}");
        }

        $backend = json_decode(file_get_contents($backendJson), true);
        if (! $backend) {
            throw new \Exception("❌ JSON inválido do Backend.");
        }

        echo "🧩 Mesclando documentação...\n";

        // === 3. Novo objeto final ===
        $final = $backend;

        $paths = [];

        // AUTH — corrige rotas removendo duplicação de /api
        foreach ($auth['paths'] as $rota => $def) {
            // Remove /api duplicado se já existir
            $rotaCorrigida = $rota;

            foreach ($def as &$m) {
                $m['servers'] = [[
                    'url'         => 'http://localhost:9000/api',
                    'description' => 'Serviço de Autenticação',
                ]];
            }

            $paths[$rotaCorrigida] = $def;
        }

        // BACKEND — mesma lógica
        foreach ($backend['paths'] as $rota => $def) {
            $rotaCorrigida = $rota;

            foreach ($def as &$m) {
                $m['servers'] = [[
                    'url'         => 'http://localhost:8000/api',
                    'description' => 'SmartPark Backend',
                ]];
            }

            $paths[$rotaCorrigida] = $def;
        }

        $final['paths'] = $paths;

        // === Tags ===
        $final['tags'] = collect(array_merge($auth['tags'] ?? [], $backend['tags'] ?? []))
            ->unique('name')
            ->values()
            ->all();

        // === Schemas ===
        $final['components']['schemas'] = array_merge(
            $auth['components']['schemas'] ?? [],
            $backend['components']['schemas'] ?? []
        );

        // === Security schemes ===
        $final['components']['securitySchemes'] = array_merge(
            $auth['components']['securitySchemes'] ?? [],
            $backend['components']['securitySchemes'] ?? []
        );

        // === Segurança global ===
        $final['security'] = [
            ['bearerAuth' => []],
        ];

        // === Servidores gerais ===
        $final['servers'] = [
            ['url' => 'http://localhost:8000/api', 'description' => 'SmartPark Backend'],
            ['url' => 'http://localhost:9000/api', 'description' => 'Serviço de Autenticação'],
        ];

        // === 4. Salva JSON final ===
        file_put_contents(
            $finalJson,
            json_encode($final, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );

        echo "✅ Documentação unificada gerada com sucesso!\n";
        echo "📘 Caminho final: {$finalJson}\n";
    }
}
