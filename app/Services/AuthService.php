<?php

namespace App\Services;

use App\ApiCode;
use App\Http\Resources\Users\UserResource;
use App\Interfaces\AuthServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService implements AuthServiceInterface
{
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $token = JWTAuth::fromUser($user);

            $user->token = $token;

            return ['data' => new UserResource($user), 'message' => 'User registered successfully', 'statusCode' => ApiCode::CREATED];
        });
    }

    public function login(array $credentials): array
    {
        if (!$token = JWTAuth::attempt($credentials)) {
            return ['data' => null, 'message' => 'Invalid credentials', 'statusCode' => ApiCode::UNAUTHORIZED];
        }

        $user = JWTAuth::user();

        $user->token = $token;

        return ['data' => new UserResource($user), 'message' => 'Logged In Successfully', 'statusCode' => ApiCode::SUCCESS];
    }

    public function refresh(): array
    {
        try {
            // Parse token from request (works even if expired, as long as within refresh_ttl)
            // JWTAuth::parseToken() will get token from Authorization header
            $token = JWTAuth::parseToken()->getToken();
            
            if (!$token) {
                return [
                    'data' => null,
                    'message' => 'Token not provided',
                    'statusCode' => ApiCode::UNAUTHORIZED
                ];
            }

            // Refresh the token (works even if expired, as long as within refresh_ttl window)
            $newToken = JWTAuth::refresh($token);
            
            // Get user from the new token
            $user = JWTAuth::setToken($newToken)->authenticate();

            if (!$user) {
                return [
                    'data' => null,
                    'message' => 'User not found',
                    'statusCode' => ApiCode::UNAUTHORIZED
                ];
            }

            $user->token = $newToken;

            return [
                'data' => new UserResource($user),
                'message' => 'Token refreshed successfully',
                'statusCode' => ApiCode::SUCCESS
            ];
        } catch (\Tymon\JWTAuth\Exceptions\TokenBlacklistedException $e) {
            return [
                'data' => null,
                'message' => 'Token has been blacklisted',
                'statusCode' => ApiCode::UNAUTHORIZED
            ];
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return [
                'data' => null,
                'message' => 'Token refresh window has expired. Please login again',
                'statusCode' => ApiCode::UNAUTHORIZED
            ];
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return [
                'data' => null,
                'message' => 'Invalid token provided',
                'statusCode' => ApiCode::UNAUTHORIZED
            ];
        } catch (\Exception $e) {
            return [
                'data' => null,
                'message' => 'Could not refresh token',
                'statusCode' => ApiCode::UNAUTHORIZED
            ];
        }
    }

    public function me(User $user): array
    {
        return [
            'data' => new UserResource($user),
            'message' => 'User retrieved successfully',
            'statusCode' => ApiCode::SUCCESS
        ];
    }

    public function updateProfile(User $user, array $data): array
    {
        return DB::transaction(function () use ($user, $data) {
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

            if (!empty($updateData)) {
                $user->update($updateData);
                $user->refresh();
            }

            return [
                'data' => new UserResource($user),
                'message' => 'Profile updated successfully',
                'statusCode' => ApiCode::SUCCESS
            ];
        });
    }

    public function deleteAccount(User $user): array
    {
        return DB::transaction(function () use ($user) {
            $user->delete();

            return [
                'data' => null,
                'message' => 'Account deleted successfully',
                'statusCode' => ApiCode::SUCCESS
            ];
        });
    }
}
