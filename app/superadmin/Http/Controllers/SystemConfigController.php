<?php

namespace App\SuperAdmin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\SuperAdmin\Models\SystemConfig;
use App\SuperAdmin\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller pour la configuration système
 */
class SystemConfigController extends Controller
{
    /**
     * Affiche la page de configuration
     */
    public function index()
    {
        $configs = SystemConfig::orderBy('key')->get()->groupBy(function($item) {
            // Grouper par catégorie basée sur le préfixe
            if (str_starts_with($item->key, 'notify_')) return 'Notifications';
            if (in_array($item->key, ['commission_rate', 'min_withdrawal_amount', 'max_withdrawal_amount'])) 
                return 'Financier';
            if (in_array($item->key, ['maintenance_mode', 'platform_name', 'session_timeout', 'max_zones_per_proprio'])) 
                return 'Système';
            return 'Autres';
        });

        return view('superadmin.settings.index', compact('configs'));
    }

    /**
     * Met à jour les configurations
     */
    public function update(Request $request)
    {
        $superadmin = Auth::guard('superadmin')->user();

        // Valider les configs importantes
        $request->validate([
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'min_withdrawal_amount' => 'nullable|numeric|min:0',
            'max_withdrawal_amount' => 'nullable|numeric|min:0',
            'session_timeout' => 'nullable|integer|min:5|max:480',
            'max_zones_per_proprio' => 'nullable|integer|min:1',
        ]);

        $updated = [];

        foreach ($request->all() as $key => $value) {
            if ($key === '_token') continue;

            $config = SystemConfig::where('key', $key)->first();
            
            if ($config) {
                $oldValue = $config->value;
                $config->update([
                    'value' => $value,
                    'updated_by' => $superadmin->id,
                ]);

                $updated[] = $key;

                // Logger la modification
                AuditLog::logAction(
                    user: $superadmin,
                    action: 'updated',
                    model: 'SystemConfig',
                    oldValues: ['value' => $oldValue],
                    newValues: ['value' => $value],
                    modelId: $config->id
                );
            }
        }

        return back()->with('success', 
            'Configurations mises à jour: ' . implode(', ', $updated)
        );
    }
}
