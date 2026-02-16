<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemConfig;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SystemConfigController extends Controller
{
    /**
     * Affiche la page de configuration
     */
    public function index()
    {
        $configs = SystemConfig::all()->keyBy('key');
        return view('superadmin.settings', compact('configs'));
    }

    /**
     * Met à jour la configuration
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'maintenance_mode' => 'boolean',
            'min_withdrawal_amount' => 'numeric|min:0',
            'max_withdrawal_amount' => 'numeric|min:0',
            'withdrawal_fee_percentage' => 'numeric|min:0|max:100',
        ]);

        $oldValues = [];
        $newValues = [];

        $fields = [
            'site_name',
            'contact_email',
            'maintenance_mode',
            'min_withdrawal_amount',
            'max_withdrawal_amount',
            'withdrawal_fee_percentage',
            'support_phone',
            'support_whatsapp',
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $value = $request->get($field);
                
                // Récupérer l'ancienne valeur
                $config = SystemConfig::where('key', $field)->first();
                $oldValue = $config ? $config->value : null;
                
                // Sauvegarder la nouvelle valeur
                SystemConfig::updateOrCreate(
                    ['key' => $field],
                    ['value' => $value, 'type' => gettype($value)]
                );

                $oldValues[$field] = $oldValue;
                $newValues[$field] = $value;

                // Mettre à jour le cache
                Cache::forget('config_' . $field);
            }
        }

        AuditLog::create([
            'super_admin_id' => Auth::guard('superadmin')->id(),
            'action' => 'update_system_config',
            'model_type' => 'SystemConfig',
            'model_id' => null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Configuration mise à jour');
    }
}
