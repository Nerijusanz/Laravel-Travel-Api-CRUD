<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Models\Travel;

class TravelStoreApiRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {

        $numberOfDays = request()->input('number_of_days');

        $numberOfNightsRule = (isset($numberOfDays) && is_numeric($numberOfDays) && $numberOfDays > 0 )? ['required', 'integer','min:0','lt:number_of_days'] : ['required','integer','min:0'];

        return [
            'is_public' => ['required','boolean'],
            'name' => ['required','string','min:2','max:255', Rule::unique(Travel::class)->whereNull('deleted_at')],
            'number_of_days' => ['required', 'integer','min:1'],
            'number_of_nights' => $numberOfNightsRule,
            'description' => ['nullable','string'],
        ];

    }

}
