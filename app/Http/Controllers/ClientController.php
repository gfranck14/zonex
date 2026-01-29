<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

/**
 * Contrôleur pour la gestion des clients.
 * 
 * Ce contrôleur gère toutes les opérations liées aux clients,
 * y compris l'affichage, la création, la modification, la suppression et le blocage/déblocage.
 */
class ClientController extends Controller
{
    /**
     * Affiche la page de gestion des clients avec filtres et pagination.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filterType = $request->input('filter_type');
        $perPage = $request->input('per_page', 10);

        // 1. Récupérer les clients (Recherche + Filtres + Pagination)
        $clients = Client::query()
            // Filtre de recherche (nom, téléphone, MAC address)
            ->when($search, function($q) use ($search) {
                $q->where('nom_complet', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%")
                  ->orWhere('mac_address', 'like', "%{$search}%");
            })
            // Filtre VIP (dépense > 10 000)
            ->when($filterType === 'vip', function($q) {
                $q->where('total_depense', '>', 10000);
            })
            // Filtre nouveaux clients (moins de 30 jours)
            ->when($filterType === 'new', function($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            })
            // Filtre clients bloqués
            ->when($filterType === 'blocked', function($q) {
                $q->where('is_blocked', true);
            })
            // Tri par date de création décroissante
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // 2. Calculer les KPIs pour le dashboard clients
        $totalClients = Client::count(); // Total de clients
        $nouveauxClients = Client::where('created_at', '>=', now()->subDays(30))->count(); // Clients ajoutés depuis 30 jours
        $clientsVIP = Client::where('total_depense', '>', 10000)->count(); // Clients VIP

        // 3. Envoyer les données à la vue
        return view('clients', compact('clients', 'totalClients', 'nouveauxClients', 'clientsVIP'));
    }

    /**
     * Traite la création d'un nouveau client.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Préparer le numéro complet (code pays + numéro local)
        $phoneCode = $request->input('phone_code', '+229');
        $localPhone = $request->input('telephone');
        $fullPhone = $phoneCode . ' ' . $localPhone;

        // Ajouter au request pour la validation unique
        $request->merge(['telephone' => $fullPhone]);

        // 2. Validation des données
        $request->validate([
            'nom_complet' => 'required|string|max:255',
            'telephone' => 'required|string|unique:clients,telephone|max:20',
            'total_depense' => 'nullable|integer'
        ]);

        // 3. Création du client dans la base de données
        Client::create([
            'nom_complet' => $request->nom_complet,
            'telephone' => $fullPhone,
            'total_depense' => $request->total_depense ?? 0,
            'derniere_zone' => 'Manuel', // Indique que le client a été ajouté manuellement
            'is_blocked' => false // Client non bloqué par défaut
        ]);

        // 4. Redirection avec message de succès
        return redirect()->route('clients')->with('success', 'Client ajouté avec succès !');
    }

    /**
     * Traite la mise à jour d'un client existant.
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);
        
        // Validation des données
        $request->validate([
             'nom_complet' => 'required|string|max:255',
             'telephone' => 'required|string|max:20|unique:clients,telephone,'.$id,
        ]);

        // Mise à jour du client
        $client->update([
            'nom_complet' => $request->nom_complet,
            'telephone' => $request->telephone
        ]);

        return response()->json(['success' => true, 'message' => 'Client mis à jour']);
    }

    /**
     * Bascule le statut de blocage d'un client.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleBlock($id)
    {
        $client = Client::findOrFail($id);
        $client->is_blocked = !$client->is_blocked; // Inversion du statut de blocage
        $client->save();
        
        $status = $client->is_blocked ? 'bloqué' : 'débloqué';
        return response()->json(['success' => true, 'message' => "Client {$status} avec succès", 'is_blocked' => $client->is_blocked]);
    }

    /**
     * Récupère l'historique des tickets d'un client.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function history($id)
    {
        $client = Client::findOrFail($id);
        $tickets = $client->tickets()
            ->with('forfait.wifizone') // Chargement précoce des relations
            ->latest()
            ->take(10) // Récupérer les 10 derniers tickets
            ->get();
        
        // Transformation des données pour la réponse JSON
        return response()->json([
            'success' => true, 
            'tickets' => $tickets->map(function($t) {
                return [
                    'date' => $t->created_at->format('d M Y'),
                    'forfait' => $t->forfait->nom ?? 'Inconnu',
                    'zone' => $t->forfait->wifizone->nom_zone ?? '-',
                    'prix' => number_format($t->forfait->prix ?? 0, 0, ',', ' ') . ' F',
                    'login' => $t->username,
                    'password' => $t->password
                ];
            })
        ]);
    }

    /**
     * Traite la suppression d'un client.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json(['success' => true, 'message' => 'Client supprimé avec succès']);
    }
}
