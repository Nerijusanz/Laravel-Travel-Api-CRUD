<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourApiResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => number_format($this->price,2),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ];

    }
}
