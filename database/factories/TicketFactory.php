<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'forfaits_id' => null, // Will be set when creating tickets
            'username' => $this->generateUniqueUsername(),
            'password' => $this->generatePassword(),
            'statut' => 'libre',
            'client_id' => null,
            'date_vente' => null,
        ];
    }

    /**
     * Generate a unique username for the ticket.
     *
     * @return string
     */
    private function generateUniqueUsername(): string
    {
        return 'ticket_' . strtolower(Str::random(8));
    }

    /**
     * Generate a password for the ticket.
     *
     * @return string
     */
    private function generatePassword(): string
    {
        return strtolower(Str::random(6));
    }

    /**
     * Set the forfait ID for the ticket.
     *
     * @param int $forfaitId
     * @return static
     */
    public function forForfait(int $forfaitId): static
    {
        return $this->state(fn (array $attributes) => [
            'forfaits_id' => $forfaitId,
        ]);
    }

    /**
     * Indicate that the ticket is sold.
     *
     * @return static
     */
    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'vendu',
            'date_vente' => now(),
        ]);
    }
}
