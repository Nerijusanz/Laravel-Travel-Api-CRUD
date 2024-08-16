<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Services\Api\V1\TourApiService;

class TourApiResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->formattedPrice(),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ];
    }

    private function formattedPrice()
    {
        return (new TourApiService)::formattedPrice($this->price);
    }
}
