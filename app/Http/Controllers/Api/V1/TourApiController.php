<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Services\Api\V1\TravelApiService;
use App\Services\Api\V1\TourApiService;
use App\Http\Requests\Api\V1\TourFilterApiRequest;
use App\Http\Resources\Api\V1\TourApiResource;
use App\Http\Resources\Api\V1\TourApiResourceCollection;

class TourApiController extends Controller
{

    public function index(TourFilterApiRequest $request,TravelApiService $travelApiService, TourApiService $tourApiService): JsonResponse
    {
        if(!$travelApiService->isPublic($request) )
            return response()->json(['errors' => 'Travel forbidden'])->setStatusCode(Response::HTTP_FORBIDDEN);

        $tours = $tourApiService->index($request);

        return (TourApiResourceCollection::collection($tours))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function show(Request $request,TravelApiService $travelApiService,TourApiService $tourApiService): JsonResponse
    {
        if(!$travelApiService->isPublic($request) )
            return response()->json(['errors' => 'Travel forbidden'])->setStatusCode(Response::HTTP_FORBIDDEN);

        $tour = $tourApiService->show($request);

        return (new TourApiResource($tour))->response()->setStatusCode(Response::HTTP_OK);
    }

}
