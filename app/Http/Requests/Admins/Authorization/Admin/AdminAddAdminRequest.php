<?php

namespace App\Http\Requests\Admins\Authorization\Admin;

use App\Traits\Validation\FailedValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AdminAddAdminRequest extends FormRequest
{
    use FailedValidationResponse;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|min:5|max:80',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:9|max:30',
            'birth_date' => 'required|date',
            'phone_number' => 'required|unique:admins,phone',
            'ssn' => 'required|numeric|unique:admins,ssn',
            'gender' => 'required',
            'description' => 'min:1|max:200',
            'admin_type' => 'required|boolean'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedValidationResponse($validator);
    }
}
