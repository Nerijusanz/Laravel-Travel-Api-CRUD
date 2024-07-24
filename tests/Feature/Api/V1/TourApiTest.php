<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Travel;
use App\Models\Tour;

class TourApiTest extends TestCase
{

    use RefreshDatabase;


    public function test_tours_by_travel_id_returns_correct_tour(): void
    {
        $user = User::factory()->create();
        $travel = Travel::factory()->create(['user_id' => $user->id,'is_public' => true]);
        $tour = Tour::factory()->create(['user_id' => $user->id,'travel_id' => $travel->id]);

        $response = $this->get('/api/v1/travels/'. $travel->id .'/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tour->id]);
    }


    public function test_tours_by_travel_id_returns_correct_pagination(): void
    {
        $toursPagination = 15;

        $user = User::factory()->create();
        $travel = Travel::factory()->create(['user_id' => $user->id,'is_public' => true]);
        $tour = Tour::factory( $toursPagination + 1 )->create(['user_id' => $user->id,'travel_id' => $travel->id]);

        $response = $this->get('/api/v1/travels/'. $travel->id .'/tours');

        $response->assertStatus(200);
        $response->assertJsonCount($toursPagination, 'data');
        $response->assertJsonPath('meta.current_page', 1);
        $response->assertJsonPath('meta.last_page', 2);

    }


    public function test_tour_price_is_correctly_formatted(): void
    {

        $user = User::factory()->create();
        $travel = Travel::factory()->create(['user_id' => $user->id,'is_public' => true]);
        $tour = Tour::factory()->create([
                    'user_id' => $user->id,
                    'travel_id' => $travel->id,
                    'price'=>99.99]);

        $response = $this->get('/api/v1/travels/'. $travel->id .'/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['price' => '99.99']);

    }

}
