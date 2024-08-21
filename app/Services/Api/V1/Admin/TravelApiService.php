<?php

namespace App\Services\Api\V1\Admin;

use App\Models\Travel;

class TravelApiService{

    public function indexTravel()
    {
        $travels = Travel::query()
                        ->with(['tours'])
                        ->paginate();

        return $travels;
    }

    public function storeTravel(array $attributes): Travel
    {
        $travel =  Travel::create($attributes);

        return $travel;
    }

    public function showTravel(Travel $travel): Travel
    {
        $travel->load(['tours']);

        return $travel;
    }

    public function updateTravel(Travel $travel, array $attributes): Travel
    {
        $travel->update($attributes);

        return $travel;
    }

    public function destroyTravel(Travel $travel): Void
    {
        $travel->load(['tours']);

        $travel->tours()->delete();

        $travel->delete();
    }

}

?>
