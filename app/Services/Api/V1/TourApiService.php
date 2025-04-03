<?php

namespace App\Services\Api\V1;

use App\Models\Tour;
use App\Models\Travel;

class TourApiService{

    public function indexTour(Travel $travel, array $data)
    {
        $travel->load(['tours']);

        $tours = $this->getTravelToursFilterByRequest($travel, $data);

        return $tours;
    }

    public function showTour(Travel $travel, Tour $tour): Tour
    {
        $travel->load(['tours']);

        $tour = $travel->tours()->findOrFail($tour->id);

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


    public static function setPriceValue(int|float $value): int
    {
        if( !isset($value) || !is_numeric($value) ) return 0;

        return (int)($value * 100);
    }


    public static function formattedPrice(int|float $value)
    {
        return number_format($value, 2);
    }

}

?>
