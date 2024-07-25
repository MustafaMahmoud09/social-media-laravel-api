<?php

namespace App\Http\Controllers\Users\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\PageRequest;
use App\Http\Requests\SearchPageRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\User\Authorization\Follow\UserAddFollowRequest;
use App\Http\Resources\User\UserMediaResource;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectPageResponse;
use App\Traits\Controllers\Response\SelectResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;
use Illuminate\Support\Facades\DB;

class UserFollowController extends Controller
{
    use EditResponse, NotFoundEditResponse, PermissionResponse, SelectResponse, SelectPageResponse, ServerErrorResponse;

    public function followers(PageRequest $request)
    {
        try {
            // Retrieve the authenticated user
            $user = auth()->guard(getUserGuard())->user();

            // Get the user's followers, ordered by name, and paginate the results
            $data = $user->followers()->orderBy('name')->paginate(10, ['*'], 'page', $request->page);

            // Return the paginated list of followers with pagination details
            return $this->selectPageResponse(
                data:  UserMediaResource::collection($data->items()),
                page: $data->currentPage(),
                lastPage: $data->lastPage(),
                type: 'followers'
            );
        } catch (Exception $ex) {
              // Handle any errors and return a server error response
            return $this->serverErrorResponse();
        }
    } //end followers


    public function searchOnFollowers(SearchPageRequest $request)
    {
        try {
            // Retrieve the authenticated user
            $user = auth()->guard(getUserGuard())->user();

            // Search the user's followers by name, ordered by name, and paginate the results
            $data = $user->followers()
                ->where('name', 'like', '%' . $request->search_key . '%')
                ->orderBy('name')
                ->paginate(10, ['*'], 'page', $request->page);

            // Return the paginated search results of followers with pagination details
            return $this->selectPageResponse(
                data: UserMediaResource::collection($data),
                page: $data->currentPage(),
                lastPage: $data->lastPage(),
                type: 'followers'
            );
        } catch (Exception $ex) {
            // Handle any errors and return a server error response
            return $this->serverErrorResponse();
        }
    } //end searchOnFollowers


    public function followings(PageRequest $request)
    {
        try {
            $user = auth()->guard(getUserGuard())->user();

            $data = $user->followings()->orderBy('name')->paginate(10, ['*'], 'page', $request->page);

            return $this->selectPageResponse(
                data: UserMediaResource::collection($data->items()),
                page: $data->currentPage(),
                lastPage: $data->lastPage(),
                type: 'followings'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end followings

    public function searchOnFollowings(SearchPageRequest $request)
    {
        try {
            $user = auth()->guard(getUserGuard())->user();

            $data = $user->followings()
                ->where('name', 'like', '%' . $request->search_key . '%')
                ->orderBy('name')
                ->paginate(10, ['*'], 'page', $request->page);

            return $this->selectPageResponse(
                data: UserMediaResource::collection($data->items()),
                page: $data->currentPage(),
                lastPage: $data->lastPage(),
                type: 'followings'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end followers


    public function store(UserAddFollowRequest $request)
    {
        try {
            $userId = auth()->guard(getUserGuard())->user()->id;

            if ($userId == $request->following) {

                return responseFormat(
                    data: null,
                    message: 'You can not follow your account',
                    status: 400
                );
            }

            DB::table('following_users')->insert(
                [
                    'follow_id' => $userId,
                    'following_id' => $request->following
                ]
            );

            return $this->editResponse(
                title: 'follow',
                type: 'store'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end store


    public function delete(KeyRequest $request)
    {
        try {
            $following = DB::table('following_users')->find($request->key);
            if (!$following) {

                return $this->notFoundEditResponse(
                    type: 'follow',
                    key: $request->key
                );
            }

            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $userAddId = $following->follow_id;
            if ($userAuthId != $userAddId) {

                return $this->permissionResponse();
            }

            DB::table('following_users')->where('id', $request->key)->delete();
            return $this->editResponse(
                title: 'follow',
                type: 'deleted'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end delete

}
