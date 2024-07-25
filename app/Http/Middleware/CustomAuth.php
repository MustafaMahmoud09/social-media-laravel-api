<?php
namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class CustomAuth
{
    public function handle(Request $request, Closure $next, $guard): Response
    {
        try {
            $token = JWTAuth::parseToken()->authenticate();

            if (auth()->guard($guard)->check()) {

                return $next($request);
            }
            return responseFormat(
                data: null,
                message: "You do not have access to more than that",
                status: 401
            );
        } catch (Exception $ex) {
            if ($ex instanceof TokenInvalidException) {

                return responseFormat(
                    data: null,
                    message: "Toke Invalid",
                    status: 401
                );
            } else if ($ex instanceof TokenExpiredException) {

                return responseFormat(
                    data: null,
                    message: "Token Expired",
                    status: 401
                );
            } else {

                return responseFormat(
                    data: null,
                    message: "Token Not Found",
                    status: 401
                );
            }
        }
    }
}
