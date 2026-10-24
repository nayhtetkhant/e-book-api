<?php

namespace App\Http\Controllers\Admin\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\V1\UserStoreRequest;
use App\Http\Requests\Admin\V1\UserUpdateRequest;
use App\Http\Resources\Admin\V1\UserResource;
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
        return response()->json(
            UserResource::collection($this->userService->getAllUsers()),
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        return response()->json(
            $this->userService->createUser($request->validated()),
            201
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
