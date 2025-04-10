<?php

namespace Tests\Feature\Api\V1\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Travel;
use Database\Seeders\RolesTableSeeder;
use Database\Seeders\tests\traits\DatabaseSeederTraitTest;

class TravelApiTest extends TestCase
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


    public function test_admin_travel_api_unauthenticated_not_logged_in_public_user_cannot_access_admin_travels_return_unauthenticate_error_status_401(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_unauthenticated_not_logged_in_public_user_cannot_access_admin_travels_return_unauthenticate_error_status_401
        */
        $response = $this->getJson(self::BASE_URL . '/admin/travels');

        $response->assertStatus(401);

        $response = $this->getJson(self::BASE_URL . '/admin/travels/1');

        $response->assertStatus(401);

        $response = $this->postJson(self::BASE_URL . '/admin/travels');

        $response->assertStatus(401);

        $response = $this->putJson(self::BASE_URL . '/admin/travels/1');

        $response->assertStatus(401);

        $response = $this->deleteJson(self::BASE_URL . '/admin/travels/1');

        $response->assertStatus(401);
    }


    public function test_admin_travel_api_authenticated_logged_in_user_cannot_add_admin_travel_return_unauthorize_error_403(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_logged_in_user_cannot_add_admin_travel_return_unauthorize_error_403
        */

        $this->actingAs($this->user);

        $response = $this->getJson(self::BASE_URL . '/admin/travels');

        $response->assertStatus(403);

        $response = $this->getJson(self::BASE_URL . '/admin/travels/1');

        $response->assertStatus(403);

        $response = $this->postJson(self::BASE_URL . '/admin/travels');

        $response->assertStatus(403);

        $response = $this->putJson(self::BASE_URL . '/admin/travels/1');

        $response->assertStatus(403);

        $response = $this->deleteJson(self::BASE_URL . '/admin/travels/1');

        $response->assertStatus(403);

    }


    public function test_admin_travel_api_authenticated_admin_add_travel_validation_error_response_status_422(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_admin_add_travel_validation_error_response_status_422
        */

        $this->actingAs($this->admin);

        $response = $this->postJson(self::BASE_URL . '/admin/travels', [
            'is_public' => NULL,
            'name' => NULL,
            'number_of_days' => NULL,
            'number_of_nights' => NULL,
            'description' => NULL,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['is_public','name','number_of_days','number_of_nights'] ]);


        $response = $this->postJson(self::BASE_URL . '/admin/travels', [
            'is_public' => $isPublicNotBooleanValue='x',
            'name' => 'Travel',
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => NULL,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['is_public'] ]);


        $response = $this->postJson(self::BASE_URL . '/admin/travels', [
            'is_public' => 1,
            'name' => $toShortName='T',
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => NULL,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name'] ]);


        $response = $this->postJson(self::BASE_URL . '/admin/travels', [
            'is_public' => 1,
            'name' => $toLongName=str()->random(256),
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => NULL,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name'] ]);


        /****************** start name unique validation *****************/

        Travel::factory()->create([
            'is_public' => 1,
            'name' => $uniqueName='Travel 1',
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => NULL,
        ]);

        $response = $this->postJson(self::BASE_URL . '/admin/travels', [
            'is_public' => 1,
            'name' => $uniqueNameAlreadyTaken=$uniqueName,
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => NULL,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name'] ]);

        /********************end name unique validation ****************/


        $response = $this->postJson(self::BASE_URL . '/admin/travels', [
            'is_public' => 1,
            'name' => 'Travel',
            'number_of_days' => $numberOfDaysIncorrectValue=0,
            'number_of_nights' => 0,
            'description' => NULL,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['number_of_days'] ]);


        $response = $this->postJson(self::BASE_URL . '/admin/travels', [
            'is_public' => 1,
            'name' => 'Travel',
            'number_of_days' => 1,
            'number_of_nights' => $numberOfNightsIncorrectValue=1,
            'description' => NULL,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['number_of_nights'] ]);

    }


    public function test_admin_travel_api_authenticated_logged_in_admin_add_travel_successfully_with_valid_data(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_logged_in_admin_add_travel_successfully_with_valid_data
        */

        $this->actingAs($this->admin);

        $endpoint = self::BASE_URL . '/admin/travels';

        $response = $this->postJson($endpoint, [
            'is_public' => 1,
            'name' => $name='Travel 1',
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => NULL,
        ]);

        $response->assertStatus(201);

        $this->assertCount(1, Travel::all());

        $this->assertDatabaseHas(Travel::class, [
            'name' => $name
        ]);

        $travel = Travel::query()
                    ->where('name',$name)
                    ->first();

        $response = $this->getJson($endpoint);

        $response->assertJsonFragment(['name' => $travel->name]);

    }


    public function test_admin_travel_api_authenticated_admin_update_travel_validation_error_response_status_422(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_admin_update_travel_validation_error_response_status_422
        */

        $this->actingAs($this->admin);


        $endpoint = self::BASE_URL . '/admin/travels/1';

        $response = $this->putJson($endpoint, [
            'is_public' => NULL,
            'name' => NULL,
            'number_of_days' => NULL,
            'number_of_nights' => NULL,
            'description' => NULL,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['is_public','name','number_of_days','number_of_nights'] ]);


        $response = $this->putJson($endpoint, [
            'is_public' => $isPublicNotBooleanValue='x',
            'name' => 'Travel',
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => NULL,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['is_public'] ]);


        $response = $this->putJson($endpoint, [
            'is_public' => 1,
            'name' => $toShortName='T',
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => NULL,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name'] ]);


        $response = $this->putJson($endpoint, [
            'is_public' => 1,
            'name' => $toLongName=str()->random(256),
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => NULL,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name'] ]);


        /****************** start name unique validation *****************/


        $travelOne = Travel::factory()->create([
            'is_public' => 1,
            'name' => 'Travel 1',
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => NULL,
        ]);

        $travelTwo = Travel::factory()->create([
            'is_public' => 1,
            'name' => 'Travel 2',
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => NULL,
        ]);


        $response = $this->putJson(self::BASE_URL . '/admin/travels/' . $travelOne->id, [
            'is_public' => 1,
            'name' => $uniqueNameAlreadyTaken=$travelTwo->name,
            'number_of_days' => 1,
            'number_of_nights' => 0,
            'description' => NULL,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name'] ]);

        /********************end name unique validation ****************/


        $response = $this->putJson($endpoint, [
            'is_public' => 1,
            'name' => 'Travel',
            'number_of_days' => $numberOfDaysIncorrectValue=0,
            'number_of_nights' => 0,
            'description' => NULL,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['number_of_days'] ]);


        $response = $this->putJson($endpoint, [
            'is_public' => 1,
            'name' => 'Travel',
            'number_of_days' => 1,
            'number_of_nights' => $numberOfNightsIncorrectValue=1,
            'description' => NULL,
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['number_of_nights'] ]);

    }


    public function test_admin_travel_api_authenticated_logged_in_admin_update_travel_successfully_with_valid_data(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_logged_in_admin_update_travel_successfully_with_valid_data
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create([
            'is_public' => $isPublic=0,
            'name' => $name='Travel 1',
            'number_of_days' => $days=1,
            'number_of_nights' => $nights=0,
            'description' => NULL,
        ]);

        $this->assertDatabaseHas(Travel::class, [
            'is_public' => $isPublic,
            'name' => $name,
            'number_of_days' => $days,
            'number_of_nights' => $nights
        ]);

        $endpoint = self::BASE_URL . '/admin/travels/' . $travel->id;


        $response = $this->putJson($endpoint, [
            'is_public' => $isPublicUpdated=1,
            'name' => $nameUpdated = 'Travel 2',
            'number_of_days' => $daysUpdated=2,
            'number_of_nights' => $nightsUpdated=1,
            'description' => NULL
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseMissing(Travel::class, [
            'name' => $name
        ]);

        $this->assertDatabaseHas(Travel::class, [
            'is_public' => $isPublicUpdated,
            'name' => $nameUpdated,
            'number_of_days' => $daysUpdated,
            'number_of_nights' => $nightsUpdated
        ]);

        $travelUpdated = Travel::query()
                    ->where('name',$nameUpdated)
                    ->first();

        $endpoint = self::BASE_URL . '/admin/travels/' . $travelUpdated->id;

        $response = $this->getJson($endpoint);

        $response->assertJsonFragment(['is_public' => $isPublicUpdated]);
        $response->assertJsonFragment(['name' => $nameUpdated]);
        $response->assertJsonFragment(['number_of_days' => $daysUpdated]);
        $response->assertJsonFragment(['number_of_nights' => $nightsUpdated]);

    }

    public function test_admin_travel_api_authenticated_logged_in_admin_delete_travel_successfully_response_status_204(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_logged_in_admin_delete_travel_successfully_response_status_204
        */

        $this->actingAs($this->admin);


        /****************ADD TRAVEL ***************/

        $endpoint = self::BASE_URL . '/admin/travels';

        $name = 'Travel 1';

        $response = $this->postJson($endpoint, [
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


        $response = $this->getJson($endpoint);
        $response->assertJsonFragment(['name' => $travel->name]);
        $response->assertJsonFragment(['slug' => $travel->slug]);


        /***************** DELETE TRAVEL ***************/


        $this->assertCount(1, Travel::all());
        $this->assertDatabaseHas(Travel::class, [
            'name' => $travel->name
        ]);

        $response = $this->deleteJson(self::BASE_URL . '/admin/travels/' . $travel->id);
        $response->assertStatus(204);

        $this->assertCount(0, Travel::all());
        $this->assertDatabaseMissing(Travel::class, [
            'name' => $travel->name
        ]);

        $response = $this->get(self::BASE_URL . '/admin/travels');

        $response->assertStatus(200);
        $response->assertJsonMissing(['name' => $travel->name]);
        $response->assertJsonMissing(['slug' => $travel->slug ]);

    }

}
