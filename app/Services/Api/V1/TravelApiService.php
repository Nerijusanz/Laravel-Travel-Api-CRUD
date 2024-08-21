<?php

namespace App\Services\Api\V1;

use App\Models\Travel;

class TravelApiService{

    public function indexTravel()
    {
        $travels = Travel::query()
                        ->with(['tours'])
                        ->public()
                        ->paginate();

        return $travels;
    }

    public function showTravel(Travel $travel): Travel
    {
        $travel->load(['tours']);

        return $travel;
    }

}

?>
