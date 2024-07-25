<?php

namespace App\Http\Controllers\Status;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyPageRequest;
use App\Http\Requests\User\Authorization\State\UserAddReactOnState;
use App\Http\Requests\User\Authorization\State\UserUpdateStateReactRequest;
use App\Http\Resources\User\UserReactResource;
use App\Models\State;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectPageResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserStatusWatcheController extends Controller
{
    use PermissionResponse, EditResponse, NotFoundEditResponse, SelectPageResponse, ServerErrorResponse;

    public function index(KeyPageRequest $request)
    {

        try {

            //state exist or no
            $state = State::find($request->key);
            if (!$state) {

                return $this->notFoundEditResponse(
                    type: 'state',
                    key: $request->key
                );
            }

            //user is added state or no
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $userAddedId = $state->user_id;
            if ($userAuthId != $userAddedId) {

                return $this->permissionResponse();
            }

            //get users watch state
            $watches = $state->userWatches()
                ->orderBy('type', 'desc')
                ->paginate(10, ['*'], 'page', $request->page);

            //success response
            return $this->selectPageResponse(
                data: UserReactResource::collection($watches),
                page: $watches->currentPage(),
                lastPage: $watches->lastPage(),
                type: 'user watches'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end index

    public function store(UserAddReactOnState $request)
    {

        try {

            //user not added state or no
            $userId = auth()->guard(getUserGuard())->user()->id;
            $userAddedId = State::find($request->state)->user_id;
            if ($userId == $userAddedId) {

                return $this->permissionResponse();
            }

            //add watche on state
            DB::table('state_user_watches')->insert(
                [
                    'type' => $request->type,
                    'state_id' => $request->state,
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            //success response
            return $this->editResponse(
                title: 'sate react',
                type: 'insert'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end store

    public function update(UserUpdateStateReactRequest $request)
    {

        try {

            //react exist or no
            $react = DB::table('state_user_watches')->find($request->key);
            if (!$react) {

                return $this->notFoundEditResponse(
                    type: 'state react',
                    key: $request->key
                );
            }

            //user auth is added state or no
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $userAddId = $react->user_id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            //update react state
            DB::table('state_user_watches')
                ->where('id', $request->key)
                ->update(
                    [
                        'type' => $request->type,
                        'updated_at' => now()
                    ]
                );

            //success response
            return $this->editResponse(
                title: 'state react',
                type: 'update'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end update
}
