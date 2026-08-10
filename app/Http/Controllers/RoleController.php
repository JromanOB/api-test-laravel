<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Services\Role\RoleService;
use App\Http\Requests\Role\CreateRoleRequest;

class RoleController extends Controller {
    public function __construct(protected RoleService $roleService) {}

    public function index()
    {
        return $this->roleService->getAll();
    }

    public function store(CreateRoleRequest $request)
    {
        return $this->roleService->create($request->validated());
    }

    public function show(int $id)
    {
        return $this->roleService->getById($id);
    }

    public function update(Request $request, int $id)
    {
        return $this->roleService->update($request->validated(), $id);
    }

    public function destroy(int $id)
    {
        return $this->roleService->delete($id);
    }
}
