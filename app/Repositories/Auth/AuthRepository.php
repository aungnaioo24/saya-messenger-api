<?php

namespace App\Repositories\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthRepository
{
    public function getUser($data)
    {
        $user = User::where('email', $data['email'])
                    ->first();

        if ($user && Hash::check($data['password'], $user->password)) {
            return $user;
        }

        return null;
    }

    public function create($data)
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function login($user)
    {
        $token = $user->createToken('user')->plainTextToken;
        $user->sessionId = $token;
        return $user;
    }

    public function logout($user)
    {
        return $user->currentAccessToken()->delete();
    }
}
