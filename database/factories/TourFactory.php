<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Travel;


class TourFactory extends Factory
{

    public function definition(): array
    {
        $current = Carbon::now();

        return [
            'user_id' => 1,
            'travel_id' => 1,
            'name' => fake()->unique()->words(3, true),
            'price' => number_format($price=rand(100,1000),2),
            'start_date' => $startDate = Carbon::parse($current->copy())->addDays(rand(0,3))->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(rand(0,3))->endOfDay()->toDateTimeString(),
        ];
    }
}
