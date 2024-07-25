<?php

namespace App\Http\Requests\User\Authorization\Message;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserAddMessageRequest extends FormRequest
{
    use FailedValidationResponse;

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'message' => 'min:1|max:5000',
            'chat' => 'required|numeric|exists:chats,id',
            'replay' => 'numeric|exists:messages,id',
            'images.*' => 'image|max:30000',
            'images' => 'array'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }
}
