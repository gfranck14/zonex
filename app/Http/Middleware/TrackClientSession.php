<?php

namespace App\Http\Middleware;

use App\Services\ClientSessionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TrackClientSession
{
    protected $sessionService;

    public function __construct(ClientSessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function handle(Request $request, Closure $next)
    {
        // Si le client est authentifié
        if (Auth::guard('client')->check()) {
            $client = Auth::guard('client')->user();
            
            // Mettre à jour l'activité de la session
            $this->sessionService->updateSessionActivity(Session::getId());
        }

        return $next($request);
    }
}
