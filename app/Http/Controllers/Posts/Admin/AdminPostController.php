<?php

namespace App\Http\Controllers\Posts\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\PageRequest;
use App\Http\Requests\SearchPageRequest;
use App\Http\Resources\Admin\Media\PostRootResource;
use App\Models\Post;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectPageResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;
use Illuminate\Http\Request;

class AdminPostController extends Controller
{
    use SelectPageResponse, NotFoundEditResponse, EditResponse, PermissionResponse, ServerErrorResponse;
    public function index(PageRequest $request)
    {

        try {

            if (auth()->guard(getAdminGuard())->user()->can('show-post')) {

                //get all posts and sort post by number of reacts
                $posts = Post::withCount('userReacts')
                    ->orderBy('user_reacts_count', 'desc')
                    ->paginate(10, ['*'], 'page', $request->page);

                //success response
                return $this->selectPageResponse(
                    data: PostRootResource::collection($posts->items()),
                    page: $posts->currentPage(),
                    lastPage: $posts->lastPage(),
                    type: 'posts'
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

            if (auth()->guard(getAdminGuard())->user()->can('show-post')) {

                //search on posts and sort post by number of reacts
                $posts = Post::withCount('userReacts')
                    ->where('post', 'like', '%' . $request->search_key . '%')
                    ->orderBy('user_reacts_count', 'desc')
                    ->paginate(10, ['*'], 'page', $request->page);

                //success response
                return $this->selectPageResponse(
                    data: PostRootResource::collection($posts->items()),
                    page: $posts->currentPage(),
                    lastPage: $posts->lastPage(),
                    type: 'posts'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end search

    public function delete(KeyRequest $request)
    {

        try {

            if (auth()->guard(getAdminGuard())->user()->can('manage-post')) {

                //post exist or no
                $post = Post::find($request->key);
                if (!$post) {

                    return $this->notFoundEditResponse(
                        type: 'post',
                        key: $request->key
                    );
                }

                //delete post
                $post->delete();

                //success response
                return $this->editResponse(
                    title: 'post',
                    type: 'deleted'
                );
            }
            return $this->permissionResponse();
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end delete
}
