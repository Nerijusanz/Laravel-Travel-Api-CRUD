<?php

namespace App\Services\Api\V1\Admin;

use App\Models\Tour;
use App\Models\Travel;

class TourApiService{

    public function indexTour(Travel $travel)
    {
        $travel->load(['tours']);

        $tours = $travel->tours()->paginate();

        return $tours;
    }

    public function storeTour(Travel $travel, array $attributes): Tour
    {
        $travel->load(['tours']);

        $tour = $travel->tours()->create($attributes);

        return $tour;
    }

    public function showTour(Travel $travel,Tour $tour): Tour
    {
        $travel->load(['tours']);

        $tour = $travel->tours()->findOrFail($tour->id);

        return $tour;
    }

    public function updateTour(Travel $travel,Tour $tour,array $attributes): Tour
    {
        $travel->load(['tours']);

        $tour = $travel->tours()->findOrFail($tour->id);

        $tour->update($attributes);

        return $tour;
    }

    public function destroyTour(Travel $travel, Tour $tour): Void
    {
        $travel->load(['tours']);

        $travel->tours()->findOrFail($tour->id)->delete();
    }

}

?>
