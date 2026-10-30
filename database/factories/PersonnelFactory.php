<?php

namespace Database\Factories;

use App\Models\Personnel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Personnel>
 */
class PersonnelFactory extends Factory
{
        protected $model = Personnel::class;

    public function definition(): array
    {
        $division = Division::inRandomOrder()->first();
        $service  = Service::where('division_id',$division?->id)->inRandomOrder()->first();
        $bureau   = Bureau::where('service_id',$service?->id)->inRandomOrder()->first();

        return [
            'id_user'          => User::factory(),
            'type_personnel_id'=> TypePersonnel::inRandomOrder()->first()?->id,
            'division_id'      => $division?->id,
            'service_id'       => $service?->id,
            'bureau_id'        => $bureau?->id,
            'nom'              => fake()->lastName(),
            'prenom'           => fake()->firstName(),
            'statut'           => true,
        ];
    }

}
