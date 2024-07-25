<?php

namespace App\Http\Controllers\System\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\User\Authorization\System\Profile\UserAddProfileRequest;
use App\Http\Requests\User\Authorization\System\Profile\UserUpdateProfileRequest;
use App\Http\Resources\User\System\Profile\UserProfileResource;
use App\Models\Profile;
use App\Traits\Controllers\File\DeleteFile;
use App\Traits\Controllers\File\UpdateFile;
use App\Traits\Controllers\File\UploadFile;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    use UploadFile, EditResponse, NotFoundEditResponse, PermissionResponse, SelectResponse, UpdateFile, DeleteFile, ServerErrorResponse;

    public function index()
    {
        try {
            $user = auth()->guard(getUserGuard())->user();

            $data = $user->profiles()->orderBy('created_at', 'desc')->get();
            $data = UserProfileResource::collection($data);

            return $this->SelectResponse(
                data: $data,
                type: 'profiles'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end index

    public function lastProfile()
    {
        try {
            $user = auth()->guard(getUserGuard())->user();

            $profile = $user->profiles()->orderBy('created_at', 'desc')->first();
            $profile = new UserProfileResource($profile);

            return $this->SelectResponse(
                data: $profile,
                type: 'profile'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end lastCover

    public function store(UserAddProfileRequest $request)
    {
        try {
            $userId = auth()->guard(getUserGuard())->user()->id;
            $path = $this->uploadFile(
                request: $request,
                key: 'image',
                folder: 'profile/' . $userId,
            );

            $profile = Profile::create(
                [
                    'path' => $path,
                    'user_id' => $userId,
                    'title' => $request->title
                ]
            );

            return $this->editResponse(
                title: 'profile',
                type: 'store'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end store

    public function edit(KeyRequest $request)
    {
        try {
            $profile = Profile::find($request->key);
            if (!$profile) {

                return $this->notFoundEditResponse(
                    type: 'profile',
                    key: $request->key
                );
            }

            $userAddId = $profile->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            $profile = new UserProfileResource($profile);
            return $this->SelectResponse(
                data: $profile,
                type: 'profile'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end edit

    public function update(UserUpdateProfileRequest $request)
    {
        try {
            $profile = Profile::find($request->key);
            if (!$profile) {

                return $this->notFoundEditResponse(
                    type: 'profile',
                    key: $request->key
                );
            }

            $userAddId = $profile->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            $path = $this->updateFile(
                oldPath: $profile->path,
                request: $request,
                key: 'image',
                folder: 'profile/' . $userAuthId
            );

            $title = $request->title;
            if (!$title) {

                $title = $profile->title;
            }

            $profile->update(
                [
                    'title' => $title,
                    'path' => $path
                ]
            );

            return $this->editResponse(
                title: 'profile',
                type: 'update'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end update

    public function delete(KeyRequest $request)
    {
        try {
            $profile = Profile::find($request->key);
            if (!$profile) {

                return $this->notFoundEditResponse(
                    type: 'profile',
                    key: $request->key
                );
            }

            $userAddId = $profile->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            $profile->delete();
            $this->deleteFile(
                path: $profile->path
            );

            return $this->editResponse(
                title: 'profile',
                type: 'delete'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end delete

}
