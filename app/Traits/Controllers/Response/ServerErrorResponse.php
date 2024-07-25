<?php

namespace App\Traits\Controllers\Response;

trait ServerErrorResponse
{

    function serverErrorResponse()
    {

        return responseFormat(
            data: null,
            message: "There is a problem with our server",
            status: 500
        );
    }
}
