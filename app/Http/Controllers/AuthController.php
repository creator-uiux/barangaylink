<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $request->email;
        $password = $request->password;

        // Check admin credentials
        if ($email === config('barangaylink.admin_email') && $password === config('barangaylink.admin_password')) {
            $user = [
                'id' => 0,
                'email' => config('barangaylink.admin_email'),
                'name' => 'Admin User',
                'firstName' => 'Admin',
                'lastName' => 'User',
                'role' => 'admin'
            ];

            // Log admin login
            ActivityLog::create([
                'user_id' => 0,
                'action' => 'admin_login',
                'description' => 'Admin logged in',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => $user,
                'redirect' => 'admin-dashboard'
            ]);
        }

        // Check regular user
        $user = User::where('email', $email)->where('is_active', true)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        // Log user login
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'entity_type' => 'user',
            'entity_id' => $user->id,
            'description' => 'User logged in',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $userData = [
            'id' => $user->id,
            'email' => $user->email,
            'firstName' => $user->first_name,
            'middleName' => $user->middle_name,
            'lastName' => $user->last_name,
            'name' => $user->name,
            'role' => $user->role,
            'address' => $user->address,
            'phone' => $user->phone
        ];

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $userData,
            'redirect' => 'dashboard'
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:100',
            'middleName' => 'nullable|string|max:100',
            'lastName' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'first_name' => $request->firstName,
            'middle_name' => $request->middleName,
            'last_name' => $request->lastName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'phone' => $request->phone,
            'role' => 'resident',
            'is_active' => true,
        ]);

        // Log registration
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'register',
            'entity_type' => 'user',
            'entity_id' => $user->id,
            'description' => 'New user registered',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $userData = [
            'id' => $user->id,
            'email' => $user->email,
            'firstName' => $user->first_name,
            'middleName' => $user->middle_name,
            'lastName' => $user->last_name,
            'name' => $user->name,
            'role' => $user->role,
            'address' => $user->address,
            'phone' => $user->phone
        ];

        return response()->json([
            'success' => true,
            'message' => 'Account created successfully',
            'user' => $userData,
            'redirect' => 'dashboard'
        ], 201);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'logout',
                'entity_type' => 'user',
                'entity_id' => $user->id,
                'description' => 'User logged out',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        // For API, we don't need to actually logout since tokens are stateless
        // The frontend should remove the token

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // In a real application, you would send an actual password reset email
        // For now, we'll just return success
        return response()->json([
            'success' => true,
            'message' => 'Password reset link sent to your email'
        ]);
    }
}
