<?php

namespace App\Http\Requests\Admins\Authorization\System\Genders;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;

class AdminAddGenderRequest extends FormRequest
{

    use FailedValidationResponse;
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'gender' => 'required|min:3|max:50|unique:genders,gender'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }

}
