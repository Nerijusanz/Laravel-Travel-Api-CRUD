<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;

class UsersTableSeeder extends Seeder
{

    public function run(): void
    {

        $admin = [
                'name'           => 'Admin',
                'email'          => 'admin@admin.com',
                'email_verified_at' => now(),
                'password'       => Hash::make('password'),
                'remember_token' => null,
                'created_at'     => now(),
                'updated_at'     => now()
        ];

        $user = [
                'name'           => 'User',
                'email'          => 'user@user.com',
                'email_verified_at' => now(),
                'password'       => Hash::make('password'),
                'remember_token' => null,
                'created_at'     => now(),
                'updated_at'     => now()
        ];


        $admin = User::create($admin);
        $admin->roles()->sync(Role::Admin());

        $user = User::create($user);
        $user->roles()->sync(Role::User());

    }
}
