<?php

namespace App\Traits\Controllers\Response;

trait RestoreResponse
{

    function restoreResponse($title)
    {
       return responseFormat(
            data: null,
            message: $title." restored in succcessfully",
            status: '201'
        );
    }
}
