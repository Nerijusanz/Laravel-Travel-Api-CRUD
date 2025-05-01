<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    private string $baseUrl;
    private string $endpoint;
    private User $admin;
    private User $user;
    private string $itemsPerPage;

    public function setUp(): void
    {
        parent::setUp();

        $this->baseUrl = config('app.settings.api.api_base_url');
        $this->itemsPerPage = config('app.settings.pagination.default_items_per_page');
        $this->endpoint = $this->baseUrl . '/travels';
        $this->admin = User::adminRole();
        $this->user = User::userRole();

    }

    public function test_travels_returns_only_public_records()
    {
        /*
        php artisan test --filter=test_travels_returns_only_public_records
        */

        $this->actingAs($this->user);

        $travelPublic = Travel::factory(['is_public' => 1])->create();
        $travelNotPublic = Travel::factory(['is_public' => 0])->create();

        $this->assertCount(2, Travel::all());

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

        $this->actingAs($this->user);

        $itemsRecords = ($this->itemsPerPage + 1);
        $page=1;

        Travel::factory(['is_public' => 1])
                        ->count($itemsRecords)
                        ->create();

        $this->assertCount($itemsRecords, Travel::all());

        $travelOutPagination = Travel::latest('id')->first();

        $response = $this->getJson($this->endpoint);
        $response->assertStatus(200);
        $response->assertJsonCount($this->itemsPerPage, 'data');
        $response->assertJsonPath('meta.current_page', $page);
        $response->assertJsonPath('meta.last_page', 2);
        $response->assertJsonPath('meta.total', $itemsRecords);
        $response->assertJsonMissing(['data.*.id' => $travelOutPagination->id]);

        $response = $this->getJson($this->endpoint . '?page=' . $page);
        $response->assertStatus(200);
        $response->assertJsonCount($this->itemsPerPage, 'data');
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
