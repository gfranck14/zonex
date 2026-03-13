<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HotspotController extends Controller
{
    public function logout(Request $request)
    {
        $user  = $request->input('user');
        $cause = $request->input('cause');
        $zone  = $request->input('zone');

        // Log pour vérifier la réception
        Log::info('Hotspot logout recu', [
            'user'  => $user,
            'cause' => $cause,
            'zone'  => $zone,
        ]);

        return response()->json([
            'status'  => 'ok',
            'message' => 'logout enregistre',
            'user'    => $user,
            'cause'   => $cause,
            'zone'    => $zone,
        ]);
    }
}