<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Authentication\UserLoginRequest;
use App\Http\Requests\User\Authentication\UserRegisterRequest;
use App\Models\GenderUser;
use App\Models\PhoneNumber;
use App\Models\User;
use App\Traits\Controllers\Response\LoginResponse;
use App\Traits\Controllers\Response\RegisterResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use LoginResponse, RegisterResponse, ServerErrorResponse;

    public function register(UserRegisterRequest $request)
    {
        try {
            // Create a new user with the provided name, email, password, and birth date
            $user = User::create(
                [
                    "name" => $request->name,
                    "email" => $request->email,
                    "password" => Hash::make($request->password), // Hash the password before storing it
                    "birth_date" => $request->birth_date
                ]
            );

            // Associate the user's phone number with the new user
            $phoneNumber = PhoneNumber::create(
                [
                    "phone_number" => $request->phone_number,
                    "user_id" => $user->id, // Link to the newly created user
                    "call_icon_id" => $request->call
                ]
            );

            // Associate the user's gender with the new user
            $gender = GenderUser::create(
                [
                    "gender_id" => $request->gender_id,
                    "user_id" => $user->id // Link to the newly created user
                ]
            );

            // Return a success response for the user registration
            return $this->registerResponse(
                type: 'user'
            );
        } catch (Exception $ex) {
            // Handle any errors and return a server error response
            return $this->serverErrorResponse();
        }
    } // end register


    public function login(UserLoginRequest $request)
    {
        try {
            // Attempt to authenticate the user using the provided email and password
            $token = Auth::guard(getUserGuard())->attempt(
                [
                    "email" => $request->email,
                    "password" => $request->password
                ]
            );

            // If authentication is successful and a token is generated
            if (!empty($token)) {
                return $this->loginResponse(
                    type: 'user',
                    token: $token
                );
            }

            // If authentication fails, return a response indicating invalid details
            return responseFormat(
                data: null,
                message: "Invalid details",
                status: 404
            );
        } catch (Exception $ex) {
            // Handle any errors and return a server error response
            return $this->serverErrorResponse();
        }
    } // end login


    public function logout()
    {
        try {
            auth()->guard(getUserGuard())->logout();

            return responseFormat(
                data: null,
                message: "User logged out successfully",
                status: 200
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end logout

}//end UserController
