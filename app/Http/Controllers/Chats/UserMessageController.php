<?php

namespace App\Http\Controllers\Chats;

use App\Events\Chat\DeleteMessageEvent;
use App\Events\Chat\MessageSendEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\KeyPageRequest;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\User\Authorization\Message\UserAddMessageRequest;
use App\Http\Requests\User\Authorization\Message\UserSearchOnMessageRequest;
use App\Http\Resources\User\Message\MessageRootResource;
use App\Jobs\SendChatToMembersJob;
use App\Models\Chat;
use App\Models\Message;
use App\Models\MultiMediaMessage;
use App\Traits\Controllers\File\DeleteFile;
use App\Traits\Controllers\File\UploadManyFile;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\EmptyRequestResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectPageResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;

class UserMessageController extends Controller
{
    use EmptyRequestResponse, PermissionResponse, UploadManyFile, EditResponse, NotFoundEditResponse, DeleteFile, SelectPageResponse, ServerErrorResponse;

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

            //user member in chat or no
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $isMember = $chat->users()->wherePivot('user_id', $userAuthId)->exists();
            if (!$isMember) {

                return $this->permissionResponse();
            }

            //get chat messages
            $messages = $chat->messages()
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'page', $request->page);

            return $this->selectPageResponse(
                data: MessageRootResource::collection($messages->items()),
                page: $messages->currentPage(),
                lastPage: $messages->lastPage(),
                type: 'messages'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end index


    public function search(UserSearchOnMessageRequest $request)
    {
        try {
            // Check if the chat exists
            $chat = Chat::find($request->chat);
            if (!$chat) {
                return $this->notFoundEditResponse(
                    type: 'chat',
                    key: $request->chat
                );
            }

            // Check if the authenticated user is a member of the chat
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $isMember = $chat->users()->wherePivot('user_id', $userAuthId)->exists();
            if (!$isMember) {
                return $this->permissionResponse();
            }

            // Search for messages in the chat containing the search key
            $messages = $chat->messages()
                ->where('message', 'like', '%' . $request->search_key . '%')
                ->orderBy('created_at', 'desc')
                ->paginate(10, ['*'], 'page', $request->page);

            // Return the search results with pagination
            return $this->selectPageResponse(
                data: MessageRootResource::collection($messages->items()),
                page: $messages->currentPage(),
                lastPage: $messages->lastPage(),
                type: 'messages'
            );
        } catch (Exception $ex) {
            // Handle any errors and return a server error response
            return $this->serverErrorResponse();
        }
    } // end search


    public function store(UserAddMessageRequest $request)
    {
        try {
            // Authenticate the user and retrieve user details
            $user = auth()->guard(getUserGuard())->user();
            $userAuthId = $user->id;

            // Retrieve the chat instance and check if the user is a member of the chat
            $chat = Chat::find($request->chat);
            $userInChat = $chat->users()->wherePivot('user_id', $userAuthId)->first();

            // Check if the user is a member of the chat
            if (!$userInChat) {
                return $this->permissionResponse();
            }

            // Validate the presence of message content or attached images
            if (!$request->message && !$request->hasFile('images')) {
                return $this->emptyRequestResponse();
            }

            // Validate the replay message, ensuring it belongs to the same chat
            if ($request->replay) {
                $baseMessage = Message::find($request->replay);
                if ($baseMessage->chat_id != $request->chat) {
                    return responseFormat(
                        data: null,
                        message: 'Invalid replay: Message belongs to a different chat',
                        status: 403
                    );
                }
            }

            // Create a new message record in the database
            $message = Message::create([
                'chat_id' => $request->chat,
                'user_id' => $userAuthId,
                'message' => $request->message,
                'message_id' => $request->replay
            ]);

            // Handle image attachments, if any
            if ($request->hasFile('images')) {
                // Store images on the server
                $paths = $this->uploadManyFile(
                    request: $request,
                    key: 'images',
                    folder: 'message/' . $userAuthId . '/' . $message->chat_id . '/' . $message->id
                );

                // Save image paths in the database
                foreach ($paths as $path) {
                    MultiMediaMessage::create([
                        'path' => $path,
                        'message_id' => $message->id
                    ]);
                }
            }

            // Update the 'last_message' timestamp for the chat
            DB::table('chats')
                ->where('id', $request->chat)
                ->update(['last_message' => now()]);

            // Record that the user has seen the message
            DB::table('message_user_watches')->insert([
                'user_id' => $userAuthId,
                'message_id' => $message->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Broadcast the new message in real-time
            broadcast(new MessageSendEvent(new MessageRootResource($message), $request->chat));

            // Dispatch a job to send this chat after updated to chat members
            dispatch(new SendChatToMembersJob($chat));

            // Return a success response
            return $this->editResponse(
                title: 'message',
                type: 'inserted'
            );
        } catch (Exception $ex) {
            // Handle any errors and return a server error response
            return $this->serverErrorResponse();
        }
    } // end store


    public function delete(KeyRequest $request)
    {
        try {
            // Find the message by the provided key
            $message = Message::find($request->key);
            if (!$message) {
                return $this->notFoundEditResponse(
                    type: 'message',
                    key: $request->key
                );
            } //end if

            // Check if the authenticated user is the one who added the message
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            $userAddedId = $message->user_id;
            if ($userAuthId != $userAddedId) {
                return $this->permissionResponse();
            } //end if

            // Retrieve associated multimedia files and delete the message
            $multiMedias = $message->multiMedias;
            $message->delete();

            // Delete each associated multimedia file from the server
            foreach ($multiMedias as $multiMedia) {
                $this->deleteFile(
                    path: $multiMedia->path
                );
            } //end foreach

           // Broadcast the message deletion event to users subscribed to the chat
            broadcast(new DeleteMessageEvent($message));

            // Dispatch a job to notify chat members about the deleted message
            dispatch(new SendChatToMembersJob($message->chat));

            return $this->editResponse(
                title: 'message',
                type: 'deleted'
            );
        } catch (Exception $ex) {
            // Handle any errors and return a server error response
            return $this->serverErrorResponse();
        }
    } //end delete

}//end UserMessageController
