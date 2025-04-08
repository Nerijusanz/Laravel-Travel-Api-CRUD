<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Travel;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class TravelFactory extends Factory
{

    public function definition(): array
    {
        $isPublic = mt_rand(0,1);
        $title = fake()->unique()->words(3, true);
        $slug = SlugService::createSlug(Travel::class, 'slug', $title);
        $numberOfDays = mt_rand(1,10);
        $numberOfNights = ($numberOfDays-1);

        return [
            'is_public' => $isPublic,
            'name' => $title,
            'slug' => $slug,
            'number_of_days' => $numberOfDays,
            'number_of_nights' => $numberOfNights,
            'description' => $title,
        ];
    }
}
