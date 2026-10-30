<?php

namespace Database\Factories;

use App\Models\Ressource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ressource>
 */
class RessourceFactory extends Factory
{
        protected $model = Ressource::class;

    public function definition(): array
    {
        return [
            'division_id'    => Division::inRandomOrder()->first()?->id,
            'type_ressource' => fake()->randomElement(['materiel','logiciel']),
            'etat'           => fake()->randomElement([
                'disponible','disponible','disponible','occupe','en_maintenance'
            ]),
            'mode_partage'   => fake()->randomElement(['exclusif','partage','reserve']),
            'nom'            => fake()->words(3, true),
            'description'    => fake()->sentence(),
            'numero_serie'   => strtoupper(fake()->bothify('??-###-???')),
            'localisation'   => 'Bureau ' . fake()->bothify('?##'),
        ];
    }

    public function materiel(): static
    {
        return $this->state(fn($a) => ['type_ressource'=>'materiel']);
    }

    public function logiciel(): static
    {
        return $this->state(fn($a) => ['type_ressource'=>'logiciel','mode_partage'=>'partage']);
    }

    public function disponible(): static
    {
        return $this->state(fn($a) => ['etat'=>'disponible']);
    }

}
