<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'fail', 'errors' => $validator->errors()], 422);
            }

            $data = $this->authService->register($request->all());

            return response()->json([
                'status' => 'success',
                'token' => $data['token']
            ]);
        } catch (Exception $e) {
            \Log::error('Register Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Registration failed'], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'fail', 'errors' => $validator->errors()], 422);
            }

            $data = $this->authService->login($request->only('email', 'password'));

            return response()->json([
                'status' => 'success',
                'token' => $data['token']
            ]);
        } catch (Exception $e) {
            \Log::error('Login Error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Login failed'], 401);
        }
    }
}
