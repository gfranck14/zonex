<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\SuperAdmin\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder pour créer le compte superadmin initial (GOD)
 */
class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer le compte GOD principal
        SuperAdmin::create([
            'name' => 'Super Administrateur',
            'email' => 'god@zonex.admin',
            'password' => Hash::make('ZoneXGod2026!'),
            'role' => 'god',
            'two_factor_enabled' => false,
            'is_active' => true,
        ]);

        $this->command->info('✅ Compte SuperAdmin GOD créé avec succès!');
        $this->command->info('📧 Email: god@zonex.admin');
        $this->command->info('🔑 Password: ZoneXGod2026!');
        $this->command->warn('⚠️  IMPORTANT: Changez ce mot de passe après la première connexion!');
    }
}
