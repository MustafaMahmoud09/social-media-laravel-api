<?php

namespace App\Http\Controllers\System\Address;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Authorization\System\Addresses\AdminAddAddressRequest;
use App\Http\Requests\Admins\Authorization\System\Addresses\AdminUpdateAddressRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\Admin\System\Address\AddressResource;
use App\Http\Resources\Admin\System\Address\SingleAddressResource;
use App\Models\Address;
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


class AdminAddressController extends Controller
{
    use PermissionResponse, NotFoundEditResponse, EditResponse, SelectResponse, RestoreResponse, GetAllData, SearchOnData, ServerErrorResponse;

    public function index()
    {

        try {

            if (auth()->guard(getAdminGuard())->user()->can('show-address')) {

                return $this->getAllData(
                    model: Address::class,
                    resource: AddressResource::class,
                    type: 'addresses'
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

            if (auth()->guard(getAdminGuard())->user()->can('show-address')) {

                return $this->searchOnData(
                    model: Address::class,
                    resource: AddressResource::class,
                    request: $request,
                    type: 'addresses',
                    key: 'position'
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

            if (auth()->guard(getAdminGuard())->user()->can('show-address')) {

                $data = Address::onlyTrashed()->with('admin')->get();
                $data = AddressResource::collection($data);

                return $this->SelectResponse(
                    data: $data,
                    type: 'addresses'
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

            if (auth()->guard(getAdminGuard())->user()->can('show-address')) {

                $data = Address::onlyTrashed()->with('admin')->where('position', 'like', '%' . $request->search_key . '%')->get();
                $data = AddressResource::collection($data);

                return $this->SelectResponse(
                    data: $data,
                    type: 'addresses'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end searchInArchive

    public function store(AdminAddAddressRequest $request)
    {

        try {

            if (auth()->guard(getAdminGuard())->user()->can('manage-address')) {

                $adminId = auth()->guard(getAdminGuard())->user()->id;
                $address = Address::create(
                    [
                        'position' => $request->position,
                        'zip_code' => $request->zip_code,
                        'admin_id' => $adminId
                    ]
                );

                return $this->editResponse(
                    title: 'address',
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

            if (auth()->guard(getAdminGuard())->user()->can('manage-address')) {

                $data = Address::find($id);

                if (!$data) {

                    return $this->notFoundEditResponse(
                        type: 'address',
                        key: $id
                    );
                }

                $adminAddGenderId = $data->admin_id;
                $adminAuthId =  auth()->guard(getAdminGuard())->user()->id;

                if ($adminAddGenderId != $adminAuthId) {

                    return $this->permissionResponse();
                }

                $data = new SingleAddressResource($data);

                return $this->SelectResponse(
                    data: $data,
                    type: 'address'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end edit

    public function update(AdminUpdateAddressRequest $request)
    {

        try {

            if (auth()->guard(getAdminGuard())->user()->can('manage-address')) {

                $address = Address::find($request->key);

                if (!$address) {

                    return $this->notFoundEditResponse(
                        type: 'address',
                        key: $request->key
                    );
                }

                $adminAuthId =  auth()->guard(getAdminGuard())->user()->id;
                $adminAddGenderid = $address->admin_id;

                if ($adminAuthId != $adminAddGenderid) {

                    return $this->permissionResponse();
                }

                $address->update([
                    'position' => $request->position,
                    'zip_code' => $request->zip_code
                ]);

                return $this->editResponse(
                    title: 'address',
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

            if (auth()->guard(getAdminGuard())->user()->can('manage-address')) {

                $address = Address::find($id);
                if (!$address) {

                    return $this->notFoundEditResponse(
                        type: 'address',
                        key: $id
                    );
                }

                $adminAuthId =  auth()->guard(getAdminGuard())->user()->id;
                $adminAddGenderid = $address->admin_id;
                if ($adminAuthId != $adminAddGenderid) {

                    return $this->permissionResponse();
                }

                $address->delete();

                return $this->editResponse(
                    title: 'address',
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

            if (auth()->guard(getAdminGuard())->user()->can('manage-address')) {

                $address = Address::onlyTrashed()->find($id);
                if (!$address) {

                    return $this->notFoundEditResponse(
                        type: 'address',
                        key: $id
                    );
                }

                $adminAuthId =  auth()->guard(getAdminGuard())->user()->id;
                $adminAddGenderId = $address->admin_id;
                if ($adminAuthId != $adminAddGenderId) {

                    return $this->permissionResponse();
                }

                $address->forceDelete();

                return $this->editResponse(
                    title: 'address',
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

            if (auth()->guard(getAdminGuard())->user()->can('manage-address')) {

                $address = Address::onlyTrashed()->find($id);
                if (!$address) {

                    return $this->notFoundEditResponse(
                        type: 'address',
                        key: $id
                    );
                }

                $address->restore();

                return $this->restoreResponse(
                    title: 'address'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end restore
}
