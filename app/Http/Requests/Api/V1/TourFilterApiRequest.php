<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class TourFilterApiRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        $priceFrom = request()->input('price_from');

        $priceToRule = (!isset($priceFrom) || !is_numeric($priceFrom) || $priceFrom < 1 )?  ['nullable','numeric','min:0'] : ['nullable', 'numeric','min:0','gte:price_from'];

        return [
            'price_from' => ['nullable','numeric','min:0'],
            'price_to' => $priceToRule,
            'start_date' => ['nullable','date'],
            'end_date' => ['nullable','date','after:start_date'],
            'sort_by' => ['nullable',Rule::in(['price'])],
            'order' => ['nullable',Rule::in(['asc', 'desc'])],
        ];
    }


    public function messages(): array
    {
        return [
            'sort_by' => "The 'sort_by' parameter accepts only 'price' value",
            'order' => "The 'order' parameter accepts only 'asc' or 'desc' values",
        ];
    }
}
