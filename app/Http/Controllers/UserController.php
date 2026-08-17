<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Services\Auth\LdapAuthenticationService;
use App\Http\Services\User\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        private readonly LdapAuthenticationService $ldapAuthenticationService,
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->userService->getAll($request);
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
    public function update(UpdateUserRequest $request, int $id)
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

    public function findLdapUser(
        Request $request
    ): JsonResponse {
        $data = $request->validate([
            'username' => [
                'required',
                'string',
            ],
        ]);

        $user = $this
            ->ldapAuthenticationService
            ->findByUsername($data['username']);

        if (! $user) {
            return response()->json([
                'message' => 'Usuario no encontrado en LDAP.',
            ], 404);
        }

        return response()->json($user);
    }
}
