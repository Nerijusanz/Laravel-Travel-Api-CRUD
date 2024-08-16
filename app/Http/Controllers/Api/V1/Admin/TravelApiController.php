<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\Travel;
use App\Http\Requests\Api\V1\Admin\TravelStoreApiRequest;
use App\Http\Requests\Api\V1\Admin\TravelUpdateApiRequest;
use App\Http\Resources\Api\V1\Admin\TravelApiResource;
use App\Http\Resources\Api\V1\Admin\TravelApiResourceCollection;


class TravelApiController extends Controller
{

    public function index()
    {
        $travels = Travel::query()->with(['tours'])->paginate();

        return TravelApiResourceCollection::collection($travels);
    }


    public function store(TravelStoreApiRequest $request)
    {

        $travel = Travel::create($request->validated());

        return (new TravelApiResource($travel))->response()->setStatusCode(Response::HTTP_CREATED);

    }


    public function show(Travel $travel)
    {
        $travel->load(['tours']);

        return new TravelApiResource($travel);
    }


    public function update(Travel $travel, TravelUpdateApiRequest $request)
    {
        $travel->update($request->safe()->except(['user_id']));

        return (new TravelApiResource($travel))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }


    public function destroy(Travel $travel)
    {
        $travel->load(['tours']);

        $travel->tours()->delete();

        $travel->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
