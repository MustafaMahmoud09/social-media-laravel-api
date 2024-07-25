<?php

namespace App\Http\Controllers\Chats;

use App\Events\Chat\AddMemeberToChatEvent;
use App\Events\Chat\DeleteChatMemberEvent;
use App\Events\Chat\UpdateChatMemberEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\KeyPageRequest;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\User\Authorization\Chat\UserAddMemberToChatRequest;
use App\Http\Requests\User\Authorization\Chat\UserUpdateMemberChatRequest;
use App\Http\Resources\User\Chat\ChatMemberResource;
use App\Models\Chat;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectPageResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;
use Illuminate\Support\Facades\DB;

class UserMemberChatController extends Controller
{
    use PermissionResponse, NotFoundEditResponse, EditResponse, SelectPageResponse, ServerErrorResponse;

    public function index(KeyPageRequest $request)
    {

        try {

            //chat exist or no
            $chat = Chat::find($request->key);
            if (!$chat) {

                return $this->notFoundEditResponse(
                    type: 'chat',
                    key: $request->key
                );
            }

            //user exit in chat or no
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $isMember = $chat->users()->wherePivot('user_id', $userAuthId)->exists();
            if (!$isMember) {

                return $this->permissionResponse();
            }

            //get memebers and sort memebers
            $members = $chat->users()
                ->orderBy('type', 'desc')
                ->paginate(10, ['*'], 'page', $request->page);

            //success reponse
            return $this->selectPageResponse(
                data: ChatMemberResource::collection($members->items()),
                page: $members->currentPage(),
                lastPage: $members->lastPage(),
                type: 'chat members'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end index


    public function store(UserAddMemberToChatRequest $request)
    {

        try {

            $chat = Chat::find($request->chat);
            if (!$chat) {

                return $this->notFoundEditResponse(
                    type: 'chat',
                    key: $request->chat
                );
            }

            if (!$chat->type) {

                return responseFormat(
                    data: null,
                    message: 'The type of chat is private and you cannot update it',
                    status: 403
                );
            }

            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $userAuthInChat = $chat->users()->wherePivot('user_id', $userAuthId)->first();

            if (!$userAuthInChat) {

                return $this->permissionResponse();
            }

            if (!$userAuthInChat->pivot->type) {

                return $this->permissionResponse();
            }

            foreach ($request->users as $user) {

                $member = DB::table('chat_users')->insert(
                    [
                        'chat_id' => $request->chat,
                        'user_id' => $user,
                        'type' => false,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );

                broadcast(new AddMemeberToChatEvent(new ChatMemberResource($member), $request->chat));

            }

            return $this->editResponse(
                title: 'member chat',
                type: 'insert'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end store


    public function update(UserUpdateMemberChatRequest $request)
    {

        try {

            $memeber = DB::table('chat_users')->find($request->key);
            if (!$memeber) {

                return $this->notFoundEditResponse(
                    type: 'chat memeber',
                    key: $request->key
                );
            }

            $chat = Chat::find($memeber->chat_id);

            if (!$chat->type) {

                return responseFormat(
                    data: null,
                    message: 'The type of chat is private and you cannot update it',
                    status: 403
                );
            }

            if ($chat->user_id == $memeber->user_id) {

                return $this->permissionResponse();
            }

            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $userAuthInChat = $chat->users()->wherePivot('user_id', $userAuthId)->first();

            if (!$userAuthInChat) {

                return $this->permissionResponse();
            }

            if (!$userAuthInChat->pivot->type || $userAuthId == $memeber->user_id) {

                return $this->permissionResponse();
            }

            $member = DB::table('chat_users')
                ->where('id', $request->key)
                ->update(
                    [
                        'type' => $request->type,
                        'updated_at' => now()
                    ]
                );

            broadcast(new UpdateChatMemberEvent(new ChatMemberResource($member), $memeber->chat_id));

            return $this->editResponse(
                title: 'chat memeber',
                type: 'updated'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end update

    public function delete(KeyRequest $request)
    {

        try {

            $memeber = DB::table('chat_users')->find($request->key);
            if (!$memeber) {

                return $this->notFoundEditResponse(
                    type: 'chat memeber',
                    key: $request->key
                );
            }

            $chat = Chat::find($memeber->chat_id);

            if (!$chat->type) {

                return responseFormat(
                    data: null,
                    message: 'The type of chat is private and you cannot update it',
                    status: 403
                );
            }

            $userAuthId = auth()->guard(getUserGuard())->user()->id;

            if ($userAuthId != $chat->user_id || $userAuthId == $memeber->user_id) {

                return $this->permissionResponse();
            }

            DB::table('chat_users')
                ->where('id', $request->key)
                ->delete();

            broadcast(new DeleteChatMemberEvent($memeber->id, $memeber->chat_id));

            return $this->editResponse(
                title: 'member chat',
                type: 'deleted'
            );
        } catch (Exception) {

            return $this->serverErrorResponse();
        }

    } //end delete

}
