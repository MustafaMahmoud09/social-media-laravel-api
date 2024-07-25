<?php

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::routes(['middleware' => ['custom-auth:' . getUserGuard()]]);

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel(
    'chat-info.{chat_id}',
    function ($user, $chat_id) {
        //user exist in chat or no
        $chat = Chat::find($chat_id);
        return $chat->users()->wherePivot('user_id', $user->id)->exists();
    }
);


Broadcast::channel(
    'user-chat.{user_id}',
    function ($user, $user_id) {
        return $user->id == $user_id;
    }
);


Broadcast::channel(
    'seeners-info.{message_id}',
    function ($user, $message_id) {
        //user created this message or no
        $message = Message::find($message_id);
        return $message->user_id == $user->id;
    }
);


Broadcast::channel(
    'seeners-info.{message_id}',
    function ($user, $message_id) {
        //user exist in chat or no
        $message = Message::find($message_id);
        return $message->user_id == $user->id;
    }
);


Broadcast::channel(
    'message-react-info.{message_id}',
    function ($user, $message_id) {
        //get message chat here
        $chat = Message::find($message_id)->chat;
        //check user is member in message chat or no
        return $chat->users()->wherePivot('user_id', $user->id)->exists();
    }
);


