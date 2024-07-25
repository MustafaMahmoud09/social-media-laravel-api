<?php

namespace App\Http\Requests\User\Authorization\System\Address;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateAddressRequest extends FormRequest
{

    use FailedValidationResponse;

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $userId = auth()->guard(getUserGuard())->user()->id;

        return [
            'key' =>  'required',
            'type' => ['required', 'numeric', 'between:0,2', Rule::unique('user_address', 'type')->where(
                    function ($query) use ($userId) {
                        return $query->where('user_id', $userId)->where('id','!=',$this->__get('key'));
                    }
                )
            ],
            'address' => 'required|exists:addresses,id'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }
}
