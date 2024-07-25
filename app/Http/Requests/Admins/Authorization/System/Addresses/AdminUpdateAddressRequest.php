<?php

namespace App\Http\Requests\Admins\Authorization\System\Addresses;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class AdminUpdateAddressRequest extends FormRequest
{
    use FailedValidationResponse;

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'key' => 'required|numeric',
            'position' => 'required|min:3|max:50|unique:addresses,position,'.$this->__get('key'),
            'zip_code' => 'required|numeric|unique:addresses,zip_code,'.$this->__get('key')
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }
}
