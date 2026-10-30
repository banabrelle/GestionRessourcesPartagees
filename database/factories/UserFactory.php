<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'matricule'         => strtoupper(fake()->bothify('??-###')),
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'remember_token'    => Str::random(10),
            'is_active'         => true,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attr) => ['email_verified_at' => null]);
    }

    public function inactif(): static
    {
        return $this->state(fn (array $attr) => ['is_active' => false]);
    }
}