<?php

namespace App\Http\Controllers;

use App\Models\Proprio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Contrôleur pour la gestion des paramètres du compte propriétaire.
 * 
 * Ce contrôleur gère toutes les opérations liées aux paramètres du compte,
 * y compris la modification du profil, du mot de passe, des paramètres WhatsApp,
 * et la gestion du statut du compte (activation/désactivation/suppression).
 */
class SettingsController extends Controller
{
    /**
     * Affiche la page des paramètres du compte propriétaire.
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $proprio = Auth::guard('proprio')->user();
        return view('settings', compact('proprio'));
    }

    /**
     * Met à jour les informations personnelles du propriétaire.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $proprio = Auth::guard('proprio')->user();

        // Validation des données
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_code' => ['required', 'string'],
            'numero' => ['required', 'string'],
        ]);

        // Combinaison du code pays et du numéro de téléphone
        $fullPhone = $data['phone_code'] . ' ' . $data['numero'];

        // Vérification de l'unicité du numéro de téléphone
        if (Proprio::where('numero', $fullPhone)->where('id', '!=', $proprio->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Ce numéro de téléphone est déjà utilisé par un autre compte.'
            ], 422);
        }

        // Mise à jour du profil
        $proprio->update([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'numero' => $fullPhone,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès'
        ]);
    }

    /**
     * Met à jour le mot de passe du propriétaire.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        $proprio = Auth::guard('proprio')->user();

        // Validation des données
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:6', 'confirmed'],
        ], [
            'new_password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'new_password.min' => 'Le mot de passe doit faire au moins 6 caractères.',
        ]);

        // Vérification du mot de passe actuel
        if (!Hash::check($request->current_password, $proprio->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Le mot de passe actuel est incorrect'
            ], 422);
        }

        // Mise à jour du mot de passe
        $proprio->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mot de passe mis à jour avec succès'
        ]);
    }

    /**
     * Met à jour les paramètres WhatsApp du propriétaire.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateWhatsApp(Request $request)
    {
        $proprio = Auth::guard('proprio')->user();

        // Validation des données
        $data = $request->validate([
            'wa_phone_code' => ['required', 'string'],
            'wa_numero' => ['required', 'string'],
            'wa_notifications_enabled' => ['required', 'boolean'],
            'wa_alert_threshold' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        // Combinaison du code pays et du numéro WhatsApp
        $fullWaPhone = $data['wa_phone_code'] . ' ' . $data['wa_numero'];

        // Mise à jour des paramètres WhatsApp
        $proprio->update([
            'wa_numero' => $fullWaPhone,
            'wa_notifications_enabled' => $data['wa_notifications_enabled'],
            'wa_alert_threshold' => $data['wa_alert_threshold'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Paramètres WhatsApp mis à jour'
        ]);
    }

    /**
     * Désactive le compte propriétaire.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate(Request $request)
    {
        $proprio = Auth::guard('proprio')->user();
        
        // Validation de la raison de désactivation
        $request->validate([
            'reason' => ['required', 'string', 'max:1000']
        ]);

        // Mise à jour du statut du compte
        $proprio->update([
            'is_active' => false,
            'deactivation_reason' => $request->reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Compte désactivé avec succès'
        ]);
    }

    /**
     * Réactive le compte propriétaire.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate()
    {
        $proprio = Auth::guard('proprio')->user();
        
        // Mise à jour du statut du compte
        $proprio->update([
            'is_active' => true,
            'deactivation_reason' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Compte réactivé avec succès'
        ]);
    }

    /**
     * Supprime définitivement le compte propriétaire.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAccount()
    {
        $proprio = Auth::guard('proprio')->user();
        
        // Déconnexion de l'utilisateur avant suppression
        Auth::guard('proprio')->logout();
        
        // Suppression du compte
        $proprio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Compte supprimé définitivement'
        ]);
    }
}
