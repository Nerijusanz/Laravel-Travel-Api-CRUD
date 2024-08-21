<?php

namespace App\Services\Api\V1;

use App\Models\Tour;
use App\Models\Travel;

class TourApiService{

    public function indexTour(Travel $travel, array $attributes)
    {
        $travel->load(['tours']);

        $tours = $this->getTravelToursFilterByRequest($travel,$attributes);

        return $tours;
    }

    public function showTour(Travel $travel, Tour $tour): Tour
    {
        $travel->load(['tours']);

        $tour = $travel->tours()->findOrFail($tour->id);

        return $tour;
    }


    public function getTravelToursFilterByRequest(Travel $travel, array $attributes)
    {
        $tours = $travel->tours()
            ->when(isset($attributes['price_from']), function ($query) use ($attributes) {
                $query->where('price', '>=', $attributes['price_from'] * 100);
            })
            ->when(isset($attributes['price_to']), function ($query) use ($attributes) {
                $query->where('price', '<=', $attributes['price_to'] * 100);
            })
            ->when(isset($attributes['date_from']), function ($query) use ($attributes) {
                $query->where('start_date', '>=', $attributes['date_from']);
            })
            ->when(isset($attributes['date_to']), function ($query) use ($attributes) {
                $query->where('start_date', '<=', $attributes['date_to']);
            })
            ->when( (isset($attributes['sort_by']) && isset($attributes['order']) ), function ($query) use ($attributes) {
                if (! in_array($attributes['sort_by'], ['price']) || (! in_array($attributes['order'], ['asc', 'desc']))) return;

                $query->orderBy($attributes['sort_by'], $attributes['order']);
            })
            ->orderBy('start_date')
            ->paginate();

        return $tours;
    }

    public static function formattedPrice(int|float $data)
    {
        return number_format($data, 2);
    }

}

?>
