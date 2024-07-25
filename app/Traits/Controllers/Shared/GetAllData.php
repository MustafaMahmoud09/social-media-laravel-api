<?php

namespace App\Traits\Controllers\Shared;
use App\Traits\Controllers\Response\SelectResponse;

trait GetAllData
{
    use SelectResponse;
    function getAllData($model, $resource, $type)
    {
        $data = $model::get();
        $data = $resource::collection($data);

        return $this->selectResponse(
            data: $data,
            type: $type
        );
    }
}
