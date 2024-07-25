<?php

namespace App\Http\Requests\Admins\Authorization\System\Calls;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AdminAddCallRequest extends FormRequest
{
    use FailedValidationResponse;

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'country' => 'required|min:3|max:50|unique:call_icons,country',
            'call' => 'required|min:2|max:50|unique:call_icons,call'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }

}
