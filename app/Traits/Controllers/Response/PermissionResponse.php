<?php

namespace App\Traits\Controllers\Response;

trait PermissionResponse
{

    function permissionResponse()
    {

        return responseFormat(
            data: null,
            message: 'You do not have permission to complete this operation',
            status: 403
        );
    }

}
