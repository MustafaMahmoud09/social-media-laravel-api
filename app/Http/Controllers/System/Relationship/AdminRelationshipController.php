<?php

namespace App\Http\Controllers\System\Relationship;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Authorization\System\Relationships\AdminAddRelationshipRequest;
use App\Http\Requests\Admins\Authorization\System\Relationships\AdminUpdateRelationshipRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\Admin\System\Relationship\RelationshipResource;
use App\Http\Resources\Admin\System\Relationship\SingleRelationshipResource;
use App\Models\Relationship;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\RestoreResponse;
use App\Traits\Controllers\Response\SelectResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use App\Traits\Controllers\Shared\GetAllData;
use App\Traits\Controllers\Shared\SearchOnData;
use Exception;

class AdminRelationshipController extends Controller
{
    use PermissionResponse, NotFoundEditResponse, EditResponse, SelectResponse, RestoreResponse, GetAllData, SearchOnData, ServerErrorResponse;

    public function index()
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('show-relationship')) {

                return $this->getAllData(
                    model: Relationship::class,
                    resource: RelationshipResource::class,
                    type: 'relationships'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end index

    public function search(SearchRequest $request)
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('show-relationship')) {

                return $this->searchOnData(
                    model: Relationship::class,
                    resource: RelationshipResource::class,
                    request: $request,
                    type: 'relationships',
                    key: 'title'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end search

    public function showArchives()
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('show-relationship')) {

                $data = Relationship::onlyTrashed()->with('admin')->get();
                $data = RelationshipResource::collection($data);

                return $this->SelectResponse(
                    data: $data,
                    type: 'relationship'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end showArchives

    public function searchInArchive(SearchRequest $request)
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('show-relationship')) {

                $data = Relationship::onlyTrashed()->with('admin')->where('title', 'like', '%' . $request->search_key . '%')->get();
                $data = RelationshipResource::collection($data);

                return $this->SelectResponse(
                    data: $data,
                    type: 'relationship'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end searchInArchive

    public function store(AdminAddRelationshipRequest $request)
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('manage-relationship')) {

                $adminId = auth()->guard(getAdminGuard())->user()->id;
                $relationship = Relationship::create(
                    [
                        'title' => $request->title,
                        'admin_id' => $adminId
                    ]
                );

                return $this->editResponse(
                    title: 'relationship',
                    type: 'insert'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end store

    public function edit($id)
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('manage-relationship')) {

                $data = Relationship::find($id);

                if (!$data) {

                    return $this->notFoundEditResponse(
                        type: 'relationship',
                        key: $id
                    );
                }

                $adminAddGenderId = $data->admin_id;
                $adminAuthId =  auth()->guard(getAdminGuard())->user()->id;

                if ($adminAddGenderId != $adminAuthId) {

                    return $this->permissionResponse();
                }

                $data = new SingleRelationshipResource($data);

                return $this->SelectResponse(
                    data: $data,
                    type: 'relationship'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end edit

    public function update(AdminUpdateRelationshipRequest $request)
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('manage-relationship')) {

                $relationship = Relationship::find($request->key);

                if (!$relationship) {

                    return $this->notFoundEditResponse(
                        type: 'relationship',
                        key: $request->key
                    );
                }

                $adminAuthId =  auth()->guard(getAdminGuard())->user()->id;
                $adminAddGenderid = $relationship->admin_id;

                if ($adminAuthId != $adminAddGenderid) {

                    return $this->permissionResponse();
                }

                $relationship->update([
                    'title' => $request->title
                ]);

                return $this->editResponse(
                    title: 'relationship',
                    type: 'update'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end update

    public function destroy($id)
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('manage-relationship')) {

                $relationship = Relationship::find($id);
                if (!$relationship) {

                    return $this->notFoundEditResponse(
                        type: 'relationship',
                        key: $id
                    );
                }

                $adminAuthId =  auth()->guard(getAdminGuard())->user()->id;
                $adminAddGenderid = $relationship->admin_id;
                if ($adminAuthId != $adminAddGenderid) {

                    return $this->permissionResponse();
                }

                $relationship->delete();

                return $this->editResponse(
                    title: 'relationship',
                    type: 'delete'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end destroy

    public function destroyArchives($id)
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('manage-relationship')) {

                $relationship = Relationship::onlyTrashed()->find($id);
                if (!$relationship) {

                    return $this->notFoundEditResponse(
                        type: 'relationship',
                        key: $id
                    );
                }

                $adminAuthId =  auth()->guard(getAdminGuard())->user()->id;
                $adminAddGenderId = $relationship->admin_id;
                if ($adminAuthId != $adminAddGenderId) {

                    return $this->permissionResponse();
                }

                $relationship->forceDelete();

                return $this->editResponse(
                    title: 'relationship',
                    type: 'delete'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end destroyArchives

    public function restore($id)
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('manage-relationship')) {

                $relationship = Relationship::onlyTrashed()->find($id);
                if (!$relationship) {

                    return $this->notFoundEditResponse(
                        type: 'relationship',
                        key: $id
                    );
                }

                $relationship->restore();

                return $this->restoreResponse(
                    title: 'relationship'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end restore
}
