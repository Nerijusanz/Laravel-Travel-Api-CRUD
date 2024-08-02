<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

use App\Models\Tour;
use App\Models\Travel;
use App\Services\Api\V1\TourApiService;
use App\Http\Requests\Api\V1\TourFilterApiRequest;
use App\Http\Resources\Api\V1\TourApiResource;

class TourApiController extends Controller
{

    public function index(Travel $travel, TourFilterApiRequest $request, TourApiService $tourApiService)
    {
        if($travel->isNotPublic()) return response()->json(['errors' => 'Travel tours are not public'], Response::HTTP_FORBIDDEN);

        $travel->load(['tours']);

        $tours = $tourApiService->getTravelToursFilterByRequest($travel,$request);

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
