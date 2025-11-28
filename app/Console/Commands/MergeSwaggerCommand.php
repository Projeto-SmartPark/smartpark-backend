<?php

namespace App\Console\Commands;

use App\Docs\MergeSwaggerDocs;
use Illuminate\Console\Command;

class MergeSwaggerCommand extends Command
{
    protected $signature = 'swagger:merge';

    protected $description = 'Gera a documentação Swagger unificada (Auth + Backend)';

    public function handle(): void
    {
        $this->info('🚀 Iniciando geração e unificação da documentação Swagger...');

        try {
            MergeSwaggerDocs::gerarDocumentacaoUnificada();
            $this->info('✅ Documentação unificada com sucesso!');
        } catch (\Exception $e) {
            $this->error('❌ Erro ao gerar documentação: '.$e->getMessage());
        }
    }
}
