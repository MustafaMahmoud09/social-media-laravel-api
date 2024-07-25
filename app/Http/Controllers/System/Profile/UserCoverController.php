<?php

namespace App\Http\Controllers\System\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\User\Authorization\System\Cover\UserAddCoverRequest;
use App\Http\Requests\User\Authorization\System\Cover\UserUpdateCoverRequest;
use App\Http\Resources\User\System\Cover\UserCoverResource;
use App\Models\Cover;
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

class UserCoverController extends Controller
{
    use UploadFile, EditResponse, NotFoundEditResponse, PermissionResponse, SelectResponse, UpdateFile, DeleteFile, ServerErrorResponse;

    public function index()
    {
        try {
            $user = auth()->guard(getUserGuard())->user();

            $data = $user->covers()->orderBy('created_at', 'desc')->get();
            $data = UserCoverResource::collection($data);

            return $this->SelectResponse(
                data: $data,
                type: 'covers'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end index

    public function lastCover()
    {
        try {
            $user = auth()->guard(getUserGuard())->user();

            $cover = $user->covers()->orderBy('created_at', 'desc')->first();
            $cover = new UserCoverResource($cover);

            return $this->SelectResponse(
                data: $cover,
                type: 'cover'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end lastCover

    public function store(UserAddCoverRequest $request)
    {
        try {
            $userId = auth()->guard(getUserGuard())->user()->id;
            $path = $this->uploadFile(
                request: $request,
                key: 'image',
                folder: 'cover/' . $userId,
            );

            $cover = Cover::create(
                [
                    'path' => $path,
                    'user_id' => $userId,
                    'title' => $request->title
                ]
            );

            return $this->editResponse(
                title: 'cover',
                type: 'store'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end store

    public function edit(KeyRequest $request)
    {
        try {
            $cover = Cover::find($request->key);
            if (!$cover) {

                return $this->notFoundEditResponse(
                    type: 'cover',
                    key: $request->key
                );
            }

            $userAddId = $cover->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            $cover = new UserCoverResource($cover);
            return $this->SelectResponse(
                data: $cover,
                type: 'cover'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end edit

    public function update(UserUpdateCoverRequest $request)
    {
        try {
            $cover = Cover::find($request->key);
            if (!$cover) {

                return $this->notFoundEditResponse(
                    type: 'cover',
                    key: $request->key
                );
            }

            $userAddId = $cover->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            $path = $this->updateFile(
                oldPath: $cover->path,
                request: $request,
                key: 'image',
                folder: 'cover/' . $userAuthId
            );

            $title = $request->title;
            if (!$title) {

                $title = $cover->title;
            }

            $cover->update(
                [
                    'title' => $title,
                    'path' => $path
                ]
            );

            return $this->editResponse(
                title: 'cover',
                type: 'update'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end update

    public function delete(KeyRequest $request)
    {
        try {
            $cover = Cover::find($request->key);
            if (!$cover) {

                return $this->notFoundEditResponse(
                    type: 'cover',
                    key: $request->key
                );
            }

            $userAddId = $cover->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            $cover->delete();
            $this->deleteFile(
                path: $cover->path
            );

            return $this->editResponse(
                title: 'cover',
                type: 'delete'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end delete
}
