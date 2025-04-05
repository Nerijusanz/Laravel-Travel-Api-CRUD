<?php

namespace Tests\Feature\Api\V1\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Models\User;
use Database\Seeders\tests\traits\DatabaseSeederTraitTest;

class AuthenticateApiTest extends TestCase
{
    use RefreshDatabase;
    use DatabaseSeederTraitTest;

    public const BASE_URL = '/api';
    private $user;


    /*********************TEST CLASS*********************/
    /*
        php artisan test --filter=AuthenticateApiTest
    */
    /****************************************************/


    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::userRole();

    }


    public function test_authenticate_login_returns_token_with_valid_credentials(): void
    {
        /*
        php artisan test --filter=test_authenticate_login_returns_token_with_valid_credentials
        */

        $response = $this->postJson(self::BASE_URL . '/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token']);
    }

    public function test_authenticate_login_return_validation_errors_with_incorrect_credentials_return_response_status_422(): void
    {
        /*
        php artisan test --filter=test_authenticate_login_return_validation_errors_with_incorrect_credentials_return_response_status_422
        */

        $response = $this->postJson(self::BASE_URL . '/login', [
            'email' => 'incorrect-email.com',
            'password' => 'pass',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['email','password'] ]);


        $response = $this->postJson(self::BASE_URL . '/login', [
            'email' => 'incorrect-email.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['email'] ]);


        $response = $this->postJson(self::BASE_URL . '/login', [
            'email' => 'admin@admin.com',
            'password' => 'pass',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['password'] ]);


    }

    public function test_authenticate_login_returns_authentication_errors_with_invalid_credentials_return_response_status_401(): void
    {
        /*
        php artisan test --filter=test_authenticate_login_returns_authentication_errors_with_invalid_credentials_return_response_status_401
        */

        $user = User::factory()->create();

        $response = $this->postJson(self::BASE_URL . '/login', [
            'email' => 'none-existing-email@user.com',
            'password' => 'password',
        ]);

        $response->assertStatus(401);
        $response->assertJsonFragment(['errors' => 'Credentials incorrect']);


        $response = $this->postJson(self::BASE_URL . '/login', [
            'email' => $user->email,
            'password' => 'bad-password',
        ]);

        $response->assertStatus(401);
        $response->assertJsonFragment(['errors' => 'Credentials incorrect']);

    }

    public function test_authenticate_logout_not_logged_in_user_cannot_logout_return_errors_unauthenticate_response_401(): void
    {
        /*
        php artisan test --filter=test_authenticate_logout_not_logged_in_user_cannot_logout_return_errors_unauthenticate_response_401
        */

        $response = $this->postJson(self::BASE_URL . '/logout');

        $response->assertStatus(401);

    }

    public function test_authenticate_logout_logged_in_user_can_logout_successfully_return_response_204(): void
    {
        /*
        php artisan test --filter=test_authenticate_logout_logged_in_user_can_logout_successfully_return_response_204
        */

        $response = $this->postJson(self::BASE_URL . '/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['access_token']);

        $this->actingAs($this->user);

        $response = $this->postJson(self::BASE_URL . '/logout');

        $response->assertStatus(204);

    }

}
