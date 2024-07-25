<?php
namespace App\Traits\Validation;

use Illuminate\Http\Exceptions\HttpResponseException;

trait FailedValidationResponse
{
    public function failedValidationResponse($validator)
    {
        throw new HttpResponseException(
            responseFormat(
                data: $validator->errors(),
                message: "error in body request",
                status: 420
            )
        );
    }

}
