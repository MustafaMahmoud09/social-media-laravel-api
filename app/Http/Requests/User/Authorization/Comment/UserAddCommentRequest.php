<?php

namespace App\Http\Requests\User\Authorization\Comment;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserAddCommentRequest extends FormRequest
{

    use FailedValidationResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comment' => 'min:1|max:150',
            'post' => 'required|numeric|exists:posts,id',
            'replay' => 'numeric|exists:comments,id',
            'images' => 'array',
            'images.*' => 'image|max:10000'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }

}
