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

        // --- Combinação das rotas (paths) ---
        $rotasCombinadas = array_merge(
            $documentacaoAuth['paths'] ?? [],
            $documentacaoBackend['paths'] ?? []
        );

        // --- Combinação das tags, priorizando Autenticação e Usuários ---
        $todasAsTags = array_merge(
            $documentacaoAuth['tags'] ?? [],
            $documentacaoBackend['tags'] ?? []
        );

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

        // --- Combinação dos componentes (schemas) ---
        $schemasCombinados = array_merge(
            $documentacaoAuth['components']['schemas'] ?? [],
            $documentacaoBackend['components']['schemas'] ?? []
        );

        // --- Montagem da documentação final ---
        $documentacaoFinal = $documentacaoBackend;
        $documentacaoFinal['paths'] = $rotasCombinadas;
        $documentacaoFinal['tags'] = $todasAsTags;
        $documentacaoFinal['components']['schemas'] = $schemasCombinados;

        // --- Geração do arquivo final ---
        file_put_contents(
            $caminhoJsonFinal,
            json_encode($documentacaoFinal, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );

        echo "\n✅ Documentação unificada gerada com sucesso!\n";
        echo "📂 Caminho do arquivo final: {$caminhoJsonFinal}\n";
        echo "💡 Ordem de exibição: Autenticação → Usuários → Demais módulos\n\n";
    }
}
