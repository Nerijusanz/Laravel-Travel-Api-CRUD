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

    public function test_travels_list_shows_only_public_records()
    {
        /*
        php artisan test --filter=test_travels_list_shows_only_public_records
        */
        $this->actingAs($this->admin);

        $publicTravel = Travel::factory()->create(['is_public' => 1]);
        $notPublicTravel = Travel::factory()->create(['is_public' => 0]);

        $this->assertCount(2,Travel::all());

        $this->assertDatabaseHas(Travel::class,[
            'name' => $publicTravel->name
        ]);

        $this->assertDatabaseHas(Travel::class,[
            'name' => $notPublicTravel->name
        ]);

        $this->actingAs($this->user);

        $response = $this->get(self::BASE_URL . '/travels');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $publicTravel->id]);
        $response->assertJsonMissing(['id' => $notPublicTravel->id]);

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
