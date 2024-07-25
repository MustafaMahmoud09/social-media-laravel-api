<?php

namespace App\Http\Controllers\Posts\User;

use App\Events\Post\CreateReactOnPostEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\KeyPageRequest;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\User\Authorization\Post\UserAddReactOnPostRequest;
use App\Http\Requests\User\Authorization\UserUpdateReactRequest;
use App\Http\Resources\User\UserReactResource;
use App\Models\Post;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectPageResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;
use Illuminate\Support\Facades\DB;

class   UserPostReactController extends Controller
{
    use EditResponse, NotFoundEditResponse, PermissionResponse, SelectPageResponse, ServerErrorResponse;

    public function index(KeyPageRequest $request)
    {

        try {

            $post = Post::find($request->key);
            if (!$post) {

                return $this->notFoundEditResponse(
                    type: 'post',
                    key: $request->key
                );
            }

            $data = $post->userReacts()
                ->orderBy('type')
                ->orderBy('name')
                ->paginate(10, ['*'], 'page', $request->page);

            return $this->selectPageResponse(
                data: UserReactResource::collection($data->items()),
                page: $data->currentPage(),
                lastPage: $data->lastPage(),
                type: 'post reacts'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end index


    public function store(UserAddReactOnPostRequest $request)
    {
        try {
            $user = auth()->guard(getUserGuard())->user();
            $userId = $user->id;

            $react = DB::table('post_user_reacts')
                ->where('user_id', $userId)
                ->where('post_id', $request->post)->first();

            if ($react) {

                DB::table('post_user_reacts')
                    ->where('user_id', $userId)
                    ->where('post_id', $request->post)
                    ->update(
                        [
                            'type' => $request->type,
                            'updated_at' => now()
                        ]
                    );
            } //end if
            else {

                $react = DB::table('post_user_reacts')->insert(
                    [
                        'type' => $request->type,
                        'post_id' => $request->post,
                        'user_id' => $userId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );

                $post = Post::find($request->post);
                event(new CreateReactOnPostEvent($react, $post, $user));
            } //end else

            return $this->editResponse(
                title: 'post react',
                type: 'insert'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end store


    public function update(UserUpdateReactRequest $request)
    {
        try {

            $react = DB::table('post_user_reacts')->find($request->key);
            if (!$react) {

                return $this->notFoundEditResponse(
                    type: 'post react',
                    key: $request->key
                );
            }

            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $userAddId = $react->user_id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            DB::table('post_user_reacts')
                ->where('id', $request->key)
                ->update(
                    [
                        'type' => $request->type,
                        'updated_at' => now()
                    ]
                );

            return $this->editResponse(
                title: 'post react',
                type: 'update'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end update


    public function delete(KeyRequest $request)
    {

        try {

            $react = DB::table('post_user_reacts')->find($request->key);
            if (!$react) {

                return $this->notFoundEditResponse(
                    type: 'post react',
                    key: $request->key
                );
            }

            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $userAddId = $react->user_id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            DB::table('post_user_reacts')
                ->where('id', $request->key)
                ->delete();

            return $this->editResponse(
                title: 'post react',
                type: 'delete'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end delete

}
