<?php

namespace App\Docs;

use Illuminate\Support\Facades\Artisan;

class MergeSwaggerDocs
{
    public static function gerarDocumentacaoUnificada(): void
    {
        echo "🚀 Gerando e unificando documentação Swagger...\n";

        $docsFile = config('l5-swagger.documentations.default.paths.docs_json', 'api-docs.json');
        $authBase = base_path('../smartpark-auth');

        $backendJson = storage_path("api-docs/{$docsFile}");
        $authJson = "{$authBase}/storage/api-docs/{$docsFile}";
        $finalJson = $backendJson;

        // === 1. Gera documentação do AUTH ===
        echo "🔐 Gerando Swagger do Auth...\n";
        if (! is_dir($authBase)) {
            throw new \Exception("❌ Diretório do Auth não encontrado: {$authBase}");
        }
        chdir($authBase);
        exec('php artisan config:clear');
        exec('php artisan l5-swagger:generate');
        chdir(base_path());

        // === 2. Gera documentação do BACKEND ===
        echo "📘 Gerando Swagger do Backend...\n";
        Artisan::call('config:clear');
        Artisan::call('l5-swagger:generate');

        // === 3. Valida existência dos JSONs ===
        if (! file_exists($authJson) || ! file_exists($backendJson)) {
            throw new \Exception("❌ Arquivo de documentação não encontrado.\nAuth: {$authJson}\nBackend: {$backendJson}");
        }

        echo "🧩 Mesclando documentação...\n";

        $auth = json_decode(file_get_contents($authJson), true);
        $backend = json_decode(file_get_contents($backendJson), true);

        if (! $auth || ! $backend) {
            throw new \Exception('❌ Erro ao decodificar JSON de Auth ou Backend.');
        }

        // === 4. Combina rotas, tags e schemas ===
        $final = $backend;
        $pathsCorrigidos = [];

        // AUTH → adiciona /api se necessário
        foreach ($auth['paths'] ?? [] as $rota => $def) {
            $rotaCorrigida = str_starts_with($rota, '/api/') ? $rota : '/api'.$rota;
            foreach ($def as &$m) {
                $m['servers'] = [[
                    'url' => env('AUTH_SERVICE_URL', 'http://127.0.0.1:9000/api'),
                    'description' => 'Serviço de Autenticação',
                ]];
            }
            $pathsCorrigidos[$rotaCorrigida] = $def;
        }

        // BACKEND → adiciona /api e remove duplicados antigos
        foreach ($backend['paths'] ?? [] as $rota => $def) {
            $rotaCorrigida = str_starts_with($rota, '/api/') ? $rota : '/api'.$rota;

            // 🔹 remove a versão antiga sem /api
            unset($final['paths'][$rota]);

            foreach ($def as &$m) {
                $m['servers'] = [[
                    'url' => env('APP_URL', 'http://127.0.0.1:8000/api'),
                    'description' => 'SmartPark Backend',
                ]];
            }

            $pathsCorrigidos[$rotaCorrigida] = $def;
        }

        $final['paths'] = $pathsCorrigidos;

        // Tags únicas
        $final['tags'] = collect(array_merge($auth['tags'] ?? [], $backend['tags'] ?? []))
            ->unique('name')
            ->values()
            ->all();

        // Schemas
        $final['components']['schemas'] = array_merge(
            $auth['components']['schemas'] ?? [],
            $backend['components']['schemas'] ?? []
        );

        // === 🔐 Mescla os securitySchemes (JWT, etc.) ===
        $final['components']['securitySchemes'] = array_merge(
            $auth['components']['securitySchemes'] ?? [],
            $backend['components']['securitySchemes'] ?? []
        );

        // === 🔒 Define segurança global para rotas protegidas ===
        $final['security'] = [
            ['bearerAuth' => []],
        ];

        // Servidores globais
        $final['servers'] = [
            ['url' => env('APP_URL', 'http://127.0.0.1:8000/api'), 'description' => 'SmartPark Backend'],
            ['url' => env('AUTH_SERVICE_URL', 'http://127.0.0.1:9000/api'), 'description' => 'Serviço de Autenticação'],
        ];

        // === 5. Grava o resultado ===
        file_put_contents(
            $finalJson,
            json_encode($final, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );

        echo "✅ Documentação unificada gerada com sucesso!\n";
        echo "📘 Caminho final: {$finalJson}\n";
    }
}
