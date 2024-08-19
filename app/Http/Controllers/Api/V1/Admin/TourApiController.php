<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

use App\Models\Tour;
use App\Models\Travel;
use App\Http\Requests\Api\V1\Admin\TourStoreApiRequest;
use App\Http\Requests\Api\V1\Admin\TourUpdateApiRequest;
use App\Http\Resources\Api\V1\Admin\TourApiResource;
use App\Http\Resources\Api\V1\Admin\TourApiResourceCollection;

class TourApiController extends Controller
{

    public function index(Travel $travel): JsonResponse
    {

        $travel->load(['tours']);

        $tours = $travel->tours()->paginate();

        return (TourApiResourceCollection::collection($tours))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function store(Travel $travel, TourStoreApiRequest $request): JsonResponse
    {

        $travel->load(['tours']);

        $tour = $travel->tours()->create($request->validated());

        return (new TourApiResource($tour))->response()->setStatusCode(Response::HTTP_CREATED);

    }

    public function show(Travel $travel, Tour $tour): JsonResponse
    {

        $travel->load(['tours']);

        $tour = $travel->tours()->findOrFail($tour->id);

        return (new TourApiResource($tour))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function update(TourUpdateApiRequest $request, Travel $travel, Tour $tour): JsonResponse
    {
        $travel->load(['tours']);

        $tour = $travel->tours()->findOrFail($tour->id);

        $tour->update($request->safe()->except(['user_id','travel_id']));

        return (new TourApiResource($tour))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Travel $travel, Tour $tour): JsonResponse
    {
        $travel->load(['tours']);

        $travel->tours()->findOrFail($tour->id)->delete();

        return response()->json(null)->setStatusCode(Response::HTTP_NO_CONTENT);
    }
}
