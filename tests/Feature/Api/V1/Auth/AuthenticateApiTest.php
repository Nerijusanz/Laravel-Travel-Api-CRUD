<?php

namespace Tests\Feature\Api\V1\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Role;
use Database\Seeders\RolesTableSeeder;

class AuthenticateApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticate_login_returns_token_with_valid_credentials(): void
    {
        //php artisan test --filter=test_authenticate_login_returns_token_with_valid_credentials

        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token']);
    }

    public function test_authenticate_login_returns_validation_error_422_with_invalid_credentials(): void
    {
        //php artisan test --filter=test_authenticate_login_returns_validation_error_422_with_invalid_credentials

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexisting@user.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
    }

    public function test_authenticate_logout_not_logged_in_user_cannot_logout_return_errors_unauthenticate_response_401(): void
    {
        /*
        php artisan test --filter=test_authenticate_logout_not_logged_in_user_cannot_logout_return_errors_unauthenticate_response_401
        */

        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(401);

    }

    public function test_authenticate_logout_logged_in_user_can_logout_successfully_return_response_204(): void
    {
        /*
        php artisan test --filter=test_authenticate_logout_logged_in_user_can_logout_successfully_return_response_204
        */

        $user = User::factory()->create();


        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token']);


        $response = $this->actingAs($user)->postJson('/api/v1/auth/logout');
        $response->assertStatus(204);

    }

}
