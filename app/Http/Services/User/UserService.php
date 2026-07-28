<?php

namespace App\Http\Services\User;

use \Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserService
{
    public function getAll(): LengthAwarePaginator
    {
        $query = User::query()->orderBy('id', 'asc');

        return $query->paginate(User::PAGINATE);
    }

    public function getById(int $id): User|null
    {
        $user = User::find($id);

        if (! $user) {
            throw new NotFoundHttpException('User not found!');
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
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return User::create($data);
    }

    public function update(array $data, int $id): User|null
    {
        $user = $this->getById($id);

        foreach ($data as $key => $value) {
            if ($value === null) {
                unset($data[$key]);
            }
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->fill($data);
        $user->save();

        return $user;
    }

    public function delete(int $id): JsonResponse
    {
        $user = $this->getById($id);

        $user->delete();

        return response()->json(['message' => 'User deleted successfully.'], 200);
    }
}