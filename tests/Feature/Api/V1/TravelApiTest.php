<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Travel;
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

    public function test_travels_returns_only_public_records()
    {
        /*
        php artisan test --filter=test_travels_returns_only_public_records
        */

        $this->actingAs($this->admin);

        $travelPublic = Travel::factory(['is_public' => 1])->create();
        $travelNotPublic = Travel::factory(['is_public' => 0])->create();

        $this->assertCount(2, Travel::all());

        $this->actingAs($this->user);

        $endpoint = self::BASE_URL . '/travels';

        $travelPublic = Travel::findOrFail($travelPublic->id);
        $travelNotPublic = Travel::findOrFail($travelNotPublic->id);

        $response = $this->getJson($endpoint);
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $travelPublic->id]);
        $response->assertJsonMissing(['id' => $travelNotPublic->id]);

    }

    public function test_travels_list_returns_correct_pagination(): void
    {
        /*
        php artisan test --filter=test_travels_list_returns_correct_pagination
        */

        $itemsPagination=15;
        $itemsRecords=$itemsPagination + 1;

        $this->actingAs($this->admin);

        $travel = Travel::factory($itemsRecords)->create(['is_public' => 1]);

        $this->assertCount($itemsRecords,Travel::all());

        $this->actingAs($this->user);

        $response = $this->get(self::BASE_URL . '/travels');

        $response->assertStatus(200);
        $response->assertJsonCount($itemsPagination, 'data');
        $response->assertJsonPath('meta.current_page', 1);
        $response->assertJsonPath('meta.last_page', 2);
    }

}
