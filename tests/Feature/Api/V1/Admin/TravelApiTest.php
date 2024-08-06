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


    public function test_admin_travel_api_unauthenticated_not_logged_in_public_user_cannot_access_admin_travels_return_unauthenticate_error_status_401(): void
    {
        //php artisan test --filter=test_admin_travel_api_unauthenticated_not_logged_in_public_user_cannot_access_admin_travels_return_unauthenticate_error_status_401

        $response = $this->postJson('/api/v1/admin/travels');

        $response->assertStatus(401);
    }


    public function test_admin_travel_api_authenticated_logged_in_user_cannot_add_admin_travel_return_unauthorize_error_403(): void
    {
        //php artisan test --filter=test_admin_travel_api_authenticated_logged_in_user_cannot_add_admin_travel_return_unauthorize_error_403

        $this->seed(RolesTableSeeder::class);
        $user = User::factory()->create();
        $role = Role::where('name', 'user')->pluck('id');
        $user->roles()->attach($role);

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels');

        $response->assertStatus(403);
    }


    public function test_admin_travel_api_authenticated_logged_in_admin_add_travel_successfully_with_valid_data(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_logged_in_admin_add_travel_successfully_with_valid_data
        */

        $this->seed(RolesTableSeeder::class);
        $user = User::factory()->create();
        $role = Role::where('name', 'admin')->pluck('id');
        $user->roles()->attach($role);

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', [
            'is_public' => 1,
            'name' => '',
        ]);

        $response->assertStatus(422);


        $name = 'Travel 1';
        $slug = "travel-1";

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', [
            'is_public' => 1,
            'name' => $name,
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => 'Travel 1 description',
        ]);

        $response->assertStatus(201);


        $response = $this->actingAs($user)->getJson('/api/v1/admin/travels');
        $response->assertJsonFragment(['name' => $name]);
        $response->assertJsonFragment(['slug' => $slug]);

        $travel = Travel::query()
                            ->where('name',$name)
                            ->where('slug',$slug)
                            ->get();

        $this->assertCount(1, $travel);

    }

    public function test_admin_travel_api_authenticated_logged_in_admin_update_travel_successfully_with_valid_data(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_logged_in_admin_update_travel_successfully_with_valid_data
        */

        $this->seed(RolesTableSeeder::class);
        $user = User::factory()->create();
        $role = Role::where('name', 'admin')->pluck('id');
        $user->roles()->attach($role);


        /****************ADD TRAVEL ***************/

        $name = 'Travel 1';
        $slug = "travel-1";

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', [
            'is_public' => 1,
            'name' => $name,
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => 'Travel 1 description',
        ]);

        $response->assertStatus(201);


        $response = $this->actingAs($user)->getJson('/api/v1/admin/travels');
        $response->assertJsonFragment(['name' => $name]);
        $response->assertJsonFragment(['slug' => $slug]);


        $travel = Travel::query()
                    ->where('name',$name)
                    ->where('slug',$slug)
                    ->get();

        $this->assertCount(1, $travel);
        $this->assertDatabaseHas('travels', [
            'name' => $name,
            'slug' => $slug
        ]);


        /*****************UPDATE TRAVEL ***************/

        $travel = $travel->first();


        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/' . $travel->id, [
            'user_id' => $travel->user_id,
            'is_public' => 1,
            'name' => $nameEmpty='',
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => 'Travel 1 update description',
        ]);

        $response->assertStatus(422);


        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/' . $travel->id, [
            'user_id' => $travel->user_id,
            'is_public' => 1,
            'name' => $name='Travel 1 updated',
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => $description='Travel 1 updated description',
        ]);

        $response->assertStatus(202);


        $response = $this->actingAs($user)->getJson('/api/v1/admin/travels/' . $travel->id);

        $response->assertJsonFragment(['name' => $name]);
        $response->assertJsonFragment(['slug' => $slug]);
        $response->assertJsonFragment(['description' => $description]);

    }

    public function test_admin_travel_api_authenticated_logged_in_admin_delete_travel_successfully_response_status_204(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_logged_in_admin_delete_travel_successfully_response_status_204
        */

        $this->seed(RolesTableSeeder::class);
        $user = User::factory()->create();
        $role = Role::where('name', 'admin')->pluck('id');
        $user->roles()->attach($role);


        /****************ADD TRAVEL ***************/

        $name = 'Travel 1';
        $slug = "travel-1";

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels', [
            'is_public' => 1,
            'name' => $name,
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => 'Travel 1 description',
        ]);

        $response->assertStatus(201);


        $response = $this->actingAs($user)->getJson('/api/v1/admin/travels');
        $response->assertJsonFragment(['name' => $name]);
        $response->assertJsonFragment(['slug' => $slug]);


        $travel = Travel::query()
                    ->where('name',$name)
                    ->where('slug',$slug)
                    ->get();

        $this->assertCount(1, $travel);
        $this->assertDatabaseHas('travels', [
            'name' => $name,
            'slug' => $slug
        ]);


        /***************** DELETE TRAVEL ***************/

        $travel = $travel->first();


        $response = $this->actingAs($user)->deleteJson('/api/v1/admin/travels/' . $travel->id);
        $response->assertStatus(204);


        $response = $this->actingAs($user)->get('/api/v1/admin/travels');

        $response->assertStatus(200);
        $response->assertJsonMissing(['name' => $travel->name]);
        $response->assertJsonMissing(['slug' => $travel->slug ]);

    }

}
