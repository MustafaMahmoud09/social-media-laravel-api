<?php

namespace App\Traits\Controllers\Response;

trait RegisterResponse
{

    function registerResponse($type)
    {
        return responseFormat(
            data: null,
            message: $type . " registered in succcessfully",
            status: 201
        );
    }
}
