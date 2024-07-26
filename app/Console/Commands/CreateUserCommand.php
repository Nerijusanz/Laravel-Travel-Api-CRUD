<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;


class CreateUserCommand extends Command
{

    protected $signature = 'users:create';


    protected $description = 'Create new user';


    public function handle()
    {
        $user=[];
        $user['name'] = $this->ask('Name of the new user');
        $user['email'] = $this->ask('Email of the new user');
        $roleName = $this->choice('Role of the new user', ['admin', 'user'], 1);
        $user['password'] = $this->secret('Enter password');
        $user['password_confirmation'] = $this->secret('Confirm password');


        $validator = Validator::make($user, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->whereNull('deleted_at')],
            'password' => ['required','confirmed', Password::min(8)],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return;
        }


        $role = Role::where('name', $roleName)->first();

        if (! $role){
            $this->error('Role \'' . $roleName .'\' not found');
            return;
        }


        DB::transaction(function () use ($user, $role) {
            $user = User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make($user['password']),
            ]);

            $user->roles()->attach($role->id);

            $this->info('User ' . $user->email . ' created successfully');
        });

    }
}
