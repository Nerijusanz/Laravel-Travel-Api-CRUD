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

    private string $baseUrl;
    private string $endpoint;
    private User $admin;
    private User $user;
    private object $travel;
    private string $itemsPerPage;

    public function setUp(): void
    {
        parent::setUp();

        $this->baseUrl = config('app.settings.api.api_base_url');
        $this->itemsPerPage = config('app.settings.pagination.default_items_per_page');
        $this->admin = User::adminRole();
        $this->user = User::userRole();
    }

    private function initApiEndPoint(): string
    {
        return $this->endpoint = $this->baseUrl . '/travels/' . $this->travel->id . '/tours';
    }

    private function initData(): void
    {
        $this->actingAs($this->user);

        $this->travel = Travel::factory()->create(['is_public' => 1]);

        $this->initApiEndPoint();
    }

    public function test_tours_by_travel_id_request_filter_tours_invalid_data_return_validation_errors_response_status_422(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_request_filter_tours_invalid_data_return_validation_errors_response_status_422
        */

        $this->initData();

        $tour = Tour::factory(['travel_id' => $this->travel->id])->create();

        $invalid = [];
        $invalid['price_from']['numeric'] = str()->random(6);
        $invalid['price_from']['min:0'] = mt_rand(-100,-1);

        $invalid['price_to']['numeric'] = str()->random(6);
        $invalid['price_to']['min:0'] = mt_rand(-100,-1);
        $invalid['price_to']['gte:price_from'] = [
                                            'price_from' => $priceFrom = $tour->price_from,
                                            'price_to' => $priceTo = ($priceFrom - 1)
                                            ];

        $invalid['start_date']['date'] = str()->random(10);

        $invalid['end_date']['date'] = str()->random(10);
        $invalid['end_date']['after_or_equal:start_date'] = [
                                            'start_date' => $startDate = $tour->start_date,
                                            'end_date' => $endDate = Carbon::parse($startDate)->subDays(1)->endOfDay()->toDateTimeString()
                                        ];

        $invalid['sort_by']['rule::in'] = str()->random(10);

        $invalid['order']['rule::in'] = str()->random(5);


        $response = $this->getJson($this->endpoint . '?price_from=' . $invalid['price_from']['numeric']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['price_from'] ]);

        $response = $this->getJson($this->endpoint . '?price_from=' . $invalid['price_from']['min:0']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['price_from'] ]);

        $response = $this->getJson($this->endpoint . '?price_to=' . $invalid['price_to']['numeric']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['price_to'] ]);

        $response = $this->getJson($this->endpoint . '?price_to=' . $invalid['price_to']['min:0']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['price_to'] ]);

        $response = $this->getJson($this->endpoint . '?price_from=' . $invalid['price_to']['gte:price_from']['price_from'] . '&price_to=' . $invalid['price_to']['gte:price_from']['price_to']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['price_to'] ]);

        $response = $this->getJson($this->endpoint . '?start_date=' . $invalid['start_date']['date']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['start_date'] ]);

        $response = $this->getJson($this->endpoint . '?end_date=' . $invalid['end_date']['date']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['end_date'] ]);

        $response = $this->getJson($this->endpoint . '?start_date=' . $invalid['end_date']['after_or_equal:start_date']['start_date'] . '&end_date=' . $invalid['end_date']['after_or_equal:start_date']['end_date']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['end_date'] ]);

        $response = $this->getJson($this->endpoint . '?sort_by=' . $invalid['sort_by']['rule::in']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['sort_by'] ]);

        $response = $this->getJson($this->endpoint . '?order=' . $invalid['order']['rule::in']);
        $response->assertStatus(422);
        $response->assertJsonStructure(['errors' => ['order'] ]);

    }

    public function test_tours_by_travel_id_returns_correct_tour(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_returns_correct_tour
        */

        $this->initData();

        $tour = Tour::factory(['travel_id' => $this->travel->id])->create();

        $tour = $this->travel->tours()->findOrFail($tour->id);

        $response = $this->getJson($this->endpoint);
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tour->id]);

    }

    public function test_tours_by_travel_id_returns_correct_pagination(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_returns_correct_pagination
        */

        $this->initData();

        $itemsRecords = ($this->itemsPerPage + 1);
        $page=1;

        for($i=1; $i<=$itemsRecords; $i++){
            Tour::factory([
                        'travel_id' => $this->travel->id,
                        'start_date'=>$startDate = Carbon::now()->addDays($i)->startOfDay()->toDateTimeString(),
                        'end_date'=>$endDate = Carbon::parse($startDate)->addDays(0)->endOfDay()->toDateTimeString(),
            ])->create();
        }

        $this->assertCount($itemsRecords, $this->travel->tours()->get());

        $tourOutPagination = $this->travel->tours()
                    ->orderBy('start_date','desc')
                    ->first();

        $response = $this->getJson($this->endpoint);
        $response->assertStatus(200);
        $response->assertJsonCount($this->itemsPerPage, 'data');
        $response->assertJsonPath('meta.current_page', $page);
        $response->assertJsonPath('meta.last_page', 2);
        $response->assertJsonPath('meta.total', $itemsRecords);
        $response->assertJsonMissing(['data.*.id' => $tourOutPagination->id]);

        $response = $this->getJson($this->endpoint . '?page=' . $page);
        $response->assertStatus(200);
        $response->assertJsonCount($this->itemsPerPage, 'data');
        $response->assertJsonPath('meta.current_page', $page);
        $response->assertJsonPath('meta.last_page', 2);
        $response->assertJsonPath('meta.total', $itemsRecords);
        $response->assertJsonMissing(['data.*.id' => $tourOutPagination->id]);

        $response = $this->getJson($this->endpoint . '?page=' . ($page2 = $page + 1) );
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('meta.current_page', $page2);
        $response->assertJsonPath('meta.last_page', 2);
        $response->assertJsonPath('meta.total', $itemsRecords);
        $response->assertJsonPath('data.0.id', $tourOutPagination->id);

    }

    public function test_tours_by_travel_id_returns_tour_price_correct_formatted(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_returns_tour_price_correct_formatted
        */

        $this->initData();

        $tour = Tour::factory(['travel_id' => $this->travel->id])->create();

        $tour = $this->travel->tours()->findOrFail($tour->id);

        $response = $this->getJson($this->endpoint);
        $response->assertStatus(200);
        $response->assertJsonFragment([
                                'id' => $tour->id,
                                'price' => $tour->price
                            ]);

    }

    public function test_tours_by_travel_id_returns_tours_by_starting_date(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_returns_tours_by_starting_date
        */

        $this->initData();

        $tourLater = Tour::factory(['travel_id' => $this->travel->id])->create();

        $tourEarlier = Tour::factory([
                                'travel_id' => $this->travel->id,
                                'start_date' => $startDate = Carbon::parse($tourLater->start_date)->subDays(mt_rand(1,10))->startOfDay()->toDateTimeString(),
                                'end_date' => $endDate = Carbon::parse($startDate)->addDays(mt_rand(0,10))->endOfDay()->toDateTimeString(),
                                ])->create();

        $this->assertCount(2, $this->travel->tours()->get());

        $tourEarlier = $this->travel->tours()->findOrFail($tourEarlier->id);
        $tourLater = $this->travel->tours()->findOrFail($tourLater->id);

        $response = $this->getJson($this->endpoint);
        $response->assertStatus(200);
        $response->assertJsonPath('data.0.id', $tourEarlier->id);
        $response->assertJsonPath('data.1.id', $tourLater->id);

    }

    public function test_tours_by_travel_id_returns_tours_by_price_min_to_max_by_start_date(): void
    {
        /*
        php artisan test --filter=test_tours_by_travel_id_returns_tours_by_price_min_to_max_by_start_date
        */

        $this->initData();

        $tourCheapLater = Tour::factory(['travel_id' => $this->travel->id])->create();

        $tourExpensive = Tour::factory([
                            'travel_id' => $this->travel->id,
                            'price' => $priceExpensive = ($tourCheapLater->price * 2 )
                            ])->create();

        $tourCheapEarlier = Tour::factory([
            'travel_id' => $this->travel->id,
            'price' => $tourCheapLater->price,
            'start_date' => $startDate = Carbon::parse($tourCheapLater->start_date)->subDays(mt_rand(1,10))->startOfDay()->toDateTimeString(),
            'end_date' => $endDate = Carbon::parse($startDate)->addDays(mt_rand(0,10))->endOfDay()->toDateTimeString(),
            ])->create();

        $this->assertCount(3, $this->travel->tours()->get());

        $tourCheapEarlier = $this->travel->tours()->findOrFail($tourCheapEarlier->id);
        $tourCheapLater = $this->travel->tours()->findOrFail($tourCheapLater->id);
        $tourExpensive = $this->travel->tours()->findOrFail($tourExpensive->id);

        $this->endpoint = $this->endpoint . '?sort_by=price&order=asc';

        $response = $this->getJson($this->endpoint);
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
                                    'price' => $priceExpensive = ($tourCheap->price * 2 )
                                    ])->create();

        $tourExpensiveEarlier = Tour::factory([
                                    'travel_id' => $travel->id,
                                    'price' => $tourExpensiveLater->price,
                                    'start_date' => $startDate = Carbon::parse($tourExpensiveLater->start_date)->subDays(mt_rand(1,10))->startOfDay()->toDateTimeString(),
                                    'end_date' => $endDate = Carbon::parse($startDate)->addDays(mt_rand(0,10))->endOfDay()->toDateTimeString(),
                                    ])->create();

        $this->assertCount(3, $travel->tours()->get());

        $this->actingAs($this->user);

        $endpoint = self::BASE_URL . '/travels/'. $travel->id .'/tours?sort_by=price&order=desc';

        $tourExpensiveEarlier = $travel->tours()->findOrFail($tourExpensiveEarlier->id);
        $tourExpensiveLater = $travel->tours()->findOrFail($tourExpensiveLater->id);
        $tourCheap = $travel->tours()->findOrFail($tourCheap->id);

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
            'price' => $priceExpensive = ($tourCheap->price * 2 )
            ])->create();

        $this->assertCount(2, $travel->tours()->get());

        $this->actingAs($this->user);

        $endpoint = self::BASE_URL . '/travels/'. $travel->id .'/tours';

        $tourCheap = $travel->tours()->findOrFail($tourCheap->id);
        $tourExpensive = $travel->tours()->findOrFail($tourExpensive->id);


        $response = $this->getJson($endpoint . '?price_to=' . $priceTo = ($tourCheap->price - 1) );
        $response->assertJsonCount(0, 'data');

        $response = $this->getJson($endpoint . '?price_to=' . $tourCheap->price);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tourCheap->id]);
        $response->assertJsonMissing(['id' => $tourExpensive->id]);

        $response = $this->getJson($endpoint . '?price_from=' . $tourCheap->price);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $tourCheap->id]);
        $response->assertJsonFragment(['id' => $tourExpensive->id]);

        $response = $this->getJson($endpoint . '?price_to=' . $priceTo = ($tourExpensive->price - 1) );
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

        $response = $this->getJson($endpoint . '?price_from=' . $priceFrom = ($tourExpensive->price + 1) );
        $response->assertJsonCount(0, 'data');

        $response = $this->getJson($endpoint . '?price_from=' . $tourCheap->price . '&price_to=' . $tourCheap->price);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $tourCheap->id]);
        $response->assertJsonMissing(['id' => $tourExpensive->id]);

        $response = $this->getJson($endpoint . '?price_from=' . $tourCheap->price . '&price_to=' . $tourExpensive->price);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $tourCheap->id]);
        $response->assertJsonFragment(['id' => $tourExpensive->id]);

        $response = $this->getJson($endpoint . '?price_from=' . $priceFrom = ($tourCheap->price + 1) . '&price_to=' . $tourExpensive->price);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $tourCheap->id]);
        $response->assertJsonFragment(['id' => $tourExpensive->id]);

        $response = $this->getJson($endpoint . '?price_from=' . $priceFrom = ($tourCheap->price + 1) . '&price_to=' . $priceTo = ($tourExpensive->price - 1) );
        $response->assertJsonCount(0, 'data');

        $response = $this->getJson($endpoint . '?price_from=' . $tourExpensive->price . '&price_to=' . $tourExpensive->price);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $tourCheap->id]);
        $response->assertJsonFragment(['id' => $tourExpensive->id]);

        $response = $this->getJson($endpoint . '?price_from=' . $priceFrom = ($tourExpensive->price + 1) . '&price_to=' . $priceTo = ($tourExpensive->price + 1) );
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
                            'end_date' => $endDate = Carbon::parse($startDate)->addDays(mt_rand(0,10))->endOfDay()->toDateTimeString(),
                            ])->create();

        $this->assertCount(2, $travel->tours()->get());

        $this->actingAs($this->user);

        $endpoint = self::BASE_URL . '/travels/' . $travel->id . '/tours';

        $tourLater = $travel->tours()->findOrFail($tourLater->id);
        $tourEarlier = $travel->tours()->findOrFail($tourEarlier->id);


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
