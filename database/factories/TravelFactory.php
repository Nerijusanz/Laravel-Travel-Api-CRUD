<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Travel>
 */
class TravelFactory extends Factory
{

    public function definition(): array
    {
        $title = fake()->sentence();
        $slug = Str::slug($title, '-');
        $numbers_of_days=rand(1,10);

        return [
            'user_id' => 1,
            'is_public' => fake()->boolean(),
            'name' => $title,
            'slug' => $slug,
            'number_of_days' => $numbers_of_days,
            'number_of_nights' => $numbers_of_days-1,
            'description' => fake()->paragraph(),
        ];
    }
}
