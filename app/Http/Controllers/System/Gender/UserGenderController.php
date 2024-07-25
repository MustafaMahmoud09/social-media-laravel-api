<?php

namespace App\Http\Controllers\System\Gender;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\User\Authorization\System\Gender\UserUpdateGenderRequest;
use App\Http\Resources\User\System\Gender\GenderResource;
use App\Http\Resources\User\System\Gender\UserGenderResource;
use App\Models\Gender;
use App\Models\GenderUser;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use App\Traits\Controllers\Shared\GetAllData;
use App\Traits\Controllers\Shared\SearchOnData;
use Exception;

class UserGenderController extends Controller
{
    use SelectResponse, NotFoundEditResponse, PermissionResponse, EditResponse, GetAllData, SearchOnData, ServerErrorResponse;
    function __construct()
    {
    }

    public function index()
    {
        try {

            return $this->getAllData(
                model: Gender::class,
                resource: GenderResource::class,
                type: 'genders'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end index

    public function search(SearchRequest $request)
    {
        try {

            return $this->searchOnData(
                model: Gender::class,
                resource: GenderResource::class,
                request: $request,
                type: 'genders',
                key: 'gender'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end search

    public function userGender()
    {

        try {

            $user = auth()->guard(getUserGuard())->user();

            $data = $user->userGender;
            $data = new UserGenderResource($data);

            return $this->SelectResponse(
                data: $data,
                type: 'user gender'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end userGender

    public function update(UserUpdateGenderRequest $request)
    {
        try {

            $gender = GenderUser::find($request->key);
            if (!$gender) {

                return $this->notFoundEditResponse(
                    type: 'gender',
                    key: $request->key
                );
            }

            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $userAdded = $gender->user_id;

            if ($userAdded != $userAuthId) {

                return $this->permissionResponse();
            }

            $gender->update(
                [
                    'gender_id' => $request->gender
                ]
            );

            return $this->editResponse(
                title: 'user gender',
                type: 'update'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end update


}
