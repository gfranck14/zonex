<?php

namespace App\Console\Commands;

use App\Services\ClientSessionService;
use Illuminate\Console\Command;

class CleanupClientSessions extends Command
{
    protected $signature = 'sessions:cleanup {--timeout=30 : Timeout en minutes}';
    protected $description = 'Nettoyer les sessions clients expirées';

    protected $sessionService;

    public function __construct(ClientSessionService $sessionService)
    {
        parent::__construct();
        $this->sessionService = $sessionService;
    }

    public function handle()
    {
        $timeout = $this->option('timeout');
        $cleanedCount = $this->sessionService->cleanupExpiredSessions($timeout);
        
        $this->info("✅ {$cleanedCount} sessions expirées ont été nettoyées.");
        
        return 0;
    }
}
