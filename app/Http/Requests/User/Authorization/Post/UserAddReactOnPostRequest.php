<?php

namespace App\Http\Requests\User\Authorization\Post;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserAddReactOnPostRequest extends FormRequest
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
            'type' => 'required|numeric|between:0,4',
            'post' => ['required', 'numeric', 'exists:posts,id']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }
}
