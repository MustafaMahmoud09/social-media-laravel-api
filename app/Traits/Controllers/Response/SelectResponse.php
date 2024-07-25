<?php

namespace App\Traits\Controllers\Response;

trait SelectResponse
{

    function SelectResponse($data, $type)
    {
        return responseFormat(
            data: $data,
            message: $type . ' selected in succcessfully',
            status: 200
        );
    }
    
}
