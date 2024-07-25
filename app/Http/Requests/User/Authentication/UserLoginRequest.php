<?php

namespace App\Http\Requests\User\Authentication;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UserLoginRequest extends FormRequest
{
    use FailedValidationResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "email" => "required|email",
            "password" => "required|min:8|max:40"
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }
}
