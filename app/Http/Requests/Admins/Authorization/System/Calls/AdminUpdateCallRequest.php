<?php

namespace App\Http\Requests\Admins\Authorization\System\Calls;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AdminUpdateCallRequest extends FormRequest
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
            'country' => 'required|min:3|max:50|unique:call_icons,country,' . $this->__get('key'),
            'call' => 'required|min:2|max:50|unique:call_icons,call,' . $this->__get('key')
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }
}
