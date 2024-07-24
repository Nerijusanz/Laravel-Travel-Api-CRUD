<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Tour;
use App\Models\Travel;
use App\Http\Resources\Api\V1\TourApiResource;

class TourApiController extends Controller
{

    public function index(Travel $travel)
    {
        $tours = $travel->tours()->orderBy('start_date')->paginate();

        return TourApiResource::collection($tours);
    }

    public function store(Request $request)
    {

    }

    public function show(Tour $tour)
    {

    }

    public function update(Request $request, Tour $tour)
    {

    }

    public function destroy(Tour $tour)
    {

    }
}
