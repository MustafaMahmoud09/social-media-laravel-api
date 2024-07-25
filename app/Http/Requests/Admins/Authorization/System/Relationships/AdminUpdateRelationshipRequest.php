<?php

namespace App\Http\Requests\Admins\Authorization\System\Relationships;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class AdminUpdateRelationshipRequest extends FormRequest
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
            'title' => 'required|min:3|max:50|unique:relationships,title,' . $this->__get('key')
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }
}
