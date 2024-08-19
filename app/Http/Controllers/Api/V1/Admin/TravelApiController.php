<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

use App\Models\Travel;
use App\Http\Requests\Api\V1\Admin\TravelStoreApiRequest;
use App\Http\Requests\Api\V1\Admin\TravelUpdateApiRequest;
use App\Http\Resources\Api\V1\Admin\TravelApiResource;
use App\Http\Resources\Api\V1\Admin\TravelApiResourceCollection;


class TravelApiController extends Controller
{

    public function index(): JsonResponse
    {
        $travels = Travel::query()->with(['tours'])->paginate();

        return (TravelApiResourceCollection::collection($travels))->response()->setStatusCode(Response::HTTP_OK);
    }


    public function store(TravelStoreApiRequest $request): JsonResponse
    {

        $travel = Travel::create($request->validated());

        return (new TravelApiResource($travel))->response()->setStatusCode(Response::HTTP_CREATED);
    }


    public function show(Travel $travel): JsonResponse
    {
        $travel->load(['tours']);

        return (new TravelApiResource($travel))->response()->setStatusCode(Response::HTTP_OK);
    }


    public function update(Travel $travel, TravelUpdateApiRequest $request): JsonResponse
    {
        $travel->update($request->safe()->except(['user_id']));

        return (new TravelApiResource($travel))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }


    public function destroy(Travel $travel): JsonResponse
    {
        $travel->load(['tours']);

        $travel->tours()->delete();

        $travel->delete();

        return response()->json(null)->setStatusCode(Response::HTTP_NO_CONTENT);
    }
}
