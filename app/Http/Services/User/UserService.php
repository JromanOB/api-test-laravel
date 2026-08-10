<?php

namespace App\Http\Services\User;

use \Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserService
{
    public function getAll(): LengthAwarePaginator
    {
        $query = User::query()->orderBy('id', 'asc')->with('roles');

        return $query->paginate(User::PAGINATE);
    }

    public function findByUsername(string $username): User
    {
        $user = User::where('username', '=', $username)->first();

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
        $user = User::find($id);

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
        $roleId = $data['role_id'];

        unset($data['role_id']);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user = User::create($data);

        $user->roles()->attach($roleId);

        return $user->load('roles');
    }

    public function update(array $data, int $id): User|null
    {
        $user = $this->getById($id);

        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]);
            }
        }

        $user->fill($data);
        $user->save();

        return $user;
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