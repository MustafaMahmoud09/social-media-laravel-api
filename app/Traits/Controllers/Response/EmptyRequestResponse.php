<?php

namespace App\Traits\Controllers\Response;

trait EmptyRequestResponse
{

    function emptyRequestResponse()
    {

        return responseFormat(
            data: null,
            message: 'all request fields is empty',
            status: 400
        );
    } //end emptyRequestResponse

} //end EmptyRequestResponse
