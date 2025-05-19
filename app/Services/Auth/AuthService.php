<?php

namespace App\Services\Auth;

use App\Repositories\Auth\AuthRepository;
use Illuminate\Support\Facades\Auth;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected $authRepo;

    public function __construct(AuthRepository $authRepo)
    {
        $this->authRepo = $authRepo;
    }

    public function register(array $data)
    {
        $user = $this->authRepo->createUser($data);
        $token = JWTAuth::fromUser($user);

        return ['token' => $token];
    }

    public function login(array $credentials)
    {
        if (!$token = Auth::attempt($credentials)) {
            throw new Exception('Invalid email or password');
        }

        return ['token' => $token];
    }
}
