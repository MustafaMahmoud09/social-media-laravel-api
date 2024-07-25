<?php

namespace App\Traits\Controllers\Response;

trait EditResponse
{

    function editResponse($title, $type)
    {

        return responseFormat(
            data: null,
            message: $title . ' ' . $type . ' in succcessfully',
            status: 201
        );
    }
}
