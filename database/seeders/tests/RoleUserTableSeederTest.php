<?php

namespace Database\Seeders\tests;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;

class RoleUserTableSeederTest extends Seeder
{

    public function run(): void
    {
        User::findOrFail(1)->roles()->sync(1);
        User::findOrFail(2)->roles()->sync(2);
    }
}
