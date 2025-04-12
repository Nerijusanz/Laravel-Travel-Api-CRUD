<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\Role;

class UsersTableSeeder extends Seeder
{

    public function run(): void
    {
        $data = [

            'admin' => [
                'name'           => 'Admin',
                'email'          => 'admin@admin.com',
                'email_verified_at' => now(),
                'password'       => Hash::make('password'),
                'remember_token' => Str::random(10),
            ],

            'user' => [
                'name'           => 'User',
                'email'          => 'user@user.com',
                'email_verified_at' => now(),
                'password'       => Hash::make('password'),
                'remember_token' => Str::random(10),
            ],
        ];

        $user = User::create($data['admin'] );
        $user->roles()->sync(Role::Admin() );

        $user = User::create($data['user'] );
        $user->roles()->sync(Role::User() );

    }
}
