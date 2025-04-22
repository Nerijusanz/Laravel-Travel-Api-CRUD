<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Travel;
use App\Models\Tour;
use App\Services\Api\V1\TourApiService;
use Database\Seeders\tests\traits\DatabaseSeederTraitTest;

class TourApiTest extends TestCase
{

    use RefreshDatabase;
    use DatabaseSeederTraitTest;

    private $admin;
    public const BASE_URL = '/api';

    /*******TEST CLASS*******
        php artisan test --filter=TourApiTest
    ************************/

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::adminRole();

    }


    public function test_tours_by_travel_id_returns_correct_tour(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_returns_correct_tour
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create(['is_public' => 1]);

        $tour = Tour::factory(['travel_id' => $travel->id])->create();

        $this->assertCount(1, $travel->tours()->get());

        $tour = $travel->tours()->first();

        $endpoint = self::BASE_URL . '/travels/'. $travel->id .'/tours';


        $response = $this->getJson($endpoint);
        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $tour->id]);

    }

    public function test_tours_by_travel_id_returns_correct_pagination(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_returns_correct_pagination
        */

        $this->actingAs($this->admin);

        $itemsPagination15 = 15;
        $itemsRecords16 = 16;

        $travel = Travel::factory(['is_public' => 1])->create();

        Tour::factory(['travel_id' => $travel->id])->count($itemsRecords16)->create();

        $this->assertCount($itemsRecords16, $travel->tours()->get());

        $endpoint = self::BASE_URL . '/travels/'. $travel->id .'/tours';

        $response = $this->getJson($endpoint);
        $response->assertStatus(200);
        $response->assertJsonCount($itemsPagination15, 'data');
        $response->assertJsonPath('meta.current_page', 1);
        $response->assertJsonPath('meta.last_page', 2);

    }

    public function test_tour_price_is_correctly_formatted(): void
    {
        /*
        php artisan test --filter=test_tour_price_is_correctly_formatted
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create(['is_public' => 1]);

        $tour = Tour::factory([
                        'travel_id' => $travel->id,
                        'price'=> 100
                        ])->create();

        $this->assertCount(1, $travel->tours()->get());

        $tour = $travel->tours()->first();

        $endpoint = self::BASE_URL . '/travels/'. $travel->id .'/tours';

        $response = $this->getJson($endpoint);
        $response->assertStatus(200);
        $response->assertJsonFragment(['price' => $tour->price]);

    }

    public function test_tours_by_travel_id_sorts_by_starting_date_correctly(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_sorts_by_starting_date_correctly
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory(['is_public' => true])->create();

        $tourLater = Tour::factory(['travel_id' => $travel->id])->create();

        $tourEarlier = Tour::factory([
                                'travel_id' => $travel->id,
                                'start_date' => $startDate = Carbon::parse($tourLater->start_date)->subDays(1)->startOfDay()->toDateTimeString(),
                                'end_date' => Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
                                ])->create();

        $this->assertCount(2, $travel->tours()->get());

        $endpoint = self::BASE_URL . '/travels/'. $travel->id .'/tours';

        $response = $this->getJson($endpoint);
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $tourEarlier->id);
        $response->assertJsonPath('data.1.id', $tourLater->id);

    }

    public function test_tours_by_travel_id_filter_by_price_min_to_max_by_start_date_asc(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_filter_by_price_min_to_max_by_start_date_asc
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create(['is_public' => true]);

        $tourCheapLater = Tour::factory(['travel_id' => $travel->id])->create();

        $tourCheapEarlier = Tour::factory([
            'travel_id' => $travel->id,
            'price' => $tourCheapLater->price,
            'start_date' => $startDate = Carbon::parse($tourCheapLater->start_date)->subDays(1)->startOfDay()->toDateTimeString(),
            'end_date' => Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
            ])->create();

        $tourExpensive = Tour::factory([
                                'travel_id' => $travel->id,
                                'price' => ($tourCheapLater->price + 1)
                                ])->create();

        $this->assertCount(3, $travel->tours()->get());

        $endpoint = self::BASE_URL . '/travels/'. $travel->id .'/tours?sort_by=price&order=asc';

        $response = $this->getJson($endpoint);
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $tourCheapEarlier->id);
        $response->assertJsonPath('data.1.id', $tourCheapLater->id);
        $response->assertJsonPath('data.2.id', $tourExpensive->id);

    }

    public function test_tours_by_travel_id_sorts_by_price_and_order_desc_and_sort_by_start_date_correctly(): void
    {

        /*
        php artisan test --filter=test_tours_by_travel_id_sorts_by_price_and_order_desc_and_sort_by_start_date_correctly
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create(['is_public' => true]);

        $this->assertCount(1, Travel::all());

        $this->assertDatabaseHas(Travel::class, [
            'name' => $travel->name
        ]);

        $travel = Travel::query()
                    ->where('name',$travel->name)
                    ->first();


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


        $this->assertCount(3, $travel->tours()->get());

        $this->assertDatabaseHas(Tour::class, [
            'name' => $expensiveLaterTour->name
        ]);

        $this->assertDatabaseHas(Tour::class, [
            'name' => $expensiveEarlierTour ->name
        ]);

        $this->assertDatabaseHas(Tour::class, [
            'name' => $cheapTour->name
        ]);

        $response = $this->get(self::BASE_URL . '/travels/'. $travel->id .'/tours?sort_by=price&order=desc');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
        $response->assertJsonPath('data.0.id', $expensiveEarlierTour->id);
        $response->assertJsonPath('data.1.id', $expensiveLaterTour->id);
        $response->assertJsonPath('data.2.id', $cheapTour->id);
    }

    public function test_tours_by_travel_id_sort_by_price_ranges_correctly(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_sort_by_price_ranges_correctly
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create(['is_public' => true]);

        $this->assertCount(1, Travel::all());

        $this->assertDatabaseHas(Travel::class, [
            'name' => $travel->name
        ]);

        $travel = Travel::query()
                    ->where('name',$travel->name)
                    ->first();


        $expensiveTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 200,
        ]);

        $cheapTour = Tour::factory()->create([
            'travel_id' => $travel->id,
            'price' => 100,
        ]);

        $this->assertCount(2, $travel->tours()->get());

        $this->assertDatabaseHas(Tour::class, [
            'name' => $expensiveTour->name
        ]);

        $this->assertDatabaseHas(Tour::class, [
            'name' => $cheapTour->name
        ]);


        $endpoint = self::BASE_URL . '/travels/'. $travel->id .'/tours';

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

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create(['is_public' => true]);


        $this->assertCount(1, Travel::all());

        $this->assertDatabaseHas(Travel::class, [
            'name' => $travel->name
        ]);

        $travel = Travel::query()
                    ->where('name',$travel->name)
                    ->first();


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

        $this->assertCount(2, $travel->tours()->get());

        $this->assertDatabaseHas(Tour::class, [
            'name' => $laterTour->name
        ]);

        $this->assertDatabaseHas(Tour::class, [
            'name' => $earlierTour->name
        ]);

        $endpoint = self::BASE_URL . '/travels/' . $travel->id . '/tours';


        $startDate = Carbon::parse($current->copy())->addDays(0)->startOfDay()->toDateTimeString();
        $response = $this->get($endpoint . '?start_date=' . $startDate);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $earlierTour->id]);
        $response->assertJsonFragment(['id' => $laterTour->id]);


        $startDate = Carbon::parse($current->copy())->addDays(1)->startOfDay()->toDateTimeString();
        $response = $this->get($endpoint . '?start_date=' . $startDate);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $laterTour->id]);
        $response->assertJsonMissing(['id' => $earlierTour->id]);


        $startDate = Carbon::parse($current->copy())->addDays(5)->startOfDay()->toDateTimeString();
        $response = $this->get($endpoint . '?start_date=' . $startDate);
        $response->assertJsonCount(0, 'data');


        $endDate = Carbon::parse($current->copy())->addDays(5)->endOfDay()->toDateTimeString();
        $response = $this->get($endpoint . '?end_date=' . $endDate);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $earlierTour->id]);
        $response->assertJsonFragment(['id' => $laterTour->id]);


        $endDate = Carbon::parse($current->copy())->addDays(1)->endOfDay()->toDateTimeString();
        $response = $this->get($endpoint . '?end_date=' . $endDate);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $earlierTour->id]);
        $response->assertJsonMissing(['id' => $laterTour->id]);


        $endDate = Carbon::parse($current->copy())->subDays(1)->endOfDay()->toDateTimeString();
        $response = $this->get($endpoint . '?end_date=' . $endDate);
        $response->assertJsonCount(0, 'data');


        $startDate = Carbon::parse($current->copy())->addDays(1)->startOfDay()->toDateTimeString();
        $endDate = Carbon::parse($startDate)->addDays(5)->endOfDay()->toDateTimeString();
        $response = $this->get($endpoint . '?start_date=' . $startDate .'&end_date=' . $endDate);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $laterTour->id]);
        $response->assertJsonMissing(['id' => $earlierTour->id]);

    }

    public function test_tour_by_travel_id_returns_validation_error_status_code_422(): void
    {

        /*
        php artisan test --filter=test_tour_by_travel_id_returns_validation_error_status_code_422
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create(['is_public' => true]);

        $this->assertCount(1, Travel::all());

        $this->assertDatabaseHas(Travel::class, [
            'name' => $travel->name
        ]);

        $travel = Travel::query()
                    ->where('name',$travel->name)
                    ->first();


        $endpoint = self::BASE_URL . '/travels/' . $travel->id . '/tours';

        $response = $this->getJson($endpoint . '?start_date=xxxx-xx-xx');
        $response->assertStatus(422);

        $response = $this->getJson($endpoint . '?price_from=abcde');
        $response->assertStatus(422);
    }

}
