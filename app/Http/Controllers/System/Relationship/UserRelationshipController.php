<?php

namespace App\Http\Controllers\System\Relationship;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\User\Authorization\System\Relationship\UserAddRelationshipRequest;
use App\Http\Requests\User\Authorization\System\Relationship\UserUpdateRelationshipRequest;
use App\Http\Resources\User\System\Relationship\RelationshipResource;
use App\Http\Resources\User\System\Relationship\UserRelationshipResource;
use App\Models\Relationship;
use App\Models\UserRelationship;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use App\Traits\Controllers\Shared\GetAllData;
use App\Traits\Controllers\Shared\SearchOnData;
use Exception;

class UserRelationshipController extends Controller
{
    use SelectResponse, NotFoundEditResponse, PermissionResponse, EditResponse, GetAllData, SearchOnData, ServerErrorResponse;
    public function __construct()
    {
    }

    public function index()
    {
        try {
            return $this->getAllData(
                model: Relationship::class,
                resource: RelationshipResource::class,
                type: 'relationships'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end index

    public function search(SearchRequest $request)
    {
        try {
            return $this->searchOnData(
                model: Relationship::class,
                resource: RelationshipResource::class,
                request: $request,
                type: 'relationships',
                key: 'title'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end search

    public function userRelationship()
    {
        try {
            $user = auth()->guard(getUserGuard())->user();

            $data = $user->relationship;
            $data = new UserRelationshipResource($data);

            return $this->SelectResponse(
                data: $data,
                type: 'user relationship'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end userGender

    public function store(UserAddRelationshipRequest $request)
    {
        try {
            $user =  auth()->guard(getUserGuard())->user();
            $userAuthId = $user->id;

            $data = $user->relationship;
            if ($data) {

                return responseFormat(
                    data: null,
                    message: 'you have relationship elready',
                    status: 401
                );
            }

            $relation = UserRelationship::create(
                [
                    'relationship_id' => $request->relationship,
                    'user_id' => $userAuthId
                ]
            );

            return $this->editResponse(
                title: 'relationship',
                type: 'store'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end store

    public function update(UserUpdateRelationshipRequest $request)
    {
        try {
            $data = UserRelationship::find($request->key);
            if (!$data) {

                return $this->notFoundEditResponse(
                    type: 'relationship',
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
                    'relationship_id' => $request->relationship,
                ]
            );

            return $this->editResponse(
                title: 'relationship',
                type: 'update'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end update

    public function delete(KeyRequest $request)
    {
        try {
            $data = UserRelationship::find($request->key);
            if (!$data) {

                return $this->notFoundEditResponse(
                    type: 'relationship',
                    key: $request->key
                );
            }

            $userAddId = $data->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            $data->delete();
            return $this->editResponse(
                title: 'relationship',
                type: 'delete'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end delete

}
