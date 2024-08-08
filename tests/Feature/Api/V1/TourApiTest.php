<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Travel;
use App\Models\Tour;

class TourApiTest extends TestCase
{

    use RefreshDatabase;

    private $user;


    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

    }


    public function test_tours_by_travel_id_returns_correct_tour(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_returns_correct_tour
        */

        $this->actingAs($this->user);

        $travel = Travel::factory()->create(['is_public' => true]);

        $tour = Tour::factory()->create(['travel_id' => $travel->id]);


        $response = $this->get('/api/v1/travels/'. $travel->id .'/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tour->id]);

    }

    public function test_tours_by_travel_id_returns_correct_pagination(): void
    {

        /*
        php artisan test --filter=test_tours_by_travel_id_returns_correct_pagination
        */

        $itemsPagination = 15;

        $this->actingAs($this->user);

        $travel = Travel::factory()->create(['is_public' => true]);
        $tour = Tour::factory( $itemsPagination + 1 )->create(['travel_id' => $travel->id]);


        $response = $this->get('/api/v1/travels/'. $travel->id .'/tours');

        $response->assertStatus(200);
        $response->assertJsonCount($itemsPagination, 'data');
        $response->assertJsonPath('meta.current_page', 1);
        $response->assertJsonPath('meta.last_page', 2);
    }

    public function test_tour_price_is_correctly_formatted(): void
    {

        /*
        php artisan test --filter=test_tour_price_is_correctly_formatted
        */

        $this->actingAs($this->user);

        $travel = Travel::factory()->create(['is_public' => true]);
        $tour = Tour::factory()->create([
                    'travel_id' => $travel->id,
                    'price'=> $price='99.99',
                ]);

        $response = $this->get('/api/v1/travels/'. $travel->id .'/tours');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['price' => number_format($price,2)]);
    }

    public function test_tours_by_travel_id_sorts_by_starting_date_correctly(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_sorts_by_starting_date_correctly
        */

        $this->actingAs($this->user);

        $travel = Travel::factory()->create(['is_public' => true]);

        $current = Carbon::now();

        $laterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'start_date' =>  $startDate = Carbon::parse($current->copy())->addDays(1)->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
        ]);

        $earlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'start_date' =>  $startDate = Carbon::parse($current->copy())->addDays(0)->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
        ]);

        $response = $this->get('/api/v1/travels/'. $travel->id .'/tours');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $earlierTour->id);
        $response->assertJsonPath('data.1.id', $laterTour->id);
    }

    public function test_tours_by_travel_id_sorts_by_price_and_order_asc_and_sort_by_start_date_correctly(): void
    {

        /*
        php artisan test --filter=test_tours_by_travel_id_sorts_by_price_and_order_asc_and_sort_by_start_date_correctly
        */

        $this->actingAs($this->user);

        $travel = Travel::factory()->create(['is_public' => true]);

        $current = Carbon::now();

        $cheapLaterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
            'start_date' =>  $startDate = Carbon::parse($current->copy())->addDays(1)->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
        ]);

        $cheapEarlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
            'start_date' =>  $startDate = Carbon::parse($current->copy())->addDays(0)->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
        ]);

        $expensiveTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 500,
        ]);


        $response = $this->get('/api/v1/travels/'. $travel->id .'/tours?sort_by=price&order=asc');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $cheapEarlierTour->id);
        $response->assertJsonPath('data.1.id', $cheapLaterTour->id);
        $response->assertJsonPath('data.2.id', $expensiveTour->id);
    }

    public function test_tours_by_travel_id_sorts_by_price_and_order_desc_and_sort_by_start_date_correctly(): void
    {

        /*
        php artisan test --filter=test_tours_by_travel_id_sorts_by_price_and_order_desc_and_sort_by_start_date_correctly
        */

        $this->actingAs($this->user);

        $travel = Travel::factory()->create(['is_public' => true]);

        $current = Carbon::now();

        $expensiveLaterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 500,
            'start_date' =>  $startDate = Carbon::parse($current->copy())->addDays(1)->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
        ]);

        $expensiveEarlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 500,
            'start_date' =>  $startDate = Carbon::parse($current->copy())->addDays(0)->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
        ]);

        $cheapTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 200,
        ]);

        $response = $this->get('/api/v1/travels/'. $travel->id .'/tours?sort_by=price&order=desc');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $expensiveEarlierTour->id);
        $response->assertJsonPath('data.1.id', $expensiveLaterTour->id);
        $response->assertJsonPath('data.2.id', $cheapTour->id);
    }

    public function test_tours_by_travel_id_sort_by_price_ranges_correctly(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_sort_by_price_ranges_correctly
        */

        $this->actingAs($this->user);

        $travel = Travel::factory()->create(['is_public' => true]);

        $expensiveTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 200,
        ]);

        $cheapTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
        ]);

        $endpoint = '/api/v1/travels/'. $travel->id .'/tours';

        $response = $this->get($endpoint . '?price_from=100');
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

        $response = $this->get($endpoint . '?price_from=150');
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $expensiveTour->id]);
        $response->assertJsonMissing(['id' => $cheapTour->id]);

        $response = $this->get($endpoint . '?price_from=250');
        $response->assertJsonCount(0, 'data');

        $response = $this->get($endpoint . '?price_to=200');
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

        $response = $this->get($endpoint . '?price_to=150');
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $cheapTour->id]);
        $response->assertJsonMissing(['id' => $expensiveTour->id]);

        $response = $this->get($endpoint . '?price_to=50');
        $response->assertJsonCount(0, 'data');

        $response = $this->get($endpoint . '?price_from=150&price_to=250');
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $expensiveTour->id]);
        $response->assertJsonMissing(['id' => $cheapTour->id]);
    }

    public function test_tours_by_travel_id_and_sort_by_starting_date_correctly(): void
    {

        /*
        php artisan test --filter=test_tours_by_travel_id_and_sort_by_starting_date_correctly
        */

        $this->actingAs($this->user);

        $travel = Travel::factory()->create(['is_public' => true]);

        $current = Carbon::now();

        $laterTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'start_date' =>  $startDate = Carbon::parse($current->copy())->addDays(2)->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
        ]);

        $earlierTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'start_date' =>  $startDate = Carbon::parse($current->copy())->addDays(0)->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(1)->endOfDay()->toDateTimeString(),
        ]);

        $endpoint = '/api/v1/travels/' . $travel->id . '/tours';


        $startDate = Carbon::parse($current->copy())->addDays(0)->startOfDay()->toDateTimeString();
        $response = $this->get($endpoint . '?date_from=' . $startDate);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $earlierTour->id]);
        $response->assertJsonFragment(['id' => $laterTour->id]);


        $startDate = Carbon::parse($current->copy())->addDays(1)->startOfDay()->toDateTimeString();
        $response = $this->get($endpoint . '?date_from=' . $startDate);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $laterTour->id]);
        $response->assertJsonMissing(['id' => $earlierTour->id]);


        $startDate = Carbon::parse($current->copy())->addDays(5)->startOfDay()->toDateTimeString();
        $response = $this->get($endpoint . '?date_from=' . $startDate);
        $response->assertJsonCount(0, 'data');


        $endDate = Carbon::parse($current->copy())->addDays(5)->endOfDay()->toDateTimeString();
        $response = $this->get($endpoint . '?date_to=' . $endDate);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $earlierTour->id]);
        $response->assertJsonFragment(['id' => $laterTour->id]);


        $endDate = Carbon::parse($current->copy())->addDays(1)->endOfDay()->toDateTimeString();
        $response = $this->get($endpoint . '?date_to=' . $endDate);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $earlierTour->id]);
        $response->assertJsonMissing(['id' => $laterTour->id]);


        $endDate = Carbon::parse($current->copy())->subDays(1)->endOfDay()->toDateTimeString();
        $response = $this->get($endpoint . '?date_to=' . $endDate);
        $response->assertJsonCount(0, 'data');


        $startDate = Carbon::parse($current->copy())->addDays(1)->startOfDay()->toDateTimeString();
        $endDate = Carbon::parse($startDate)->addDays(5)->endOfDay()->toDateTimeString();
        $response = $this->get($endpoint . '?date_from=' . $startDate .'&date_to=' . $endDate);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $laterTour->id]);
        $response->assertJsonMissing(['id' => $earlierTour->id]);

    }

    public function test_tour_by_travel_id_returns_validation_error_status_code_422(): void
    {

        /*
        php artisan test --filter=test_tour_by_travel_id_returns_validation_error_status_code_422
        */

        $this->actingAs($this->user);

        $travel = Travel::factory()->create(['is_public' => true]);

        $response = $this->getJson('/api/v1/travels/' . $travel->id . '/tours?date_from=abcde');
        $response->assertStatus(422);

        $response = $this->getJson('/api/v1/travels/' . $travel->id . '/tours?price_from=abcde');
        $response->assertStatus(422);
    }

}
