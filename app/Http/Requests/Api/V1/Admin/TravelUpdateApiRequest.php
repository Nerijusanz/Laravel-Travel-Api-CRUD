<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use App\Models\User;
use App\Models\Travel;

class TravelUpdateApiRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'user_id' => ['required','integer', Rule::exists(User::class,'id')],
            'is_public' => ['required','boolean'],
            'name' => ['required','string','min:2','max:255', Rule::unique(Travel::class)->whereNull('deleted_at')->ignore($this->travel)],
            'number_of_days' => ['required', 'integer','min:1'],
            'number_of_nights' => ['required', 'integer','lt:number_of_days'],
            'description' => ['nullable','string'],
        ];
    }

}
