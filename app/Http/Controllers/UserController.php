<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Services\User\UserService;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = $this->userService->getAll();

        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserRequest $request)
    {
        $user = $this->userService->create($request->validated());

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
