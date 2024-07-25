<?php

namespace App\Http\Controllers\System\PhoneNumber;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Authorization\System\Calls\AdminAddCallRequest;
use App\Http\Requests\Admins\Authorization\System\Calls\AdminUpdateCallRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\Admin\System\Call\CallResource;
use App\Http\Resources\Admin\System\Call\SingleCallResource;
use App\Models\CallIcon;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\RestoreResponse;
use App\Traits\Controllers\Response\SelectResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use App\Traits\Controllers\Shared\GetAllData;
use App\Traits\Controllers\Shared\SearchOnData;
use Exception;
use Illuminate\Http\Request;

class AdminCallIconController extends Controller
{
    use PermissionResponse, NotFoundEditResponse, EditResponse, SelectResponse, RestoreResponse, GetAllData, SearchOnData, ServerErrorResponse;

    public function index()
    {

        try {

            if (auth()->guard(getAdminGuard())->user()->can('show-call')) {

                return $this->getAllData(
                    model: CallIcon::class,
                    resource: CallResource::class,
                    type: 'call icons'
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

            if (auth()->guard(getAdminGuard())->user()->can('show-call')) {

                return $this->searchOnData(
                    model: CallIcon::class,
                    resource: CallResource::class,
                    request: $request,
                    type: 'call icons',
                    key: 'country'
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

            if (auth()->guard(getAdminGuard())->user()->can('show-call')) {

                $data = CallIcon::onlyTrashed()->with('admin')->get();
                $data = CallResource::collection($data);

                return $this->SelectResponse(
                    data: $data,
                    type: 'call icons'
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
            if (auth()->guard(getAdminGuard())->user()->can('show-call')) {

                $data = CallIcon::onlyTrashed()->with('admin')->where('country', 'like', '%' . $request->search_key . '%')->get();
                $data = CallResource::collection($data);

                return $this->SelectResponse(
                    data: $data,
                    type: 'call icons'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end searchInArchive

    public function store(AdminAddCallRequest $request)
    {
        try {

            if (auth()->guard(getAdminGuard())->user()->can('manage-call')) {

                $adminId = auth()->guard(getAdminGuard())->user()->id;
                $address = CallIcon::create(
                    [
                        'country' => $request->country,
                        'call' => $request->call,
                        'admin_id' => $adminId
                    ]
                );

                return $this->editResponse(
                    title: 'call icons',
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

            if (auth()->guard(getAdminGuard())->user()->can('manage-call')) {

                $data = CallIcon::find($id);

                if (!$data) {

                    return $this->notFoundEditResponse(
                        type: 'call icon',
                        key: $id
                    );
                }

                $adminAddGenderId = $data->admin_id;
                $adminAuthId =  auth()->guard(getAdminGuard())->user()->id;

                if ($adminAddGenderId != $adminAuthId) {

                    return $this->permissionResponse();
                }

                $data = new SingleCallResource($data);

                return $this->SelectResponse(
                    data: $data,
                    type: 'call icon'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end edit

    public function update(AdminUpdateCallRequest $request)
    {
        try {

            if (auth()->guard(getAdminGuard())->user()->can('manage-call')) {

                $call = CallIcon::find($request->key);

                if (!$call) {

                    return $this->notFoundEditResponse(
                        type: 'call icon',
                        key: $request->key
                    );
                }

                $adminAuthId =  auth()->guard(getAdminGuard())->user()->id;
                $adminAddGenderid = $call->admin_id;

                if ($adminAuthId != $adminAddGenderid) {

                    return $this->permissionResponse();
                }

                $call->update([
                    'call' => $request->call,
                    'country' => $request->country
                ]);

                return $this->editResponse(
                    title: 'call icon',
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
            if (auth()->guard(getAdminGuard())->user()->can('manage-call')) {

                $call = CallIcon::find($id);
                if (!$call) {

                    return $this->notFoundEditResponse(
                        type: 'call icon',
                        key: $id
                    );
                }

                $adminAuthId =  auth()->guard(getAdminGuard())->user()->id;
                $adminAddGenderid = $call->admin_id;
                if ($adminAuthId != $adminAddGenderid) {

                    return $this->permissionResponse();
                }

                $call->delete();

                return $this->editResponse(
                    title: 'call icon',
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
            if (auth()->guard(getAdminGuard())->user()->can('manage-call')) {

                $call = CallIcon::onlyTrashed()->find($id);
                if (!$call) {

                    return $this->notFoundEditResponse(
                        type: 'call icon',
                        key: $id
                    );
                }

                $adminAuthId =  auth()->guard(getAdminGuard())->user()->id;
                $adminAddGenderId = $call->admin_id;
                if ($adminAuthId != $adminAddGenderId) {

                    return $this->permissionResponse();
                }

                $call->forceDelete();

                return $this->editResponse(
                    title: 'call icon',
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
            if (auth()->guard(getAdminGuard())->user()->can('manage-call')) {

                $call = CallIcon::onlyTrashed()->find($id);
                if (!$call) {

                    return $this->notFoundEditResponse(
                        type: 'call icon',
                        key: $id
                    );
                }

                $call->restore();

                return $this->restoreResponse(
                    title: 'call icon'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end restore
}
