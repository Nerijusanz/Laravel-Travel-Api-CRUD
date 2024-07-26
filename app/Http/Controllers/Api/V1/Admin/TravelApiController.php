<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Travel;
use App\Http\Requests\Api\V1\Admin\TravelStoreApiRequest;
use App\Http\Resources\Api\V1\Admin\TravelApiResource;

class TravelApiController extends Controller
{

    public function index()
    {

    }


    public function store(TravelStoreApiRequest $request)
    {

        $travel = Travel::create($request->validated());

        return new TravelApiResource($travel);

    }


    public function show(Travel $travel)
    {

    }


    public function update(Request $request, Travel $travel)
    {

    }


    public function destroy(Travel $travel)
    {

    }
}
