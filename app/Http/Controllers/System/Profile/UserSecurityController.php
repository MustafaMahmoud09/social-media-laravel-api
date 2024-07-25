<?php

namespace App\Http\Controllers\System\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Authorization\Security\UserUpdateEmailRequest;
use App\Http\Requests\User\Authorization\Security\UserUpdateNameRequest;
use App\Http\Requests\User\Authorization\Security\UserUpdatePasswordRequest;
use App\Http\Resources\User\Profile\ProfileUserResource;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\SelectResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSecurityController extends Controller
{
    use EditResponse, SelectResponse, ServerErrorResponse;

    public function index()
    {
        try {
            //get user auth
            $user = auth()->guard(getUserGuard())->user();
            $user = new ProfileUserResource($user);

            //success response
            return $this->SelectResponse(
                data: $user,
                type: 'selected'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    }

    public function updateEmail(UserUpdateEmailRequest $request)
    {
        try {
            //get user auth id
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            //update email
            DB::table('users')
                ->where('id', $userAuthId)
                ->update(
                    [
                        'email' => $request->email
                    ]
                );

            //success response
            return $this->editResponse(
                title: 'email',
                type: 'update'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end updateEmail

    public function updatePassword(UserUpdatePasswordRequest $request)
    {
        try {
            //check user enter old password success or no
            $user = auth()->guard(getUserGuard())->user();
            $userAuthId = $user->id;
            $passwordCheck = Hash::check($request->old_password, $user->password);
            if (!$passwordCheck) {

                return responseFormat(
                    data: null,
                    message: 'old password input not equal old password',
                    status: 403
                );
            }

            //update password
            DB::table('users')
                ->where('id', $userAuthId)
                ->update(
                    [
                        'password' => Hash::make($request->password)
                    ]
                );

            //success response
            return $this->editResponse(
                title: 'password',
                type: 'update'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end updatePassword

    public function updateName(UserUpdateNameRequest $request)
    {
        try {
            //get user auth id
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            //update password
            DB::table('users')
                ->where('id', $userAuthId)
                ->update(
                    [
                        'name' => $request->name
                    ]
                );

            //success response
            return $this->editResponse(
                title: 'name',
                type: 'update'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end updateName

}//end SecurityController
