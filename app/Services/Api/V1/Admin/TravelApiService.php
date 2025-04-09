<?php

namespace App\Services\Api\V1\Admin;

use Illuminate\Http\Request;

use App\Models\Travel;
use App\Http\Requests\Api\V1\Admin\TravelStoreApiRequest;
use App\Http\Requests\Api\V1\Admin\TravelUpdateApiRequest;

class TravelApiService
{

    public function index()
    {
        $travels = Travel::query()
                        ->with(['tours'])
                        ->paginate();

        return $travels;
    }

    public function store(TravelStoreApiRequest $request): Travel
    {
        $travel = Travel::create($request->validated() );

        return $travel;
    }

    public function show(Request $request): Travel
    {
        $travel = Travel::findOrFail($request->route('travel') );

        $travel->load(['tours']);

        return $travel;
    }

    public function update(TravelUpdateApiRequest $request): Travel
    {
        $validated = $request->safe()->except(['user_id']);

        $travel = Travel::findOrFail($request->route('travel') );

        $travel->update($validated);

        return $travel;
    }

    public function destroy(Request $request): Void
    {
        $travel = Travel::findOrFail($request->route('travel') );

        $travel->load(['tours']);

        $travel->tours()->delete();

        $travel->delete();
    }

}
