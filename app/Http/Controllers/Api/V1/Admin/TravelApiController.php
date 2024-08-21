<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

use App\Models\Travel;
use App\Services\Api\V1\Admin\TravelApiService;
use App\Http\Requests\Api\V1\Admin\TravelStoreApiRequest;
use App\Http\Requests\Api\V1\Admin\TravelUpdateApiRequest;
use App\Http\Resources\Api\V1\Admin\TravelApiResource;
use App\Http\Resources\Api\V1\Admin\TravelApiResourceCollection;


class TravelApiController extends Controller
{

    public function index(TravelApiService $travelApiService): JsonResponse
    {
        $travels = $travelApiService->indexTravel();

        return (TravelApiResourceCollection::collection($travels))->response()->setStatusCode(Response::HTTP_OK);
    }


    public function store(TravelStoreApiRequest $request,TravelApiService $travelApiService): JsonResponse
    {
        $travel = $travelApiService->storeTravel($request->validated());

        return (new TravelApiResource($travel))->response()->setStatusCode(Response::HTTP_CREATED);
    }


    public function show(Travel $travel, TravelApiService $travelApiService): JsonResponse
    {
        $travel = $travelApiService->showTravel($travel);

        return (new TravelApiResource($travel))->response()->setStatusCode(Response::HTTP_OK);
    }


    public function update(Travel $travel, TravelUpdateApiRequest $request, TravelApiService $travelApiService): JsonResponse
    {
        $validatedRequest = $request->safe()->except(['user_id']);

        $travel = $travelApiService->updateTravel($travel,$validatedRequest);

        return (new TravelApiResource($travel))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }


    public function destroy(Travel $travel, TravelApiService $travelApiService): JsonResponse
    {
        $travelApiService->destroyTravel($travel);

        return response()->json(null)->setStatusCode(Response::HTTP_NO_CONTENT);
    }
}
