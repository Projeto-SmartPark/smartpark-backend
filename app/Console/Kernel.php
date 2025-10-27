<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Registra os comandos Artisan personalizados.
     */
    protected $commands = [
        \App\Console\Commands\MergeSwaggerCommand::class,
    ];

    /**
     * Define o agendamento de comandos (não vamos usar agora).
     */
    protected function schedule(Schedule $schedule): void
    {
        // ex: $schedule->command('inspire')->hourly();
    }

    /**
     * Registra comandos base do Laravel.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
