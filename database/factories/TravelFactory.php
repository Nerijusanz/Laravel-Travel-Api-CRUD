<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use \Cviebrock\EloquentSluggable\Services\SlugService;

use App\Models\Travel;

class TravelFactory extends Factory
{
    private bool $isPublic;
    private string $name;
    private string $slug;
    private int $numberOfDays;
    private int $numberOfNights;
    private string $description;

    private function loadData(): Void
    {
        $this->isPublic = mt_rand(0,1);
        $this->name = fake()->unique()->words(2, true);
        $this->slug = SlugService::createSlug(Travel::class, 'slug', $this->name);
        $this->numberOfDays = mt_rand(1,10);
        $this->numberOfNights = ($this->numberOfDays - 1);
        $this->description = $this->name;
    }

    public function definition(): array
    {
        $this->loadData();

        return [
            'is_public' => $this->isPublic,
            'name' => $this->name,
            'slug' => $this->slug,
            'number_of_days' => $this->numberOfDays,
            'number_of_nights' => $this->numberOfNights,
            'description' => $this->description,
        ];
    }
}
