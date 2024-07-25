<?php

namespace App\Http\Controllers\System\PhoneNumber;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\User\Authorization\System\PhoneNumber\UserAddPhoneaNumberRequest;
use App\Http\Requests\User\Authorization\System\PhoneNumber\UserUpdatePhoneNumberRequest;
use App\Http\Resources\User\System\PhoneNumber\IconResource;
use App\Http\Resources\User\System\PhoneNumber\PhoneNumberResource;
use App\Models\CallIcon;
use App\Models\PhoneNumber;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use App\Traits\Controllers\Shared\GetAllData;
use App\Traits\Controllers\Shared\SearchOnData;
use Exception;

class UserPhoneNumberController extends Controller
{
    use GetAllData, SearchOnData, EditResponse, SelectResponse, NotFoundEditResponse, PermissionResponse, ServerErrorResponse;

    public function index()
    {
        try {
            return $this->getAllData(
                model: CallIcon::class,
                resource: IconResource::class,
                type: 'call icons'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end index

    public function search(SearchRequest $request)
    {
        try {
            return $this->searchOnData(
                model: CallIcon::class,
                resource: IconResource::class,
                request: $request,
                type: 'call icons',
                key: 'country'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end search

    public function userPhoneNumber()
    {
        try {
            $user = auth()->guard(getUserGuard())->user();

            $data = $user->phoneNumbers;
            $data = PhoneNumberResource::collection($data);

            return $this->SelectResponse(
                data: $data,
                type: 'phone number'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end userGender

    public function searchOnPhoneNumber(SearchRequest $request)
    {
        try {
            $user = auth()->guard(getUserGuard())->user();

            $data = $user->phoneNumbers()->where('phone_number', 'like', '%' . $request->search_key . '%')->get();
            $data = PhoneNumberResource::collection($data);

            return $this->SelectResponse(
                data: $data,
                type: 'phone number'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end userGender

    public function store(UserAddPhoneaNumberRequest $request)
    {
        try {
            $userAuthId = auth()->guard(getUserGuard())->user()->id;

            $phoneNumber = PhoneNumber::create(
                [
                    'phone_number' => $request->phone,
                    'call_icon_id' => $request->call,
                    'user_id' => $userAuthId
                ]
            );

            return $this->editResponse(
                title: 'phone number',
                type: 'store'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end store

    public function edit(KeyRequest $request)
    {
        try {
            $data = PhoneNumber::find($request->key);
            if (!$data) {

                return $this->notFoundEditResponse(
                    type: 'phone number',
                    key: $request->key
                );
            }

            $userAddId = $data->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;

            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            $data = new PhoneNumberResource($data);
            return $this->SelectResponse(
                data: $data,
                type: 'phone number'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end edit

    public function update(UserUpdatePhoneNumberRequest $request)
    {
        try {
            $data = PhoneNumber::find($request->key);
            if (!$data) {

                return $this->notFoundEditResponse(
                    type: 'phone number',
                    key: $request->key
                );
            }

            $userAddId = $data->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            $data->update(
                [
                    'phone_number' => $request->phone,
                    'call_icon_id' => $request->call
                ]
            );

            return $this->editResponse(
                title: 'phone number',
                type: 'update'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end update

    public function delete(KeyRequest $request)
    {
        try {
            $data = PhoneNumber::find($request->key);
            if (!$data) {

                return $this->notFoundEditResponse(
                    type: 'phone number',
                    key: $request->key
                );
            }

            $userAddId = $data->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            $phoneNumberUser = PhoneNumber::where('user_id', $userAuthId)->get();

            if (count($phoneNumberUser) <= 1) {

                return responseFormat(
                    data: null,
                    message: "you have single phone number",
                    status: 400
                );
            }

            $data->delete();
            return $this->editResponse(
                title: 'phone number',
                type: 'delete'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end delete


}
