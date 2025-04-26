<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use Database\Seeders\tests\traits\DatabaseSeederTraitTest;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Travel;
use App\Models\Tour;

class TourApiTest extends TestCase
{
    /*
    php artisan test --filter=TourApiTest
    */

    use RefreshDatabase;
    use DatabaseSeederTraitTest;

    private $admin;
    public const BASE_URL = '/api';


    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::adminRole();

    }

    public function test_tours_by_travel_id_request_filter_tours_invalid_data_return_validation_errors_response_status_422(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_request_filter_tours_invalid_data_return_validation_errors_response_status_422
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create(['is_public' => 1]);

        $tour = Tour::factory(['travel_id' => $travel->id])->create();

        $this->assertCount(1, $travel->tours()->get());

        $endpoint = self::BASE_URL . '/travels/' . $travel->id . '/tours';

        $invalid = [];
        $invalid['price_from']['numeric'] = str()->random(6);
        $invalid['price_from']['min:0'] = mt_rand(-100,-1);

        $invalid['price_to']['numeric'] = str()->random(6);
        $invalid['price_to']['min:0'] = mt_rand(-100,-1);
        $invalid['price_to']['gte:price_from'] = [
                                            $priceFrom = $tour->price_from,
                                            $priceTo = ($priceFrom - 1),
                                            'price_from' => $priceFrom,
                                            'price_to' => $priceTo
                                            ];

        $invalid['start_date']['date'] = str()->random(10);

        $invalid['end_date']['date'] = str()->random(10);
        $invalid['end_date']['after_or_equal:start_date'] = [
                                            $startDate = $tour->start_date,
                                            $endDate = Carbon::parse($startDate)->subDays(1)->endOfDay()->toDateTimeString(),
                                            'start_date' => $startDate, 'end_date' => $endDate ];

        $invalid['sort_by']['rule::in'] = str()->random(10);

        $invalid['order']['rule::in'] = str()->random(5);


        $response = $this->getJson($endpoint . '?price_from=' . $invalid['price_from']['numeric']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['price_from'] ]);

        $response = $this->getJson($endpoint . '?price_from=' . $invalid['price_from']['min:0']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['price_from'] ]);

        $response = $this->getJson($endpoint . '?price_to=' . $invalid['price_to']['numeric']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['price_to'] ]);

        $response = $this->getJson($endpoint . '?price_to=' . $invalid['price_to']['min:0']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['price_to'] ]);

        $response = $this->getJson($endpoint . '?price_from=' . $invalid['price_to']['gte:price_from']['price_from'] . '&price_to=' . $invalid['price_to']['gte:price_from']['price_to']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['price_to'] ]);

        $response = $this->getJson($endpoint . '?start_date=' . $invalid['start_date']['date']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['start_date'] ]);

        $response = $this->getJson($endpoint . '?end_date=' . $invalid['end_date']['date']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['end_date'] ]);

        $response = $this->getJson($endpoint . '?start_date=' . $invalid['end_date']['after_or_equal:start_date']['start_date'] . '&end_date=' . $invalid['end_date']['after_or_equal:start_date']['end_date']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['end_date'] ]);

        $response = $this->getJson($endpoint . '?sort_by=' . $invalid['sort_by']['rule::in']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['sort_by'] ]);

        $response = $this->getJson($endpoint . '?order=' . $invalid['order']['rule::in']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['order'] ]);

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

    public function test_tours_by_travel_id_returns_tour_price_correct_formatted(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_returns_tour_price_correct_formatted
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create(['is_public' => 1]);

        $tour = Tour::factory(['travel_id' => $travel->id])->create();

        $this->assertCount(1, $travel->tours()->get());

        $tour = $travel->tours()->first();

        $endpoint = self::BASE_URL . '/travels/'. $travel->id .'/tours';

        $response = $this->getJson($endpoint);
        $response->assertStatus(200);
        $response->assertJsonFragment(['price' => $tour->price]);

    }

    public function test_tours_by_travel_id_returns_tours_by_starting_date(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_returns_tours_by_starting_date
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory(['is_public' => 1])->create();

        $tourLater = Tour::factory(['travel_id' => $travel->id])->create();

        $tourEarlier = Tour::factory([
                                'travel_id' => $travel->id,
                                'start_date' => $startDate = Carbon::parse($tourLater->start_date)->subDays(mt_rand(1,10))->startOfDay()->toDateTimeString(),
                                'end_date' => Carbon::parse($startDate)->addDays(mt_rand(0,10))->endOfDay()->toDateTimeString(),
                                ])->create();

        $this->assertCount(2, $travel->tours()->get());

        $endpoint = self::BASE_URL . '/travels/'. $travel->id .'/tours';

        $response = $this->getJson($endpoint);
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $tourEarlier->id);
        $response->assertJsonPath('data.1.id', $tourLater->id);

    }

    public function test_tours_by_travel_id_returns_tours_by_price_min_to_max_by_start_date(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_returns_tours_by_price_min_to_max_by_start_date
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create(['is_public' => 1]);

        $tourCheapLater = Tour::factory(['travel_id' => $travel->id])->create();

        $tourExpensive = Tour::factory([
                            'travel_id' => $travel->id,
                            'price' => ($tourCheapLater->price + $tourCheapLater->price)
                            ])->create();

        $tourCheapEarlier = Tour::factory([
            'travel_id' => $travel->id,
            'price' => $tourCheapLater->price,
            'start_date' => $startDate = Carbon::parse($tourCheapLater->start_date)->subDays(mt_rand(1,10))->startOfDay()->toDateTimeString(),
            'end_date' => Carbon::parse($startDate)->addDays(mt_rand(0,10))->endOfDay()->toDateTimeString(),
            ])->create();

        $this->assertCount(3, $travel->tours()->get());

        $endpoint = self::BASE_URL . '/travels/'. $travel->id .'/tours?sort_by=price&order=asc';

        $response = $this->getJson($endpoint);
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $tourCheapEarlier->id);
        $response->assertJsonPath('data.1.id', $tourCheapLater->id);
        $response->assertJsonPath('data.2.id', $tourExpensive->id);

    }

    public function test_tours_by_travel_id_returns_tours_by_price_max_to_min_by_start_date(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_returns_tours_by_price_max_to_min_by_start_date
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory()->create(['is_public' => 1]);

        $tourCheap = Tour::factory(['travel_id' => $travel->id])->create();

        $tourExpensiveLater = Tour::factory([
                                    'travel_id' => $travel->id,
                                    'price' => ($tourCheap->price + $tourCheap->price)
                                    ])->create();

        $tourExpensiveEarlier = Tour::factory([
                                    'travel_id' => $travel->id,
                                    'price' => $tourExpensiveLater->price,
                                    'start_date' => $startDate = Carbon::parse($tourExpensiveLater->start_date)->subDays(mt_rand(1,10))->startOfDay()->toDateTimeString(),
                                    'end_date' => Carbon::parse($startDate)->addDays(mt_rand(1,10))->endOfDay()->toDateTimeString(),
                                    ])->create();

        $this->assertCount(3, $travel->tours()->get());

        $endpoint = self::BASE_URL . '/travels/'. $travel->id .'/tours?sort_by=price&order=desc';

        $response = $this->getJson($endpoint);
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $tourExpensiveEarlier->id);
        $response->assertJsonPath('data.1.id', $tourExpensiveLater->id);
        $response->assertJsonPath('data.2.id', $tourCheap->id);

    }

    public function test_tours_by_travel_id_returns_tours_by_price_range(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_returns_tours_by_price_range
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory(['is_public' => 1])->create();

        $tourCheap = Tour::factory(['travel_id' => $travel->id])->create();

        $tourExpensive = Tour::factory([
            'travel_id' => $travel->id,
            'price' => ($tourCheap->price + $tourCheap->price)
            ])->create();

        $this->assertCount(2, $travel->tours()->get());


        $endpoint = self::BASE_URL . '/travels/'. $travel->id .'/tours';

        $response = $this->getJson($endpoint . '?price_to=' . ($tourCheap->price - 1) );
        $response->assertJsonCount(0, 'data');

        $response = $this->getJson($endpoint . '?price_to=' . $tourCheap->price);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tourCheap->id]);
        $response->assertJsonMissing(['id' => $tourExpensive->id]);

        $response = $this->getJson($endpoint . '?price_from=' . $tourCheap->price);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $tourCheap->id]);
        $response->assertJsonFragment(['id' => $tourExpensive->id]);

        $response = $this->getJson($endpoint . '?price_to=' . ($tourExpensive->price - 1) );
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tourCheap->id]);
        $response->assertJsonMissing(['id' => $tourExpensive->id]);

        $response = $this->getJson($endpoint . '?price_to=' . $tourExpensive->price);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $tourCheap->id]);
        $response->assertJsonFragment(['id' => $tourExpensive->id]);

        $response = $this->getJson($endpoint . '?price_from=' . $tourExpensive->price);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $tourCheap->id]);
        $response->assertJsonFragment(['id' => $tourExpensive->id]);

        $response = $this->getJson($endpoint . '?price_from=' . ($tourExpensive->price + 1) );
        $response->assertJsonCount(0, 'data');

        $response = $this->getJson($endpoint . '?price_from=' . $tourCheap->price . '&price_to=' . $tourCheap->price);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tourCheap->id]);
        $response->assertJsonMissing(['id' => $tourExpensive->id]);

        $response = $this->getJson($endpoint . '?price_from=' . $tourCheap->price . '&price_to=' . $tourExpensive->price);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $tourCheap->id]);
        $response->assertJsonFragment(['id' => $tourExpensive->id]);

        $response = $this->getJson($endpoint . '?price_from=' . ($tourCheap->price + 1) . '&price_to=' . $tourExpensive->price);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $tourCheap->id]);
        $response->assertJsonFragment(['id' => $tourExpensive->id]);

        $response = $this->getJson($endpoint . '?price_from=' . ($tourCheap->price + 1) . '&price_to=' . ($tourExpensive->price - 1) );
        $response->assertJsonCount(0, 'data');

        $response = $this->getJson($endpoint . '?price_from=' . $tourExpensive->price . '&price_to=' . $tourExpensive->price);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $tourCheap->id]);
        $response->assertJsonFragment(['id' => $tourExpensive->id]);

        $response = $this->getJson($endpoint . '?price_from=' . ($tourExpensive->price + 1) . '&price_to=' . ($tourExpensive->price + 1) );
        $response->assertJsonCount(0, 'data');

    }

    public function test_tours_by_travel_id_return_tours_by_date_range(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_return_tours_by_date_range
        */

        $this->actingAs($this->admin);

        $travel = Travel::factory(['is_public' => 1])->create();

        $tourLater = Tour::factory(['travel_id' => $travel->id])->create();

        $tourEarlier = Tour::factory([
                            'travel_id' => $travel->id,
                            'start_date' => $startDate = Carbon::parse($tourLater->start_date)->subDays(mt_rand(5,10))->startOfDay()->toDateTimeString(),
                            'end_date' => Carbon::parse($startDate)->addDays(mt_rand(0,5))->endOfDay()->toDateTimeString(),
                            ])->create();

        $this->assertCount(2, $travel->tours()->get());

        $endpoint = self::BASE_URL . '/travels/' . $travel->id . '/tours';


        $response = $this->getJson($endpoint . '?start_date=' . $tourEarlier->start_date);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $tourEarlier->id]);
        $response->assertJsonFragment(['id' => $tourLater->id]);

        $startDate = Carbon::parse($tourEarlier->start_date)->addDays(1)->startOfDay()->toDateTimeString();
        $response = $this->getJson($endpoint . '?start_date=' . $startDate);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $tourEarlier->id]);
        $response->assertJsonFragment(['id' => $tourLater->id]);

        $response = $this->getJson($endpoint . '?end_date=' . $tourEarlier->start_date);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tourEarlier->id]);
        $response->assertJsonMissing(['id' => $tourLater->id]);

        $startDate = Carbon::parse($tourEarlier->start_date)->subDays(1)->startOfDay()->toDateTimeString();
        $response = $this->getJson($endpoint . '?end_date=' . $startDate);
        $response->assertJsonCount(0, 'data');


        $response = $this->getJson($endpoint . '?start_date=' . $tourLater->start_date);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $tourEarlier->id]);
        $response->assertJsonFragment(['id' => $tourLater->id]);

        $startDate = Carbon::parse($tourLater->start_date)->addDays(1)->startOfDay()->toDateTimeString();
        $response = $this->getJson($endpoint . '?start_date=' . $startDate);
        $response->assertJsonCount(0, 'data');

        $response = $this->getJson($endpoint . '?end_date=' . $tourLater->start_date);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $tourEarlier->id]);
        $response->assertJsonFragment(['id' => $tourLater->id]);

        $startDate = Carbon::parse($tourLater->start_date)->subDays(1)->startOfDay()->toDateTimeString();
        $response = $this->getJson($endpoint . '?end_date=' . $startDate);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tourEarlier->id]);
        $response->assertJsonMissing(['id' => $tourLater->id]);


        $startDate = Carbon::parse($tourEarlier->start_date)->subDays(2)->startOfDay()->toDateTimeString();
        $endDate = Carbon::parse($tourEarlier->start_date)->subDays(1)->startOfDay()->toDateTimeString();
        $response = $this->getJson($endpoint . '?start_date=' . $startDate .'&end_date=' . $endDate);
        $response->assertJsonCount(0, 'data');

        $startDate = Carbon::parse($tourEarlier->start_date)->subDays(1)->startOfDay()->toDateTimeString();
        $endDate = $tourEarlier->start_date;
        $response = $this->getJson($endpoint . '?start_date=' . $startDate .'&end_date=' . $endDate);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tourEarlier->id]);
        $response->assertJsonMissing(['id' => $tourLater->id]);

        $startDate = $tourEarlier->start_date;
        $endDate = Carbon::parse($tourLater->start_date)->subDays(1)->startOfDay()->toDateTimeString();
        $response = $this->getJson($endpoint . '?start_date=' . $startDate .'&end_date=' . $endDate);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tourEarlier->id]);
        $response->assertJsonMissing(['id' => $tourLater->id]);

        $startDate = $tourEarlier->start_date;
        $endDate = $tourLater->start_date;
        $response = $this->getJson($endpoint . '?start_date=' . $startDate .'&end_date=' . $endDate);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $tourEarlier->id]);
        $response->assertJsonFragment(['id' => $tourLater->id]);

        $startDate = Carbon::parse($tourEarlier->start_date)->addDays(1)->startOfDay()->toDateTimeString();
        $endDate = Carbon::parse($tourLater->start_date)->subDays(1)->startOfDay()->toDateTimeString();
        $response = $this->getJson($endpoint . '?start_date=' . $startDate .'&end_date=' . $endDate);
        $response->assertJsonCount(0, 'data');

        $startDate = Carbon::parse($tourLater->start_date)->subDays(1)->startOfDay()->toDateTimeString();
        $endDate = $tourLater->start_date;
        $response = $this->getJson($endpoint . '?start_date=' . $startDate .'&end_date=' . $endDate);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $tourEarlier->id]);
        $response->assertJsonFragment(['id' => $tourLater->id]);

        $startDate = $tourLater->start_date;
        $endDate = Carbon::parse($tourLater->start_date)->addDays(1)->startOfDay()->toDateTimeString();
        $response = $this->getJson($endpoint . '?start_date=' . $startDate .'&end_date=' . $endDate);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $tourEarlier->id]);
        $response->assertJsonFragment(['id' => $tourLater->id]);

        $startDate = Carbon::parse($tourLater->start_date)->addDays(1)->startOfDay()->toDateTimeString();
        $endDate = Carbon::parse($tourLater->start_date)->addDays(2)->startOfDay()->toDateTimeString();
        $response = $this->getJson($endpoint . '?start_date=' . $startDate .'&end_date=' . $endDate);
        $response->assertJsonCount(0, 'data');

    }

}
