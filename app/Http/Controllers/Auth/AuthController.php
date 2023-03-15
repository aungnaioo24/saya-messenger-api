<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LogoutRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Repositories\Auth\AuthRepository;

/**
 * @group  User - Authentication
 * APIs for User Authentication Process
 */
class AuthController extends Controller
{
    private $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    /**
     * Register
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $user = $this->authRepository->create($data);
        $user = $this->authRepository->login($user);

        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * Login
     */
    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $user = $this->authRepository->getUser($data);

        if (!$user) {
            return response()->json([
                'message' => 'Wrong Password'
            ], 400);
        }

        $user = $this->authRepository->login($user);
        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * Logout
     *
     * @authenticated
     */
    public function logout(LogoutRequest $request)
    {
        $request->validated();

        $user = auth()->user();
        $this->authRepository->logout($user);

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }
}
