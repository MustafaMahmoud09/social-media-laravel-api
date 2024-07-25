<?php

namespace App\Http\Controllers\System\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\Authorization\System\Socials\AdminAddSocialRequest;
use App\Http\Requests\Admins\Authorization\System\Socials\AdminUpdateSocialRequest;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\Admin\System\Social\SingleSocialResource;
use App\Http\Resources\Admin\System\Social\SocialResource;
use App\Models\Social;
use App\Traits\Controllers\File\DeleteFile;
use App\Traits\Controllers\File\UpdateFile;
use App\Traits\Controllers\File\UploadFile;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\RestoreResponse;
use App\Traits\Controllers\Response\SelectResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use App\Traits\Controllers\Shared\GetAllData;
use App\Traits\Controllers\Shared\SearchOnData;
use Exception;

class AdminSocialController extends Controller
{
    use UploadFile, EditResponse, NotFoundEditResponse, PermissionResponse, SelectResponse, DeleteFile, UpdateFile, RestoreResponse, GetAllData, SearchOnData, ServerErrorResponse;

    public function index()
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('show-social')) {

                return $this->getAllData(
                    model: Social::class,
                    resource: SocialResource::class,
                    type: 'socials'
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
            if (auth()->guard(getAdminGuard())->user()->can('show-social')) {

                return $this->searchOnData(
                    model: Social::class,
                    resource: SocialResource::class,
                    request: $request,
                    type: 'socials',
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
            if (auth()->guard(getAdminGuard())->user()->can('show-social')) {

                $data = Social::onlyTrashed()->get();
                $data = SocialResource::collection($data);

                return $this->SelectResponse(
                    data: $data,
                    type: 'socials'
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
            if (auth()->guard(getAdminGuard())->user()->can('show-social')) {

                $data = Social::onlyTrashed()->where('title', 'like', '%' . $request->search_key . '%')->get();
                $data = SocialResource::collection($data);

                return $this->SelectResponse(
                    data: $data,
                    type: 'socials'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end searchInArchive

    public function store(AdminAddSocialRequest $request)
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('manage-social')) {

                $adminId = auth()->guard(getAdminGuard())->user()->id;
                $path = $this->uploadFile(
                    request: $request,
                    key: 'image',
                    folder: 'social'
                );

                $social = Social::create(
                    [
                        'title' => $request->name,
                        'path' => $path,
                        'description' => $request->description,
                        'admin_id' => $adminId
                    ]
                );

                return $this->editResponse(
                    title: 'social',
                    type: 'store'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end store

    function edit(KeyRequest $request)
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('manage-social')) {

                $social = Social::find($request->key);

                if (!$social) {

                    return $this->notFoundEditResponse(
                        type: 'social',
                        key: $request->key
                    );
                }

                $adminAuthId = auth()->guard(getAdminGuard())->user()->id;
                $adminAddId = $social->admin_id;

                if ($adminAddId != $adminAuthId) {

                    return $this->permissionResponse();
                }

                $social = new SingleSocialResource($social);

                return $this->SelectResponse(
                    data: $social,
                    type: 'social'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end edit

    public function update(AdminUpdateSocialRequest $request)
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('manage-social')) {

                $social = Social::find($request->key);
                if (!$social) {

                    return $this->notFoundEditResponse(
                        type: 'social',
                        key: $request->key,
                    );
                }

                $adminAuthId = auth()->guard(getAdminGuard())->user()->id;
                $adminAddId = $social->admin_id;
                if ($adminAuthId != $adminAddId) {

                    return $this->permissionResponse();
                }

                $description = $request->description;
                if (!$description) {

                    $description = $social->description;
                }

                $path = $this->updateFile(
                    oldPath: $social->path,
                    request: $request,
                    key: 'image',
                    folder: 'social'
                );

                $social->update(
                    [
                        'title' => $request->name,
                        'path' => $path,
                        'description' => $description
                    ]
                );

                return $this->editResponse(
                    title: 'social',
                    type: 'update'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end update

    public function destroy(KeyRequest $request)
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('manage-social')) {

                $social = Social::find($request->key);
                if (!$social) {

                    return $this->notFoundEditResponse(
                        type: 'social',
                        key: $request->key,
                    );
                }

                $adminAuthId = auth()->guard(getAdminGuard())->user()->id;
                $adminAddId = $social->admin_id;
                if ($adminAuthId != $adminAddId) {

                    return $this->permissionResponse();
                }

                $social->delete();
                return $this->editResponse(
                    title: 'social',
                    type: 'delete'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end destroy

    public function destroyArchives(KeyRequest $request)
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('manage-social')) {

                $social = Social::onlyTrashed()->find($request->key);
                if (!$social) {

                    return $this->notFoundEditResponse(
                        type: 'social',
                        key: $request->key,
                    );
                }

                $adminAuthId = auth()->guard(getAdminGuard())->user()->id;
                $adminAddId = $social->admin_id;
                if ($adminAuthId != $adminAddId) {

                    return $this->permissionResponse();
                }

                $social->forceDelete();
                $this->deleteFile(
                    path: $social->path
                );

                return $this->editResponse(
                    title: 'social',
                    type: 'delete'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end destroyArchives

    public function restore(KeyRequest $request)
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('manage-social')) {

                $social = Social::onlyTrashed()->find($request->key);
                if (!$social) {

                    return $this->notFoundEditResponse(
                        type: 'social',
                        key: $request->key,
                    );
                }

                $adminAuthId = auth()->guard(getAdminGuard())->user()->id;
                $adminAddId = $social->admin_id;
                if ($adminAuthId != $adminAddId) {

                    return $this->permissionResponse();
                }

                $social->restore();
                return $this->restoreResponse(
                    title: 'social'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end restore

}
