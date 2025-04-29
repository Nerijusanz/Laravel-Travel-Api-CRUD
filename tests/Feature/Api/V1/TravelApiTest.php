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

    private User $admin;
    private User $user;
    public const BASE_URL = '/api';
    private string $endpoint;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::adminRole();
        $this->user = User::userRole();
        $this->endpoint = self::BASE_URL . '/travels';
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

        $travelPublic = Travel::findOrFail($travelPublic->id);
        $travelNotPublic = Travel::findOrFail($travelNotPublic->id);

        $response = $this->getJson($this->endpoint);
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $travelPublic->id]);
        $response->assertJsonMissing(['id' => $travelNotPublic->id]);

    }

    public function test_travels_returns_correct_pagination(): void
    {
        /*
        php artisan test --filter=test_travels_returns_correct_pagination
        */

        $itemsPerPage = config('app.settings.pagination.default_items_per_page');
        $itemsRecords = ($itemsPerPage + 1);
        $page=1;

        $this->actingAs($this->admin);

        Travel::factory(['is_public' => 1])
                        ->count($itemsRecords)
                        ->create();

        $this->assertCount($itemsRecords, Travel::all());

        $this->actingAs($this->user);

        $travelOutPagination = Travel::all()->last();


        $response = $this->getJson($this->endpoint);
        $response->assertStatus(200);
        $response->assertJsonCount($itemsPerPage, 'data');
        $response->assertJsonPath('meta.current_page', $page);
        $response->assertJsonPath('meta.last_page', 2);
        $response->assertJsonPath('meta.total', $itemsRecords);
        $response->assertJsonMissing(['data.*.id' => $travelOutPagination->id]);

        $response = $this->getJson($this->endpoint . '?page=' . $page);
        $response->assertStatus(200);
        $response->assertJsonCount($itemsPerPage, 'data');
        $response->assertJsonPath('meta.current_page', $page);
        $response->assertJsonPath('meta.last_page', 2);
        $response->assertJsonPath('meta.total', $itemsRecords);
        $response->assertJsonMissing(['data.*.id' => $travelOutPagination->id]);

        $response = $this->getJson($this->endpoint . '?page=' . $page2 = ($page + 1) );
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('meta.current_page', $page2);
        $response->assertJsonPath('meta.last_page', 2);
        $response->assertJsonPath('meta.total', $itemsRecords);
        $response->assertJsonPath('data.0.id', $travelOutPagination->id);

    }

}
