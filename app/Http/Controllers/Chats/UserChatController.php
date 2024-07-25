<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\PageRequest;
use App\Http\Requests\SearchPageRequest;
use App\Http\Requests\User\Authorization\Chat\UserAddChatRequest;
use App\Http\Requests\User\Authorization\Chat\UserUpdateChatRequest;
use App\Http\Resources\User\Chat\ChatRootResource;
use App\Jobs\SendChatToMembersJob;
use App\Jobs\SendDeleteChatNotificationToMembersJob;
use App\Models\Chat;
use App\Models\ChatImage;
use App\Traits\Controllers\File\UpdateFile;
use App\Traits\Controllers\File\UploadFile;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\EmptyRequestResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectPageResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;
use Illuminate\Support\Facades\DB;

class UserChatController extends Controller
{
    use UploadFile, EditResponse, NotFoundEditResponse, PermissionResponse, EmptyRequestResponse, UpdateFile, SelectPageResponse, ServerErrorResponse;

    public function index(PageRequest $request)
    {
        try {
            $user = auth()->guard(getUserGuard())->user();
            //get all user chats and sort this chats from last message بتاعت كل شات مبعوته امتي
            $chats = $user->chats()
                ->orderBy('last_message', 'desc')
                ->paginate(10, ['*'], 'page', $request->page);

            return $this->selectPageResponse(
                data: ChatRootResource::collection($chats->items()),
                page: $chats->currentPage(),
                lastPage: $chats->lastPage(),
                type: 'chats'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end index


    public function search(SearchPageRequest $request)
    {
        try {
            $user = auth()->guard(getUserGuard())->user();
            $chats = $user->chats()
                ->where('title', 'like', '%' . $request->search_key . '%')
                ->orderBy('last_message', 'desc')
                ->paginate(10, ['*'], 'page', $request->page);

            return $this->selectPageResponse(
                data: ChatRootResource::collection($chats->items()),
                page: $chats->currentPage(),
                lastPage: $chats->lastPage(),
                type: 'chats'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end search


    public function store(UserAddChatRequest $request)
    {
        try {
            //get auth user
            $user = auth()->guard(getUserGuard())->user();
            //chat is private -- type == false
            if (!$request->type) {

                if ($request->hasFile('image') || $request->title || count($request->users) > 1) {

                    return responseFormat(
                        data: null,
                        message: 'You do not have the right to add additional data in a private chat',
                        status: 403
                    );
                }

                //check chat already exist
                $chats = $user->chats()->wherePivot('type', false)->get();
                $chatUsers = $chats
                    ->flatMap(
                        function ($chat) {
                            return $chat->users;
                        }
                    );

                $check = false;
                $otherUserId = $request->users[0];
                foreach ($chatUsers as $chatUser) {

                    if ($chatUser->pivot->user_id == $otherUserId) {
                        $check = true;
                        break;
                    }
                }

                if ($check) {

                    return responseFormat(
                        data: null,
                        message: 'The chat already exists',
                        status: 403
                    );
                } //

                $userAuthTypeInChat = false;
            } else {

                if (!$request->title) {

                    return responseFormat(
                        data: null,
                        message: 'You must add a title to the group',
                        status: 403
                    );
                }
                $userAuthTypeInChat = true;
            }

            $userAuthId = $user->id;
            $chat = Chat::create(
                [
                    'title' => $request->title,
                    'type' => $request->type,
                    'user_id' => $userAuthId
                ]
            );

            //user chat -- type == true -- user is adminstrator
            DB::table('chat_users')->insert(
                [
                    'type' => $userAuthTypeInChat,
                    'user_id' => $userAuthId,
                    'chat_id' => $chat->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            //other user in chat
            foreach ($request->users as $user) {

                DB::table('chat_users')->insert(
                    [
                        'type' => false,
                        'user_id' => $user,
                        'chat_id' => $chat->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }

            if ($request->hasFile('image')) {

                $path = $this->uploadFile(
                    request: $request,
                    key: 'image',
                    folder: 'chat/' . $chat->id
                );

                ChatImage::create(
                    [
                        'path' => $path,
                        'chat_id' => $chat->id
                    ]
                );
            }

            //send chat to chat member
            dispatch(new SendChatToMembersJob($chat));

            return $this->editResponse(
                title: 'chat',
                type: 'store'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end store


    public function update(UserUpdateChatRequest $request)
    {
        try {

            $chat = Chat::find($request->key);
            if (!$chat) {

                return $this->notFoundEditResponse(
                    type: 'chat',
                    key: $request->key
                );
            }

            if (!$chat->type) {

                return responseFormat(
                    data: null,
                    message: 'The type of chat is private and you cannot update it',
                    status: 403
                );
            }

            if (!$request->title && !$request->hasFile('image')) {

                return $this->emptyRequestResponse();
            }

            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $userAuthInChat = $chat->users()->wherePivot('user_id', $userAuthId)->first();
            if (!$userAuthInChat) {

                return $this->permissionResponse();
            }

            if (!$userAuthInChat->pivot->type) {

                return $this->permissionResponse();
            }

            if ($request->title) {

                $chat->update(
                    [
                        'title' => $request->title
                    ]
                );
            }

            if ($request->hasFile('image')) {

                $chatImage = $chat->image;
                if ($chatImage) {

                    $path = $this->updateFile(
                        oldPath: $chatImage->path,
                        request: $request,
                        key: 'image',
                        folder: 'chat/' . $chat->id
                    );

                    $chatImage->update(
                        [
                            'paht' => $path
                        ]
                    );
                } else {

                    $path = $this->uploadFile(
                        request: $request,
                        key: 'image',
                        folder: 'chat/' . $chat->id
                    );

                    ChatImage::create(
                        [
                            'path' => $path,
                            'chat_id' => $chat->id
                        ]
                    );
                }
            }

            //send chat to chat member
            dispatch(new SendChatToMembersJob($chat));

            return $this->editResponse(
                title: 'chat',
                type: 'update'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }//end catch

    } //end update


    //delete group
    public function delete(KeyRequest $request)
    {
        try {
            $chat = Chat::find($request->key);
            if (!$chat) {

                return $this->notFoundEditResponse(
                    type: 'chat',
                    key: $request->key
                );
            }

            if (!$chat->type) {
                return responseFormat(
                    data: null,
                    message: 'The type of chat is private and you cannot delete it',
                    status: 403
                );
            }//end if

            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $userAddId = $chat->user_id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            //get chat member here
            $chatMembers = $chat->users()->get();

            //delete chat here
            $chat->delete();

            //send delete chat notification here
            dispatch(new SendDeleteChatNotificationToMembersJob($chatMembers, $request->key));

            return $this->editResponse(
                title: 'chat',
                type: 'deleted'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end delete

}//end UserChatController
