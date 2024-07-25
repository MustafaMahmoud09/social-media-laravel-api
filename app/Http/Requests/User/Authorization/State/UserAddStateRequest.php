<?php

namespace App\Http\Requests\User\Authorization\State;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserAddStateRequest extends FormRequest
{

    use FailedValidationResponse;

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'state' => 'min:1|max:500',
            'theme' => 'required|numeric|between:0,9',
            'validity' => 'required|numeric|between:0,2',
            'image' => 'image|max:30000'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }
}
