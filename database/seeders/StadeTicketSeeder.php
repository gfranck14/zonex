<?php

namespace Database\Seeders;

use App\Models\Forfait;
use App\Models\Ticket;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StadeTicketSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the forfait "Illimité 1 semaine" (5000 F - WIFIZONE STADE)
        $forfait = Forfait::where('nom', 'Illimité 1 semaine')->first();

        if (!$forfait) {
            $this->command->error('Forfait "Illimité 1 semaine" not found!');
            return;
        }

        $this->command->info("Found forfait: {$forfait->nom} (ID: {$forfait->id}, Prix: {$forfait->prix} F)");

        // Check how many tickets already exist for this forfait
        $existingCount = Ticket::where('forfaits_id', $forfait->id)->count();
        $this->command->info("Existing tickets for this forfait: {$existingCount}");

        // Generate 1000 new tickets
        $ticketsToCreate = 1000;

        $this->command->info("Creating {$ticketsToCreate} new tickets...");

        // Create tickets
        Ticket::factory()
            ->count($ticketsToCreate)
            ->forForfait($forfait->id)
            ->create();

        $newCount = Ticket::where('forfaits_id', $forfait->id)->count();
        $this->command->info("Successfully created {$ticketsToCreate} tickets!");
        $this->command->info("Total tickets for this forfait: {$newCount}");
    }
}
