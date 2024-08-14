<?php

namespace Tests\Feature\Api\V1\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Role;
use App\Models\Travel;
use Database\Seeders\RolesTableSeeder;

class TravelApiTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    public const BASE_URL = '/api';

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }


    public function test_admin_travel_api_unauthenticated_not_logged_in_public_user_cannot_access_admin_travels_return_unauthenticate_error_status_401(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_unauthenticated_not_logged_in_public_user_cannot_access_admin_travels_return_unauthenticate_error_status_401
        */

        $response = $this->postJson(self::BASE_URL . '/admin/travels');

        $response->assertStatus(401);
    }


    public function test_admin_travel_api_authenticated_logged_in_user_cannot_add_admin_travel_return_unauthorize_error_403(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_logged_in_user_cannot_add_admin_travel_return_unauthorize_error_403
        */

        $this->seed(RolesTableSeeder::class);

        $this->user->roles()->attach(Role::User());

        $response = $this->actingAs($this->user)->postJson(self::BASE_URL . '/admin/travels');

        $response->assertStatus(403);
        $this->assertCount(0, Travel::all());

    }


    public function test_admin_travel_api_authenticated_logged_in_admin_add_travel_successfully_with_valid_data(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_logged_in_admin_add_travel_successfully_with_valid_data
        */

        $this->seed(RolesTableSeeder::class);

        $this->user->roles()->attach(Role::Admin());

        $response = $this->actingAs($this->user)->postJson(self::BASE_URL . '/admin/travels', [
            'is_public' => 1,
            'name' => '',
        ]);

        $response->assertStatus(422);
        $this->assertCount(0, Travel::all());


        $name = 'Travel 1';

        $response = $this->actingAs($this->user)->postJson(self::BASE_URL . '/admin/travels', [
            'is_public' => 1,
            'name' => $name,
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => 'Travel 1 description',
        ]);

        $response->assertStatus(201);

        $this->assertCount(1, Travel::all());

        $this->assertDatabaseHas(Travel::class, [
            'name' => $name
        ]);

        $travel = Travel::query()
                    ->where('name',$name)
                    ->first();

        $response = $this->actingAs($this->user)->getJson(self::BASE_URL . '/admin/travels');
        $response->assertJsonFragment(['name' => $travel->name]);
        $response->assertJsonFragment(['slug' => $travel->slug]);

    }

    public function test_admin_travel_api_authenticated_logged_in_admin_update_travel_successfully_with_valid_data(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_logged_in_admin_update_travel_successfully_with_valid_data
        */

        $this->seed(RolesTableSeeder::class);

        $this->user->roles()->attach(Role::Admin());

        /****************ADD TRAVEL ***************/

        $name = 'Travel 1';

        $this->assertCount(0, Travel::all());


        $endpoint = self::BASE_URL . '/admin/travels';

        $response = $this->actingAs($this->user)->postJson($endpoint, [
            'is_public' => 1,
            'name' => $name,
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => 'Travel 1 description',
        ]);

        $response->assertStatus(201);

        $this->assertCount(1, Travel::all());

        $this->assertDatabaseHas(Travel::class, [
            'name' => $name
        ]);

        $travel = Travel::query()
                    ->where('name',$name)
                    ->first();


        $response = $this->actingAs($this->user)->getJson($endpoint);
        $response->assertJsonFragment(['name' => $travel->name]);
        $response->assertJsonFragment(['slug' => $travel->slug]);


        /*****************UPDATE TRAVEL ***************/


        $endpoint = self::BASE_URL . '/admin/travels/' . $travel->id;

        $response = $this->actingAs($this->user)->putJson($endpoint, [
            'user_id' => $travel->user_id,
            'is_public' => 1,
            'name' => $nameEmpty='',
            'number_of_days' => 1,
            'number_of_nights' => 0,
        ]);

        $response->assertStatus(422);

        $this->assertDatabaseHas(Travel::class, [
            'name' => $travel->name
        ]);

        $nameUpdated = $travel->name . ' Updated';

        $response = $this->actingAs($this->user)->putJson($endpoint, [
            'user_id' => $travel->user_id,
            'is_public' => 1,
            'name' => $nameUpdated,
            'number_of_days' => 1,
            'number_of_nights' => 0,
        ]);

        $response->assertStatus(202);

        $this->assertDatabaseMissing(Travel::class, [
            'name' => $travel->name
        ]);

        $this->assertDatabaseHas(Travel::class, [
            'name' => $nameUpdated
        ]);

        $travel = Travel::query()
                    ->where('name',$nameUpdated)
                    ->first();


        $response = $this->actingAs($this->user)->getJson($endpoint);

        $response->assertJsonFragment(['name' => $travel->name]);
        $response->assertJsonFragment(['slug' => $travel->slug]);


    }

    public function test_admin_travel_api_authenticated_logged_in_admin_delete_travel_successfully_response_status_204(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_logged_in_admin_delete_travel_successfully_response_status_204
        */

        $this->seed(RolesTableSeeder::class);

        $this->user->roles()->attach(Role::Admin());


        /****************ADD TRAVEL ***************/

        $endpoint = self::BASE_URL . '/admin/travels';

        $name = 'Travel 1';

        $response = $this->actingAs($this->user)->postJson($endpoint, [
            'is_public' => 1,
            'name' => $name,
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => 'Travel 1 description',
        ]);

        $response->assertStatus(201);

        $this->assertCount(1, Travel::all());

        $this->assertDatabaseHas(Travel::class, [
            'name' => $name
        ]);

        $travel = Travel::query()
                    ->where('name',$name)
                    ->first();


        $response = $this->actingAs($this->user)->getJson($endpoint);
        $response->assertJsonFragment(['name' => $travel->name]);
        $response->assertJsonFragment(['slug' => $travel->slug]);


        /***************** DELETE TRAVEL ***************/


        $this->assertCount(1, Travel::all());
        $this->assertDatabaseHas(Travel::class, [
            'name' => $travel->name
        ]);

        $response = $this->actingAs($this->user)->deleteJson(self::BASE_URL . '/admin/travels/' . $travel->id);
        $response->assertStatus(204);

        $this->assertCount(0, Travel::all());
        $this->assertDatabaseMissing(Travel::class, [
            'name' => $travel->name
        ]);

        $response = $this->actingAs($this->user)->get(self::BASE_URL . '/admin/travels');

        $response->assertStatus(200);
        $response->assertJsonMissing(['name' => $travel->name]);
        $response->assertJsonMissing(['slug' => $travel->slug ]);

    }

}
