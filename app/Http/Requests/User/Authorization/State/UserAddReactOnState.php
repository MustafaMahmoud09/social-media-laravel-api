<?php

namespace App\Http\Requests\User\Authorization\State;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserAddReactOnState extends FormRequest
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
            'state' => [
                'required','exists:states,id', Rule::unique('state_user_watches','state_id')->where(
                    function ($query) use ($userId) {
                        return $query->where('user_id', $userId);
                    }
                )
            ],
            'type' => 'numeric|between:0,4',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }
}
