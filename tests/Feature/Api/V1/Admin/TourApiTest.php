<?php

namespace Tests\Feature\Api\V1\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
use App\Models\Travel;
use App\Models\Tour;
use Database\Seeders\RolesTableSeeder;


class TourApiTest extends TestCase
{

    use RefreshDatabase;

    public function test_admin_tour_api_unauthenticated_not_logged_in_public_user_cannot_access_admin_tours_return_unauthenticate_error_status_401(): void
    {
        /*
        php artisan test --filter=test_admin_tour_api_unauthenticated_not_logged_in_public_user_cannot_access_admin_tours_return_unauthenticate_error_status_401
        */

        $this->seed(RolesTableSeeder::class);
        $user = User::factory()->create();
        $role = Role::where('name', 'user')->pluck('id');
        $user->roles()->attach($role);

        $this->actingAs($user);

        $travel = Travel::factory()->create([
            'is_public' => 1,
            'name' => 'Travel 1',
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => 'Travel 1 description',
        ]);

        Auth::logout();

        $response = $this->postJson('/api/v1/admin/travels/' . $travel->id . '/tours');

        $response->assertStatus(401);
    }

    public function test_admin_tour_api_authenticated_logged_in_user_cannot_add_admin_tour_return_unauthorize_error_403(): void
    {
        /*
        php artisan test --filter=test_admin_tour_api_authenticated_logged_in_user_cannot_add_admin_tour_return_unauthorize_error_403
        */

        $this->seed(RolesTableSeeder::class);
        $user = User::factory()->create();
        $role = Role::where('name', 'user')->pluck('id');
        $user->roles()->attach($role);

        $this->actingAs($user);

        $travel = Travel::factory()->create([
            'is_public' => 1,
            'name' => 'Travel 1',
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => 'Travel 1 description',
        ]);

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/' . $travel->id . '/tours');

        $response->assertStatus(403);
    }

    public function test_admin_tour_api_authenticated_logged_in_admin_add_tour_successfully_with_valid_data_return_response_status_201(): void
    {
        /*
        php artisan test --filter=test_admin_tour_api_authenticated_logged_in_admin_add_tour_successfully_with_valid_data_return_response_status_201
        */

        $this->seed(RolesTableSeeder::class);
        $user = User::factory()->create();
        $role = Role::where('name', 'admin')->pluck('id');
        $user->roles()->attach($role);

        $this->actingAs($user);

        $travel = Travel::factory()->create([
            'is_public' => 1,
            'name' => 'Travel 1',
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => 'Travel 1 description',
        ]);


        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/' . $travel->id . '/tours', [
            'travel_id' => $travel->id,
            'name' => '',
            'price' => '',
            'start_date' => '',
            'end_date' => '',
        ]);

        $response->assertStatus(422);


        $current = Carbon::now();

        $response = $this->actingAs($user)->postJson('/api/v1/admin/travels/' . $travel->id . '/tours', [
            'travel_id' => $travel->id,
            'name' => $name='Tour 1',
            'price' => $price=100,
            'start_date' =>  $startDate = $current->addDays(0)->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
        ]);


        $response->assertStatus(201);

        $response = $this->actingAs($user)->get('/api/v1/admin/travels/' . $travel->id . '/tours');
        $response->assertJsonFragment(['name' => $name]);
        $response->assertJsonFragment(['price' => number_format($price,2)]);

    }

    public function test_admin_tour_api_authenticated_logged_in_admin_update_tour_successfully_with_valid_data_response_status_202(): void
    {
        /*
        php artisan test --filter=test_admin_tour_api_authenticated_logged_in_admin_update_tour_successfully_with_valid_data_response_status_202
        */

        $this->seed(RolesTableSeeder::class);
        $user = User::factory()->create();
        $role = Role::where('name', 'admin')->pluck('id');
        $user->roles()->attach($role);

        $this->actingAs($user);

        $travel = Travel::factory()->create([
            'is_public' => 1,
            'name' => 'Travel 1',
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => 'Travel 1 description',
        ]);

        $current = Carbon::now();

        $tour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'name' => 'Tour 1',
            'price' => 100,
            'start_date' =>  $startDate = $current->addDays(0)->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
        ]);


        $current = Carbon::now();

        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/' . $tour->travel_id . '/tours/' . $tour->id, [
            'user_id' => $tour->user_id,
            'travel_id' => $tour->travel_id,
            'name' => $nameEmpty='',
            'price' => 150,
            'start_date' =>  $startDate = $current->addDays(0)->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
        ]);


        $response->assertStatus(422);


        $response = $this->actingAs($user)->putJson('/api/v1/admin/travels/' . $tour->travel_id . '/tours/' . $tour->id, [
            'user_id' => $tour->user_id,
            'travel_id' => $tour->travel_id,
            'name' => $name='Tour 1 updated',
            'price' => $price=150,
            'start_date' =>  $startDate = $current->addDays(0)->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
        ]);


        $response->assertStatus(202);

        $response = $this->actingAs($user)->get('/api/v1/admin/travels/' . $tour->travel_id . '/tours/' . $tour->id);
        $response->assertJsonFragment(['name' => $name]);
        $response->assertJsonFragment(['price' => number_format($price,2)]);

    }

    public function test_admin_tour_api_authenticated_logged_in_admin_delete_tour_successfully_response_status_204(): void
    {
        /*
        php artisan test --filter=test_admin_tour_api_authenticated_logged_in_admin_delete_tour_successfully_response_status_204
        */

        $this->seed(RolesTableSeeder::class);
        $user = User::factory()->create();
        $role = Role::where('name', 'admin')->pluck('id');
        $user->roles()->attach($role);

        $this->actingAs($user);

        $travel = Travel::factory()->create([
            'is_public' => 1,
            'name' => 'Travel 1',
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => 'Travel 1 description',
        ]);

        $current = Carbon::now();

        $tour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'name' => $name='Tour 1',
            'price' => 100,
            'start_date' =>  $startDate = $current->addDays(0)->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
        ]);


        $response = $this->actingAs($user)->get('/api/v1/admin/travels/' . $travel->id . '/tours');

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => $name]);


        $response = $this->actingAs($user)->deleteJson('/api/v1/admin/travels/' . $travel->id . '/tours/' . $tour->id);
        $response->assertStatus(204);


        $response = $this->actingAs($user)->get('/api/v1/admin/travels/' . $travel->id . '/tours');

        $response->assertStatus(200);
        $response->assertJsonMissing(['name' => $name]);

    }

}
