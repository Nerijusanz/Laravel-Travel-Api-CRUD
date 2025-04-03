<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use Carbon\Carbon;
use App\Utilities\Numbers;

class TourFactory extends Factory
{

    public function definition(): array
    {
        $current = Carbon::now();

        return [
            'user_id' => 1,
            'travel_id' => 1,
            'name' => fake()->unique()->words(3, true),
            'price' => $price = Numbers::generateRandomFloat(0,1000),
            'start_date' => $startDate = Carbon::parse($current->copy())->addDays(mt_rand(0,3))->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(mt_rand(0,3))->endOfDay()->toDateTimeString(),
        ];
    }
}
