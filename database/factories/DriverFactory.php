<?php

namespace Database\Factories;

use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Driver>
 */
class DriverFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = Faker::create();

        $longitude = $faker->randomFloat(4, 69.58, 69.68);
        $latitude = $faker->randomFloat(4, 40.25, 40.32);

        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'login' => $this->faker->unique()->userName,
            'password' => bcrypt('123123'),
            'longitude' => $longitude,
            'latitude' => $latitude,
            'balance' => $this->faker->randomFloat(2, 0, 1000),
            'rating' => $this->faker->randomFloat(2, 0, 0.99),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'updated_at' => now()->toDateTimeString(),
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
