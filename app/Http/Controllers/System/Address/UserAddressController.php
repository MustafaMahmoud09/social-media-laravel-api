<?php

namespace App\Http\Controllers\System\Address;

use App\Http\Controllers\Controller;
use App\Http\Requests\KeyRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\User\Authorization\System\Address\UserUpdateAddressRequest;
use App\Http\Requests\User\Authorization\System\Address\UserAddAddressRequest;
use App\Http\Resources\User\System\Address\AddressResource;
use App\Http\Resources\User\System\Address\UserAddressResource;
use App\Models\Address;
use App\Traits\Controllers\Response\EditResponse;
use App\Traits\Controllers\Response\NotFoundEditResponse;
use App\Traits\Controllers\Response\PermissionResponse;
use App\Traits\Controllers\Response\SelectResponse;
use App\Traits\Controllers\Response\ServerErrorResponse;
use App\Traits\Controllers\Shared\GetAllData;
use App\Traits\Controllers\Shared\SearchOnData;
use Exception;
use Illuminate\Support\Facades\DB;

class UserAddressController extends Controller
{
    use GetAllData, SearchOnData, EditResponse, SelectResponse, NotFoundEditResponse, PermissionResponse, ServerErrorResponse;
    public function __construct()
    {
    }

    public function index()
    {

        try {

            return $this->getAllData(
                model: Address::class,
                resource: AddressResource::class,
                type: 'addresses'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end index

    public function search(SearchRequest $request)
    {

        try {

            return $this->searchOnData(
                model: Address::class,
                resource: AddressResource::class,
                request: $request,
                type: 'address',
                key: 'position'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end search

    public function userAddresses()
    {

        try {

            $user = auth()->guard(getUserGuard())->user();

            $data = $user->address;
            $data = UserAddressResource::collection($data);

            return $this->SelectResponse(
                data: $data,
                type: 'addresses'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end userGender

    public function searchOnAddress(SearchRequest $request)
    {

        try {

            $user = auth()->guard(getUserGuard())->user();

            $data = $user->address()->where('position', 'like', '%' . $request->search_key . '%')->get();
            $data = UserAddressResource::collection($data);

            return $this->SelectResponse(
                data: $data,
                type: 'addresses'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end userGender

    public function store(UserAddAddressRequest $request)
    {

        try {

            $userAuthId = auth()->guard(getUserGuard())->user()->id;

            $userAddress = DB::table('user_address')->insert([
                'type' => $request->type,
                'address_id' => $request->address,
                'user_id' => $userAuthId,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return $this->editResponse(
                title: 'address',
                type: 'store'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end store

    public function edit(KeyRequest $request)
    {

        try {

            $user = auth()->guard(getUserGuard())->user();

            $data = $user->address()->where('user_address.id', $request->key)->first();
            if (!$data) {

                return $this->notFoundEditResponse(
                    type: 'address',
                    key: $request->key
                );
            }


            $userAddId = $data->pivot->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;

            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            $data = new UserAddressResource($data);
            return $this->SelectResponse(
                data: $data,
                type: 'address'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end edit

    public function update(UserUpdateAddressRequest $request)
    {

        try {

            $data = DB::table('user_address')->find($request->key);
            if (!$data) {

                return $this->notFoundEditResponse(
                    type: 'phone number',
                    key: $request->key
                );
            }

            $userAddId = $data->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            DB::table('user_address')->where('id', $request->key)->update(
                [
                    'type' => $request->type,
                    'address_id' => $request->address
                ]
            );

            return $this->editResponse(
                title: 'address',
                type: 'update'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end update


    public function delete(KeyRequest $request)
    {

        try {

            $data = DB::table('user_address')->find($request->key);
            if (!$data) {

                return $this->notFoundEditResponse(
                    type: 'address',
                    key: $request->key
                );
            }

            $userAddId = $data->user_id;
            $userAuthId = auth()->guard(getUserGuard())->user()->id;
            if ($userAddId != $userAuthId) {

                return $this->permissionResponse();
            }

            DB::table('user_address')->where('id', $request->key)->delete();
            return $this->editResponse(
                title: 'address',
                type: 'delete'
            );
        } catch (Exception $ex) {

            return $this->serverErrorResponse();
        }
    } //end delete
}
