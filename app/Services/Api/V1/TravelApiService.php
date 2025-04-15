<?php

namespace App\Services\Api\V1;

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

    public function show(Travel $travel): Travel
    {
        $travel->load(['tours']);

        return $travel;
    }

}
