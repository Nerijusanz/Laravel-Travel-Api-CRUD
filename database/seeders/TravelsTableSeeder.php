<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Travel;

class TravelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $travels = [
            [
                'id'=> 1,
                'user_id' => 1,
                'is_public' => 1,
                'name' => 'Travel 1',
                'slug' => 'travel-1',
                'number_of_days' => 1,
                'number_of_nights' => 0,
                'description' => 'Travel description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 2,
                'user_id' => 1,
                'is_public' => 1,
                'name' => 'Travel 2',
                'slug' => 'travel-2',
                'number_of_days' => 2,
                'number_of_nights' => 1,
                'description' => 'Travel description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 3,
                'user_id' => 1,
                'is_public' => 1,
                'name' => 'Travel 3',
                'slug' => 'travel-3',
                'number_of_days' => 3,
                'number_of_nights' => 2,
                'description' => 'Travel description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 4,
                'user_id' => 1,
                'is_public' => 1,
                'name' => 'Travel 4',
                'slug' => 'travel-4',
                'number_of_days' => 4,
                'number_of_nights' => 3,
                'description' => 'Travel description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 5,
                'user_id' => 1,
                'is_public' => 1,
                'name' => 'Travel 5',
                'slug' => 'travel-5',
                'number_of_days' => 5,
                'number_of_nights' => 4,
                'description' => 'Travel description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 6,
                'user_id' => 1,
                'is_public' => 1,
                'name' => 'Travel6',
                'slug' => 'travel-6',
                'number_of_days' => 6,
                'number_of_nights' => 5,
                'description' => 'Travel description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 7,
                'user_id' => 1,
                'is_public' => 1,
                'name' => 'Travel 7',
                'slug' => 'travel-7',
                'number_of_days' => 7,
                'number_of_nights' => 6,
                'description' => 'Travel description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 8,
                'user_id' => 1,
                'is_public' => 1,
                'name' => 'Travel 8',
                'slug' => 'travel-8',
                'number_of_days' => 8,
                'number_of_nights' => 7,
                'description' => 'Travel description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 9,
                'user_id' => 1,
                'is_public' => 1,
                'name' => 'Travel 9',
                'slug' => 'travel-9',
                'number_of_days' => 9,
                'number_of_nights' => 8,
                'description' => 'Travel description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 10,
                'user_id' => 1,
                'is_public' => 1,
                'name' => 'Travel 10',
                'slug' => 'travel-10',
                'number_of_days' => 10,
                'number_of_nights' => 9,
                'description' => 'Travel description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 11,
                'user_id' => 1,
                'is_public' => 1,
                'name' => 'Travel 11',
                'slug' => 'travel-11',
                'number_of_days' => 11,
                'number_of_nights' => 10,
                'description' => 'Travel description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 12,
                'user_id' => 1,
                'is_public' => 1,
                'name' => 'Travel 12',
                'slug' => 'travel-12',
                'number_of_days' => 12,
                'number_of_nights' => 11,
                'description' => 'Travel description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 13,
                'user_id' => 1,
                'is_public' => 1,
                'name' => 'Travel 13',
                'slug' => 'travel-13',
                'number_of_days' => 13,
                'number_of_nights' => 12,
                'description' => 'Travel description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 14,
                'user_id' => 1,
                'is_public' => 1,
                'name' => 'Travel 14',
                'slug' => 'travel-14',
                'number_of_days' => 14,
                'number_of_nights' => 13,
                'description' => 'Travel description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 15,
                'user_id' => 1,
                'is_public' => 1,
                'name' => 'Travel 15',
                'slug' => 'travel-15',
                'number_of_days' => 15,
                'number_of_nights' => 14,
                'description' => 'Travel description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 16,
                'user_id' => 1,
                'is_public' => 1,
                'name' => 'Travel 16',
                'slug' => 'travel-16',
                'number_of_days' => 16,
                'number_of_nights' => 15,
                'description' => 'Travel description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 17,
                'user_id' => 1,
                'is_public' => 0,
                'name' => 'Travel 17',
                'slug' => 'travel-17',
                'number_of_days' => 17,
                'number_of_nights' => 16,
                'description' => 'Travel is not public description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 18,
                'user_id' => 1,
                'is_public' => 0,
                'name' => 'Travel 18',
                'slug' => 'travel-18',
                'number_of_days' => 18,
                'number_of_nights' => 17,
                'description' => 'Travel is not public description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 19,
                'user_id' => 1,
                'is_public' => 0,
                'name' => 'Travel 19',
                'slug' => 'travel-19',
                'number_of_days' => 19,
                'number_of_nights' => 18,
                'description' => 'Travel is not public description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
            [
                'id'=> 20,
                'user_id' => 1,
                'is_public' => 0,
                'name' => 'Travel 20',
                'slug' => 'travel-20',
                'number_of_days' => 20,
                'number_of_nights' => 19,
                'description' => 'Travel is not public description',
                'created_at'     => now(),
                'updated_at'     => now()
            ],
        ];

        Travel::insert($travels);
    }
}
