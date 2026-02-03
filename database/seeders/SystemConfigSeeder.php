<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\SuperAdmin\Models\SystemConfig;

/**
 * Seeder pour créer les configurations système par défaut
 */
class SystemConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            // Configurations financières
            [
                'key' => 'commission_rate',
                'value' => '5.0',
                'type' => 'number',
                'description' => 'Taux de commission de la plateforme (%)',
            ],
            [
                'key' => 'min_withdrawal_amount',
                'value' => '5000',
                'type' => 'number',
                'description' => 'Montant minimum pour demande de retrait (FCFA)',
            ],
            [
                'key' => 'max_withdrawal_amount',
                'value' => '1000000',
                'type' => 'number',
                'description' => 'Montant maximum par retrait (FCFA)',
            ],

            // Configurations système
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Mode maintenance activé',
            ],
            [
                'key' => 'platform_name',
                'value' => 'ZoneX WiFi Profit Manager',
                'type' => 'string',
                'description' => 'Nom de la plateforme',
            ],

            // Limites et seuils
            [
                'key' => 'max_zones_per_proprio',
                'value' => '50',
                'type' => 'number',
                'description' => 'Nombre maximum de zones par propriétaire',
            ],
            [
                'key' => 'session_timeout',
                'value' => '30',
                'type' => 'number',
                'description' => 'Durée de session en minutes',
            ],

            // Notifications
            [
                'key' => 'notify_new_withdrawal',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Notifier les superadmins des nouvelles demandes de retrait',
            ],
            [
                'key' => 'notify_new_proprio',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Notifier les superadmins des nouvelles inscriptions propriétaires',
            ],
        ];

        foreach ($configs as $config) {
            SystemConfig::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }

        $this->command->info('✅ ' . count($configs) . ' configurations système créées avec succès!');
    }
}
