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
        return $this->userService->getAll();
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
    public function show(int $id)
    {
        return $this->userService->getById($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $user = $this->userService->update($request->validated(), $id);

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        return $this->userService->delete($id);
    }
    
    public function desactivate(int $id)
    {
        return $this->userService->desactivate($id);
    }

    public function activate(int $id)
    {
        return $this->userService->activate($id);
    }

    public function addRoles(Request $request, int $id)
    {
        $roleIds = $request->input('role_ids', $request->input('roleIds', []));

        if (! is_array($roleIds)) {
            $roleIds = [$roleIds];
        }

        $user = $this->userService->addRoles($id, $roleIds);

        return new UserResource($user);
    }

    public function removeRoles(Request $request, int $id)
    {
        $roleIds = $request->input('role_ids', $request->input('roleIds', []));

        if (! is_array($roleIds)) {
            $roleIds = [$roleIds];
        }

        $user = $this->userService->removeRoles($id, $roleIds);

        return new UserResource($user);
    }
}
