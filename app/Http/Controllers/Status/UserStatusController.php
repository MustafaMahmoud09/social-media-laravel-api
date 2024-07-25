<?php

namespace App\Http\Controllers\Status;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyPageRequest;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\PageRequest;
use App\Http\Requests\User\Authorization\State\UserAddStateRequest;
use App\Http\Resources\User\State\StateOtherResource;
use App\Http\Resources\User\UserMediaResource;
use App\Models\MultiMediaState;
use App\Models\State;
use App\Models\User;
use App\Traits\Controllers\File\DeleteFile;
use App\Traits\Controllers\File\UploadFile;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\EmptyRequestResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectPageResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserStatusController extends Controller
{
    use EmptyRequestResponse, UploadFile, EditResponse, NotFoundEditResponse, PermissionResponse, DeleteFile, SelectPageResponse, ServerErrorResponse;

    public function index(PageRequest $request)
    {
        try {
            // Retrieve the authenticated user
            $user = auth()->guard(getUserGuard())->user();

            // Get the user's followings if have valid status
            $followings = $user
                ->followings()
                ->whereHas('states', function ($query) {

                    //get current time
                    $currentDate = Carbon::now();

                    // Filter states based on their validity duration
                    $query->where(
                        function ($subQuery) use ($currentDate) {
                            $subQuery->where('created_at', '>', $currentDate->copy()->subHours(12))
                                ->where('validity', 0);
                        }
                    )->orWhere(
                        function ($subQuery) use ($currentDate) {
                            $subQuery->where('created_at', '>', $currentDate->copy()->subHours(24))
                                ->where('validity', 1);
                        }
                    )->orWhere(
                        function ($subQuery) use ($currentDate) {
                            $subQuery->where('created_at', '>', $currentDate->copy()->subHours(48))
                                ->where('validity', 2);
                        }
                    );
                })->orderBy('last_state', 'desc')->paginate(10, ['*'], 'page', $request->page);

            //success response
            return $this->selectPageResponse(
                data: UserMediaResource::collection($followings->items()),
                page: $followings->currentPage(),
                lastPage: $followings->lastPage(),
                type: 'following states'
            );
        } catch (Exception $ex) {
            // Handle any errors and return a server error response
            return $this->serverErrorResponse();
        }
    } //end index

    public function userStates(KeyPageRequest $request)
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

            //get validity user state
            $currentDate = Carbon::now();
            $states = $user->states()
                ->where(
                    function ($subQuery) use ($currentDate) {
                        $subQuery->where(

                            function ($subQuery) use ($currentDate) {
                                $subQuery->where('created_at', '>', $currentDate->copy()->subHours(12))
                                    ->where('validity', 0);
                            }
                        )->orWhere(

                            function ($subQuery) use ($currentDate) {
                                $subQuery->where('created_at', '>', $currentDate->copy()->subHours(24))
                                    ->where('validity', 1);
                            }
                        )->orWhere(

                            function ($subQuery) use ($currentDate) {
                                $subQuery->where('created_at', '>', $currentDate->copy()->subHours(48))
                                    ->where('validity', 2);
                            }
                        );
                    }
                )->paginate(10, ['*'], 'page', $request->page);
            //toSql

            //success response
            return $this->selectPageResponse(
                data: StateOtherResource::collection($states->items()),
                type: 'user states',
                page: $states->currentPage(),
                lastPage: $states->lastPage()
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end userStates

    public function store(UserAddStateRequest $request)
    {

        try {

            if (!$request->state && !$request->hasFile('image')) {

                return $this->emptyRequestResponse();
            }

            $user = auth()->guard(getUserGuard())->user();
            $userId = $user->id;
            $state = State::create(
                [
                    'state' => $request->state,
                    'theme' => $request->theme,
                    'validity' => $request->validity,
                    'user_id' => $userId
                ]
            );

            if ($request->hasFile('image')) {

                $path = $this->uploadFile(
                    request: $request,
                    key: 'image',
                    folder: 'state/' . $userId . '/' . $state->id
                );

                MultiMediaState::create(
                    [
                        'path' => $path,
                        'state_id' => $state->id
                    ]
                );
            }

            //update last add state
            DB::table('users')
                ->where('id', $userId)
                ->update(
                    [
                        'last_state' => now()
                    ]
                );

            return $this->editResponse(
                title: 'state',
                type: 'store'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end store

    public function delete(KeyRequest $requst)
    {

        try {

            //state exist or no
            $state = State::find($requst->key);
            if (!$state) {

                return $this->notFoundEditResponse(
                    type: 'state',
                    key: $requst->key
                );
            }

            //user auth added state or no
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $userAddedId = $state->user_id;
            if ($userAuthId != $userAddedId) {

                return $this->permissionResponse();
            }

            //delete state
            $image = $state->multiMedia;
            $state->delete();
            if ($image) {

                $this->deleteFile(
                    path: $image->path
                );
            }

            //success response
            return $this->editResponse(
                title: 'state',
                type: 'deleted'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }

    } //end delete
}
