<?php

namespace App\Http\Requests\User\Authorization\Security;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateEmailRequest extends FormRequest
{
    use FailedValidationResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userAuthId = auth()->guard(getUserGuard())->user()->id;
        return [
           'email' => 'required|email|unique:users,email,'.$userAuthId
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }
}
