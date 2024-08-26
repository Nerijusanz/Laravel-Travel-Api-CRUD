<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Role;

class RolesTableSeeder extends Seeder
{

    public function run(): void
    {
        $roles = [
            [
                'id'    => 1,
                'name' => Role::ADMIN,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id'    => 2,
                'name' => Role::USER,
                'created_at' => now(),
                'updated_at' => now()
            ],

        ];

        Role::insert($roles);
    }
}
