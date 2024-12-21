<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\AuthRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            $user = $this->authRepository->register($data);
            return response()->json([
                'message' => 'Registration successful.',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        try {
            $token = $this->authRepository->login($credentials);
            return response()->json([
                'message' => 'Login successful.',
                'token' => $token,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Login failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred during login.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout()
    {
        try {
            $this->authRepository->logout();
            return response()->json([
                'message' => 'Logout successful.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Logout failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
