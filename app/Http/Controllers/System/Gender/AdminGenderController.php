<?php

namespace App\Http\Controllers\System\Gender;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Authorization\System\Genders\AdminAddGenderRequest;
use App\Http\Requests\Admins\Authorization\System\Genders\AdminUpdateGenderRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\Admin\System\Gender\GenderResource;
use App\Http\Resources\Admin\System\Gender\SingleGenderResource;
use App\Models\Gender;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\RestoreResponse;
use App\Traits\Controllers\Response\SelectResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use App\Traits\Controllers\Shared\GetAllData;
use App\Traits\Controllers\Shared\SearchOnData;
use Exception;

class AdminGenderController extends Controller
{
    use PermissionResponse, NotFoundEditResponse, EditResponse, SelectResponse, RestoreResponse, GetAllData, SearchOnData, ServerErrorResponse;

    public function index()
    {

        try {

            if (auth()->guard(getAdminGuard())->user()->can('show-gender')) {

                return $this->getAllData(
                    model: Gender::class,
                    resource: GenderResource::class,
                    type: 'genders'
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

            if (auth()->guard(getAdminGuard())->user()->can('show-gender')) {

                return $this->searchOnData(
                    model: Gender::class,
                    resource: GenderResource::class,
                    request: $request,
                    type: 'genders',
                    key: 'gender'
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

            if (auth()->guard(getAdminGuard())->user()->can('show-gender')) {

                $data = Gender::onlyTrashed()->with('admin')->get();
                $data = GenderResource::collection($data);

                return $this->SelectResponse(
                    data: $data,
                    type: 'genders'
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

            if (auth()->guard(getAdminGuard())->user()->can('show-gender')) {

                $data = Gender::onlyTrashed()->with('admin')->where('gender', 'like', '%' . $request->search_key . '%')->get();
                $data = GenderResource::collection($data);

                return $this->SelectResponse(
                    data: $data,
                    type: 'genders'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end searchInArchive

    public function store(AdminAddGenderRequest $request)
    {

        try {

            if (auth()->guard(getAdminGuard())->user()->can('manage-gender')) {

                $adminId = auth()->guard(getAdminGuard())->user()->id;
                $gender = Gender::create(
                    [
                        'gender' => $request->gender,
                        'admin_id' => $adminId
                    ]
                );

                return $this->editResponse(
                    title: 'gender',
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

            if (auth()->guard(getAdminGuard())->user()->can('manage-gender')) {

                $data = Gender::find($id);

                if (!$data) {

                    return $this->notFoundEditResponse(
                        type: 'gender',
                        key: $id
                    );
                }

                $adminAddGenderId = $data->admin_id;
                $adminAuthId =  auth()->guard(getAdminGuard())->user()->id;

                if ($adminAddGenderId != $adminAuthId) {

                    return $this->permissionResponse();
                }

                $data = new SingleGenderResource($data);

                return $this->SelectResponse(
                    data: $data,
                    type: 'gender'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end edit

    public function update(AdminUpdateGenderRequest $request)
    {

        try {

            if (auth()->guard(getAdminGuard())->user()->can('manage-gender')) {

                $gender = Gender::find($request->key);

                if (!$gender) {

                    return $this->notFoundEditResponse(
                        type: 'gender',
                        key: $request->key
                    );
                }

                $adminAuthId =  auth()->guard(getAdminGuard())->user()->id;
                $adminAddGenderid = $gender->admin_id;

                if ($adminAuthId != $adminAddGenderid) {

                    return $this->permissionResponse();
                }

                $gender->update([
                    'gender' => $request->gender
                ]);

                return $this->editResponse(
                    title: 'gender',
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

            if (auth()->guard(getAdminGuard())->user()->can('manage-gender')) {

                $gender = Gender::find($id);
                if (!$gender) {

                    return $this->notFoundEditResponse(
                        type: 'gender',
                        key: $id
                    );
                }

                $adminAuthId =  auth()->guard(getAdminGuard())->user()->id;
                $adminAddGenderid = $gender->admin_id;
                if ($adminAuthId != $adminAddGenderid) {

                    return $this->permissionResponse();
                }

                $gender->delete();

                return $this->editResponse(
                    title: 'gender',
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

            if (auth()->guard(getAdminGuard())->user()->can('manage-gender')) {

                $gender = Gender::onlyTrashed()->find($id);
                if (!$gender) {

                    return $this->notFoundEditResponse(
                        type: 'gender',
                        key: $id
                    );
                }

                $adminAuthId =  auth()->guard(getAdminGuard())->user()->id;
                $adminAddGenderId = $gender->admin_id;
                if ($adminAuthId != $adminAddGenderId) {

                    return $this->permissionResponse();
                }

                $gender->forceDelete();

                return $this->editResponse(
                    title: 'gender',
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

            if (auth()->guard(getAdminGuard())->user()->can('manage-gender')) {

                $gender = Gender::onlyTrashed()->find($id);
                if (!$gender) {

                    return $this->notFoundEditResponse(
                        type: 'gender',
                        key: $id
                    );
                }

                $gender->restore();

                return $this->restoreResponse(
                    title: 'gender'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end restore
}
