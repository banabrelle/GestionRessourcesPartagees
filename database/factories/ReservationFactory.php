<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
        protected $model = Reservation::class;

    public function definition(): array
    {
        $debut = fake()->dateTimeBetween('-3 months', '+1 month');
        $fin   = (clone $debut)->modify('+' . fake()->numberBetween(1, 8) . ' hours');

        return [
            'personnel_id'  => Personnel::inRandomOrder()->first()?->id,
            'ressource_id'  => Ressource::disponible()->inRandomOrder()->first()?->id,
            'date_debut'    => $debut,
            'date_fin'      => $fin,
            'motif'         => fake()->sentence(12),
            'statut'        => fake()->randomElement([
                'en_attente','valide_chef','valide_admin','rejete_chef','termine'
            ]),
            'conflit_detecte' => false,
        ];
    }

    public function enAttente(): static
    {
        return $this->state(fn($a) => ['statut'=>'en_attente']);
    }

    public function valide(): static
    {
        return $this->state(fn($a) => [
            'statut'=>'valide_admin',
            'date_val_chef'=>now()->subHours(5),
            'date_val_admin'=>now()->subHours(2),
        ]);
    }

}
