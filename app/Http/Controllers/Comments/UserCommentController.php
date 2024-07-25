<?php

namespace App\Http\Controllers\Comments;

use App\Events\Comment\CreateCommentOnPostEvent;
use App\Events\Comment\CreateCommentReplayOnCommentEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\KeyPageRequest;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\User\Authorization\Comment\UserAddCommentRequest;
use App\Http\Requests\User\Authorization\Comment\UserUpdateCommentRequest;
use App\Http\Resources\User\Comment\CommentEditResource;
use App\Http\Resources\User\Comment\CommentRootResource;
use App\Models\Comment;
use App\Models\MultiMediaComment;
use App\Models\Post;
use App\Traits\Controllers\File\DeleteFile;
use App\Traits\Controllers\File\UploadManyFile;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\EmptyRequestResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectPageResponse;
use App\Traits\Controllers\Response\SelectResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;

class UserCommentController extends Controller
{
    use EmptyRequestResponse, UploadManyFile, EditResponse, NotFoundEditResponse, PermissionResponse, SelectResponse, DeleteFile, SelectPageResponse, ServerErrorResponse;

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

            $data = $post->comments()
                ->where('comment_id', null)
                ->orderBy('created_at')
                ->paginate(10, ['*'], 'page', $request->page);

            return $this->selectPageResponse(
                data: CommentRootResource::collection($data->items()),
                page: $data->currentPage(),
                lastPage: $data->lastPage(),
                type: 'comments'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end index


    public function replaiesComment(KeyPageRequest $request)
    {

        try {

            $comment = Comment::find($request->key);
            if (!$comment) {

                return $this->notFoundEditResponse(
                    type: 'comment',
                    key: $request->key
                );
            }

            $data = $comment->comments()
                ->orderBy('created_at')
                ->paginate(10, ['*'], 'page', $request->page);

            return $this->selectPageResponse(
                data: CommentRootResource::collection($data->items()),
                page: $data->currentPage(),
                lastPage: $data->lastPage(),
                type: 'comments'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end replaiesComment


    public function store(UserAddCommentRequest $request)
    {

        try {

            if (!$request->comment && !$request->hasFile('images')) {

                return $this->emptyRequestResponse();
            }

            if ($request->replay) {

                $base_comment = Comment::find($request->replay);
                if ($base_comment->post_id != $request->post) {

                    return responseFormat(
                        data: null,
                        message: 'dont have replay by comment in post on other post',
                        status: 403
                    );
                }
            }

            $user = auth()->guard(getUserGuard())->user();
            $userId = $user->id;
            $comment = Comment::create(
                [
                    'comment' => $request->comment,
                    'post_id' => $request->post,
                    'comment_id' => $request->replay,
                    'user_id' => $userId
                ]
            );

            if ($request->hasFile('images')) {

                $paths = $this->uploadManyFile(
                    request: $request,
                    key: 'images',
                    folder: 'comment/' . $comment->id
                );

                foreach ($paths as $path) {

                    MultiMediaComment::create(
                        [
                            'path' => $path,
                            'comment_id' => $comment->id
                        ]
                    );
                }
            }


            $post = $comment->post;
            if (!$request->replay && $post->user_id != $userId) {

                //send notification to user created post
                event(new CreateCommentOnPostEvent($comment, $user));
            } else {

                $commentReplay = $comment->commentReplay;
                if ($commentReplay->user_id != $userId) {

                    //send notification to user create base comment
                    event(new CreateCommentReplayOnCommentEvent($comment, $user));
                }
            }


            return $this->editResponse(
                title: 'comment',
                type: 'insert'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end store


    public function edit(KeyRequest $request)
    {

        try {

            $comment = Comment::find($request->key);
            if (!$comment) {

                return $this->notFoundEditResponse(
                    type: 'comment',
                    key: $request->key
                );
            }

            $userAddId = $comment->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            $comment = new CommentEditResource($comment);
            return $this->SelectResponse(
                data: $comment,
                type: 'comment'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end edit


    public function update(UserUpdateCommentRequest $request)
    {

        try {

            $comment = Comment::find($request->key);
            if (!$comment) {

                return $this->notFoundEditResponse(
                    type: 'comment',
                    key: $request->key
                );
            }

            if (!$request->comment && !$request->hasFile('images')) {

                return $this->emptyRequestResponse();
            }

            $userAddId = $comment->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            $comment->update(
                [
                    'comment' => $request->comment
                ]
            );

            $multiMedias = $comment->multiMedias;
            foreach ($multiMedias as $multiMedia) {

                $multiMedia->delete();
                $this->deleteFile(
                    path: $multiMedia->path
                );
            }

            if ($request->hasFile('images')) {

                $paths = $this->uploadManyFile(
                    request: $request,
                    key: 'images',
                    folder: 'comment/' . $comment->id
                );

                foreach ($paths as $path) {

                    MultiMediaComment::create(
                        [
                            'path' => $path,
                            'comment_id' => $comment->id
                        ]
                    );
                }
            }

            return $this->editResponse(
                title: 'comment',
                type: 'update'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end update


    public function delete(KeyRequest $request)
    {

        try {

            $comment = Comment::find($request->key);
            if (!$comment) {

                return $this->notFoundEditResponse(
                    type: 'comment',
                    key: $request->key
                );
            }

            $userAddId = $comment->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            $mutliMedias = $comment->multiMedias;
            $comment->delete();
            foreach ($mutliMedias as $multiMedia) {

                $this->deleteFile(
                    path: $multiMedia->path
                );
            }
            return $this->editResponse(
                title: 'comment',
                type: 'deleted'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end delete
}
