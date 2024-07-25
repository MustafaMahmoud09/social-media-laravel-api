<?php

namespace App\Http\Requests\User\Authorization\Post;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserAddPostRequest extends FormRequest
{
    use FailedValidationResponse;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'post' => 'min:1|max:150',
            'share' => 'numeric|exists:posts,id',
            'images.*' => 'image|max:15000',
            'images' => 'array'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }
}
