<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponseTrait;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;

class AuthController extends Controller
{
    protected $authService;
    use ApiResponseTrait;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return $this->fail('Validation failed', 422, $validator->errors());
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = JWTAuth::fromUser($user);

            return $this->success([
                'user' => $user,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ], 'User registered successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Registration failed: ' . $e->getMessage());
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return $this->fail('Validation failed', 422, $validator->errors());
        }

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->fail('Invalid email or password', 401);
            }

            return $this->success([
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ], 'Login successful');
        } catch (\Exception $e) {
            return $this->error('Something went wrong during login');
        }
    }

     public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return $this->success(null, 'Logged out successfully');
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->error('Failed to logout, token invalid or expired', 500);
        }
    }
}
