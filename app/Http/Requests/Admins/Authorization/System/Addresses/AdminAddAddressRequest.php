<?php

namespace App\Http\Requests\Admins\Authorization\System\Addresses;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;

class AdminAddAddressRequest extends FormRequest
{
    use FailedValidationResponse;

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'position' => 'required|min:3|max:50|unique:addresses,position',
            'zip_code' => 'required|unique:addresses,zip_code|numeric'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }
}
