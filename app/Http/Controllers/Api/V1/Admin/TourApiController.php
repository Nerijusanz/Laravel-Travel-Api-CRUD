<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

use App\Services\Api\V1\Admin\TourApiService;
use App\Http\Requests\Api\V1\Admin\TourStoreApiRequest;
use App\Http\Requests\Api\V1\Admin\TourUpdateApiRequest;
use App\Http\Resources\Api\V1\Admin\TourApiResource;
use App\Http\Resources\Api\V1\Admin\TourApiResourceCollection;

class TourApiController extends Controller
{

    public function index(Request $request, TourApiService $tourApiService): JsonResponse
    {
        $tours = $tourApiService->index($request);

        return (TourApiResourceCollection::collection($tours))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function store(TourStoreApiRequest $request, TourApiService $tourApiService): JsonResponse
    {
        $tour = $tourApiService->store($request);

        return (new TourApiResource($tour))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Request $request, TourApiService $tourApiService): JsonResponse
    {
        $tour = $tourApiService->show($request);

        return (new TourApiResource($tour))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function update(TourUpdateApiRequest $request, TourApiService $tourApiService): JsonResponse
    {
        $tour = $tourApiService->update($request);

        return (new TourApiResource($tour))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Request $request, TourApiService $tourApiService): JsonResponse
    {
        $tourApiService->destroy($request);

        return response()->json()->setStatusCode(Response::HTTP_NO_CONTENT);
    }
}
