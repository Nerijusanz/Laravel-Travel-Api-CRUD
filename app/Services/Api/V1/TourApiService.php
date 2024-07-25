<?php

namespace App\Services\Api\V1;

class TourApiService{

    public function getTravelToursFilterByRequest($travel,$request){

        $tours = $travel->tours()
            ->when($request->price_from, function ($query) use ($request) {
                $query->where('price', '>=', $request->price_from * 100);
            })
            ->when($request->price_to, function ($query) use ($request) {
                $query->where('price', '<=', $request->price_to * 100);
            })
            ->when($request->date_from, function ($query) use ($request) {
                $query->where('start_date', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($query) use ($request) {
                $query->where('start_date', '<=', $request->date_to);
            })
            ->when($request->sort_by, function ($query) use ($request) {
                if (! in_array($request->sort_by, ['price']) || (! in_array($request->order, ['asc', 'desc']))) return;

                $query->orderBy($request->sort_by, $request->order);
            })
            ->orderBy('start_date')
            ->paginate();

        return $tours;
    }

}

?>
