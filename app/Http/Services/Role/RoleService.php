<?php

namespace App\Http\Services\Role;

use App\Models\Role;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\JsonResponse;

class RoleService
{
    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return Role::query()->orderBy('id', 'asc')->get();
    }

    public function findByRolename(string $Rolename): Role|null
    {
        $role = Role::where('name', '=', $Rolename)->first();

        if (! $role) {
            throw new NotFoundHttpException('Rol no encontrado!');
        }

        return $role;
    }

    public function getById(int $id): Role|null
    {
        $role = Role::find($id);

        if (! $role) {
            throw new NotFoundHttpException('Rol no encontrado!');
        }

        return $role;
    }

    public function create(array $data): Role
    {
        return Role::create($data);
    }

    public function update(array $data, int $id): Role|null
    {
        $role = $this->getById($id);

        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]);
            }
        }
        $role->fill($data);
        $role->save();

        return $role;
    }

    public function delete(int $id): JsonResponse
    {
        $role = $this->getById($id);

        if (! $role) {
            throw new NotFoundHttpException('Rol no encontrado!');
        }

        if ($role->users()->count() > 0) {
            return response()->json(['message' => 'No se puede eliminar el rol porque tiene usuarios asociados!'], 400);
        }
        $role->delete();

        return response()->json(['message' => 'Rol eliminado exitosamente.'], 200);
    }
}