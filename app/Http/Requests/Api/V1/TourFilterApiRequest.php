<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;

class TourFilterApiRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'price_from' => ['nullable','numeric'],
            'price_to' => ['nullable','numeric'],
            'date_from' => ['nullable','date'],
            'date_to' => ['nullable','date'],
            'sort_by' => ['nullable',Rule::in(['price'])],
            'order' => ['nullable',Rule::in(['asc', 'desc'])],
        ];
    }


    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY));
    }


    public function messages(): array
    {
        return [
            'sort_by' => "The 'sort_by' parameter accepts only 'price' value",
            'order' => "The 'order' parameter accepts only 'asc' or 'desc' values",
        ];
    }
}
