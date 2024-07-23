<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Travel;

class TravelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_travels_list_shows_only_public_records()
    {
        $publicTravel = Travel::factory()->create(['is_public' => true]);
        $notPublicTravel = Travel::factory()->create(['is_public' => false]);

        $response = $this->get('/api/v1/travels');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $publicTravel->id]);
        $response->assertJsonMissing(['id' => $notPublicTravel->id]);

    }

    public function test_travels_list_returns_correct_pagination(): void
    {
        Travel::factory(16)->create(['is_public' => true]);

        $response = $this->get('/api/v1/travels');

        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonPath('meta.last_page', 2);
    }

}
