<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Models\Tour;
use App\Models\Travel;

class TourStoreApiRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'travel_id' => ['required','integer', Rule::exists(Travel::class,'id')->whereNull('deleted_at')],
            'name' => ['required','string','min:2','max:255', Rule::unique(Tour::class)->whereNull('deleted_at')],
            'price' => ['required', 'numeric','min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
        ];
    }

}
