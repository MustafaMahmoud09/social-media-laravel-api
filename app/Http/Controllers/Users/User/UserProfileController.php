<?php

namespace App\Http\Controllers\Users\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyPageRequest;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\SearchPageRequest;
use App\Http\Resources\User\Post\PostShowResource;
use App\Http\Resources\User\Profile\ProfileBasicResource;
use App\Http\Resources\User\Profile\ProfileSecondaryResource;
use App\Http\Resources\User\UserMediaResource;
use App\Models\User;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectPageResponse;
use App\Traits\Controllers\Response\SelectResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    use SelectPageResponse, PermissionResponse, NotFoundEditResponse, SelectResponse, ServerErrorResponse;

    public function search(SearchPageRequest $request)
    {
        try {
            //search on users
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $users = User::withCount('followers')
                ->where('name', 'like', '%' . $request->search_key . '%')
                ->where('id', '!=', $userAuthId)
                ->orderBy('followers_count', 'desc')
                ->paginate(10, ['*'], 'page', $request->page);


            //success response
            return $this->selectPageResponse(
                data: UserMediaResource::collection($users->items()),
                page: $users->currentPage(),
                lastPage: $users->lastPage(),
                type: 'profiles'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end search

    public function userPosts(KeyPageRequest $request)
    {
        try {
            //check user exist or no
            $user = User::find($request->key);
            if (!$user) {

                return $this->notFoundEditResponse(
                    type: 'user',
                    key: $request->key
                );
            }

            //check user auth not user id
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAuthId == $user->id) {

                return $this->permissionResponse();
            }

            //get user posts
            $posts = $user->posts()
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'page', $request->page);

            //success response
            return $this->selectPageResponse(
                data: PostShowResource::collection($posts->items()),
                page: $posts->currentPage(),
                lastPage: $posts->lastPage(),
                type: 'posts'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end userPosts

    public function userDataBasic(KeyRequest $request)
    {
        try {
            //check user exist or no
            $user = User::find($request->key);
            if (!$user) {

                return $this->notFoundEditResponse(
                    type: 'user',
                    key: $request->key
                );
            }

            //check user auth not user id
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAuthId == $user->id) {

                return $this->permissionResponse();
            }

            //format
            $user = new ProfileBasicResource($user);

            //success response
            return $this->SelectResponse(
                data: $user,
                type: 'user basic'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end userDataBasic

    public function userDateSecondary(KeyRequest $request)
    {
        try {
            //check user exist or no
            $user = User::find($request->key);
            if (!$user) {

                return $this->notFoundEditResponse(
                    type: 'user',
                    key: $request->key
                );
            }

            //check user auth not user id
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAuthId == $user->id) {

                return $this->permissionResponse();
            }
            //format data
            $user = new ProfileSecondaryResource($user);

            //success response
            return $this->SelectResponse(
                data: $user,
                type: 'user secondary'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end userDateSecondary

}
