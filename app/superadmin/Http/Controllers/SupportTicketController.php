<?php

namespace App\SuperAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\SuperAdmin\Models\SupportTicket;
use App\Models\Proprio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller pour la gestion des tickets de support
 */
class SupportTicketController extends Controller
{
    /**
     * Liste des tickets
     */
    public function index(Request $request)
    {
        $query = SupportTicket::with(['proprio', 'assignedTo'])->orderBy('created_at', 'desc');

        // Filtre par statut
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }

        // Filtre par priorité
        if ($request->has('priority')) {
            $query->byPriority($request->priority);
        }

        // Filtre par assignation
        if ($request->has('assigned_to_me') && $request->assigned_to_me) {
            $query->assignedTo(Auth::guard('superadmin')->id());
        }

        $tickets = $query->paginate(20);

        return view('superadmin.tickets.index', compact('tickets'));
    }

    /**
     * Détails d'un ticket
     */
    public function show($id)
    {
        $ticket = SupportTicket::with(['proprio', 'assignedTo'])->findOrFail($id);
        
        return view('superadmin.tickets.show', compact('ticket'));
    }

    /**
     * Assigner un ticket
     */
    public function assign(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        
        $assignedTo = $request->assigned_to ?? Auth::guard('superadmin')->id();
        
        $ticket->assign($assignedTo);

        return back()->with('success', 'Ticket assigné avec succès.');
    }

    /**
     * Répondre à un ticket (placeholder pour futur système de messagerie)
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|min:10',
        ]);

        // TODO: Implémenter système de messages/réponses

        return back()->with('success', 'Réponse envoyée (fonctionnalité en développement).');
    }

    /**
     * Mettre à jour le statut d'un ticket
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);

        $ticket = SupportTicket::findOrFail($id);
        
        if ($request->status === 'resolved') {
            $ticket->markAsResolved();
        } elseif ($request->status === 'closed') {
            $ticket->close();
        } elseif ($request->status === 'open') {
            $ticket->reopen();
        } else {
            $ticket->update(['status' => $request->status]);
        }

        return back()->with('success', 'Statut du ticket mis à jour.');
    }

    /**
     * Fermer un ticket
     */
    public function close($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->close();

        return back()->with('success', 'Ticket fermé avec succès.');
    }
}
