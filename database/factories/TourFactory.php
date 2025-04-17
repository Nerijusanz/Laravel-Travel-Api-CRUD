<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use Carbon\Carbon;
use App\Utilities\Numbers;

class TourFactory extends Factory
{

    private Carbon $current;
    private string $name;
    private int|float $price;
    private string $startDate;
    private string $endDate;


    private function loadData(): Void
    {
        $this->current = Carbon::now();
        $this->name = fake()->unique()->words(2, true);
        $this->price = Numbers::generateRandomFloat(0,1000);
        $this->startDate = ($this->current->copy())->addDays(mt_rand(0,10))->startOfDay()->toDateTimeString();
        $this->endDate = Carbon::parse($this->startDate)->addDays(mt_rand(0,10))->endOfDay()->toDateTimeString();
    }

    public function definition(): array
    {
        $this->loadData();

        return [
            'name' => $this->name,
            'price' => $this->price,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate
        ];
    }
}
