<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Travel;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class TravelFactory extends Factory
{

    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);
        $slug = SlugService::createSlug(Travel::class, 'slug', $title);
        $numberOfDays=rand(1,10);

        return [
            'user_id' => 1,
            'is_public' => fake()->boolean(),
            'name' => $title,
            'slug' => $slug,
            'number_of_days' => (($numberOfDays < 1)? 1 : $numberOfDays),
            'number_of_nights' => (($numberOfDays > 0)? ($numberOfDays-1) : 0),
            'description' => fake()->sentence(),
        ];
    }
}
