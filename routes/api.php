<?php

use App\Http\Controllers\Auth\{AdminController, UserController};
use App\Http\Controllers\Chats\{UserChatController, UserMemberChatController, UserMessageController, UserMessageReactController, UserMessageWatcheController};
use App\Http\Controllers\Comments\{UserCommentController, UserCommentReactController};
use App\Http\Controllers\Posts\Admin\{AdminPostController};
use App\Http\Controllers\Posts\User\{UserPostController, UserPostReactController};
use App\Http\Controllers\Status\{UserStatusController, UserStatusWatcheController};
use App\Http\Controllers\System\Address\{AdminAddressController, UserAddressController};
use App\Http\Controllers\System\Gender\{AdminGenderController, UserGenderController};
use App\Http\Controllers\System\PhoneNumber\{AdminCallIconController, UserPhoneNumberController};
use App\Http\Controllers\System\Profile\{UserCoverController, UserProfileController, UserSecurityController};
use App\Http\Controllers\System\Relationship\{AdminRelationshipController, UserRelationshipController};
use App\Http\Controllers\System\Social\{AdminSocialController};
use App\Http\Controllers\Users\Admin\{AdminManageUserProfileController};
use App\Http\Controllers\Users\User\{UserFollowController, UserProfileController as UserUserProfileController};
use App\Http\Controllers\Admins\{ManageAdminController};
use App\Http\Controllers\Broadcast\UseBroadcastAuthController;
use App\Http\Controllers\Notifications\UserNotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//admin Authentication
Route::post('admin/login', [AdminController::class, 'login']);
//admin Authorization
Route::group(
    [
        'middleware' => ['custom-auth:' . getAdminGuard()],
        'prefix' => 'admin/'
    ],
    function () {

        Route::get('logout', [AdminController::class, 'logout']);

        Route::controller(AdminGenderController::class)->group(
            function () {
                Route::post('gender/add', 'store');
                Route::get('geander/search', 'search');
                Route::get('gender/{id}', 'edit');
                Route::put('gender/update', 'update');
                Route::delete('gender/delete/{id}', 'destroy');
                Route::delete('gender/archive/delete/{id}', 'destroyArchives');
                Route::get('gender/get/all', 'index');
                Route::get('gender/archive/get/all', 'showArchives');
                Route::get('gender/archive/search', 'searchInArchive');
                Route::put('gender/archive/restore/{id}', 'restore');
            }
        );

        Route::controller(AdminAddressController::class)->group(
            function () {
                Route::post('address/add', 'store');
                Route::get('address/search', 'search');
                Route::get('address/{id}', 'edit');
                Route::put('address/update', 'update');
                Route::delete('address/delete/{id}', 'destroy');
                Route::delete('address/archive/delete/{id}', 'destroyArchives');
                Route::get('address/get/all', 'index');
                Route::get('address/search', 'search');
                Route::get('address/archive/get/all', 'showArchives');
                Route::get('address/archive/search', 'searchInArchive');
                Route::put('address/archive/restore/{id}', 'restore');
            }
        );

        Route::controller(AdminRelationshipController::class)->group(
            function () {
                Route::post('relationship/add', 'store');
                Route::get('relationship/search', 'search');
                Route::get('relationship/{id}', 'edit');
                Route::put('relationship/update', 'update');
                Route::delete('relationship/delete/{id}', 'destroy');
                Route::delete('relationship/archive/delete/{id}', 'destroyArchives');
                Route::get('relationship/get/all', 'index');
                Route::get('relationship/search', 'search');
                Route::get('relationship/archive/get/all', 'showArchives');
                Route::get('relationship/archive/search', 'searchInArchive');
                Route::put('relationship/archive/restore/{id}', 'restore');
            }
        );

        Route::controller(AdminCallIconController::class)->group(
            function () {
                Route::post('call/add', 'store');
                Route::get('call/search', 'search');
                Route::get('call/{id}', 'edit');
                Route::put('call/update', 'update');
                Route::delete('call/delete/{id}', 'destroy');
                Route::delete('call/archive/delete/{id}', 'destroyArchives');
                Route::get('call/get/all', 'index');
                Route::get('call/search', 'search');
                Route::get('call/archive/get/all', 'showArchives');
                Route::get('call/archive/search', 'searchInArchive');
                Route::put('call/archive/restore/{id}', 'restore');
            }
        );

        Route::controller(AdminSocialController::class)->group(
            function () {
                Route::get('social/edit', 'edit');
                Route::post('social/add', 'store');
                Route::post('social/update', 'update');
                Route::delete('social/delete', 'destroy');
                Route::put('social/restore', 'restore');
                Route::delete('social/archive/delete', 'destroyArchives');
                Route::get('social/get/all', 'index');
                Route::get('social/search', 'search');
                Route::get('social/archive/get/all', 'showArchives');
                Route::get('social/archive/search', 'searchInArchive');
            }
        );

        Route::controller(AdminManageUserProfileController::class)->group(
            function () {
                Route::get('user/get/all', 'index');
                Route::get('user/search', 'search');
                Route::post('user/documentation/add', 'documment');
                Route::delete('user/documentation/delete', 'deleteDocument');
            }
        );

        Route::controller(AdminPostController::class)->group(
            function () {
                Route::get('media/post/get/all', 'index');
                Route::get('media/post/search', 'search');
                Route::delete('media/post/delete', 'delete');
            }
        );

        Route::controller(ManageAdminController::class)->group(
            function () {
                Route::post('admin/add', 'store');
                Route::get('admin/get/all', 'index');
                Route::get('admin/search', 'search');
            }
        );
    }
);


//user Authentication
Route::controller(UserController::class)->group(function () {
    Route::post("user/register", "register");
    Route::post("user/login", "login");
});

//user Authorization
Route::group(
    [
        'middleware' => ['custom-auth:' . getUserGuard()],
        'prefix' => 'user/'
    ],
    function () {

        Route::post('/broadcasting/auth', [UseBroadcastAuthController::class, 'authenticate']);

        Route::get('logout', [UserController::class, 'logout']);

        Route::controller(UserGenderController::class)->group(
            function () {
                Route::get('gender/get/all', 'index');
                Route::get('gender/search', 'search');
                Route::get('gender/get/me', 'userGender');
                Route::put('gender/update', 'update');
            }
        );

        Route::controller(UserPhoneNumberController::class)->group(
            function () {
                Route::get('call/get/all', 'index');
                Route::get('call/search', 'search');
                Route::get('phone/get/all', 'userPhoneNumber');
                Route::get('phone/search', 'searchOnPhoneNumber');
                Route::post('phone/add', 'store');
                Route::get('phone/edit', 'edit');
                Route::put('phone/update', 'update');
                Route::delete('phone/delete', 'delete');
            }
        );

        Route::controller(UserAddressController::class)->group(
            function () {
                Route::get('address/get/all', 'index');
                Route::get('address/search', 'search');
                Route::post('userAddress/add', 'store');
                Route::get('userAddress/get/all', 'userAddresses');
                Route::get('userAddress/search', 'searchOnAddress');
                Route::get('userAddress/edit', 'edit');
                Route::put('userAddress/update', 'update');
                Route::delete('userAddress/delete', 'delete');
            }
        );

        Route::controller(UserRelationshipController::class)->group(
            function () {
                Route::get('relationship/get/all', 'index');
                Route::get('relationship/search', 'search');
                Route::post('relationship/add', 'store');
                Route::get('relationship/get/me', 'userRelationship');
                Route::put('relationship/update', 'update');
                Route::delete('relationship/delete', 'delete');
            }
        );

        Route::controller(UserCoverController::class)->group(
            function () {
                Route::post('cover/add', 'store');
                Route::get('cover/edit', 'edit');
                Route::post('cover/update', 'update');
                Route::delete('cover/delete', 'delete');
                Route::get('cover/get/me/all', 'index');
                Route::get('cover/get/me/last', 'lastCover');
            }
        );

        Route::controller(UserProfileController::class)->group(
            function () {
                Route::post('profile/add', 'store');
                Route::get('profile/edit', 'edit');
                Route::post('profile/update', 'update');
                Route::delete('profile/delete', 'delete');
                Route::get('profile/get/me/all', 'index');
                Route::get('profile/get/me/last', 'lastProfile');
            }
        );

        Route::controller(UserPostController::class)->group(
            function () {
                Route::post('post/add', 'store');
                Route::get('post/edit', 'edit');
                Route::post('post/update', 'update');
                Route::delete('post/delete', 'destroy');
                Route::get('post/followings/get', 'index');
                Route::get('post/search', 'search');
            }
        );

        Route::controller(UserPostReactController::class)->group(
            function () {
                Route::post('post/react/add', 'store');
                Route::put('post/react/update', 'update');
                Route::delete('post/react/delete', 'delete');
                Route::get('post/react/get/all', 'index');
            }
        );

        Route::controller(UserFollowController::class)->group(
            function () {
                Route::post('follow/add', 'store');
                Route::delete('follow/delete', 'delete');
                Route::get('follow/followers/all', 'followers');
                Route::get('follow/followings/all', 'followings');
                Route::get('follow/followers/search', 'searchOnFollowers');
                Route::get('follow/followings/search', 'searchOnFollowings');
            }
        );

        Route::controller(UserCommentController::class)->group(
            function () {
                Route::post('comment/add', 'store');
                Route::get('comment/edit', 'edit');
                Route::post('comment/update', 'update');
                Route::delete('comment/delete', 'delete');
                Route::get('comment/post/get/all', 'index');
                Route::get('comment/replaies/get/all', 'replaiesComment');
            }
        );

        Route::controller(UserCommentReactController::class)->group(
            function () {
                Route::post('comment/react/add', 'store');
                Route::put('comment/react/update', 'update');
                Route::delete('comment/react/delete', 'delete');
                Route::get('comment/react/get/all', 'index');
            }
        );

        Route::controller(UserStatusController::class)->group(
            function () {
                Route::post('state/add', 'store');
                Route::delete('state/delete', 'delete');
                Route::get('state/following/get', 'index');
                Route::get('state/user/get', 'userStates');
            }
        );

        Route::controller(UserStatusWatcheController::class)->group(
            function () {
                Route::post('state/watch/add', 'store');
                Route::put('state/watch/update', 'update');
                Route::get('state/watch/get/all', 'index');
            }
        );
        Route::controller(UserChatController::class)->group(
            function () {
                Route::post('chat/add', 'store');
                Route::delete('chat/delete', 'delete');
                Route::post('chat/update', 'update');
                Route::get('chat/get/all', 'index');
                Route::get('chat/search', 'search');
            }
        );

        Route::controller(UserMemberChatController::class)->group(
            function () {
                Route::post('chat/member/add', 'store');
                Route::put('chat/member/update', 'update');
                Route::delete('chat/member/delete', 'delete');
                Route::get('chat/member/get', 'index');
            }
        );

        Route::controller(UserMessageController::class)->group(
            function () {
                Route::post('message/add', 'store');
                Route::delete('message/delete', 'delete');
                Route::get('message/chat/get/all', 'index');
                Route::get('message/chat/search', 'search');
            }
        );

        Route::controller(UserMessageReactController::class)->group(
            function () {
                Route::post('message/react/add', 'store');
                Route::put('message/react/update', 'update');
                Route::delete('message/react/delete', 'delete');
                Route::get('message/react/get/all', 'index');
            }
        );

        Route::controller(UserMessageWatcheController::class)->group(
            function () {
                Route::post('message/watche/add', 'store');
                Route::get('message/watche/get/all', 'index');
            }
        );

        Route::controller(UserSecurityController::class)->group(
            function () {
                Route::put('security/email/update', 'updateEmail');
                Route::put('security/password/update', 'updatePassword');
                Route::put('security/name/update', 'updateName');
                Route::get('basic/data/me', 'index');
            }
        );

        Route::controller(UserUserProfileController::class)->group(
            function () {
                Route::get('media/profile/search', 'search');
                Route::get('media/profile/post', 'userPosts');
                Route::get('media/profile/data/basic', 'userDataBasic');
                Route::get('media/profile/data/secondary', 'userDateSecondary');
            }
        );

        Route::controller(UserNotificationController::class)->group(
            function () {
                Route::get('notifications/get/all', 'index');
                Route::put('notifications/read/all', 'readAllNotifications');
                Route::put('notifications/read/single', 'readSingleNotification');
            }
        );

    }
);
