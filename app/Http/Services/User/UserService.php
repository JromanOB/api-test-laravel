<?php

namespace App\Http\Services\User;

use \Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;

class UserService
{
    public function getAll(Request $request) {
        $limit = $request->integer('limit', 10);
        $offset = $request->integer('offset', 0);
        $search = $request->query('search');

        $query = User::query()
            ->with('roles')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('username', 'like', "%{$search}%")
                        ->orWhere('fullname', 'like', "%{$search}%");
                });
            });

        $total = $query->count();

        $users = $query
            ->skip($offset)
            ->take($limit)
            ->get();

        return response()->json([
            'total' => $total,
            'rows' => $users,
        ]);
    }

    public function findByUsername(string $username): User
    {
        $user = User::with('roles')
            ->where('username', $username)
            ->first();

        if (! $user) {
            throw new NotFoundHttpException('Usuario no encontrado!');
        }

        if ($user->is_active != true) {
            throw new HttpResponseException(
                response()->json(['message' => 'El usuario no esta activo.'], 403)
            );
        }

        return $user;
    }

    public function getById(int $id): User|null
    {
        $user = User::with('roles')
            ->find($id);

        if (! $user) {
            throw new NotFoundHttpException('Usuario no encontrado!');
        }

        return $user;
    }

    public function checkCredentials(string $email, string $password): User|bool {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return false;
        }

        $isPasswordValid = Hash::check($password, $user->password);

        return $isPasswordValid ? $user : false;
    }

    public function create(array $data): User
    {
        $roleIds = $data['role_ids'];

        unset($data['role_ids']);

        $user = User::create($data);

        $user->roles()->sync($roleIds);

        return $user->load('roles');
    }

    public function update(array $data, int $id): User|null
    {
        $user = $this->getById($id);

        // Verifica si se enviaron roles
        $hasRoles = array_key_exists('role_ids', $data);

        // Obtiene los IDs de los roles
        $roleIds = $data['role_ids'] ?? [];

        // role_ids no es una columna de users
        unset($data['role_ids']);

        // Elimina únicamente campos null
        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]);
            }
        }

        $user->fill($data);
        $user->save();

        // Actualiza la relación muchos a muchos
        if ($hasRoles) {
            $user->roles()->sync($roleIds);
        }

        return $user->load('roles');
    }

    public function delete(int $id): JsonResponse
    {
        $user = $this->getById($id);

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado exitosamente.'], 200);
    }

    public function desactivate(int $id): JsonResponse
    {
        $user = $this->getById($id);

        $user->update(['is_active' => false]);

        return response()->json(['message' => 'Usuario desactivado exitosamente.'], 200);
    }

    public function activate(int $id): JsonResponse
    {
        $user = $this->getById($id);

        $user->update(['is_active' => true]);

        return response()->json(['message' => 'Usuario activado exitosamente.'], 200);
    }

    public function addRoles(int $id, array $roleIds): User|null
    {
        $user = $this->getById($id);

        $user->roles()->attach($roleIds);

        return $user->load('roles');
    }

    public function removeRoles(int $id, array $roleIds): User|null
    {
        $user = $this->getById($id);

        $user->roles()->detach($roleIds);

        return $user->load('roles');
    }
}