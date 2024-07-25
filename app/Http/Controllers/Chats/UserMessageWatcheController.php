<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyPageRequest;
use App\Http\Requests\KeyRequest;
use App\Http\Resources\User\Message\MessageSeenResource;
use App\Jobs\WatcheMessageJop;
use App\Models\Chat;
use App\Models\Message;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectPageResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;
use Illuminate\Http\Request;

class UserMessageWatcheController extends Controller
{
    use NotFoundEditResponse, PermissionResponse, EditResponse, SelectPageResponse, ServerErrorResponse;

    public function index(KeyPageRequest $request)
    {
        try {
            //message exist or no
            $message = Message::find($request->key);
            if (!$message) {

                return $this->notFoundEditResponse(
                    type: 'message',
                    key: $request->key
                );
            }

            //user auth is write this message or no
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $userWriteId = $message->user_id;
            if ($userAuthId != $userWriteId) {

                return $this->permissionResponse();
            }

            //get message seens
            $seens = $message->userWatches()
                ->wherePivot('user_id', '!=', $userAuthId)
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'page', $request->page);

            return $this->selectPageResponse(
                data: MessageSeenResource::collection($seens->items()),
                page: $seens->currentPage(),
                lastPage: $seens->lastPage(),
                type: 'message seens'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end index


    public function store(KeyRequest $request)
    {

        try {

            //CHAT EXIST OR NO
            $chat = Chat::find($request->key);
            if (!$chat) {

                return $this->notFoundEditResponse(
                    type: 'chat',
                    key: $request->key
                );
            }

            //USER AUTH MEMBER OR NO
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $userInChat = $chat->users()->wherePivot('user_id', $userAuthId)->first();
            if (!$userInChat) {
                return $this->permissionResponse();
            }

            //store watche unseen messages
            dispatch(new WatcheMessageJop($chat, $userAuthId));

            //SUCCESS RESPONSE
            return $this->editResponse(
                title: 'watche messages chat',
                type: 'inserted'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }

    } //end store

}//end UserMessageWatcheController
