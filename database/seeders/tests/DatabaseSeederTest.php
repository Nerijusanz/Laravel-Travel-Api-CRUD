<?php

namespace Database\Seeders\tests;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeederTest extends Seeder
{

    public function run(): void
    {
        $this->call(UsersTableSeederTest::class);
        $this->call(RolesTableSeederTest::class);
        $this->call(RoleUserTableSeederTest::class);

    }
}
