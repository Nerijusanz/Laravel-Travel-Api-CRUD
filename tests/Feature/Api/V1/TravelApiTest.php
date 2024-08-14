<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Travel;

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

    public function test_travels_list_shows_only_public_records()
    {
        /*
        php artisan test --filter=test_travels_list_shows_only_public_records
        */

        //dd(self::BASE_URL);

        $this->actingAs($this->user);

        $publicTravel = Travel::factory()->create(['is_public' => true]);
        $notPublicTravel = Travel::factory()->create(['is_public' => false]);

        $this->assertCount(2,Travel::all());

        $this->assertDatabaseHas(Travel::class,[
            'name' => $publicTravel->name
        ]);

        $this->assertDatabaseHas(Travel::class,[
            'name' => $notPublicTravel->name
        ]);

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

        $this->actingAs($this->user);

        $travel = Travel::factory($itemsRecords)->create(['is_public' => true]);

        $this->assertCount($itemsRecords,Travel::all());

        $response = $this->get(self::BASE_URL . '/travels');

        $response->assertStatus(200);
        $response->assertJsonCount($itemsPagination, 'data');
        $response->assertJsonPath('meta.current_page', 1);
        $response->assertJsonPath('meta.last_page', 2);
    }

}
