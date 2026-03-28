<?php

namespace App\Console\Commands;

use App\Jobs\GenerateMikrotikTicketsJob;
use App\Models\Forfait;
use App\Models\Ticket;
use Illuminate\Console\Command;

class CheckStockLevels extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'stock:check';

    /**
     * The console command description.
     */
    protected $description = 'Vérifie les niveaux de stock de tous les forfaits et dispatche des jobs de réapprovisionnement si nécessaire (filet de sécurité quotidien).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('📦 Vérification des niveaux de stock...');

        $forfaits = Forfait::where('auto_replenish', true)
            ->where('stock_max', '>', 0)
            ->with('wifizone')
            ->get();

        if ($forfaits->isEmpty()) {
            $this->info('✅ Aucun forfait avec auto-replenish activé.');
            return 0;
        }

        $dispatched = 0;

        foreach ($forfaits as $forfait) {
            $currentStock = Ticket::where('forfaits_id', $forfait->id)
                ->where('statut', 'libre')
                ->count();

            $seuil = ceil($forfait->stock_max * ($forfait->seuil_alerte / 100));

            if ($currentStock <= $seuil) {
                $this->warn("⚠️  {$forfait->nom} : Stock bas ({$currentStock}/{$forfait->stock_max}). Dispatch du job...");
                GenerateMikrotikTicketsJob::dispatch($forfait->id);
                $dispatched++;
            } else {
                $this->line("  ✅ {$forfait->nom} : OK ({$currentStock}/{$forfait->stock_max})");
            }
        }

        $this->info("📊 Résultat : {$dispatched} job(s) dispatché(s) sur {$forfaits->count()} forfait(s) vérifiés.");

        return 0;
    }
}
