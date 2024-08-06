<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

use App\Models\User;
use App\Models\Travel;
use Illuminate\Validation\Rule;

class TravelStoreApiRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'is_public' => ['required','boolean'],
            'name' => ['required','string','min:2','max:255', Rule::unique(Travel::class)->whereNull('deleted_at')],
            'number_of_days' => ['required', 'integer'],
            'number_of_nights' => ['required', 'integer','lt:number_of_days'],
            'description' => ['nullable','string'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json(['errors' => $validator->errors()], Response::HTTP_UNPROCESSABLE_ENTITY));
    }


    public function messages(): array
    {
        return [

        ];
    }
}
