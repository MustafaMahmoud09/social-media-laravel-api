<?php

namespace App\Http\Requests\User\Authentication;


use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UserRegisterRequest extends FormRequest
{
    use FailedValidationResponse;

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            "email" => "required|email|unique:users,email",
            "name" => "required|max:80|min:5",
            "password" => "required|max:40|min:8|confirmed",
            "gender_id" => "required|exists:genders,id",
            'birth_date' => "required|date",
            "call" => "required|exists:call_icons,id",
            "phone_number" => "required|min:8|max:50|unique:phone_numbers"
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }
}
