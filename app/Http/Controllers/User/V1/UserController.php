<?php

namespace App\Http\Controllers\User\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\V1\UserStoreRequest;
use App\Http\Requests\User\V1\UserUpdateRequest;
use App\Http\Resources\User\V1\UserResource;
use App\Http\Services\UserService;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        public UserService $userService,   
    )
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        return response()->json(
            UserResource::make($this->userService->createUser($request->validated())),
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json(
            UserResource::make($this->userService->getUser($user)),
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        return response()->json(
            UserResource::make($this->userService->updateUser($user, $request->validated())),
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
