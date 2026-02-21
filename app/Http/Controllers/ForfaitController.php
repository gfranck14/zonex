<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Forfait;
use App\Models\Ticket;
use App\Models\ImportHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    public function index()
    {
        $proprioId = Auth::guard('proprio')->id();
        
        // 1. Récupérer les zones WiFi du propriétaire pour le menu déroulant "Ajouter"
        $wifizones = \App\Models\WifiZone::where('proprio_id', $proprioId)->get();

        // 2. Récupérer tous les forfaits de toutes les zones du propriétaire
        $forfaits = Forfait::whereHas('wifizone', function($query) use ($proprioId) {
            $query->where('proprio_id', $proprioId);
        })->withCount('tickets')->get();

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
        
        // Calcul des KPIs pour la liste des tickets
        $totalTicketsCount = (clone $ticketsQuery)->count();
        $availableTicketsCount = (clone $ticketsQuery)->where('statut', 'libre')->count();
        $soldTicketsValue = (clone $ticketsQuery)->where('statut', 'vendu')->with('forfait')->get()->sum(function($ticket) {
            return $ticket->forfait ? $ticket->forfait->prix : 0;
        });
        
        // Appliquer le tri et la pagination
        $tickets = $ticketsQuery->latest('date_vente')
            ->paginate($perPage)
            ->appends(request()->query());

        // 7. Récupérer l'historique des imports de tickets CSV
        $imports = \App\Models\ImportHistory::where('proprio_id', $proprioId)
                    ->latest()
                    ->take(20)
                    ->get();

        return view('tickets', compact('forfaits', 'wifizones', 'zoneStockStatus', 'totalTickets', 'zoneStats', 'tickets', 'imports', 'totalTicketsCount', 'availableTicketsCount', 'soldTicketsValue'));
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
                    if ($csvProfile && $csvProfile !== trim($forfait->profile_mikrotik)) {
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
            'expected_profile' => trim($forfait->profile_mikrotik),
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
        $request->validate([
            'wifizones_id' => 'required|exists:wifizones,id', // <--- Validation Zone
            'nom' => 'required|string|max:255',
            'prix' => 'required|numeric',
            'validite' => 'required|string',
            'profile_mikrotik' => 'required|string',
            'color_class' => 'required|string',
        ]);

        Forfait::create([
            'wifizones_id' => $request->wifizones_id, // <--- Enregistrement Zone
            'nom' => $request->nom,
            'prix' => $request->prix,
            'validite' => $request->validite,
            'profile_mikrotik' => $request->profile_mikrotik,
            'description' => $request->description,
            'color_class' => $request->color_class,
        ]);

        return redirect()->back()->with('success', 'Forfait créé avec succès !');
    }

    /**
     * Update the specified forfait
     */
    public function update(Request $request, $id)
    {
        // Nouveau : On vérifie que le forfait appartient à une zone qui appartient au proprio
        $forfait = Forfait::where('id', $id)
            ->whereHas('wifizone', function($query) {
                $query->where('proprio_id', Auth::guard('proprio')->id());
            })->firstOrFail();

        $request->validate([
            'nom' => 'required|string|max:255',
            'prix' => 'required|integer|min:0',
            'validite' => 'required|string|max:255',
            'profile_mikrotik' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color_class' => 'nullable|string|max:255'
        ]);

        $forfait->update([
            'nom' => $request->nom,
            'prix' => $request->prix,
            'validite' => $request->validite,
            'profile_mikrotik' => $request->profile_mikrotik,
            'description' => $request->description,
            'color_class' => $request->color_class ?? 'bg-brand-blue'
        ]);

        return redirect()->route('forfait_ticket')->with('success', 'Forfait mis à jour avec succès !');
    }

    /**
     * Remove the specified forfait
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

        // Supprimer les tickets non vendus (statut = 'libre')
        $forfait->tickets()->where('statut', 'libre')->delete();
        
        // Mettre à NULL les tickets vendus/utilisés (garder pour historique)
        $forfait->tickets()->whereIn('statut', ['vendu', 'utilisé'])->update(['forfaits_id' => null]);

        // Supprimer le forfait
        $forfait->delete();

        return redirect()->route('forfait_ticket')->with('success', 'Forfait supprimé avec succès !');
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
}
