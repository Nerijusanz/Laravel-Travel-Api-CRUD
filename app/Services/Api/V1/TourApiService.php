<?php

namespace App\Services\Api\V1;

use Illuminate\Http\Request;

use App\Models\Tour;
use App\Models\Travel;
use App\Http\Requests\Api\V1\TourFilterApiRequest;
use App\Utilities\Numbers;

class TourApiService
{

    public function index(TourFilterApiRequest $request)
    {
        $travel = Travel::findOrFail($request->route('travel') );

        $travel->load(['tours']);

        $validated = $request->validated();

        $tours = $this->getTravelToursFilterByRequest($travel, $validated);

        return $tours;
    }

    public function show(Request $request): Tour
    {
        $travel = Travel::findOrFail($request->route('travel') );

        $tour = $travel->tours()->findOrFail($request->route('tour') );

        return $tour;
    }

    public function getTravelToursFilterByRequest(Travel $travel, array $req)
    {
        return $travel->tours()
            ->when( ( isset($req['price_from']) || isset($req['price_to']) ), function ($query) use ($req) {

                if(isset($req['price_from']) && isset($req['price_to']) ){

                    $priceFrom = Numbers::setAttributeNumberValue($req['price_from']);
                    $priceTo = Numbers::setAttributeNumberValue($req['price_to']);

                    $query->whereBetween('price', [$priceFrom,$priceTo]);

                    return;
                }

                if(isset($req['price_from']) && !isset($req['price_to']) ){

                    $priceFrom = Numbers::setAttributeNumberValue($req['price_from']);

                    $query->where('price', '>=', $priceFrom);

                    return;
                }

                if(isset($req['price_to']) && !isset($req['price_from']) ){

                    $priceTo = Numbers::setAttributeNumberValue($req['price_to']);

                    $query->where('price', '<=', $priceTo);

                    return;
                }

            })
            ->when( ( isset($req['start_date']) || isset($req['end_date']) ), function ($query) use ($req) {

                if( isset($req['start_date']) && isset($req['end_date']) ){

                    $query->whereBetween('start_date', [ $req['start_date'],$req['end_date'] ]);

                    return;
                }

                if( isset($req['start_date']) && !isset($req['end_date']) ){

                    $query->where('start_date', '>=' , $req['start_date']);

                    return;
                }

                if( isset($req['end_date']) && !isset($req['start_date']) ){

                    $query->where('start_date', '<=' , $req['end_date']);

                    return;
                }

            })
            ->when( (isset($req['sort_by']) && isset($req['order']) ), function ($query) use ($req) {

                $query->orderBy($req['sort_by'], $req['order']);

            })
            ->orderBy('start_date')
            ->paginate();

    }

}
