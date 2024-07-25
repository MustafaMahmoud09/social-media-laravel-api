<?php

namespace App\Http\Requests\User\Authorization\Chat;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserAddChatRequest extends FormRequest
{
    use FailedValidationResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = auth()->guard(getUserGuard())->user()->id;
        return [
           'title' => 'min:1|max:50',
           'type' => 'required|boolean',
           'users.*' => 'required|numeric|exists:users,id|not_in:'.$userId,
           'users' => 'required|array',
           'image' => 'image|max:30000'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }

}
