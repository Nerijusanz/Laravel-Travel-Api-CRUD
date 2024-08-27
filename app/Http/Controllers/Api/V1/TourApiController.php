<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Models\Tour;
use App\Models\Travel;
use App\Services\Api\V1\TourApiService;
use App\Http\Requests\Api\V1\TourFilterApiRequest;
use App\Http\Resources\Api\V1\TourApiResource;
use App\Http\Resources\Api\V1\TourApiResourceCollection;

class TourApiController extends Controller
{

    public function index(Travel $travel, TourFilterApiRequest $request, TourApiService $tourApiService): JsonResponse
    {
        if($travel->isNotPublic()) return response()->json(['errors' => 'Travel forbidden'])->setStatusCode(Response::HTTP_FORBIDDEN);

        $validatedRequest = $request->validated();

        $tours = $tourApiService->indexTour($travel,$validatedRequest);

        return (TourApiResourceCollection::collection($tours))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function show(Travel $travel, Tour $tour, TourApiService $tourApiService): JsonResponse
    {
        if($travel->isNotPublic()) return response()->json(['errors' => 'Travel forbidden'])->setStatusCode(Response::HTTP_FORBIDDEN);

        $tour = $tourApiService->showTour($travel,$tour);

        return (new TourApiResource($tour))->response()->setStatusCode(Response::HTTP_OK);
    }

}
