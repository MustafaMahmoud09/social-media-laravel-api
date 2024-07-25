<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\PageRequest;
use App\Http\Resources\User\Notification\NotificationResource;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectPageResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use Exception;
use Illuminate\Http\Request;

class UserNotificationController extends Controller
{

    use SelectPageResponse, ServerErrorResponse, EditResponse, PermissionResponse, NotFoundEditResponse;

    public function index(PageRequest $request)
    {
        try {
            $user = auth()->guard(getUserGuard())->user();

            $notifications = $user->notifications()
                ->orderByDesc('created_at')
                ->paginate(10, ['*'], 'page', $request->page);


            return $this->selectPageResponse(
                data: NotificationResource::collection($notifications->items()),
                page: $notifications->currentPage(),
                type: 'notifications',
                lastPage: $notifications->lastPage()
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end index


    public function readAllNotifications()
    {
        try {
            $user = auth()->guard(getUserGuard())->user();

            $notifications = $user->unreadNotifications;

            $notifications->markAsRead();

            return $this->editResponse(
                title: "notifications",
                type: 'read'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end readAllNotifications



    public function readSingleNotification(KeyRequest $request)
    {
        try {
            $user = auth()->guard(getUserGuard())->user();

            $notification = $user->unreadNotifications()->find($request->key);

            if (!$notification) {
                return $this->notFoundEditResponse(
                    type: 'notification',
                    key: $request->key
                );
            }

            if ($notification->notifiable_id != $user->id) {
                return $this->permissionResponse();
            } //end if

            $notification->markAsRead();

            return $this->editResponse(
                title: "notification",
                type: 'read'
            );
        } catch (Exception $ex) {
            return $this->serverErrorResponse();
        }
    } //end readSingleNotification


}//end UserNotificationController
