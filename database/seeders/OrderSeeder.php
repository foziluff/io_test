<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;


class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $index) {
            $status = $faker->randomElement(['поступил', 'водитель назначен', 'водитель на месте', 'исполняется', 'выполнен']);
            $driverId = ($status === 'поступил') ? null : $faker->randomElement([1, 2, 3]);
            $startLongitude = $faker->randomFloat(4, 69.58, 69.68);
            $startLatitude = $faker->randomFloat(4, 40.25, 40.32);
            $endLongitude = $faker->randomFloat(4, 69.58, 69.68);
            $endLatitude = $faker->randomFloat(4, 40.25, 40.32);

            Order::create([
                'startLongitude' => $startLongitude,
                'startLatitude' => $startLatitude,
                'endLongitude' => $endLongitude,
                'endLatitude' => $endLatitude,
                'amount' => $faker->randomFloat(2, 10, 500),
                'status' => $status,
                'driver_id' => $driverId
            ]);
        }
    }
}
