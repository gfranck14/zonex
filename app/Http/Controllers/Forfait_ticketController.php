<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Forfait;  
use App\Models\Wifizone; 

class Forfait_ticketController extends Controller
{
  public function index()
{
    $proprio = auth()->user();

    $wifizones = WifiZone::with('forfaits')
        ->where('proprio_id', $proprio->id)
        ->get();

    if ($wifizones->isEmpty()) {
        return view('proprio.forfait_ticket', [
            'etat' => 'no_wifizone'
        ]);
    }

    return view('proprio.forfait_ticket', [
        'etat' => 'has_wifizone',
        'wifizones' => $wifizones
    ]);
}


}
