<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Authentication\AdminLoginRequest;
use App\Traits\Controllers\Response\LoginResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    use LoginResponse, ServerErrorResponse;
    public function login(AdminLoginRequest $request)
    {
        try {

            $token = Auth::guard(getAdminGuard())->attempt(
                [
                    "email" => $request->email,
                    "password" => $request->password
                ]
            );

            if (!empty($token)) {

                return $this->loginResponse(
                    type: 'admin',
                    token: $token
                );
            }

            return responseFormat(
                data: null,
                message: "Invalid details",
                status: 404
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }

    }

    public function logout()
    {

        try {

            auth()->guard(getAdminGuard())->logout();

            return responseFormat(
                data: null,
                message: "User logged out successfully",
                status: 200
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }

    }

}
