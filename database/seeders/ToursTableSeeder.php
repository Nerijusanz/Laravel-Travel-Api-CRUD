<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Carbon\Carbon;
use App\Models\Tour;

class ToursTableSeeder extends Seeder
{

    public function run(): void
    {

        $current = Carbon::now();

        $tours = [
            [
                'id'=> 1,
                'user_id' => 1,
                'travel_id' => 1,
                'name' => 'Tour 1 (Travel 1)',
                'price' => 10000,
                'start_date' => $start_date = $current->copy()->addDays(0)->startOfDay(),
                'end_date' => $start_date->copy()->addDays(0)->endOfDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'=> 2,
                'user_id' => 1,
                'travel_id' => 1,
                'name' => 'Tour 2 (Travel 1)',
                'price' => 15000,
                'start_date' => $start_date = $current->copy()->addDays(1)->startOfDay(),
                'end_date' => $start_date->copy()->addDays(0)->endOfDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'=> 3,
                'user_id' => 1,
                'travel_id' => 2,
                'name' => 'Tour 3 (Travel 2)',
                'price' => 20000,
                'start_date' => $start_date = $current->copy()->addDays(0)->startOfDay(),
                'end_date' => $start_date->copy()->addDays(1)->endOfDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'=> 4,
                'user_id' => 1,
                'travel_id' => 2,
                'name' => 'Tour 4 (Travel 2)',
                'price' => 25000,
                'start_date' => $start_date = $current->copy()->addDays(1)->startOfDay(),
                'end_date' => $start_date->copy()->addDays(1)->endOfDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'=> 5,
                'user_id' => 1,
                'travel_id' => 3,
                'name' => 'Tour 5 (Travel 3)',
                'price' => 30000,
                'start_date' => $start_date = $current->copy()->addDays(0)->startOfDay(),
                'end_date' => $start_date->copy()->addDays(2)->endOfDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'=> 6,
                'user_id' => 1,
                'travel_id' => 3,
                'name' => 'Tour 6 (Travel 3)',
                'price' => 35000,
                'start_date' => $start_date = $current->copy()->addDays(1)->startOfDay(),
                'end_date' => $start_date->copy()->addDays(2)->endOfDay(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Tour::insert($tours);

    }
}
