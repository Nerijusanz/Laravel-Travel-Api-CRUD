<?php

namespace App\Services\Api\V1\Admin;

use Illuminate\Http\Request;

use App\Models\Travel;
use App\Models\Tour;
use App\Http\Requests\Api\V1\Admin\TourStoreApiRequest;
use App\Http\Requests\Api\V1\Admin\TourUpdateApiRequest;

class TourApiService
{

    public function index(Request $request)
    {
        $travel = Travel::findOrFail($request->route('travel') );

        $travel->load(['tours']);

        $tours = $travel->tours()->paginate();

        return $tours;
    }

    public function store(TourStoreApiRequest $request): Tour
    {
        $travel = Travel::findOrFail($request->route('travel') );

        $tour = $travel->tours()->create($request->validated() );

        return $tour;
    }

    public function show(Request $request): Tour
    {
        $travel = Travel::findOrFail($request->route('travel') );

        $tour = $travel->tours()->findOrFail($request->route('tour') );

        return $tour;
    }

    public function update(TourUpdateApiRequest $request): Tour
    {
        $travel = Travel::findOrFail($request->route('travel') );

        $tour = $travel->tours()->findOrFail($request->route('tour') );

        $tour->update($request->validated() );

        return $tour;
    }

    public function destroy(Request $request): Void
    {
        $travel = Travel::findOrFail($request->route('travel') );

        $tour = $travel->tours()->findOrFail($request->route('tour') );

        $tour->delete();
    }

}