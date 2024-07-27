<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

use App\Models\User;
use App\Models\Travel;
use Illuminate\Validation\Rule;

class TourStoreApiRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {

        return [
            'user_id' => ['required','integer', Rule::exists(User::class,'id')],
            'travel_id' => ['required','integer', Rule::exists(Travel::class,'id')],
            'name' => ['required','string','min:2','max:255'],
            'price' => ['required', 'numeric'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
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
