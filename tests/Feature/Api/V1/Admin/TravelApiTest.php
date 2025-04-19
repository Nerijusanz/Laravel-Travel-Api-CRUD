<?php

namespace Tests\Feature\Api\V1\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use Database\Seeders\tests\traits\DatabaseSeederTraitTest;
use App\Models\User;
use App\Models\Travel;

class TravelApiTest extends TestCase
{
    /*
    php artisan test --filter=TravelApiTest
    */

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

    public function test_admin_travel_api_unauthenticated_user_cannot_access_admin_travels_return_response_error_status_401(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_unauthenticated_user_cannot_access_admin_travels_return_response_error_status_401
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

    public function test_admin_travel_api_authenticated_user_cannot_access_admin_travels_return_response_error_status_403(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_user_cannot_access_admin_travels_return_response_error_status_403
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

    public function test_admin_travel_api_authenticated_admin_add_travel_invalid_data_return_validation_error_response_status_422(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_admin_add_travel_invalid_data_return_validation_error_response_status_422
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create();

        $endpoint = self::BASE_URL . '/admin/travels';

        $invalid = [];
        $invalid['is_public_format'] = ['is_public' => str()->random(3)];
        $invalid['name_to_short'] = ['name' => str()->random(1)];
        $invalid['name_to_long'] = ['name' => str()->random(256)];
        $invalid['name_unique'] = ['name' => $travel->name];
        $invalid['number_of_days_format'] = ['number_of_days' => str()->random(10)];
        $invalid['number_of_days_min_1'] = ['number_of_days' => mt_rand(-5,0)];
        $invalid['number_of_nights_format'] = ['number_of_nights' => str()->random(10)];
        $invalid['number_of_nights_min_0'] = ['number_of_nights' => mt_rand(-100,-1)];
        $invalid['number_of_nights_less_number_of_days'] = ['number_of_days' => $travel->number_of_days,'number_of_nights' => $travel->number_of_days];


        $response = $this->postJson($endpoint, []);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['is_public','name','number_of_days','number_of_nights'] ]);


        $response = $this->postJson($endpoint, $invalid['is_public_format']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['is_public'] ]);


        $response = $this->postJson($endpoint, $invalid['name_to_short']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name'] ]);


        $response = $this->postJson($endpoint, $invalid['name_to_long']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name'] ]);


        $response = $this->postJson($endpoint, $invalid['name_unique']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name'] ]);


        $response = $this->postJson($endpoint, $invalid['number_of_days_format']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['number_of_days'] ]);


        $response = $this->postJson($endpoint, $invalid['number_of_days_min_1']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['number_of_days'] ]);


        $response = $this->postJson($endpoint, $invalid['number_of_nights_format']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['number_of_nights'] ]);


        $response = $this->postJson($endpoint, $invalid['number_of_nights_min_0']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['number_of_nights'] ]);


        $response = $this->postJson($endpoint, $invalid['number_of_nights_less_number_of_days']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['number_of_nights'] ]);

    }

    public function test_admin_travel_api_authenticated_admin_update_travel_invalid_data_return_validation_error_response_status_422(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_admin_update_travel_invalid_data_return_validation_error_response_status_422
        */

        $this->actingAs($this->admin);

        $travelOne = Travel::factory()->create();
        $travel = Travel::factory()->create();

        $endpoint = self::BASE_URL . '/admin/travels/' . $travel->id;

        $invalid = [];
        $invalid['is_public_format'] = ['is_public' => str()->random(3)];
        $invalid['name_to_short'] = ['name' => str()->random(1)];
        $invalid['name_to_long'] = ['name' => str()->random(256)];
        $invalid['name_unique'] = ['name' => $travelOne->name];
        $invalid['number_of_days_format'] = ['number_of_days' => str()->random(10)];
        $invalid['number_of_days_min_1'] = ['number_of_days' => mt_rand(-5,0)];
        $invalid['number_of_nights_format'] = ['number_of_nights' => str()->random(10)];
        $invalid['number_of_nights_min_0'] = ['number_of_nights' => mt_rand(-100,-1)];
        $invalid['number_of_nights_less_number_of_days'] = ['number_of_days' => $travel->number_of_days,'number_of_nights' => $travel->number_of_days];


        $response = $this->putJson($endpoint, []);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['is_public','name','number_of_days','number_of_nights'] ]);


        $response = $this->putJson($endpoint, $invalid['is_public_format']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['is_public'] ]);


        $response = $this->putJson($endpoint, $invalid['name_to_short']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name'] ]);


        $response = $this->putJson($endpoint, $invalid['name_to_long']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name'] ]);


        $response = $this->putJson($endpoint, $invalid['name_unique']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['name'] ]);


        $response = $this->putJson($endpoint, $invalid['number_of_days_format']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['number_of_days'] ]);


        $response = $this->putJson($endpoint, $invalid['number_of_days_min_1']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['number_of_days'] ]);


        $response = $this->putJson($endpoint, $invalid['number_of_nights_format']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['number_of_nights'] ]);


        $response = $this->putJson($endpoint, $invalid['number_of_nights_min_0']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['number_of_nights'] ]);


        $response = $this->putJson($endpoint, $invalid['number_of_nights_less_number_of_days']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['number_of_nights'] ]);

    }

    public function test_admin_travel_api_authenticated_admin_add_travel_valid_data_return_response_status_201(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_admin_add_travel_valid_data_return_response_status_201
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create();

        $travelNew = [
            'is_public' => $travel->is_public,
            'name' => str()->random(10),
            'number_of_days' => $travel->number_of_days,
            'number_of_nights' => $travel->number_of_nights,
            'description' => $travel->description
        ];

        $endpoint = self::BASE_URL . '/admin/travels';

        $this->assertCount(1, Travel::all());

        $response = $this->postJson($endpoint,$travelNew);
        $response->assertStatus(201);

        $this->assertCount(2, Travel::all());

        $travelNew = Travel::query()->latest()->first();

        $response = $this->getJson($endpoint);
        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $travelNew->id]);

    }

    public function test_admin_travel_api_authenticated_admin_update_travel_valid_data_return_response_status_200(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_admin_update_travel_valid_data_return_response_status_200
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create();

        $travelUpdated = [
            'is_public' => $travel->is_public,
            'name' => str()->random(10),
            'number_of_days' => $travel->number_of_days,
            'number_of_nights' => $travel->number_of_nights,
            'description' => $travel->description
        ];

        $endpoint = self::BASE_URL . '/admin/travels/' . $travel->id;

        $response = $this->putJson($endpoint,$travelUpdated);
        $response->assertStatus(200);


        $response = $this->getJson($endpoint);
        $response->assertStatus(200);
        $response->assertJsonFragment($travelUpdated);

    }

    public function test_admin_travel_api_authenticated_admin_delete_travel_return_response_status_204(): void
    {
        /*
        php artisan test --filter=test_admin_travel_api_authenticated_admin_delete_travel_return_response_status_204
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create();

        $endpoint = self::BASE_URL . '/admin/travels/' . $travel->id;

        $this->assertCount(1,Travel::all());

        $response = $this->getJson($endpoint);
        $response->assertStatus(200);
        $response->assertJsonFragment(['id'=>$travel->id]);


        $response = $this->deleteJson($endpoint);
        $response->assertStatus(204);

        $this->assertCount(0,Travel::all());

        $response = $this->getJson($endpoint);
        $response->assertStatus(404);

    }

}
