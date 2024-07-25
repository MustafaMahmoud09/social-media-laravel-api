<?php
namespace App\Traits\Controllers\Response;

trait SelectPageResponse
{

    public function selectPageResponse($data, $page, $type, $lastPage)
    {
        $response = [
            'current_page' => $page,
            'last_page' => $lastPage,
            'data' => $data,
            'message' => $type . ' selected in successfully',
            'status' => 200
        ];
        return response()->json($response, 200);
    }
}
