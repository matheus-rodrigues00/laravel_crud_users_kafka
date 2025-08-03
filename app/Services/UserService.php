<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function getAllUsers(): Collection
    {
        return User::all();
    }   

    public function getUserById(int $id): User
    {
        $user = User::find($id);
        
        if (!$user) {
            throw new ModelNotFoundException("User with ID {$id} not found");
        }
        
        return $user;
    }

    public function createUser(array $data): User
    {
        $this->validateUserData($data);
        
        if (User::where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages([
                'email' => ['The email has already been taken.']
            ]);
        }
        
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function updateUser(int $id, array $data): User
    {
        $user = $this->getUserById($id);
        
        $this->validateUpdateData($data, $user->id);
        
        $updateData = [];
        
        if (isset($data['name'])) {
            $updateData['name'] = $data['name'];
        }
        
        if (isset($data['email'])) {
            $updateData['email'] = $data['email'];
        }
        
        if (isset($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }
        
        if (empty($updateData)) {
            throw new \InvalidArgumentException('No data provided for update');
        }
        
        $user->update($updateData);
        
        return $user->fresh();
    }

    public function deleteUser(int $id): bool
    {
        $user = $this->getUserById($id);
        return $user->delete();
    }

    private function validateUserData(array $data): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ];
        
        $validator = \Validator::make($data, $rules);
        
        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }
    }

    private function validateUpdateData(array $data, int $userId): void
    {
        $rules = [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $userId,
            'password' => 'sometimes|required|string|min:6',
        ];
        
        $validator = \Validator::make($data, $rules);
        
        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }
    }

    public function getUserCount(): int
    {
        return User::count();
    }

    public function userExists(int $id): bool
    {
        return User::where('id', $id)->exists();
    }
} 