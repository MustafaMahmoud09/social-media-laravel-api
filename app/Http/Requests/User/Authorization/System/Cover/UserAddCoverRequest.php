<?php

namespace App\Http\Requests\User\Authorization\System\Cover;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserAddCoverRequest extends FormRequest
{
    use FailedValidationResponse;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'min:1|max:100',
            'image' => 'required|image|max:30000'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }

}
