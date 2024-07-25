<?php

namespace App\Traits\Controllers\Response;

trait LoginResponse
{
    
    function loginResponse($type, $token)
    {

        return responseFormat(
            data: $token,
            message: $type . " logged in succcessfully",
            status: 200
        );
    }

}
