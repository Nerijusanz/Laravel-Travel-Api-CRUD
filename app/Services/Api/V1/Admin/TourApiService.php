<?php

namespace App\Services\Api\V1\Admin;

use Illuminate\Http\Request;
use Carbon\Carbon;

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

        $validated = $request->validated();

        $validated['start_date'] = Carbon::parse($validated['start_date'])->startOfDay()->toDateTimeString();
        $validated['end_date'] = Carbon::parse($validated['end_date'])->endOfDay()->toDateTimeString();

        $tour = $travel->tours()->create($validated);

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

        $validated = $request->validated();

        $validated['start_date'] = Carbon::parse($validated['start_date'])->startOfDay()->toDateTimeString();
        $validated['end_date'] = Carbon::parse($validated['end_date'])->endOfDay()->toDateTimeString();

        $tour->update($validated);

        return $tour;
    }

    public function destroy(Request $request): Void
    {
        $travel = Travel::findOrFail($request->route('travel') );

        $tour = $travel->tours()->findOrFail($request->route('tour') );

        $tour->delete();
    }

}