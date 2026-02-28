<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * User Service — Authentication & Profile API
 *
 * Microservice: user-service (port 9003)
 * Database:     user_db (mysql-user:3312)
 * Patterns:     Stateless Auth, Password Hashing, Input Validation
 */
class UserServiceController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone'    => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $id = DB::table('users')->insertGetId([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'phone'      => $request->phone ?? '',
            'address'    => $request->address ?? '',
            'active'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info('[USER] Registered', ['user_id' => $id, 'email' => $request->email]);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'    => $id,
                'name'  => $request->name,
                'email' => $request->email,
            ],
            'message' => 'User registered successfully',
        ], 201)->header('X-Service', 'user-service');
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            Log::warning('[USER] Login failed', ['email' => $request->email]);

            return response()->json([
                'success' => false,
                'error'   => 'Invalid email or password',
            ], 401);
        }

        Log::info('[USER] Login success', ['user_id' => $user->id]);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'token'   => 'valid-user-token',
            'message' => 'Login successful',
        ])->header('X-Service', 'user-service');
    }

    public function show(int $id): JsonResponse
    {
        $user = DB::table('users')
            ->select(['id', 'name', 'email', 'phone', 'address'])
            ->where('id', $id)
            ->first();

        if (!$user) {
            return response()->json(['success' => false, 'error' => 'User not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $user,
        ])->header('X-Service', 'user-service');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = DB::table('users')->where('id', $id)->first();

        if (!$user) {
            return response()->json(['success' => false, 'error' => 'User not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'    => 'sometimes|string|max:255',
            'phone'   => 'sometimes|string',
            'address' => 'sometimes|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        DB::table('users')->where('id', $id)->update(
            array_merge(
                $request->only(['name', 'phone', 'address']),
                ['updated_at' => now()]
            )
        );

        Log::info('[USER] Profile updated', ['user_id' => $id]);

        $updated = DB::table('users')
            ->select(['id', 'name', 'email', 'phone', 'address'])
            ->where('id', $id)
            ->first();

        return response()->json([
            'success' => true,
            'data'    => $updated,
            'message' => 'Profile updated',
        ])->header('X-Service', 'user-service');
    }

    public function index(Request $request): JsonResponse
    {
        $users = DB::table('users')
            ->select(['id', 'name', 'email', 'phone', 'created_at'])
            ->orderBy('id', 'DESC')
            ->paginate(20);

        return response()->json($users)
            ->header('X-Service', 'user-service');
    }
}
