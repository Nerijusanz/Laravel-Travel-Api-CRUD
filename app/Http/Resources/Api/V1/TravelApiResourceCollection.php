<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Api\V1\TourApiResource;

class TravelApiResourceCollection extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'number_of_days' => $this->number_of_days,
            'number_of_nights' => $this->number_of_nights,
            'description' => $this->description,
            'tours' => $this->whenLoaded('tours', fn () => TourApiResource::collection($this->tours))
        ];
    }
}
