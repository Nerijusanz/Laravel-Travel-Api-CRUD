<?php

namespace App\Services\Api\V1;

use Illuminate\Http\Request;

use App\Models\Travel;

class TravelApiService
{

    public function index()
    {
        $travels = Travel::query()
                        ->with(['tours'])
                        ->public()
                        ->paginate();

        return $travels;
    }

    public function show(Request $request): Travel
    {
        $travel = Travel::findOrFail($request->route('travel') );

        $travel->load(['tours']);

        return $travel;
    }

    public static function isPublic(Request $request): bool
    {
        $travel = Travel::findOrFail($request->route('travel') );

        return $travel->is_public;
    }

}
