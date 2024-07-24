<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Travel;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{

    public function definition(): array
    {
        $current = Carbon::now();

        return [
            'user_id' => User::factory()->count(1)->create(),
            'travel_id' => Travel::factory()->count(1)->create(),
            'name' => fake()->sentence(),
            'price' => rand(100,1000),
            'start_date' => $start_date = $current->copy()->addDays(rand(0,3))->startOfDay(),
            'end_date' => $start_date->copy()->addDays(rand(0,3))->endOfDay()

        ];
    }
}
