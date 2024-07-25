<?php

namespace App\Http\Requests\Admins\Authorization\System\Socials;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateSocialRequest extends FormRequest
{
    use FailedValidationResponse;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'required|numeric',
            'name' => 'required|min:3|max:50|unique:socials,title,' . $this->__get('key'),
            'image' => 'required|image',
            'description' => 'min:10|max:500'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }
}
