<?php

namespace App\Services\Api\V1\Admin;

use App\Models\Travel;

class TravelApiService
{

    public function indexTravel()
    {
        $travels = Travel::query()
                        ->with(['tours'])
                        ->paginate();

        return $travels;
    }

    public function storeTravel(array $data): Travel
    {
        $travel =  Travel::create($data);

        return $travel;
    }

    public function showTravel(Travel $travel): Travel
    {
        $travel->load(['tours']);

        return $travel;
    }

    public function updateTravel(Travel $travel, array $data): Travel
    {
        $travel->update($data);

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
