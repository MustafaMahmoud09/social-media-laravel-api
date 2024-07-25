<?php

namespace App\Http\Controllers\Comments;

use App\Events\Comment\CreateReactOnCommentEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\KeyPageRequest;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\User\Authorization\Comment\UserAddReactOnCommentRequest;
use App\Http\Requests\User\Authorization\UserUpdateReactRequest;
use App\Http\Resources\User\UserReactResource;
use App\Models\Comment;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectPageResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;
use Illuminate\Support\Facades\DB;

class UserCommentReactController extends Controller
{
    use EditResponse, NotFoundEditResponse, PermissionResponse, SelectPageResponse, ServerErrorResponse;

    public function index(KeyPageRequest $request)
    {

        try {

            $comment = Comment::find($request->key);
            if (!$comment) {

                return $this->notFoundEditResponse(
                    type: 'comment',
                    key: $request->key
                );
            }

            $data = $comment->userReacts()
                ->orderBy('type')
                ->orderBy('name')
                ->paginate(10, ['*'], 'page', $request->page);


            return $this->selectPageResponse(
                data: UserReactResource::collection($data->items()),
                page: $data->currentPage(),
                lastPage: $data->lastPage(),
                type: 'comment reacts'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end index


    public function store(UserAddReactOnCommentRequest $request)
    {

        try {

            $user = auth()->guard(getUserGuard())->user();

            $userId = $user->id;

            $react = DB::table('comment_user_reacts')
                ->where('user_id', $userId)
                ->where('comment_id', $request->comment)->first();

            if ($react) {

                DB::table('comment_user_reacts')
                    ->where('user_id', $userId)
                    ->where('comment_id', $request->comment)
                    ->update(
                        [
                            'type' => $request->type,
                            'updated_at' => now()
                        ]
                    );
            } //end if
            else {

                $react = DB::table('comment_user_reacts')->insert(
                    [
                        'type' => $request->type,
                        'comment_id' => $request->comment,
                        'user_id' => $userId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );

                $comment = Comment::find($request->comment);
                event(new CreateReactOnCommentEvent($react, $comment,$user));

            } //end else

            return $this->editResponse(
                title: 'comment react',
                type: 'insert'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end store


    public function update(UserUpdateReactRequest $request)
    {

        try {

            $react = DB::table('comment_user_reacts')->find($request->key);
            if (!$react) {

                return $this->notFoundEditResponse(
                    type: 'comment react',
                    key: $request->key
                );
            }

            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $userAddId = $react->user_id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            DB::table('comment_user_reacts')
                ->where('id', $request->key)
                ->update(
                    [
                        'type' => $request->type,
                        'updated_at' => now()
                    ]
                );

            return $this->editResponse(
                title: 'comment react',
                type: 'update'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end update


    public function delete(KeyRequest $request)
    {

        try {

            $react = DB::table('comment_user_reacts')->find($request->key);
            if (!$react) {

                return $this->notFoundEditResponse(
                    type: 'comment react',
                    key: $request->key
                );
            }

            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $userAddId = $react->user_id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            DB::table('comment_user_reacts')
                ->where('id', $request->key)
                ->delete();

            return $this->editResponse(
                title: 'comment react',
                type: 'delete'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end delete

}
