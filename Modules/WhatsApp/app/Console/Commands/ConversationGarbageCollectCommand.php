<?php

namespace Modules\WhatsApp\Console\Commands;

use Illuminate\Console\Command;
use Modules\WhatsApp\Models\WhatsAppConversation;

class ConversationGarbageCollectCommand extends Command
{
    protected $signature   = 'whatsapp:gc-conversations';
    protected $description = 'Remove conversas WhatsApp expiradas (inativas há mais de 24h)';

    public function handle(): int
    {
        $deleted = WhatsAppConversation::where('expires_at', '<', now())->delete();

        $this->info("🗑️ {$deleted} conversas expiradas removidas.");

        return self::SUCCESS;
    }
}
