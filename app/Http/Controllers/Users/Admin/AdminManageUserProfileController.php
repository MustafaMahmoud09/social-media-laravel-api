<?php

namespace App\Http\Controllers\Users\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\PageRequest;
use App\Http\Requests\SearchPageRequest;
use App\Http\Resources\Admin\User\UserDocumentResource;
use App\Models\User;
use App\Models\UserDocumentation;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectPageResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;
use Illuminate\Http\Request;

class AdminManageUserProfileController extends Controller
{
    use SelectPageResponse, NotFoundEditResponse, EditResponse, PermissionResponse, ServerErrorResponse;
    public function index(PageRequest $request)
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('show-user')) {

                //get all users
                $users = User::withCount('followers', 'followings')
                    ->orderBy('followers_count', 'desc')
                    ->paginate(10, ['*'], 'page', $request->page);

                //success response
                return $this->selectPageResponse(
                    data: UserDocumentResource::collection($users->items()),
                    page: $users->currentPage(),
                    lastPage: $users->lastPage(),
                    type: 'users'
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
            if (auth()->guard(getAdminGuard())->user()->can('show-user')) {

                //search on users
                $users = User::withCount('followers', 'followings')
                    ->where('name', 'like', '%' . $request->search_key . '%')
                    ->orderBy('followers_count', 'desc')
                    ->paginate(10, ['*'], 'page', $request->page);

                //success response
                return $this->selectPageResponse(
                    data: UserDocumentResource::collection($users->items()),
                    page: $users->currentPage(),
                    lastPage: $users->lastPage(),
                    type: 'users'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end search

    public function documment(KeyRequest $request)
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('manage-user')) {

                //user exist or no
                $user = User::find($request->key);
                if (!$user) {

                    return $this->notFoundEditResponse(
                        type: 'user',
                        key: $request->key
                    );
                }

                //already document or no
                $documentation = $user->documentation;
                if ($documentation) {

                    return responseFormat(
                        data: null,
                        message: 'already document this user',
                        status: 403
                    );
                }

                //store documentation
                $adminAuthId = auth()->guard(getAdminGuard())->user()->id;
                UserDocumentation::create(
                    [
                        'admin_id' => $adminAuthId,
                        'user_id' => $request->key
                    ]
                );

                //response success
                return $this->editResponse(
                    title: 'user document',
                    type: 'inserted'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end documment

    public function deleteDocument(KeyRequest $request)
    {
        try {
            if (auth()->guard(getAdminGuard())->user()->can('manage-user')) {

                //user exist or no
                $user = User::find($request->key);
                if (!$user) {

                    return $this->notFoundEditResponse(
                        type: 'user',
                        key: $request->key
                    );
                }

                //document exist or no
                $documentation = $user->documentation;
                if (!$documentation) {

                    return $this->notFoundEditResponse(
                        type: 'docummentation',
                        key: $request->key
                    );
                }

                //admin auth is added or no
                $adminAuthId = auth()->guard(getAdminGuard())->user()->id;
                $adminAddedId = $documentation->admin_id;
                if ($adminAuthId != $adminAddedId) {

                    return $this->permissionResponse();
                }

                //delete documentation
                $documentation->delete();

                //success response
                return $this->editResponse(
                    title: 'documentation',
                    type: 'deleted'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end deleteDocument
}
