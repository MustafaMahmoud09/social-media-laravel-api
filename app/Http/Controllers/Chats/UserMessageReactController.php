<?php

namespace App\Http\Controllers\Chats;

use App\Events\Chat\AddReactOnMessageEvent;
use App\Events\Chat\DeleteReactMessageEvent;
use App\Events\Chat\MessageSendEvent;
use App\Events\Chat\UpdateMessageEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\KeyPageRequest;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\User\Authorization\Message\UserAddReactOnMessageRequest;
use App\Http\Requests\User\Authorization\UserUpdateReactRequest;
use App\Http\Resources\User\Message\MessageRootResource;
use App\Http\Resources\User\UserReactResource;
use App\Models\Message;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectPageResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;
use Illuminate\Support\Facades\DB;

class UserMessageReactController extends Controller
{
    use PermissionResponse, EditResponse, NotFoundEditResponse, SelectPageResponse, ServerErrorResponse;

    public function index(KeyPageRequest $request)
    {
        try {
            //check message exist or no
            $message = Message::find($request->key);
            if (!$message) {

                return $this->notFoundEditResponse(
                    type: 'message',
                    key: $request->key
                );
            }

            //check user exist in chat as member or no
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $chat = $message->chat;
            $isMember = $chat->users()->wherePivot('user_id', $userAuthId)->exists();
            if (!$isMember) {

                return $this->permissionResponse();
            }

            //get message reacts
            $reacts = $message->userReacts()
                ->orderBy('type', 'asc')
                ->paginate(10, ['*'], 'page', $request->page);

            //success response
            return $this->selectPageResponse(
                data: UserReactResource::collection($reacts),
                page: $reacts->currentPage(),
                lastPage: $reacts->lastPage(),
                type: 'messages'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end index


    public function store(UserAddReactOnMessageRequest $request)
    {
        try {
            //user exist in chat or no
            $userAuthId = auth()->guard(getUserGuard())->user()->id;

            $message = Message::find($request->message);
            $chat = $message->chat;

            $userInChat = $chat->users()->wherePivot('user_id', $userAuthId)->first();
            if (!$userInChat) {

                return $this->permissionResponse();
            }

            $react = DB::table('message_user_reacts')
                ->where('user_id', $userAuthId)
                ->where('message_id', $request->message)
                ->first();


            if ($react) {

                $react = DB::table('message_user_reacts')
                    ->where('user_id', $userAuthId)
                    ->where('message_id', $request->message)
                    ->update(
                        [
                            'type' => $request->type,
                            'updated_at' => now()
                        ]
                    );
            } //end if
            else {

                //add react
                $react = DB::table('message_user_reacts')
                    ->insert(
                        [
                            'type' => $request->type,
                            'user_id' => $userAuthId,
                            'message_id' => $request->message,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    );
            } //end else

            //realtime database to send message in realtime
            broadcast(new UpdateMessageEvent(new MessageRootResource($message)));

            //realtime database for send message react to members
            broadcast(
                new AddReactOnMessageEvent(
                    react: new UserReactResource($react),
                    messageId: $request->message
                )
            );

            //success response
            return $this->editResponse(
                title: 'message react',
                type: 'inserted'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end store


    public function update(UserUpdateReactRequest $request)
    {

        try {
            //react exist or no
            $react = DB::table('message_user_reacts')->find($request->key);
            if (!$react) {

                return $this->notFoundEditResponse(
                    type: 'message react',
                    key: $request->key
                );
            }

            //user auth is added react or no
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $userAddId = $react->user_id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            //update react
            DB::table('message_user_reacts')
                ->where('id', $request->key)
                ->update(
                    [
                        'type' => $request->type,
                        'updated_at' => now()
                    ]
                );

            //success response
            return $this->editResponse(
                title: 'message react',
                type: 'update'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end update


    public function delete(KeyRequest $request)
    {
        try {
            //react exist or no
            $react = DB::table('message_user_reacts')->find($request->key);
            if (!$react) {

                return $this->notFoundEditResponse(
                    type: 'message react',
                    key: $request->key
                );
            }

            //user auth is added react or no
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $userAddId = $react->user_id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            //delete react
            DB::table('message_user_reacts')
                ->where('id', $request->key)
                ->delete();

            //get message here
            $message = Message::find($react->message_id);

            //realtime database to send message in realtime
            broadcast(new UpdateMessageEvent(new MessageRootResource($message)));

            //realtime database for delete react message from reacts
            broadcast(
                new DeleteReactMessageEvent(
                    reactId: $react->id,
                    messageId: $react->message_id
                )
            );

            //success response
            return $this->editResponse(
                title: 'message react',
                type: 'delete'
            );
        } //end try
        catch (Exception $ex) {
            return $this->serverErrorResponse();
        } //end catch

    } //end delete

}//end UserMessageReactController
