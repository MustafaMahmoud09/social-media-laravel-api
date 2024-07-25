<?php

namespace App\Traits\Controllers\Response;

trait NotFoundEditResponse
{

    function notFoundEditResponse($type, $key)
    {

        return responseFormat(
            data: null,
            message: 'not found ' . $type . ' have id = ' . $key,
            status: 404
        );
    }
}
