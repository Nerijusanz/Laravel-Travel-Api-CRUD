<?php

namespace App\Services\Api\V1;

use Illuminate\Http\Request;

use App\Models\Tour;
use App\Models\Travel;
use App\Http\Requests\Api\V1\TourFilterApiRequest;

class TourApiService
{

    public function index(TourFilterApiRequest $request)
    {
        $travel = Travel::findOrFail($request->route('travel') );

        $travel->load(['tours']);

        $validated = $request->validated();

        $tours = $this->getTravelToursFilterByRequest($travel, $validated);

        return $tours;
    }

    public function show(Request $request): Tour
    {
        $travel = Travel::findOrFail($request->route('travel') );

        $tour = $travel->tours()->findOrFail($request->route('tour') );

        return $tour;
    }

    public function getTravelToursFilterByRequest(Travel $travel, array $req)
    {
        return $travel->tours()
            ->when(isset($req['price_from']), function ($query) use ($req) {
                $query->where('price', '>=', $req['price_from'] * 100);
            })
            ->when(isset($req['price_to']), function ($query) use ($req) {
                $query->where('price', '<=', $req['price_to'] * 100);
            })
            ->when(isset($req['date_from']), function ($query) use ($req) {
                $query->where('start_date', '>=', $req['date_from']);
            })
            ->when(isset($req['date_to']), function ($query) use ($req) {
                $query->where('start_date', '<=', $req['date_to']);
            })
            ->when( (isset($req['sort_by']) && isset($req['order']) ), function ($query) use ($req) {
                if (! in_array($req['sort_by'], ['price']) || (! in_array($req['order'], ['asc', 'desc']))) return;

                $query->orderBy($req['sort_by'], $req['order']);
            })
            ->orderBy('start_date')
            ->paginate();

    }

    public static function getPriceValue(int $value)
    {
        $value = ($value / 100);

        return number_format($value,2);

    }

    public static function setPriceValue(int|float|string $value): int
    {
        if( !isset($value) || !is_numeric($value) ) return 0;

        return (int)($value * 100);
    }

}
