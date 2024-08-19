<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

use App\Models\Travel;
use App\Http\Resources\Api\V1\TravelApiResource;
use App\Http\Resources\Api\V1\TravelApiResourceCollection;


class TravelApiController extends Controller
{

    public function index(): JsonResponse
    {
        $travels = Travel::query()->with(['tours'])->public()->paginate();

        return (TravelApiResourceCollection::collection($travels))->response()->setStatusCode(Response::HTTP_OK);
    }

    public function show(Travel $travel): JsonResponse
    {
        if($travel->isNotPublic()) return response()->json(['errors' => 'Travel forbidden'])->setStatusCode(Response::HTTP_FORBIDDEN);

        $travel->load(['tours']);

        return (new TravelApiResource($travel))->response()->setStatusCode(Response::HTTP_OK);
    }

}
