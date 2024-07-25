<?php

namespace App\Http\Requests\Admins\Authorization\System\Genders;


use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class AdminUpdateGenderRequest extends FormRequest
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
            'gender' => 'required|min:3|max:50|unique:genders,gender,'.$this->__get('key')
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }

}
