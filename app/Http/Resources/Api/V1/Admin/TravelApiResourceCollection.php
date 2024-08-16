<?php

namespace App\Http\Resources\Api\V1\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Api\V1\Admin\TourApiResource;

class TravelApiResourceCollection extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'is_public' => $this->is_public,
            'name' => $this->name,
            'slug' => $this->slug,
            'number_of_days' => $this->number_of_days,
            'number_of_nights' => $this->number_of_nights,
            'description' => $this->description,
            'tours' => $this->whenLoaded('tours', fn () => TourApiResource::collection($this->tours))
        ];
    }
}
