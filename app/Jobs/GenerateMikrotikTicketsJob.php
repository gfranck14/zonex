<?php

namespace App\Jobs;

use App\Models\Forfait;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateMikrotikTicketsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Nombre de tentatives avant abandon.
     */
    public $tries = 3;

    /**
     * Backoff progressif entre les tentatives (secondes).
     * 1ère: 60s, 2ème: 300s (5min), 3ème: 900s (15min)
     */
    public $backoff = [60, 300, 900];

    /**
     * Timeout maximum du job (en secondes).
     */
    public $timeout = 120;

    /**
     * L'ID du forfait à réapprovisionner.
     */
    protected int $forfaitId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $forfaitId)
    {
        $this->forfaitId = $forfaitId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $forfait = Forfait::with('wifizone')->find($this->forfaitId);

        if (!$forfait) {
            Log::warning("🚫 Job annulé : Forfait ID {$this->forfaitId} introuvable.");
            return;
        }

        $zone = $forfait->wifizone;
        if (!$zone) {
            Log::warning("🚫 Job annulé : Zone introuvable pour le forfait {$forfait->nom}.");
            return;
        }

        // --- Double vérification du stock avant de générer ---
        $currentStock = Ticket::where('forfaits_id', $forfait->id)
            ->where('statut', 'libre')
            ->count();
        $maxStock = $forfait->stock_max ?: 200;

        if ($currentStock >= $maxStock) {
            Log::info("🚫 Job annulé : Stock déjà plein ({$currentStock}/{$maxStock}) pour {$forfait->nom}.");
            return;
        }

        $quantity = $maxStock - $currentStock;

        Log::info("🚀 JOB AUTO-REPLENISH DÉMARRÉ", [
            'forfait' => $forfait->nom,
            'zone' => $zone->nom_zone,
            'stock_actuel' => $currentStock,
            'stock_max' => $maxStock,
            'quantite_a_generer' => $quantity,
            'tentative' => $this->attempts(),
        ]);

        $batchId = uniqid('AUTO_');

        // Créer l'entrée d'historique (Lot)
        $history = \App\Models\ImportHistory::create([
            'proprio_id' => $zone->proprio_id,
            'nom_fichier' => 'Réapprovisionnement Auto',
            'zone_nom' => $zone->nom_zone,
            'forfait_nom' => $forfait->nom,
            'quantite' => 0, // Sera mis à jour
            'statut' => 'pending',
            'observation' => 'Génération automatique en cours...',
            'import_batch_id' => $batchId
        ]);

        // --- Connexion MikroTik via Service ---
        $mikrotikService = app(\App\Services\MikrotikSyncService::class);
        
        try {
            $mikrotikService->connect($zone);
        } catch (\Exception $e) {
            Log::error("❌ Job échoué : Connexion MikroTik impossible pour {$zone->nom_zone}.", [
                'error' => $e->getMessage(),
                'tentative' => $this->attempts(),
            ]);
            // Laisser le job réessayer (throw pour déclencher le retry)
            throw $e;
        }

        // --- Génération des tickets ---
        $generatedCount = 0;
        $profileName = $forfait->nom;

        // Déterminer la durée MikroTik à partir de temps_limit
        $tempsLimit = $forfait->temps_limit ?? '1 heure';
        $mikrotikTime = $this->parseDurationToMikrotik($tempsLimit);

        for ($i = 0; $i < $quantity; $i++) {
            $user = strtolower(Str::random(6));
            $pass = Str::random(6);

            try {
                // Création Hotspot User sur MikroTik
                $mikrotikService->createUser(
                    $zone,
                    $user,
                    $pass,
                    $profileName,
                    $mikrotikTime,
                    'Auto-gen ZoneX ' . now()->format('Y-m-d')
                );

                // Création en base locale
                Ticket::create([
                    'forfaits_id' => $forfait->id,
                    'wifizones_id' => $zone->id,
                    'username' => $user,
                    'password' => $pass,
                    'statut' => 'libre',
                    'import_batch_id' => $batchId,
                ]);

                $generatedCount++;
            } catch (\Exception $e) {
                Log::warning("⚠️ Ticket #{$i} échoué pour {$forfait->nom}: " . $e->getMessage());
                // On continue avec les suivants au lieu d'arrêter tout
            }
        }

        // Finaliser l'historique
        if ($history) {
            $history->update([
                'quantite' => $generatedCount,
                'statut' => $generatedCount > 0 ? 'succes' : 'echec',
                'observation' => "Génération automatique de {$generatedCount} tickets terminée."
            ]);
        }

        Log::info("✅ JOB AUTO-REPLENISH TERMINÉ", [
            'forfait' => $forfait->nom,
            'zone' => $zone->nom_zone,
            'generes' => $generatedCount,
            'demandes' => $quantity,
            'batch' => $batchId,
        ]);
    }

    /**
     * Convertit une durée ZoneX ("1 heure", "30 minute", "2 jour") en format MikroTik ("1h", "30m", "2d").
     */
    private function parseDurationToMikrotik(string $duration): string
    {
        $parts = explode(' ', trim($duration));
        $value = $parts[0] ?? '1';
        $unit = strtolower($parts[1] ?? 'heure');

        $map = [
            'minute' => 'm',
            'heure'  => 'h',
            'jour'   => 'd',
            'mois'   => 'd', // Converti en jours (approximation)
        ];

        $suffix = $map[$unit] ?? 'h';

        // Si mois, on multiplie par 30
        if ($unit === 'mois') {
            $value = (int)$value * 30;
        }

        return $value . $suffix;
    }

    /**
     * Handle a job failure (après toutes les tentatives épuisées).
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("💀 JOB AUTO-REPLENISH DÉFINITIVEMENT ÉCHOUÉ", [
            'forfait_id' => $this->forfaitId,
            'error' => $exception->getMessage(),
            'tentatives' => $this->attempts(),
        ]);
    }
}
