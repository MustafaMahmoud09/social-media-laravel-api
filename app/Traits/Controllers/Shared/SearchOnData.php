<?php

namespace App\Traits\Controllers\Shared;
use App\Traits\Controllers\Response\SelectResponse;

trait SearchOnData
{
    use SelectResponse;
    function searchOnData($model,$resource,$request,$type,$key)
    {

        $data = $model::where($key, 'like', '%' . $request->search_key . '%')->get();
        $data = $resource::collection($data);

        return $this->SelectResponse(
            data: $data,
            type: $type
        );
    }
}
