<?php

namespace Tests\Feature\Api\V1\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Travel;
use App\Models\Tour;
use Database\Seeders\RolesTableSeeder;
use Database\Seeders\tests\traits\DatabaseSeederTraitTest;


class TourApiTest extends TestCase
{

    use RefreshDatabase;
    use DatabaseSeederTraitTest;

    private $admin;
    private $user;
    public const BASE_URL = '/api';


    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::adminRole();
        $this->user = User::userRole();

    }


    public function test_admin_tour_api_unauthenticated_not_logged_in_user_cannot_access_admin_tours_return_unauthenticate_error_status_401(): void
    {
        /*
        php artisan test --filter=test_admin_tour_api_unauthenticated_not_logged_in_user_cannot_access_admin_tours_return_unauthenticate_error_status_401

        */

        $response = $this->getJson(self::BASE_URL . '/admin/travels/1/tours');

        $response->assertStatus(401);

        $response = $this->getJson(self::BASE_URL . '/admin/travels/1/tours/1');

        $response->assertStatus(401);

        $response = $this->postJson(self::BASE_URL . '/admin/travels/1/tours');

        $response->assertStatus(401);

        $response = $this->putJson(self::BASE_URL . '/admin/travels/1/tours/1');

        $response->assertStatus(401);

        $response = $this->deleteJson(self::BASE_URL . '/admin/travels/1/tours/1');

        $response->assertStatus(401);
    }

    public function test_admin_tour_api_authenticated_logged_in_not_admin_user_cannot_access_admin_tours_return_unauthorize_error_status_403(): void
    {
        /*
        php artisan test --filter=test_admin_tour_api_authenticated_logged_in_not_admin_user_cannot_access_admin_tours_return_unauthorize_error_status_403
        */

        $this->actingAs($this->user);

        $response = $this->getJson(self::BASE_URL . '/admin/travels/1/tours');

        $response->assertStatus(403);

        $response = $this->getJson(self::BASE_URL . '/admin/travels/1/tours/1');

        $response->assertStatus(403);

        $response = $this->postJson(self::BASE_URL . '/admin/travels/1/tours');

        $response->assertStatus(403);

        $response = $this->putJson(self::BASE_URL . '/admin/travels/1/tours/1');

        $response->assertStatus(403);

        $response = $this->deleteJson(self::BASE_URL . '/admin/travels/1/tours/1');

        $response->assertStatus(403);

    }

    public function test_admin_tour_api_authenticated_logged_in_admin_add_tour_with_incorrect_data_return_validation_error_response_status_422(): void
    {
        /*
        php artisan test --filter=test_admin_tour_api_authenticated_logged_in_admin_add_tour_with_incorrect_data_return_validation_error_response_status_422
        */

        $this->actingAs($this->admin);

        $current = Carbon::now();


        $response = $this->postJson(self::BASE_URL . '/admin/travels/1/tours', [
            'name' => NULL,
            'price' => NULL,
            'start_date' => NULL,
            'end_date' => NULL,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name','price','start_date','end_date'] ]);


        $response = $this->postJson(self::BASE_URL . '/admin/travels/1/tours', [
            'name' => $toShortName='T',
            'price' => 100,
            'start_date' => $startDate = $current->copy()->addDays(0)->startOfDay(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay(),
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name'] ]);


        $response = $this->postJson(self::BASE_URL . '/admin/travels/1/tours', [
            'name' => $toLongName=str()->random(256),
            'price' => 100,
            'start_date' => $startDate = $current->copy()->addDays(0)->startOfDay(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay(),
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name'] ]);


        /****************** Start Name Unique Validation *****************/

        $travel = Travel::factory()->create();

        Tour::factory()->create([
            'travel_id' => $travel->id,
            'name' => $uniqueName='Tour Unique',
        ]);

        $response = $this->postJson(self::BASE_URL . '/admin/travels/' . $travel->id . '/tours', [
            'name' => $uniqueNameAlreadyTaken=$uniqueName,
            'price' => 100,
            'start_date' => $startDate = $current->copy()->addDays(0)->startOfDay(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay(),
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name'] ]);

        /******************************************************************/

        $response = $this->postJson(self::BASE_URL . '/admin/travels/1/tours', [
            'name' => 'Tour 1',
            'price' => $priceMustBeCorrectNumber='1xx',
            'start_date' => $startDate = $current->copy()->addDays(0)->startOfDay(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay(),
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['price'] ]);


        $response = $this->postJson(self::BASE_URL . '/admin/travels/1/tours', [
            'name' => 'Tour 1',
            'price' => $priceMustBeMinZero='-1.00',
            'start_date' => $startDate = $current->copy()->addDays(0)->startOfDay(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay(),
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['price'] ]);


        $response = $this->postJson(self::BASE_URL . '/admin/travels/1/tours', [
            'name' => 'Tour 1',
            'price' => 100,
            'start_date' => $starDateIncorrectValue='xxxx-xx-xx xx:xx:xx',
            'end_date' => $endDateIncorrectValue='xxxx-xx-xx xx:xx:xx',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['start_date','end_date'] ]);


        $response = $this->postJson(self::BASE_URL . '/admin/travels/1/tours', [
            'name' => 'Tour 1',
            'price' => 100,
            'start_date' => $startDate = $current->copy()->addDays(0)->startOfDay(),
            'end_date' => $endDateIncorrectValue = 'xxxx-xx-xx',
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['end_date'] ]);


        $response = $this->postJson(self::BASE_URL . '/admin/travels/1/tours', [
            'name' => 'Tour 1',
            'price' => 100,
            'start_date' => $startDate = $current->copy()->addDays(0)->startOfDay(),
            'end_date' => $endDateMustBeAfterStartDate = Carbon::parse($startDate)->addDays(0)->startOfDay(),
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['end_date'] ]);

    }

    public function test_admin_tour_api_authenticated_admin_add_tour_with_valid_data_return_response_status_201(): void
    {
        /*
        php artisan test --filter=test_admin_tour_api_authenticated_admin_add_tour_with_valid_data_return_response_status_201
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create();

        $tour = Tour::factory(['travel_id' => $travel->id])->create();

        $tourNew = [
            'name' => str()->random(10),
            'price' => $tour->price,
            'start_date' =>  $tour->start_date,
            'end_date' => $tour->end_date,
        ];


        $endpoint = self::BASE_URL . '/admin/travels/' . $travel->id . '/tours';

        $this->assertCount(1, $travel->tours()->get());


        $response = $this->postJson($endpoint,$tourNew);

        $response->assertStatus(201);

        $this->assertCount(2, $travel->tours()->get());


        $tourNew = $travel->tours()->latest()->first();

        $response = $this->getJson($endpoint);

        $response->assertStatus(200);

        $response->assertJsonFragment(['id' => $tourNew->id]);

    }

    public function test_admin_tour_api_authenticated_admin_update_tour_with_valid_data_response_status_200(): void
    {
        /*
        php artisan test --filter=test_admin_tour_api_authenticated_admin_update_tour_with_valid_data_response_status_200
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create();

        $tour = Tour::factory(['travel_id' => $travel->id])->create();

        $this->assertCount(1,$travel->tours()->get());


        $tourUpdated = [
            'name' => $nameUpdated = $tour->name . $tour->id,
            'price' => $tour->price,
            'start_date' =>  $tour->start_date,
            'end_date' => $tour->end_date,
        ];


        $endpoint = self::BASE_URL . '/admin/travels/' . $travel->id . '/tours/' . $tour->id;

        $response = $this->putJson($endpoint,$tourUpdated);

        $response->assertStatus(200);


        $response = $this->getJson($endpoint);

        $response->assertStatus(200);
        $response->assertJsonFragment($tourUpdated);

    }

    public function test_admin_tour_api_authenticated_admin_delete_tour_response_status_204(): void
    {
        /*
        php artisan test --filter=test_admin_tour_api_authenticated_admin_delete_tour_response_status_204
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create();

        $tour = Tour::factory(['travel_id' => $travel->id])->create();

        $this->assertCount(1,$travel->tours()->get());


        $endpoint = self::BASE_URL . '/admin/travels/' . $travel->id . '/tours/' . $tour->id;

        $response = $this->getJson($endpoint);

        $response->assertStatus(200);
        $response->assertJsonFragment(['id'=>$tour->id]);


        $response = $this->deleteJson($endpoint);

        $response->assertStatus(204);

        $this->assertCount(0,$travel->tours()->get());


        $response = $this->getJson($endpoint);

        $response->assertStatus(404);

    }

}
