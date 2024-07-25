<?php

namespace App\Http\Controllers\Posts\User;

use App\Events\Post\CreatePostEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\PageRequest;
use App\Http\Requests\SearchPageRequest;
use App\Http\Requests\User\Authorization\Post\UserAddPostRequest;
use App\Http\Requests\User\Authorization\Post\UserUpdatePostRequest;
use App\Http\Resources\User\Post\PostEditResource;
use App\Http\Resources\User\Post\PostShowResource;
use App\Models\MultiMediaPost;
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

class UserPostController extends Controller
{
    use UploadManyFile, EmptyRequestResponse, EditResponse, NotFoundEditResponse, PermissionResponse, SelectResponse, DeleteFile, SelectPageResponse, ServerErrorResponse;

    public function index(PageRequest $request)
    {
        try {

            $user = auth()->guard(getUserGuard())->user();

            $data = $user->followings->flatMap(function ($followings) {
                return $followings->posts;
            })->sortByDesc('created_at')->forPage($request->page, 10);

            $postCount = (int) count(
                $user->followings->flatMap(function ($followings) {
                    return $followings->posts;
                })
            );

            $lastPage = $postCount / 10;

            if ($postCount % 10 != 0 || $postCount == 0) {
                $lastPage += 1;
            } //end if

            return $this->selectPageResponse(
                data: PostShowResource::collection($data),
                page: $request->page,
                lastPage: $lastPage,
                type: 'posts'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end index

    public function search(SearchPageRequest $request)
    {

        try {

            $data = Post::where('post', 'like', '%' . $request->search_key . '%')
                ->paginate(10, ['*'], 'page', $request->page);

            return $this->selectPageResponse(
                data: PostShowResource::collection($data->items()),
                page: $data->currentPage(),
                lastPage: $data->lastPage(),
                type: 'posts'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end search


    public function store(UserAddPostRequest $request)
    {

        try {

            if (!$request->post && !$request->share && !$request->hasFile('images')) {

                return $this->emptyRequestResponse();
            }

            $user = auth()->guard(getUserGuard())->user();
            $userAuthId = $user->id;

            $basePostId = null;
            if ($request->share) {
                $basePost = Post::find($request->share);
                if ($basePost->post_id) {

                    $basePostId = $basePost->post_id;
                } else {

                    $basePostId = $basePost->id;
                }
            }

            $post = Post::create(
                [
                    'post' => $request->post,
                    'post_id' => $basePostId,
                    'user_id' => $userAuthId
                ]
            );

            if ($request->hasFile('images') && !$request->share) {

                $pathes = $this->uploadManyFile(
                    request: $request,
                    key: 'images',
                    folder: 'post/' . $userAuthId . '/' . $post->id
                );

                foreach ($pathes as $path) {

                    MultiMediaPost::create(
                        [
                            'post_id' => $post->id,
                            'path' => $path
                        ]
                    );
                }
            }

            //send database notification to followers
            event(new CreatePostEvent($post, $user));

            return $this->editResponse(
                title: 'post',
                type: 'store'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end store


    public function edit(KeyRequest $request)
    {

        try {

            $post = Post::find($request->key);
            if (!$post) {

                return $this->notFoundEditResponse(
                    type: 'post',
                    key: $request->key
                );
            }

            $userAddId = $post->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            $post = new PostEditResource($post);
            return $this->SelectResponse(
                data: $post,
                type: 'post'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end edit


    public function update(UserUpdatePostRequest $request)
    {

        try {

            $post = Post::find($request->key);
            if (!$post) {

                return $this->notFoundEditResponse(
                    type: 'post',
                    key: $request->key
                );
            }

            if (!$request->post && !$post->post_id && !$request->hasFile('images')) {

                return $this->emptyRequestResponse();
            }

            $userAddId = $post->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            $post->update(
                [
                    'post' => $request->post,
                ]
            );

            $oldMultiMedias = $post->multiMedias()->get();

            foreach ($oldMultiMedias as $multiMedia) {

                $multiMedia->delete();
                $this->deleteFile(
                    path: $multiMedia->path
                );
            }

            if ($request->hasFile('images') && !$post->post_id) {


                $pathes = $this->uploadManyFile(
                    request: $request,
                    key: 'images',
                    folder: 'post/' . $userAuthId . '/' . $post->id
                );

                foreach ($pathes as $path) {

                    MultiMediaPost::create(
                        [
                            'post_id' => $post->id,
                            'path' => $path
                        ]
                    );
                }
            }

            return $this->editResponse(
                title: 'post',
                type: 'update'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    }


    public function destroy(KeyRequest $request)
    {

        try {

            $post = Post::find($request->key);
            if (!$post) {

                return $this->notFoundEditResponse(
                    type: 'post',
                    key: $request->key
                );
            }

            $userAddId = $post->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            $multiMedias = $post->multiMedias;
            $post->delete();

            foreach ($multiMedias as $multiMedia) {

                $this->deleteFile(
                    path: $multiMedia->path
                );
            }

            return $this->editResponse(
                title: 'post',
                type: 'deleted'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end delete

}//end PostController
