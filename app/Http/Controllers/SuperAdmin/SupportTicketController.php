<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Controller pour la gestion des tickets de support
 */
class SupportTicketController extends Controller
{
    /**
     * Liste tous les tickets de support
     */
    public function index(Request $request)
    {
        // Filtrage par statut et priorité
        $status = $request->get('status', 'all');
        $priority = $request->get('priority', 'all');

        // Données statiques pour la démo
        $tickets = collect([
            (object)[
                'id' => 1,
                'sujet' => 'Problème de connexion WiFi',
                'description' => 'Les clients ne peuvent pas se connecter au réseau WiFi',
                'proprio_nom' => 'Koffi Amani',
                'status' => 'open',
                'priority' => 'high',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(1),
                'messages_count' => 3,
            ],
            (object)[
                'id' => 2,
                'sujet' => 'Demande de remboursement',
                'description' => 'Un client demande un remboursement pour un ticket',
                'proprio_nom' => 'Diallo Mamadou',
                'status' => 'pending',
                'priority' => 'medium',
                'created_at' => now()->subHours(5),
                'updated_at' => now()->subHours(4),
                'messages_count' => 2,
            ],
            (object)[
                'id' => 3,
                'sujet' => 'Question sur les tarifs',
                'description' => 'Comment configurer les tarifs pour mes zones ?',
                'proprio_nom' => 'N\'guessan Konan',
                'status' => 'resolved',
                'priority' => 'low',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
                'messages_count' => 5,
            ],
            (object)[
                'id' => 4,
                'sujet' => 'Bugs dans le tableau de bord',
                'description' => 'Le graphique des revenus ne s\'affiche pas correctement',
                'proprio_nom' => 'Sow Fatou',
                'status' => 'open',
                'priority' => 'high',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(1),
                'messages_count' => 8,
            ],
            (object)[
                'id' => 5,
                'sujet' => 'Activation de compte',
                'description' => 'Mon compte n\'est pas encore activé',
                'proprio_nom' => 'Acket Jean',
                'status' => 'closed',
                'priority' => 'medium',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
                'messages_count' => 4,
            ],
        ]);

        // Filtrer par statut
        if ($status !== 'all') {
            $tickets = $tickets->where('status', $status);
        }

        // Filtrer par priorité
        if ($priority !== 'all') {
            $tickets = $tickets->where('priority', $priority);
        }

        // Stats
        $stats = [
            'open' => $tickets->where('status', 'open')->count(),
            'pending' => $tickets->where('status', 'pending')->count(),
            'resolved' => $tickets->where('status', 'resolved')->count(),
            'closed' => $tickets->where('status', 'closed')->count(),
            'high_priority' => $tickets->where('priority', 'high')->whereIn('status', ['open', 'pending'])->count(),
        ];

        return view('superadmin.super_admin_tickets', compact('tickets', 'stats', 'status', 'priority'));
    }

    /**
     * Affiche les détails d'un ticket
     */
    public function show($id)
    {
        // Données statiques pour la démo
        $ticket = (object)[
            'id' => $id,
            'sujet' => 'Problème de connexion WiFi',
            'description' => 'Les clients ne peuvent pas se connecter au réseau WiFi',
            'proprio_nom' => 'Koffi Amani',
            'proprio_email' => 'koffi@email.com',
            'status' => 'open',
            'priority' => 'high',
            'created_at' => now()->subHours(2),
            'updated_at' => now()->subHours(1),
            'messages' => collect([
                (object)[
                    'id' => 1,
                    'user_type' => 'proprio',
                    'user_name' => 'Koffi Amani',
                    'message' => 'Bonjour, j\'ai un problème avec mes zones WiFi.',
                    'created_at' => now()->subHours(2),
                ],
                (object)[
                    'id' => 2,
                    'user_type' => 'support',
                    'user_name' => 'Support ZoneX',
                    'message' => 'Bonjour, pouvez-vous nous donner plus de détails ?',
                    'created_at' => now()->subHours(1),
                ],
                (object)[
                    'id' => 3,
                    'user_type' => 'proprio',
                    'user_name' => 'Koffi Amani',
                    'message' => 'Les clients disent que le mot de passe ne fonctionne pas.',
                    'created_at' => now()->subMinutes(30),
                ],
            ]),
        ];

        return view('superadmin.super_admin_tickets', compact('ticket'));
    }

    /**
     * Répond à un ticket
     */
    public function reply(Request $request, $id)
    {
        return back()->with('success', 'Réponse envoyée avec succès');
    }

    /**
     * Ferme un ticket
     */
    public function close(Request $request, $id)
    {
        return back()->with('success', 'Ticket fermé avec succès');
    }

    /**
     * Change la priorité d'un ticket
     */
    public function updatePriority(Request $request, $id)
    {
        return back()->with('success', 'Priorité mise à jour');
    }
}
