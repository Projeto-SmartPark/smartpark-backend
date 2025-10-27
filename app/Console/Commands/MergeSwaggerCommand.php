<?php

namespace App\Console\Commands;

use App\Docs\MergeSwaggerDocs;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class MergeSwaggerCommand extends Command
{
    protected $signature = 'swagger:merge';

    protected $description = 'Gera a documentação Swagger unificada (Auth + Backend)';

    public function handle(): void
    {
        $this->info('🚀 Iniciando geração e unificação da documentação Swagger...');

        // Gera o Swagger do backend
        $this->info('📘 Gerando Swagger do backend...');
        $this->executar(['php', 'artisan', 'l5-swagger:generate']);

        // Gera o Swagger do Auth (no outro repositório)
        $this->info('🔐 Gerando Swagger do auth...');
        $processAuth = new Process(['php', 'artisan', 'l5-swagger:generate'], base_path('../smartpark-auth'));
        $processAuth->run();

        if (! $processAuth->isSuccessful()) {
            $this->error("❌ Erro ao gerar Swagger no Auth:\n".$processAuth->getErrorOutput());

            return;
        }

        // Faz a mesclagem
        $this->info('🧩 Mesclando documentação...');
        MergeSwaggerDocs::gerarDocumentacaoUnificada();

        $this->info('✅ Documentação unificada com sucesso!');
    }

    private function executar(array $comando): void
    {
        $process = new Process($comando, base_path());
        $process->setTimeout(300);
        $process->run(function ($type, $buffer) {
            echo $buffer;
        });
    }
}
