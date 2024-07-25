<?php

namespace App\Http\Requests\User\Authorization\Chat;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserAddMemberToChatRequest extends FormRequest
{
    use FailedValidationResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $chatId = $this->__get('chat');
        return [
            'chat' => 'required|numeric',
            'users.*' => [
                'required', 'exists:users,id', Rule::unique('chat_users', 'user_id')->where(
                    function ($query) use ($chatId) {
                        return $query->where('chat_id', $chatId);
                    }
                )
            ],
            'users' => 'required|array'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }
}
