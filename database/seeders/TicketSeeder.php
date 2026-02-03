<?php

namespace Database\Seeders;

use App\Models\Forfait;
use App\Models\Ticket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the forfait "Forfait de 1 Jour"
        $forfait = Forfait::where('nom', 'Forfait de 1 Jour')->first();

        if (!$forfait) {
            $this->command->error('Forfait "UAC WIFIZONE Forfait 1 jour" not found!');
            $this->command->info('Available forfaits:');
            $forfaits = Forfait::all(['id', 'nom']);
            foreach ($forfaits as $f) {
                $this->command->info("- ID: {$f->id}, Nom: {$f->nom}");
            }
            return;
        }

        $this->command->info("Found forfait: {$forfait->nom} (ID: {$forfait->id})");

        // Check how many tickets already exist for this forfait
        $existingCount = Ticket::where('forfaits_id', $forfait->id)->count();
        $this->command->info("Existing tickets for this forfait: {$existingCount}");

        // Generate 500 new tickets
        $ticketsToCreate = 500;

        $this->command->info("Creating {$ticketsToCreate} new tickets...");

        // Create tickets in chunks for better performance
        Ticket::factory()
            ->count($ticketsToCreate)
            ->forForfait($forfait->id)
            ->create();

        $newCount = Ticket::where('forfaits_id', $forfait->id)->count();
        $this->command->info("Successfully created {$ticketsToCreate} tickets!");
        $this->command->info("Total tickets for this forfait: {$newCount}");
    }
}
