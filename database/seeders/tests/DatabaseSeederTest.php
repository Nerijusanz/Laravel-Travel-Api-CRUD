<?php

namespace Database\Seeders\tests;

use Illuminate\Database\Seeder;

use Database\Seeders\RolesTableSeeder;
use Database\Seeders\UsersTableSeeder;

class DatabaseSeederTest extends Seeder
{

    public function run(): void
    {

        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);

    }
}
