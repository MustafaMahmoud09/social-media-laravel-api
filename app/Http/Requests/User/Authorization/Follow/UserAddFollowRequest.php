<?php

namespace App\Http\Requests\User\Authorization\Follow;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserAddFollowRequest extends FormRequest
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
            'following' => [
                'required', 'numeric', 'exists:users,id', Rule::unique('following_users', 'following_id')->where(
                    function ($query) use ($userId) {
                        return $query->where('follow_id', $userId);
                    }
                )
            ]
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }
}
