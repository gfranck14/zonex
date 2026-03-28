<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Forfait;
use App\Models\Ticket;
use App\Models\WifiZone;
use App\Models\ImportHistory;
use RouterOS\Client;
use RouterOS\Query;
use RouterOS\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

/**
 * Contrôleur pour la gestion des forfaits et des tickets WiFi.
 * 
 * Ce contrôleur permet de gérer les forfaits (création, modification, suppression),
 * les tickets (importation CSV, suppression, listing) et de suivre l'historique des imports.
 */
class ForfaitController extends Controller
{
    /**
     * Affiche la page de gestion des forfaits et des tickets.
     * 
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $proprioId = Auth::guard('proprio')->id();
        
        // --- NOUVEAU : Gérer la zone active persistée ---
        $activeZoneId = $request->query('filter_zone') ?? session('active_zone_id', 'all');
        // Si présent dans l'URL, on le synchronise en session pour les prochains rechargements
        if ($request->has('filter_zone')) {
            session(['active_zone_id' => $activeZoneId]);
        }
        
        // 1. Récupérer les zones WiFi du propriétaire pour le menu déroulant "Ajouter"
        $wifizones = \App\Models\WifiZone::where('proprio_id', $proprioId)->get();

        // 2. Récupérer tous les forfaits de toutes les zones du propriétaire
        $forfaits = Forfait::whereHas('wifizone', function($query) use ($proprioId) {
            $query->where('proprio_id', $proprioId);
        })->withCount(['tickets as tickets_count' => function($q) {
            $q->where('statut', 'libre');
        }])->get();

        // 3. Calculer le statut de stock pour chaque zone (icônes d'état)
        $zoneStockStatus = [];
        foreach ($wifizones as $zone) {
            $zoneForfaits = $forfaits->where('wifizones_id', $zone->id);
            
            if ($zoneForfaits->isEmpty()) {
                // Aucun forfait dans la zone => stock vide
                $zoneStockStatus[$zone->id] = [
                    'status' => 'empty',
                    'icon' => 'fa-times-circle',
                    'color' => 'text-red-500'
                ];
            } else {
                $hasLowStock = false;
                $hasNoStock = false;
                
                foreach ($zoneForfaits as $forfait) {
                    $ticketCount = $forfait->tickets_count ?? 0;
                    
                    if ($ticketCount == 0) {
                        $hasNoStock = true;
                    } elseif ($ticketCount <= 20) {
                        $hasLowStock = true;
                    }
                }
                
                if ($hasNoStock) {
                    $zoneStockStatus[$zone->id] = [
                        'status' => 'empty',
                        'icon' => 'fa-times-circle',
                        'color' => 'text-red-500'
                    ];
                } elseif ($hasLowStock) {
                    $zoneStockStatus[$zone->id] = [
                        'status' => 'low',
                        'icon' => 'fa-exclamation-triangle',
                        'color' => 'text-orange-500'
                    ];
                } else {
                    $zoneStockStatus[$zone->id] = [
                        'status' => 'good',
                        'icon' => 'fa-check-circle',
                        'color' => 'text-green-500'
                    ];
                }
            }
        }

        // 4. Calculer le total des tickets pour les KPIs
        $totalTickets = $forfaits->sum('tickets_count');

        // 5. Calculer les KPIs dynamiques par zone pour les stocks critiques
        $zoneStats = [];
        
        // Stats globales (Toutes les zones)
        $allForfaitsWithTickets = $forfaits->map(function($forfait) {
            return [
                'name' => $forfait->nom,
                'zone' => $forfait->wifizone->nom_zone,
                'stock' => $forfait->tickets_count ?? 0,
                'status' => $this->getStockStatus($forfait->tickets_count ?? 0),
                'percentage' => $this->getStockPercentage($forfait->tickets_count ?? 0)
            ];
        })->sortBy('stock')->take(4)->values()->toArray();
        
        $zoneStats['all'] = $allForfaitsWithTickets;

        // Stats par zone spécifique
        foreach ($wifizones as $zone) {
            $zoneForfaitsWithTickets = $forfaits->filter(function($forfait) use ($zone) {
                return $forfait->wifizones_id == $zone->id;
            })->map(function($forfait) {
                return [
                    'name' => $forfait->nom,
                    'zone' => $forfait->wifizone->nom_zone,
                    'stock' => $forfait->tickets_count ?? 0,
                    'status' => $this->getStockStatus($forfait->tickets_count ?? 0),
                    'percentage' => $this->getStockPercentage($forfait->tickets_count ?? 0)
                ];
            })->sortBy('stock')->take(4)->values()->toArray();
            
            $zoneStats[$zone->id] = $zoneForfaitsWithTickets;
        }

        // 6. Récupérer les tickets avec filtres et pagination dynamique
        $perPage = request()->input('per_page', 10);

        $ticketsQuery = \App\Models\Ticket::with(['forfait', 'forfait.wifizone', 'client'])
            ->whereHas('forfait.wifizone', function($query) use ($proprioId) {
                $query->where('proprio_id', $proprioId);
            })
            // Filtre par zone WiFi
            ->when(request('filter_zone'), function($q) {
                $q->whereHas('forfait.wifizone', function($wq) {
                    $wq->where('id', request('filter_zone'));
                });
            })
            // Filtre de recherche par username
            ->when(request('search'), function($q) {
                $search = request('search');
                $q->where('username', 'like', "%{$search}%");
            })
            // Filtre par forfait
            ->when(request('filter_forfait'), function($q) {
                $q->where('forfaits_id', request('filter_forfait'));
            })
            // Filtre par statut de ticket
            ->when(request('filter_statut'), function($q) {
                $q->where('statut', request('filter_statut'));
            })
            // Filtre par date
            ->when(request('filter_date'), function($q) {
                $q->whereDate('date_vente', request('filter_date'));
            });
        
        // Calcul des KPIs pour la liste des tickets (incluant les données historiques/supprimées pour le CA)
        $totalTicketsCount = (clone $ticketsQuery)->withTrashed()->count();
        $availableTicketsCount = (clone $ticketsQuery)->where('statut', 'libre')->count(); // Uniquement le stock réel
        $soldTicketsValue = (clone $ticketsQuery)->withTrashed()
            ->where('statut', 'vendu')
            ->sum('prix_achat');
        
        // Appliquer le tri et la pagination
        $tickets = $ticketsQuery->latest('date_vente')
            ->paginate($perPage)
            ->appends(request()->query());

        // 7. Récupérer l'historique des imports de tickets CSV (Pagination réelle)
        $perPageHistory = request()->input('per_page_history', 10);
        $imports = \App\Models\ImportHistory::where('proprio_id', $proprioId)
                    ->latest()
                    ->paginate($perPageHistory, ['*'], 'page_history')
                    ->appends(request()->query());

        return view('tickets', compact(
            'forfaits', 
            'wifizones', 
            'zoneStockStatus', 
            'totalTickets', 
            'zoneStats', 
            'tickets', 
            'imports', 
            'totalTicketsCount', 
            'availableTicketsCount', 
            'soldTicketsValue',
            'activeZoneId'
        ));
    }

    /**
     * Supprime un ticket spécifique.
     * 
     * @param int $id ID du ticket à supprimer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyTicket($id)
    {
        $proprioId = Auth::guard('proprio')->id();
        
        $ticket = \App\Models\Ticket::with(['forfait.wifizone'])->find($id);
        
        if (!$ticket) {
            return redirect()->back()->with('error', 'Ticket non trouvé.');
        }
        
        // Vérifier que le ticket appartient bien au propriétaire
        if ($ticket->forfait->wifizone->proprio_id != $proprioId) {
            return redirect()->back()->with('error', 'Action non autorisée.');
        }
        
        // --- 🔴 SUPPRESSION SUR MIKROTIK (Synchronisée) ---
        try {
            $mikrotikService = app(\App\Services\MikrotikSyncService::class);
            $mikrotikService->removeUsersByUsernames($ticket->forfait->wifizone, [$ticket->username]);
        } catch (\Exception $e) {
            Log::warning("⚠️ Échec suppression ticket {$ticket->username} sur MikroTik: " . $e->getMessage());
        }
        
        $ticket->delete();
        
        return redirect()->back()->with('success', 'Ticket supprimé avec succès.');
    }

    /**
     * Détermine le statut du stock en fonction du nombre de tickets.
     * 
     * @param int $ticketCount Nombre de tickets disponibles
     * @return string Statut du stock ('critical', 'low', 'medium', 'good')
     */
    private function getStockStatus($ticketCount)
    {
        if ($ticketCount == 0) {
            return 'critical';
        } elseif ($ticketCount <= 20) {
            return 'low';
        } elseif ($ticketCount <= 50) {
            return 'medium';
        } else {
            return 'good';
        }
    }

    /**
     * Calcule le pourcentage de stock par rapport à un maximum optimal de 100 tickets.
     * 
     * @param int $ticketCount Nombre de tickets disponibles
     * @return int Pourcentage de stock (0-100)
     */
    private function getStockPercentage($ticketCount)
    {
        $maxStock = 100; // Stock maximum optimal
        return min(100, round(($ticketCount / $maxStock) * 100));
    }

    public function import(Request $request)
    {
        try {
            // 1. Validation des entrées de base
            $request->validate([
                'file' => 'required|file|max:4096', // Plus de mimes strict (flou sur Win)
                'import-zone' => 'required|exists:wifizones,id',
                'import-package' => 'required|exists:forfaits,id',
            ]);

            $proprioId = Auth::guard('proprio')->id();
            $zoneId = $request->input('import-zone');
            $forfaitId = $request->input('import-package');
            $ignoreDuplicates = $request->input('ignore_duplicates') === 'true';

            // 2. SÉCURITÉ & COHÉRENCE
            $zone = \App\Models\WifiZone::where('id', $zoneId)->where('proprio_id', $proprioId)->first();
            if (!$zone) {
                return response()->json(['success' => false, 'message' => "Cette zone ne vous appartient pas."], 403);
            }

            $forfait = Forfait::where('id', $forfaitId)->where('wifizones_id', $zoneId)->first();
            if (!$forfait) {
                return response()->json(['success' => false, 'message' => "Ce forfait n'est pas lié à la zone sélectionnée."], 422);
            }

            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            
            $importedCount = 0;
            $duplicateTicketCount = 0;
            $profileErrorCount = 0;
            $detectedProfile = null;
            $importBatchId = uniqid('import_', true);
            if (($handle = fopen($file->getPathname(), "r")) !== FALSE) {
                // Lire les en-têtes
                $headers = fgetcsv($handle, 1000, ",");
                
                // Nettoyage BOM UTF-8 plus robuste
                if ($headers && isset($headers[0])) {
                    $headers[0] = trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $headers[0]));
                    $headers[0] = str_replace('"', '', $headers[0]); // Nettoyage extra
                }
            
            if (!$headers) {
                fclose($handle);
                return response()->json([
                    'success' => false, 
                    'message' => "Le fichier semble vide ou illisible."
                ], 422);
            }

            // 3. VALIDATION FORMAT MIKROTIK (Audit Point C)
            // On cherche les colonnes clés peu importe la casse
            $headerMap = array_map('strtolower', $headers);
            if (!in_array('username', $headerMap) || !in_array('password', $headerMap)) {
                fclose($handle);
                return response()->json([
                    'success' => false, 
                    'error_type' => 'invalid_format', 
                    'message' => "Format invalide. Colonnes 'Username' et 'Password' requises."
                ]);
            }

            // Index des colonnes
            $idxUser = array_search('username', $headerMap);
            $idxPass = array_search('password', $headerMap);
            $idxProfile = array_search('profile', $headerMap);

            DB::beginTransaction();
            try {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (count($data) < 2) {
                        continue; // Ligne vide
                    }

                    $username = trim($data[$idxUser] ?? '');
                    $password = trim($data[$idxPass] ?? '');
                    $csvProfile = ($idxProfile !== false) ? trim($data[$idxProfile] ?? '') : null;

                    if (!$username || !$password) {
                        continue;
                    }

                    // Capturer le profil détecté pour le diagnostic
                    if ($csvProfile && !$detectedProfile) {
                        $detectedProfile = $csvProfile;
                    }

                    // Vérification Profil
                    if ($csvProfile && $csvProfile !== trim($forfait->nom)) {
                        $profileErrorCount++;
                        continue;
                    }

                    // 4b. GESTION DOUBLON TICKET (Row Level)
                    // On vérifie l'existence UNIQUEMENT pour le forfait selectionné
                    // en utilisant la combinaison username + password
                    $exists = \App\Models\Ticket::where('username', $username)
                        ->where('password', $password)
                        ->where('forfaits_id', $forfaitId)
                        ->exists();

                    if ($exists) {
                        $duplicateTicketCount++;
                        // On ignore simplement le doublon, sans bloquer l'import
                        continue;
                    }


                    \App\Models\Ticket::create([
                        'forfaits_id' => $forfaitId,
                        'username' => $username,
                        'password' => $password,
                        'statut' => 'libre',
                        'import_batch_id' => $importBatchId
                    ]);
                    $importedCount++;
                }
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                fclose($handle);
                // Record failed import history due to exception
                \App\Models\ImportHistory::create([
                    'proprio_id' => $proprioId,
                    'nom_fichier' => $fileName,
                    'zone_nom' => $zone->nom_zone,
                    'forfait_nom' => $forfait->nom,
                    'quantite' => $importedCount,
                    'statut' => 'echec',
                    'observation' => "Erreur système: " . $e->getMessage(),
                    'import_batch_id' => $importBatchId
                ]);
                return response()->json([
                    'success' => false,
                    'message' => "Erreur lors de l'import : " . $e->getMessage()
                ], 500);
            }
            fclose($handle);
        } else {
            // Handle case where file cannot be opened
            \App\Models\ImportHistory::create([
                'proprio_id' => $proprioId,
                'nom_fichier' => $fileName,
                'zone_nom' => $zone->nom_zone,
                'forfait_nom' => $forfait->nom,
                'quantite' => 0,
                'statut' => 'echec',
                'observation' => "Impossible d'ouvrir le fichier."
            ]);
            return response()->json([
                'success' => false,
                'message' => "Impossible d'ouvrir le fichier CSV."
            ], 500);
        }

        // Sauvegarde Historique (Audit Point A - Correction Zone Nom)
        $statut = 'echec';
        if ($importedCount > 0) {
            if ($profileErrorCount > 0 || $duplicateTicketCount > 0) {
                $statut = 'partiel';
                $observation = "Importé {$importedCount} tickets. ";
                if ($duplicateTicketCount > 0) $observation .= "{$duplicateTicketCount} doublons ignorés. ";
                if ($profileErrorCount > 0) $observation .= "{$profileErrorCount} tickets avec profil Mikrotik incorrect ignorés.";
            } else {
                $statut = 'succes';
                $observation = "Importé {$importedCount} tickets.";
            }
        } else {
            $statut = 'echec';
            $observation = "Aucun ticket importé. ";
            if ($duplicateTicketCount > 0) $observation .= "{$duplicateTicketCount} doublons trouvés. ";
            if ($profileErrorCount > 0) $observation .= "{$profileErrorCount} tickets avec profil Mikrotik incorrect.";
            if (empty($observation)) $observation = "Fichier vide ou données invalides.";
        }


        $history = \App\Models\ImportHistory::create([
            'proprio_id' => $proprioId,
            'nom_fichier' => $fileName,
            'zone_nom' => $zone->nom_zone, // Vrai nom de la zone sélectionnée
            'forfait_nom' => $forfait->nom,
            'quantite' => $importedCount,
            'statut' => $statut,
            'observation' => $observation, // Save the observation
            'import_batch_id' => $importBatchId
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Importation terminée.',
            'imported' => $importedCount,
            'duplicates' => $duplicateTicketCount,
            'profile_errors' => $profileErrorCount,
            'expected_profile' => trim($forfait->nom),
            'found_profile' => $detectedProfile,
            'history_id' => $history->id ?? null
        ]);

        } catch (\Throwable $e) {
        \Log::error("Erreur Import CSV: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => "Erreur critique : " . $e->getMessage(),
            'debug' => config('app.debug') ? $e->getTraceAsString() : null
        ], 500);
    }
    }

    public function store(Request $request)
    {
        $proprioId = Auth::guard('proprio')->id();
        $request->validate([
            'wifizones_id' => [
                'required',
                'exists:wifizones,id',
                function ($attribute, $value, $fail) use ($proprioId) {
                    if (!\App\Models\WifiZone::where('id', $value)->where('proprio_id', $proprioId)->exists()) {
                        $fail("Cette zone ne vous appartient pas.");
                    }
                },
            ],
            'nom' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Forfait::where('wifizones_id', $request->wifizones_id)
                        ->where('nom', $value)
                        ->exists();
                    if ($exists) {
                        $fail("Un forfait avec ce nom existe déjà dans cette zone.");
                    }
                }
            ],
            'prix' => 'required|numeric',
            'limite_temps' => 'required|numeric|min:1',
            'limite_temps_unite' => 'required|in:minute,heure,jour,mois',
            'validite' => 'required|numeric|min:1',
            'validite_unite' => 'required|in:minute,heure,jour,mois',
            'description' => 'nullable|string|max:100',
            'color_class' => 'required|string',
            'stock_max' => 'nullable|numeric|min:1|max:200',
            'seuil_alerte' => 'nullable|numeric|min:1|max:100',
        ]);

        // Construire les valeurs de limite_temps et validite avec leurs unités
        $limiteTemps = $request->limite_temps . ' ' . $request->limite_temps_unite;
        $validite = $request->validite . ' ' . $request->validite_unite;

        Forfait::create([
            'wifizones_id' => $request->wifizones_id, // <--- Enregistrement Zone
            'nom' => $request->nom,
            'prix' => $request->prix,
            'validite' => $validite,
            'temps_limit' => $limiteTemps, // Utiliser le nouveau champ temps_limit
            'description' => $request->description,
            'color_class' => $request->color_class,
            'stock_max' => $request->stock_max ?: 200,
            'seuil_alerte' => $request->seuil_alerte ?? 15,
            'auto_replenish' => $request->has('auto_replenish')
        ]);

        Log::info('🎯 FORFAIT CRÉÉ AVEC SUCCÈS', [
            'forfait_nom' => $request->nom,
            'forfait_prix' => $request->prix,
            'forfait_limite_temps' => $limiteTemps,
            'forfait_validite' => $validite,
            'temps_limit' => $limiteTemps, // Nouveau champ
            'wifizones_id' => $request->wifizones_id,
            'timestamp' => now()->toISOString()
        ]);

        // Créer automatiquement le profil Mikrotik après la création du forfait
        try {
            Log::info('🚀 DÉBUT CRÉATION AUTOMATIQUE PROFIL MIKROTIK APRÈS FORFAIT');
            $this->createMikrotikProfileSimple($request->nom, $limiteTemps, $request->wifizones_id);
            Log::info('✅ PROFIL MIKROTIK CRÉÉ AUTOMATIQUEMENT AVEC SUCCÈS');
        } catch (Exception $e) {
            Log::error('❌ ERREUR CRÉATION AUTOMATIQUE PROFIL MIKROTIK', [
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'forfait_nom' => $request->nom,
                'timestamp' => now()->toISOString()
            ]);
        }

        return redirect()->back()->with('success', 'Forfait créé avec succès !');
    }

    /**
     * Update the specified forfait
     */
    public function update(Request $request, $id)
    {
        $forfait = Forfait::where('id', $id)
            ->whereHas('wifizone', function($query) {
                $query->where('proprio_id', Auth::guard('proprio')->id());
            })->firstOrFail();

        $request->validate([
            'nom' => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
            'limite_temps' => 'required|numeric|min:1',
            'limite_temps_unite' => 'required|in:minute,heure,jour,mois',
            'validite' => 'required|numeric|min:1',
            'validite_unite' => 'required|in:minute,heure,jour,mois',
            'description' => 'nullable|string|max:100',
            'color_class' => 'nullable|string|max:255',
            'stock_max' => 'nullable|numeric|min:1|max:200',
            'seuil_alerte' => 'nullable|numeric|min:1|max:100',
        ]);

        // Construire les valeurs consolidées
        $temps_limit = $request->limite_temps . ' ' . $request->limite_temps_unite;
        $validite = $request->validite . ' ' . $request->validite_unite;

        $forfait->update([
            'nom' => $request->nom,
            'prix' => $request->prix,
            'temps_limit' => $temps_limit,
            'validite' => $validite,
            'description' => $request->description,
            'color_class' => $request->color_class ?? 'bg-brand-blue',
            'stock_max' => $request->stock_max ?: 200,
            'seuil_alerte' => $request->seuil_alerte ?? 15,
            'auto_replenish' => $request->has('auto_replenish')
        ]);

        return redirect()->route('proprio.forfait_ticket')->with('success', 'Forfait mis à jour avec succès !');
    }

    /**
     * Vérifie s'il y a des sessions actives pour un forfait sur MikroTik.
     */
    public function checkActiveSessions($id, \App\Services\MikrotikSyncService $mikrotikService)
    {
        try {
            $forfait = Forfait::where('id', $id)
                ->whereHas('wifizone', function($query) {
                    $query->where('proprio_id', Auth::guard('proprio')->id());
                })->firstOrFail();

            $activeSessions = $mikrotikService->getActiveSessionsByProfile($forfait->wifizone, $forfait->nom);

            return response()->json([
                'success' => true,
                'count' => count($activeSessions),
                'forfait_nom' => $forfait->nom
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * Supprime le forfait et les tickets non vendus (statut = 'libre')
     * Garde les tickets vendus/utilisés mais retire leur référence au forfait
     */
    public function destroy(Request $request, $id)
    {
        // Vérifie que le forfait appartient à une zone qui appartient au proprio
        $forfait = Forfait::where('id', $id)
            ->whereHas('wifizone', function($query) {
                $query->where('proprio_id', Auth::guard('proprio')->id());
            })->firstOrFail();

        // --- 🔴 SUPPRESSION SUR MIKROTIK (Synchronisée) ---
        $cleanMikrotik = $request->get('clean_mikrotik', '1') == '1';

        if ($cleanMikrotik) {
            try {
                $mikrotikService = app(\App\Services\MikrotikSyncService::class);
                
                // 1. Déconnexion des clients actifs si demandé
                if ($request->has('force_disconnect') && $request->force_disconnect == '1') {
                    $activeSessions = $mikrotikService->getActiveSessionsByProfile($forfait->wifizone, $forfait->nom);
                    if (!empty($activeSessions)) {
                        $sessionIds = array_column($activeSessions, '.id');
                        $mikrotikService->removeActiveSessions($forfait->wifizone, $sessionIds);
                    }
                }

                // 2. Nettoyer TOUS les utilisateurs du profil sur MikroTik
                $mikrotikService->removeUsersByProfile($forfait->wifizone, $forfait->nom);

                // 3. Supprimer le profil MikroTik
                $mikrotikService->removeProfileByName($forfait->wifizone, $forfait->nom);
            } catch (\Exception $e) {
                \Log::warning("⚠️ Échec nettoyage MikroTik lors de la suppression du forfait '{$forfait->nom}' : " . $e->getMessage());
            }
        }

        // 1. Soft Delete de tous les tickets associés localement
        // (Précédemment on les supprimait définitivement ou on les détachait)
        $forfait->tickets()->delete();

        // 2. Soft Delete du forfait
        $forfait->delete();

        return redirect()->route('proprio.forfait_ticket')->with('success', 'Forfait supprimé avec succès !');
    }

    /**
     * Get forfait details for editing
     */
    public function edit($id)
    {
        // Nouveau : On vérifie que le forfait appartient à une zone qui appartient au proprio
        $forfait = Forfait::where('id', $id)
            ->whereHas('wifizone', function($query) {
                $query->where('proprio_id', Auth::guard('proprio')->id());
            })->firstOrFail();

        return response()->json([
            'success' => true,
            'forfait' => $forfait
        ]);
    }




    /**
     * Générer des tickets via l'API Mikrotik EvilFreelancer
     */
    public function generateTickets(Request $request, \App\Services\MikrotikSyncService $mikrotikService)
    {
        Log::info('🚀 DÉBUT GÉNÉRATION DE TICKETS - generateTickets');
        Log::info('📍 Données reçues:', [
            'zone_id' => $request->zone_id,
            'forfait_id' => $request->forfait_id,
            'ticket_count' => $request->ticket_count,
            'auto_validate' => $request->auto_validate,
            'ip' => $request->ip(),
            'timestamp' => now()->toISOString()
        ]);

        try {
            $proprioId = Auth::guard('proprio')->id();
            // Validation des données
            $request->validate([
                'zone_id' => 'required|exists:wifizones,id',
                'forfait_id' => 'required|exists:forfaits,id',
                'ticket_count' => 'required|integer|min:1|max:1000'
            ]);

            // Récupérer les informations avec vérification de propriété (Audit Point 6 & 10)
            $zone = \App\Models\WifiZone::where('id', $request->zone_id)
                ->where('proprio_id', $proprioId)
                ->firstOrFail();

            $forfait = Forfait::where('id', $request->forfait_id)
                ->where('wifizones_id', $request->zone_id)
                ->firstOrFail();

            $ticketCount = (int)$request->ticket_count;

            // --- NOUVEAU : Limitation du Stock ---
            $currentStock = Ticket::where('forfaits_id', $forfait->id)->where('statut', 'libre')->count();
            $maxStock = $forfait->stock_max ?: 200;

            if ($currentStock >= $maxStock) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Le stock idéal ($maxStock) est déjà atteint ou dépassé ($currentStock tickets libres). Impossible de générer plus."
                ], 400);
            }

            if (($currentStock + $ticketCount) > $maxStock) {
                $originalCount = $ticketCount;
                $ticketCount = $maxStock - $currentStock;
                Log::info("⚠️ Quantité ajustée de {$originalCount} vers {$ticketCount} pour respecter le stock idéal de {$maxStock}.");
            }
            // -------------------------------------

            Log::info('📋 Informations récupérées:', [
                'zone' => $zone->nom_zone,
                'forfait' => $forfait->nom,
                'temps_limit' => $forfait->temps_limit,
                'ticket_count' => $ticketCount
            ]);

            // Vérifier les credentials API
            if (!$zone->api_host || !$zone->api_user || !$zone->api_password) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Les accès API MikroTik ne sont pas configurés pour cette zone (api_host, api_user ou api_password manquant)."
                ], 400);
            }

            // Configuration Client Mikrotik via Service Centralisé
            try {
                $mikrotikService->connect($zone);
                Log::info('✅ Client Mikrotik connecté dynamiquement via le service pour la zone: ' . $zone->nom_zone);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ], 500);
            }

            // Vérifier si le profil existe sur le Mikrotik
            try {
                $profiles = $mikrotikService->getProfiles($zone);
                $profileExists = collect($profiles)->contains('name', $forfait->nom);
                
                if (!$profileExists) {
                    Log::error('❌ PROFIL MIKROTIK INTROUVABLE', [
                        'profile_name' => $forfait->nom,
                        'forfait_id' => $forfait->id,
                        'zone' => $zone->nom_zone
                    ]);
                    
                    return response()->json([
                        'status' => 'error',
                        'message' => "Le profil '{$forfait->nom}' n'existe pas sur le Mikrotik. Veuillez d'abord le créer."
                    ], 400);
                }
                
                Log::info('✅ PROFIL MIKROTIK VÉRIFIÉ', [
                    'profile_name' => $forfait->nom,
                    'profile_exists' => true
                ]);
                
            } catch (\Exception $e) {
                Log::error('❌ ERREUR VÉRIFICATION PROFIL MIKROTIK', [
                    'profile_name' => $forfait->nom,
                    'error' => $e->getMessage()
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'message' => "Erreur lors de la vérification du profil Mikrotik: " . $e->getMessage()
                ], 500);
            }

            $generatedTickets = [];
            $successCount = 0;
            $errorCount = 0;
            $batchId = uniqid('GEN_');

            // Créer l'entrée d'historique (Lot)
            $history = \App\Models\ImportHistory::create([
                'proprio_id' => $proprioId,
                'nom_fichier' => 'Génération Manuelle',
                'zone_nom' => $zone->nom_zone,
                'forfait_nom' => $forfait->nom,
                'quantite' => 0, // Sera mis à jour à la fin
                'statut' => 'pending',
                'observation' => 'Initialisation de la génération...',
                'import_batch_id' => $batchId
            ]);

            // Générer les tickets un par un
            for ($i = 1; $i <= $ticketCount; $i++) {
                try {
                    // Générer un identifiant unique
                    $username = $this->generateUsername();
                    $password = '1234'; // Mot de passe fixe selon spécification

                    Log::info('📝 PRÉPARATION TICKET #' . $i, [
                        'username' => $username,
                        'password' => $password,
                        'profile' => $forfait->nom
                    ]);

                    // Créer le ticket sur Mikrotik via le Service (connexion partagée grâce au cache)
                    $mikrotikService->createUser(
                        $zone, 
                        $username, 
                        $password, 
                        $forfait->nom, 
                        null, 
                        "Genéré via ZoneX - {$forfait->nom}"
                    );

                    // Sauvegarder en base de données avec le batchId de l'historique
                    $ticket = \App\Models\Ticket::create([
                        'username' => $username,
                        'password' => $password,
                        'statut' => 'libre',
                        'forfaits_id' => $forfait->id,
                        'import_batch_id' => $batchId,
                    ]);

                    $generatedTickets[] = [
                        'username' => $username,
                        'password' => $password,
                        'status' => 'libre'
                    ];

                    $successCount++;

                    Log::info('✅ Ticket généré:', [
                        'username' => $username,
                        'password' => $password,
                        'profile' => $forfait->nom,
                        'iteration' => $i . '/' . $ticketCount,
                        'ticket_id' => $ticket->id
                    ]);

                    // Petit délai pour éviter la surcharge
                    if ($i % 10 === 0) {
                        usleep(100000); // 0.1 seconde toutes les 10 itérations
                    }

                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error('❌ Erreur génération ticket ' . $i, [
                        'error' => $e->getMessage(),
                        'iteration' => $i . '/' . $ticketCount
                    ]);
                }
            }

            // Mettre à jour l'historique avec le compte final
            if ($history) {
                $history->update([
                    'quantite' => $successCount,
                    'statut' => $successCount === $ticketCount ? 'succes' : ($successCount > 0 ? 'partiel' : 'echec'),
                    'observation' => "Génération de {$successCount} tickets terminée." . ($errorCount > 0 ? " ({$errorCount} erreurs)" : "")
                ]);
            }

            Log::info('✅ GÉNÉRATION TERMINÉE', [
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'total_requested' => $ticketCount,
                'zone' => $zone->nom_zone,
                'forfait' => $forfait->nom,
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => "{$successCount} tickets générés avec succès" . ($errorCount > 0 ? " ({$errorCount} erreurs)" : ""),
                'data' => [
                    'generated_count' => $successCount,
                    'error_count' => $errorCount,
                    'tickets' => $generatedTickets,
                    'zone' => $zone->nom_zone,
                    'forfait' => $forfait->nom,
                    'history' => $history
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ ERREUR GÉNÉRATION TICKETS', [
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la génération des tickets: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Générer un username au format ABCDE
     */
    private function generateUsername(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $username = '';
        
        for ($i = 0; $i < 5; $i++) {
            $username .= $characters[rand(0, strlen($characters) - 1)];
        }
        
        return $username;
    }

    /**
     * Convertir la validité en nombre de jours
     */
    private function convertValidityToDays($validity): int
    {
        $validity = strtolower($validity);
        
        if (str_contains($validity, 'jour')) {
            return (int) filter_var($validity, FILTER_SANITIZE_NUMBER_INT);
        } elseif (str_contains($validity, 'semaine')) {
            return (int) filter_var($validity, FILTER_SANITIZE_NUMBER_INT) * 7;
        } elseif (str_contains($validity, 'mois')) {
            return (int) filter_var($validity, FILTER_SANITIZE_NUMBER_INT) * 30;
        } elseif (str_contains($validity, 'an')) {
            return (int) filter_var($validity, FILTER_SANITIZE_NUMBER_INT) * 365;
        }
        
        return 30; // Par défaut
    }

    /**
     * Créer un profil Mikrotik via l'API RouterOS
     * Utilise la librairie RouterOS (evilfreelancer/routeros-api-php)
     */
    public function createMikrotikProfile(Request $request)
    {
        // Log immédiat pour vérifier que le controller est appelé
        Log::info('🚀 CONTROLLER APPELÉ - createMikrotikProfile');
        Log::info('📍 Méthode HTTP:', ['method' => $request->method()]);
        Log::info('📍 URL:', ['url' => $request->fullUrl()]);
        Log::info('📍 IP Client:', ['ip' => $request->ip()]);
        Log::info('📍 User Agent:', ['user_agent' => $request->userAgent()]);
        Log::info('⏰ Timestamp:', ['timestamp' => now()->toISOString()]);
        
        // Log des données brutes reçues
        Log::info('📥 DONNÉES BRUTES REÇUES:', [
            'all_data' => $request->all(),
            'json_data' => $request->json()->all(),
            'content_type' => $request->header('Content-Type'),
            'timestamp' => now()->toISOString()
        ]);

        // Validation des données reçues
        try {
            Log::info('🔍 DÉBUT VALIDATION');
            $validated = $request->validate([
                'profile_name' => 'required|string|max:255',
                'rate_limit' => 'nullable|string',
                'session_timeout' => 'nullable|string',
                'shared_users' => 'nullable|integer',
                'idle_timeout' => 'nullable|string',
                'keepalive_timeout' => 'nullable|string',
            ]);
            
            Log::info('✅ VALIDATION RÉUSSIE', [
                'validated_data' => $validated,
                'timestamp' => now()->toISOString()
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ ERREUR VALIDATION', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
                'timestamp' => now()->toISOString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur de validation: ' . implode(', ', $e->errors())
            ], 422);
        }

        try {
            Log::info('🚀 DÉBUT CRÉATION PROFIL MIKROTIK', [
                'profile_name' => $request->profile_name,
                'rate_limit' => $request->rate_limit,
                'session_timeout' => $request->session_timeout,
                'shared_users' => $request->shared_users,
                'idle_timeout' => $request->idle_timeout,
                'keepalive_timeout' => $request->keepalive_timeout,
                'host' => '10.0.0.2',
                'user' => 'wifipay_api',
                'ip' => $request->ip(),
                'timestamp' => now()->toISOString()
            ]);

            // 1. Configuration de la connexion RouterOS
            Log::info('📧 CONFIGURATION CONNEXION ROUTEROS');
            $config = new Config([
                'host' => '10.0.0.2',
                'user' => 'wifipay_api',
                'pass' => 'wifipay_pass',
                'port' => 8728,
                'timeout' => 10,
            ]);

            Log::info('📡 Connexion RouterOS configurée', [
                'host' => '10.0.0.2',
                'port' => 8728,
                'user' => 'wifipay_api',
                'timestamp' => now()->toISOString()
            ]);

            // 2. Initialisation du client
            Log::info('🔌 INITIALISATION CLIENT ROUTEROS');
            $client = new Client($config);

            Log::info('✅ Client RouterOS initialisé', [
                'timestamp' => now()->toISOString()
            ]);

            // 3. Préparation de la requête de création du profil
            Log::info('📝 PRÉPARATION REQUÊTE MIKROTIK');
            // Chemin MikroTik : /ip/hotspot/user/profile/add
            $query = new Query('/ip/hotspot/user/profile/add');
            $query->equal('name', $request->profile_name); // Nom du profil
            
            Log::info('📋 PARAMÈTRES DE BASE AJOUTÉS', [
                'name' => $request->profile_name,
                'timestamp' => now()->toISOString()
            ]);
            
            // Paramètres optionnels
            if ($request->rate_limit) {
                $query->equal('rate-limit', $request->rate_limit); // Ex: "2M/2M"
                Log::info('📋 PARAMÈTRE rate-limit AJOUTÉ', ['value' => $request->rate_limit]);
            }
            
            if ($request->session_timeout) {
                $query->equal('session-timeout', $request->session_timeout); // Ex: "02:00:00"
                Log::info('📋 PARAMÈTRE session-timeout AJOUTÉ', ['value' => $request->session_timeout]);
            }
            
            if ($request->shared_users) {
                $query->equal('shared-users', $request->shared_users); // Ex: 1
                Log::info('📋 PARAMÈTRE shared-users AJOUTÉ', ['value' => $request->shared_users]);
            }
            
            if ($request->idle_timeout) {
                $query->equal('idle-timeout', $request->idle_timeout); // Ex: "30m"
                Log::info('📋 PARAMÈTRE idle-timeout AJOUTÉ', ['value' => $request->idle_timeout]);
            }
            
            if ($request->keepalive_timeout) {
                $query->equal('keepalive-timeout', $request->keepalive_timeout); // Ex: "2m"
                Log::info('📋 PARAMÈTRE keepalive-timeout AJOUTÉ', ['value' => $request->keepalive_timeout]);
            }

            // Paramètres par défaut
            $query->equal('status-autorefresh', '1m'); // Rafraîchissement toutes les minutes
            $query->equal('add-mac-cookie', 'yes'); // Cookie MAC
            
            Log::info('📋 PARAMÈTRES PAR DÉFAUT AJOUTÉS', [
                'status-autorefresh' => '1m',
                'add-mac-cookie' => 'yes',
                'timestamp' => now()->toISOString()
            ]);

            // 4. Exécution sur le Mikrotik
            Log::info('📤 ENVOI REQUÊTE VERS MIKROTIK', [
                'path' => '/ip/hotspot/user/profile/add',
                'params' => [
                    'name' => $request->profile_name,
                    'rate-limit' => $request->rate_limit ?? 'non défini',
                    'session-timeout' => $request->session_timeout ?? 'non défini',
                    'shared-users' => $request->shared_users ?? 1,
                    'idle-timeout' => $request->idle_timeout ?? 'non défini',
                    'keepalive-timeout' => $request->keepalive_timeout ?? 'non défini',
                ],
                'timestamp' => now()->toISOString()
            ]);
            
            Log::info('⏳ DÉBUT EXÉCUTION COMMANDE MIKROTIK');
            $response = $client->query($query)->read();
            Log::info('✅ COMMANDE MIKROTIK EXÉCUTÉE', [
                'response' => $response,
                'timestamp' => now()->toISOString()
            ]);

            Log::info('✅ PROFIL MIKROTIK CRÉÉ AVEC SUCCÈS', [
                'profile_name' => $request->profile_name,
                'rate_limit' => $request->rate_limit,
                'session_timeout' => $request->session_timeout,
                'response' => $response,
                'ip' => $request->ip(),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Profil Mikrotik créé avec succès',
                'data' => [
                    'profile_name' => $request->profile_name,
                    'response' => $response
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ ERREUR CRÉATION PROFIL MIKROTIK', [
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'profile_name' => $request->profile_name,
                'request_data' => $request->all(),
                'ip' => $request->ip(),
                'timestamp' => now()->toISOString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la création du profil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crée un profil MikroTik simple à partir du nom et du timeout.
     * Utilisé lors de la création d'un forfait.
     */
    public function createMikrotikProfileSimple($profileName, $sessionTimeout, $zoneId = null)
    {
        Log::info("🚀 createMikrotikProfileSimple : Création profil '$profileName'", [
            'timeout' => $sessionTimeout,
            'zone_id' => $zoneId
        ]);

        try {
            // 1. Récupérer la zone
            if (!$zoneId) {
                throw new \Exception("ID de zone manquant pour la création du profil MikroTik.");
            }

            $proprioId = Auth::guard('proprio')->id();
            $zone = WifiZone::where('id', $zoneId)
                ->where('proprio_id', $proprioId)
                ->first();
            if (!$zone) {
                throw new \Exception("Zone non trouvée ou non autorisée (ID: $zoneId).");
            }

            // 2. Préparer le service MikroTik
            $mikrotikService = app(\App\Services\MikrotikSyncService::class);
            $client = $mikrotikService->connect($zone);

            // 3. Charger le script de notification (Automatique)
            $mikrotikService->setupWebhookScript($zone);

            // 4. Convertir le timeout ZoneX vers MikroTik
            $mtTimeout = $this->parseToMikrotikTime($sessionTimeout);

            // 5. Créer le profil Hotspot User
            $query = (new Query('/ip/hotspot/user/profile/add'))
                ->equal('name', $profileName)
                ->equal('session-timeout', $mtTimeout ?: '00:00:00')
                ->equal('status-autorefresh', '1m')
                ->equal('add-mac-cookie', 'yes')
                ->equal('shared-users', '1');

            $response = $client->query($query)->read();

            // 6. Injecter les hooks (Automatique)
            $mikrotikService->patchProfileHooks($zone, $profileName);

            Log::info("✅ Profil MikroTik '$profileName' créé et configuré", ['response' => $response]);
            return true;

        } catch (\Exception $e) {
            Log::error("❌ Échec createMikrotikProfileSimple : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Convertit une durée ZoneX (ex: "30 minute", "1 heure") en format MikroTik (ex: "30m", "1h").
     */
    private function parseToMikrotikTime($zonexTime)
    {
        if (!$zonexTime || $zonexTime == 'Non défini') return null;
        
        $parts = explode(' ', trim($zonexTime));
        if (count($parts) < 2) return $zonexTime;

        $val = (int)$parts[0];
        $unit = strtolower($parts[1]);

        if (str_contains($unit, 'min')) return $val . 'm';
        if (str_contains($unit, 'heur')) return $val . 'h';
        if (str_contains($unit, 'jour')) return $val . 'd';
        if (str_contains($unit, 'mois')) return ($val * 30) . 'd';

        return $val . 'h';
    }

    /**
     * Helper pour obtenir un client MikroTik configuré pour une zone
     */
    /**
     * Importation des profils MikroTik vers les forfaits ZoneX
     */
    public function importMikrotikProfiles(Request $request, $zoneId, \App\Services\MikrotikSyncService $mikrotikService)
    {
        Log::info('🔄 DÉBUT IMPORTATION PROFILS MIKROTIK', ['zone_id' => $zoneId]);

        $proprioId = Auth::guard('proprio')->id();
        $zone = \App\Models\WifiZone::where('id', $zoneId)
                                    ->where('proprio_id', $proprioId)
                                    ->firstOrFail();

        try {
            $profiles = $mikrotikService->getProfiles($zone);

            Log::info('📥 PROFILS RÉCUPÉRÉS DEPUIS MIKROTIK', ['count' => count($profiles)]);

            $importedCount = 0;
            $skippedCount = 0;

            foreach ($profiles as $profile) {
                // On ignore le profil par défaut 'default'
                if ($profile['name'] === 'default') continue;

                // Vérifier si le forfait existe déjà pour cette zone
                $exists = Forfait::where('wifizones_id', $zone->id)
                                ->where('nom', $profile['name'])
                                ->exists();

                if (!$exists) {
                    // Création du forfait "brouillon" (prix = 0)
                    Forfait::create([
                        'wifizones_id' => $zone->id,
                        'nom'          => $profile['name'],
                        'prix'         => 0, // État brouillon
                        'temps_limit'  => $profile['session-timeout'] ?? 'Non défini',
                        'validite'     => $this->guessValiditeFromTimeout($profile['session-timeout'] ?? null),
                        'description'  => 'Profil importé du MikroTik. Limite: ' . ($profile['rate-limit'] ?? 'Aucune'),
                        'color_class'  => 'bg-gray-100', // Style brouillon
                    ]);

                    // NOUVEAU : Automatisation Webhook pour profil importé
                    try {
                        $mikrotikService->setupWebhookScript($zone);
                        $mikrotikService->patchProfileHooks($zone, $profile['name']);
                    } catch (\Exception $e) {
                        Log::warning("⚠️ Échec configuration hooks pour profil importé {$profile['name']}");
                    }

                    $importedCount++;
                } else {
                    $skippedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Synchronisation terminée.",
                'data' => [
                    'imported' => $importedCount,
                    'skipped' => $skippedCount,
                    'total_found' => count($profiles) - 1 // -1 pour 'default'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('❌ ERREUR SYNC MIKROTIK', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Synchronisation des Utilisateurs MikroTik -> Tickets ZoneX
     */
    public function syncMikrotikTickets($zoneId, \App\Services\MikrotikSyncService $mikrotikService)
    {
        try {
            $proprioId = Auth::guard('proprio')->id();
            $zone = WifiZone::where('id', $zoneId)
                ->where('proprio_id', $proprioId)
                ->firstOrFail();

            // 1. Récupérer les utilisateurs Hotspot
            $mikrotikUsers = $mikrotikService->getUsers($zone);

            $importedCount = 0;
            $updatedCount = 0;
            $ignoredCount = 0;

            // 2. Récupérer tous les forfaits de cette zone pour le mapping par profil
            $forfaits = Forfait::where('wifizones_id', $zoneId)->get();
            $forfaitsByProfile = $forfaits->keyBy('nom'); // On assume que le profil MT = Nom du forfait ZoneX

            foreach ($mikrotikUsers as $mtUser) {
                $username = $mtUser['name'] ?? null;
                if (!$username || $username === 'default-trial') continue;

                $profile = $mtUser['profile'] ?? 'default';
                $comment = $mtUser['comment'] ?? '';
                $limitUptime = $mtUser['limit-uptime'] ?? null;
                $uptime = $mtUser['uptime'] ?? '0s';

                // Vérifier si le ticket existe déjà (dans les forfaits de cette zone)
                $ticket = Ticket::where('username', $username)
                    ->whereIn('forfaits_id', $forfaits->pluck('id'))
                    ->first();

                // Déterminer si le ticket est expiré sur MikroTik
                $isExpiredOnMT = (str_contains(strtolower($comment), 'expired')) || 
                                 ($limitUptime && $uptime !== '0s' && $uptime === $limitUptime);

                if ($ticket) {
                    // Si le ticket existe, on met à jour son statut s'il est expiré sur MT
                    if ($isExpiredOnMT && $ticket->statut !== 'épuisé') {
                        $ticket->update(['statut' => 'épuisé']);
                        $updatedCount++;
                    } else {
                        $ignoredCount++;
                    }
                } else {
                    // Si le ticket n'existe pas, on l'importe s'il correspond à un forfait connu
                    $forfait = $forfaitsByProfile->get($profile);
                    
                    if ($forfait) {
                        Ticket::create([
                            'username'    => $username,
                            'password'    => $mtUser['password'] ?? '',
                            'forfaits_id' => $forfait->id,
                            'statut'      => $isExpiredOnMT ? 'épuisé' : 'libre',
                            'prix_achat'  => $forfait->prix,
                        ]);
                        $importedCount++;
                    } else {
                        $ignoredCount++;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Synchronisation terminée.",
                'details' => [
                    'imported' => $importedCount,
                    'updated' => $updatedCount,
                    'ignored' => $ignoredCount
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error("Erreur synchro tickets Zone $zoneId: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Erreur MikroTik : " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tente de deviner la validité à partir du timeout MikroTik
     */
    private function guessValiditeFromTimeout($timeout)
    {
        if (!$timeout) return '1h';
        
        // Ex: 01:00:00 -> 1h, 24:00:00 -> 1j, etc.
        if (strpos($timeout, 'd') !== false) {
            return str_replace('d', 'j', $timeout);
        }
        
        $parts = explode(':', $timeout);
        if (count($parts) >= 1) {
            $hours = (int)$parts[0];
            if ($hours >= 24) return ceil($hours/24) . 'j';
            if ($hours > 0) return $hours . 'h';
            
            // Si 0 heure, regarder les minutes
            if (isset($parts[1])) {
                $mins = (int)$parts[1];
                if ($mins > 0) return $mins . 'm';
            }
            return '1h';
        }

        return '1h';
    }

    /**
     * Active/Désactive l'affichage du forfait sur le portail client (Shop)
     */
    public function toggleActive(Request $request, $id)
    {
        try {
            $proprioId = Auth::guard('proprio')->id();
            $forfait = Forfait::where('id', $id)
                ->whereHas('wifizone', function($query) use ($proprioId) {
                    $query->where('proprio_id', $proprioId);
                })->firstOrFail();

            $forfait->is_active = !$forfait->is_active;
            $forfait->save();

            return response()->json([
                'success' => true,
                'is_active' => $forfait->is_active,
                'message' => $forfait->is_active ? 'Profil affiché sur le portail' : 'Profil masqué du portail'
            ]);
        } catch (\Exception $e) {
            \Log::error("Erreur toggleActive forfait $id: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification de l\'état'
            ], 500);
        }
    }
    /**
     * Vérifie si le stock d'un forfait est bas et dispatche un Job de réapprovisionnement.
     * Méthode NON-BLOQUANTE : le job s'exécute en arrière-plan via la Queue.
     */
    public function checkAndReplenishStock(Forfait $forfait)
    {
        if (!$forfait->auto_replenish || !$forfait->stock_max) {
            return false;
        }

        // 1. Compter le stock actuel (tickets libres)
        $currentStock = Ticket::where('forfaits_id', $forfait->id)
            ->where('statut', 'libre')
            ->count();

        // 2. Calculer le seuil (ex: 15% de 200 = 30)
        $seuil = ceil($forfait->stock_max * ($forfait->seuil_alerte / 100));

        Log::info('📦 VÉRIFICATION AUTO-REPLENISH', [
            'forfait' => $forfait->nom,
            'stock_actuel' => $currentStock,
            'seuil' => $seuil,
            'stock_max' => $forfait->stock_max
        ]);

        // 3. Si stock <= seuil, on dispatche le Job en arrière-plan
        if ($currentStock <= $seuil) {
            Log::info("🚀 AUTO-REPLENISH : Dispatch du Job en arrière-plan pour {$forfait->nom}");
            \App\Jobs\GenerateMikrotikTicketsJob::dispatch($forfait->id);
            return true;
        }

        return false;
    }
    /**
     * Persiste la zone WiFi sélectionnée en session via AJAX.
     */
    public function setActiveZone(Request $request)
    {
        $zoneId = $request->input('zone_id');
        session(['active_zone_id' => $zoneId]);
        return response()->json(['success' => true]);
    }
}
