<?php

namespace App\Services\Api\V1;

class TourApiService{

    public function getTravelToursFilterByRequest($travel,$attribute){

        $tours = $travel->tours()
            ->when($attribute->price_from, function ($query) use ($attribute) {
                $query->where('price', '>=', $attribute->price_from * 100);
            })
            ->when($attribute->price_to, function ($query) use ($attribute) {
                $query->where('price', '<=', $attribute->price_to * 100);
            })
            ->when($attribute->date_from, function ($query) use ($attribute) {
                $query->where('start_date', '>=', $attribute->date_from);
            })
            ->when($attribute->date_to, function ($query) use ($attribute) {
                $query->where('start_date', '<=', $attribute->date_to);
            })
            ->when($attribute->sort_by, function ($query) use ($attribute) {
                if (! in_array($attribute->sort_by, ['price']) || (! in_array($attribute->order, ['asc', 'desc']))) return;

                $query->orderBy($attribute->sort_by, $attribute->order);
            })
            ->orderBy('start_date')
            ->paginate();

        return $tours;
    }

    public static function formattedPrice(int|float $data)
    {
        return number_format($data, 2);
    }

}

?>
