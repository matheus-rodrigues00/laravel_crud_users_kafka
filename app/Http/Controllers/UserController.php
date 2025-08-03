<?php

namespace App\Http\Controllers;

use App\Events\UserCreated;
use App\Models\User;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    use ApiResponse;

    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(): JsonResponse
    {
        try {
            $users = $this->userService->getAllUsers();
            return $this->successResponse($users, 'Users retrieved successfully');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve users', config('app.debug') ? $e->getMessage() : 'An error occurred while fetching users');
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser($request->all());
            
            event(new UserCreated($user));
            
            return $this->successResponse($user, 'User created successfully', 201);
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to create user', config('app.debug') ? $e->getMessage() : 'An error occurred while creating the user');
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->userService->getUserById($id);
            return $this->successResponse($user, 'User retrieved successfully');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('User not found');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to retrieve user', config('app.debug') ? $e->getMessage() : 'An error occurred while fetching the user');
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $user = $this->userService->updateUser($id, $request->all());
            return $this->successResponse($user, 'User updated successfully');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('User not found');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse('No data provided for update', 400, $e->getMessage());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to update user', config('app.debug') ? $e->getMessage() : 'An error occurred while updating the user');
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->userService->deleteUser($id);
            return $this->successResponse(null, 'User deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->notFoundResponse('User not found');
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Failed to delete user', config('app.debug') ? $e->getMessage() : 'An error occurred while deleting the user');
        }
    }
} 