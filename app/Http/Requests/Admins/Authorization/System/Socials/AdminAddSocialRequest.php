<?php

namespace App\Http\Requests\Admins\Authorization\System\Socials;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AdminAddSocialRequest extends FormRequest
{
    use FailedValidationResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
           'name' => 'required|min:3|max:50|unique:socials,title',
           'image' => 'required|image',
           'description' => 'min:10|max:500'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }
}
