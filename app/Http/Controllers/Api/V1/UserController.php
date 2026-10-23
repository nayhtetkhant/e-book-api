<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\UserStoreRequest;
use App\Http\Services\UserService;
use Illuminate\Http\Request;

use function App\Helpers\successResponse;

class UserController extends Controller
{

    public function __construct(
        private UserService $userService,
    )
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {

        return successResponse($this->userService->createUser($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
