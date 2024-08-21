<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

use App\Models\Tour;
use App\Models\Travel;
use App\Services\Api\V1\Admin\TourApiService;
use App\Http\Requests\Api\V1\Admin\TourStoreApiRequest;
use App\Http\Requests\Api\V1\Admin\TourUpdateApiRequest;
use App\Http\Resources\Api\V1\Admin\TourApiResource;
use App\Http\Resources\Api\V1\Admin\TourApiResourceCollection;

class TourApiController extends Controller
{

    public function index(Travel $travel, TourApiService $tourApiService): JsonResponse
    {
        $tours = $tourApiService->indexTour($travel);

        return (TourApiResourceCollection::collection($tours))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function store(Travel $travel, TourStoreApiRequest $request, TourApiService $tourApiService): JsonResponse
    {
        $tour = $tourApiService->storeTour($travel, $request->validated());

        return (new TourApiResource($tour))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Travel $travel, Tour $tour, TourApiService $tourApiService): JsonResponse
    {
        $tour = $tourApiService->showTour($travel,$tour);

        return (new TourApiResource($tour))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function update(Travel $travel, Tour $tour, TourUpdateApiRequest $request, TourApiService $tourApiService): JsonResponse
    {
        $validatedRequest = $request->safe()->except(['user_id','travel_id']);

        $tour = $tourApiService->updateTour($travel,$tour,$validatedRequest);

        return (new TourApiResource($tour))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Travel $travel, Tour $tour, TourApiService $tourApiService): JsonResponse
    {
        $tourApiService->destroyTour($travel,$tour);

        return response()->json(null)->setStatusCode(Response::HTTP_NO_CONTENT);
    }
}
