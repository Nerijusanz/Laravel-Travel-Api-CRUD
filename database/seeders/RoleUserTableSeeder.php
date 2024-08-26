<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Role;

class RoleUserTableSeeder extends Seeder
{

    public function run(): void
    {
        User::findOrFail(1)->roles()->sync( Role::Admin() );
        User::findOrFail(2)->roles()->sync( Role::User() );
    }
}
