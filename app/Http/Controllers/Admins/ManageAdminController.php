<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Authorization\Admin\AdminAddAdminRequest;
use App\Http\Requests\PageRequest;
use App\Http\Requests\SearchPageRequest;
use App\Http\Resources\Admin\Admin\AdminResource;
use App\Models\Admin;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectPageResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;
use Illuminate\Http\Request;

class ManageAdminController extends Controller
{
    use PermissionResponse, EditResponse, SelectPageResponse, ServerErrorResponse;

    public function index(PageRequest $request)
    {

        try {
            //you have permission or no
            if (auth()->guard(getAdminGuard())->user()->can('show-admin')) {
                //get admins
                $adminAuthId = auth()->guard(getAdminGuard())->user()->id;
                $admins = Admin::where('id', '!=', $adminAuthId)->paginate(10, ['*'], 'page', $request->page);

                //success response
                return $this->selectPageResponse(
                    data: AdminResource::collection($admins->items()),
                    page: $admins->lastPage(),
                    lastPage: $admins->lastPage(),
                    type: 'admins'
                );
            }

            return $this->permissionResponse();
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }

    } //end index

    public function search(SearchPageRequest $request)
    {

        try {

            //you have permission or no
            if (auth()->guard(getAdminGuard())->user()->can('show-admin')) {
                //search on admins
                $adminAuthId = auth()->guard(getAdminGuard())->user()->id;
                $admins = Admin::where('id', '!=', $adminAuthId)
                    ->where('name', 'like', '%' . $request->search_key . '%')
                    ->paginate(10, ['*'], 'page', $request->page);

                //success response
                return $this->selectPageResponse(
                    data: AdminResource::collection($admins->items()),
                    page: $admins->currentPage(),
                    lastPage: $admins->lastPage(),
                    type: 'admins'
                );
            }

            return $this->permissionResponse();
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }

    } //end index

    public function store(AdminAddAdminRequest $request)
    {
        try {

            //you have permission or no
            if (auth()->guard(getAdminGuard())->user()->can('manage-admin')) {

                //insert admin
                $admin = Admin::create(
                    [
                        'name' => $request->name,
                        'descritption' => $request->description,
                        'email' => $request->email,
                        'password' => bcrypt($request->password),
                        'birth_date' => $request->birth_date,
                        'phone' => $request->phone_number,
                        'ssn' => $request->ssn,
                        'gender' => $request->gender
                    ]
                );

                //if admin type == true admin is media admin
                //if admin type == false admin is system admin
                if ($request->admin_type) {
                    //role have id = 2 is media admin
                    $admin->assignRole(2);
                } else {
                    //role have id = 3 is system admin
                    $admin->assignRole(3);
                }

                //success response
                return $this->editResponse(
                    title: 'admin',
                    type: 'inserted'
                );
            }

            return $this->permissionResponse();
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }

    } //end store

}
