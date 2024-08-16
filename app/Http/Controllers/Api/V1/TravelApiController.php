<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Travel;
use App\Http\Resources\Api\V1\TravelApiResource;
use App\Http\Resources\Api\V1\TravelApiResourceCollection;


class TravelApiController extends Controller
{

    public function index()
    {
        $travels = Travel::query()->public()->paginate();

        return TravelApiResourceCollection::collection($travels);
    }

    public function store(Request $request)
    {

    }

    public function show(Travel $travel)
    {
        if($travel->isNotPublic()) return response()->json(['errors' => 'Travel are not public'], Response::HTTP_FORBIDDEN);

        $travel->load(['tours']);

        return new TravelApiResource($travel);
    }

    public function update(Request $request, Travel $travel)
    {

    }

    public function destroy(Travel $travel)
    {

    }
}
