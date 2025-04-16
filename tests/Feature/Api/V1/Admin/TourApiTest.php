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

    public function test_admin_tour_api_authenticated_logged_in_admin_add_tour_successfully_with_valid_data_return_response_status_201(): void
    {
        /*
        php artisan test --filter=test_admin_tour_api_authenticated_logged_in_admin_add_tour_successfully_with_valid_data_return_response_status_201
        */

        $this->actingAs($this->admin);

        $current = Carbon::now();

        $travel = Travel::factory()->create();

        $endpoint = self::BASE_URL . '/admin/travels/' . $travel->id . '/tours';

        $tourOne = [
            'name' => 'Tour One',
            'price' => 100,
            'start_date' =>  $startDate = $current->copy()->addDays(0)->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
        ];

        $response = $this->postJson($endpoint,$tourOne);

        $response->assertStatus(201);

        $this->assertCount(1, $travel->tours()->get());

        $tourOne = $travel->tours()
                    ->where('name',$tourOne['name'])
                    ->first();

        $tourOneResult = [
            'id' => $tourOne->id,
            'name' => $tourOne->name,
            'price' => $tourOne->price,
            'start_date' => $tourOne->start_date,
            'end_date' => $tourOne->end_date
        ];

        $response = $this->getJson($endpoint);

        $response->assertStatus(200);

        $response->assertJsonFragment($tourOneResult);

    }

    public function test_admin_tour_api_authenticated_logged_in_admin_update_tour_successfully_with_valid_data_response_status_200(): void
    {
        /*
        php artisan test --filter=test_admin_tour_api_authenticated_logged_in_admin_update_tour_successfully_with_valid_data_response_status_200
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create([
            'is_public' => 1,
            'name' => 'Travel 1',
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => 'Travel 1 description',
        ]);

        $this->assertCount(1, Travel::all());

        $this->assertDatabaseHas(Travel::class, [
            'name' => $travel->name
        ]);


        $travel = Travel::query()
                    ->where('name',$travel->name)
                    ->first();


        $current = Carbon::now();

        $tour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'name' => 'Tour 1',
            'price' => 100,
            'start_date' =>  $startDate = $current->addDays(0)->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
        ]);

        $this->assertCount(1, $travel->tours()->get());

        $this->assertDatabaseHas(Tour::class, [
            'name' => $tour->name
        ]);

        $tour = $travel->tours()
                ->where('name',$tour->name)
                ->first();


        $current = Carbon::now();

        $endpoint = self::BASE_URL . '/admin/travels/' . $travel->id . '/tours/' . $tour->id;

        $response = $this->putJson($endpoint, [
            'user_id' => $tour->user_id,
            'travel_id' => $tour->travel_id,
            'name' => $nameEmpty='',
            'price' => 150,
            'start_date' =>  $startDate = $current->addDays(0)->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
        ]);


        $response->assertStatus(422);

        $this->assertCount(1, $travel->tours()->get());

        $this->assertDatabaseHas(Tour::class, [
            'name' => $tour->name
        ]);


        $response = $this->putJson($endpoint, [
            'user_id' => $tour->user_id,
            'travel_id' => $tour->travel_id,
            'name' => $nameUpdated= $tour->name . ' Updated',
            'price' => 150,
            'start_date' =>  $startDate = $current->addDays(0)->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
        ]);


        $response->assertStatus(200);

        $this->assertCount(1, $travel->tours()->get());

        $this->assertDatabaseMissing(Tour::class, [
            'name' => $tour->name
        ]);

        $this->assertDatabaseHas(Tour::class, [
            'name' => $nameUpdated
        ]);

        $tour = $travel->tours()
                    ->where('name',$nameUpdated)
                    ->first();

        $response = $this->get(self::BASE_URL . '/admin/travels/' . $travel->id . '/tours');
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['name' => $tour->name]);
        $response->assertJsonFragment(['price' => number_format($tour->price,2)]);

    }

    public function test_admin_tour_api_authenticated_logged_in_admin_delete_tour_successfully_response_status_204(): void
    {
        /*
        php artisan test --filter=test_admin_tour_api_authenticated_logged_in_admin_delete_tour_successfully_response_status_204
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create([
            'is_public' => 1,
            'name' => 'Travel 1',
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => 'Travel 1 description',
        ]);

        $this->assertCount(1, Travel::all());

        $this->assertDatabaseHas(Travel::class, [
            'name' => $travel->name
        ]);

        $travel = Travel::query()
                    ->where('name',$travel->name)
                    ->first();


        $current = Carbon::now();

        $tour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'name' => 'Tour 1',
            'price' => 100,
            'start_date' =>  $startDate = $current->addDays(0)->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
        ]);

        $this->assertCount(1, $travel->tours()->get());

        $this->assertDatabaseHas(Tour::class, [
            'name' => $tour->name
        ]);

        $tour = $travel->tours()
                ->where('name',$tour->name)
                ->first();


        $response = $this->get(self::BASE_URL . '/admin/travels/' . $travel->id . '/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['name' => $tour->name]);


        $response = $this->deleteJson(self::BASE_URL . '/admin/travels/' . $travel->id . '/tours/' . $tour->id);
        $response->assertStatus(204);

        $this->assertCount(0, $travel->tours()->get());
        $this->assertDatabaseMissing(Tour::class, [
            'name' => $tour->name
        ]);

        $response = $this->get(self::BASE_URL . '/admin/travels/' . $travel->id . '/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');
        $response->assertJsonMissing(['name' => $tour->name]);

    }

}
