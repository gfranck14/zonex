<?php

namespace App\Console\Commands;

use App\Models\ClientSession;
use Illuminate\Console\Command;

class DiagnoseClientSessions extends Command
{
    protected $signature = 'sessions:diagnose {--client-id= : ID du client spécifique}';
    protected $description = 'Diagnostiquer les sessions clients actives';

    public function handle()
    {
        $query = ClientSession::with('client');
        
        if ($clientId = $this->option('client-id')) {
            $query->where('client_id', $clientId);
        }

        $sessions = $query->orderBy('last_activity', 'desc')->get();

        if ($sessions->isEmpty()) {
            $this->info('Aucune session active trouvée.');
            return 0;
        }

        $this->table(
            ['ID', 'Client', 'IP Address', 'Last Activity', 'Duration'],
            $sessions->map(function ($session) {
                return [
                    $session->id,
                    $session->client->pseudo ?? 'N/A',
                    $session->ip_address,
                    $session->last_activity->format('Y-m-d H:i:s'),
                    $session->created_at->diffForHumans($session->last_activity),
                ];
            })
        );

        $this->info("Total: {$sessions->count()} sessions actives");
        
        return 0;
    }
}
