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

    public function getTravelToursFilterByRequest(Travel $travel, array $data)
    {
        return $travel->tours()
            ->when(isset($data['price_from']), function ($query) use ($data) {
                $query->where('price', '>=', $data['price_from'] * 100);
            })
            ->when(isset($data['price_to']), function ($query) use ($data) {
                $query->where('price', '<=', $data['price_to'] * 100);
            })
            ->when(isset($data['date_from']), function ($query) use ($data) {
                $query->where('start_date', '>=', $data['date_from']);
            })
            ->when(isset($data['date_to']), function ($query) use ($data) {
                $query->where('start_date', '<=', $data['date_to']);
            })
            ->when( (isset($data['sort_by']) && isset($data['order']) ), function ($query) use ($data) {
                if (! in_array($data['sort_by'], ['price']) || (! in_array($data['order'], ['asc', 'desc']))) return;

                $query->orderBy($data['sort_by'], $data['order']);
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
