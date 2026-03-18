<?php

namespace Modules\WhatsApp\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\WhatsApp\Services\EvolutionApiClient;

class WhatsAppHealthCheckCommand extends Command
{
    protected $signature   = 'whatsapp:health-check';
    protected $description = 'Verifica a conexão com a instância do Evolution API';

    public function handle(EvolutionApiClient $client): int
    {
        try {
            $status = $client->getConnectionStatus();
            $state  = $status['instance']['state'] ?? $status['state'] ?? 'unknown';

            if ($state === 'open') {
                $this->info("✅ WhatsApp conectado. Estado: {$state}");
                Log::info('[WhatsApp] Connection healthy', ['state' => $state]);
            } else {
                $this->warn("⚠️ WhatsApp desconectado. Estado: {$state}");
                Log::warning('[WhatsApp] Connection unhealthy', ['state' => $state]);
            }
        } catch (\Throwable $e) {
            $this->error("❌ Erro ao verificar conexão: {$e->getMessage()}");
            Log::error('[WhatsApp] Health check failed', ['error' => $e->getMessage()]);
        }

        return self::SUCCESS;
    }
}
