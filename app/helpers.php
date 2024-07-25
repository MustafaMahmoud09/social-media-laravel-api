<?php

if (!function_exists('responseFormat')) {
    function responseFormat($data = [],   $message,   $status,   $headers = [])
    {
        $response = [
            "data" => $data,
            "message" => $message,
            "status" => $status
        ];

        return response()->json(
            $response,
            $status,
            $headers
        );
    }
}

if (!function_exists('getAdminGuard')) {
    function getAdminGuard()
    {
        return 'admin-api';
    }
}

if (!function_exists('getUserGuard')) {
    function getUserGuard()
    {
        return 'user-api';
    }
}

if (!function_exists('getDomain')) {
    function getDomain()
    {
        return 'http://127.0.0.1:8000/';
    }
}

if (!function_exists('getBaseUrlFile')) {
    function getBaseUrlFile()
    {
        return getDomain() . 'storage/';
    }
}
