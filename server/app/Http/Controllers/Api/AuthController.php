<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Log incoming request for debugging
        \Log::info('Login attempt', [
            'email' => $request->email,
            'password_length' => strlen($request->password ?? ''),
            'device_name' => $request->device_name,
        ]);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            \Log::error('Login validation failed', [
                'errors' => $validator->errors()->toArray(),
                'request' => $request->all(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        // Try to authenticate
        if (!$token = auth('api')->attempt($credentials)) {
            // Check if employee exists with this email
            $employee = Employee::where('email', $request->email)->first();
            
            if (!$employee) {
                // Auto-register for device ID based login (demo purposes)
                $employee = $this->autoRegisterEmployee($request);
                $token = auth('api')->login($employee);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                ], 401);
            }
        }

        $employee = auth('api')->user();

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'refresh_token' => $this->createRefreshToken($employee),
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->name,
                'email' => $employee->email,
                'is_active' => $employee->is_active,
            ],
        ]);
    }

    private function autoRegisterEmployee(Request $request)
    {
        // Extract device ID from email (format: deviceid@device.com)
        $deviceId = str_replace('@device.com', '', $request->email);
        
        // Use device name if provided, otherwise generate from device ID
        $deviceName = $request->input('device_name');
        $employeeName = $deviceName ?: 'Device ' . substr($deviceId, 0, 8);

        return Employee::create([
            'name' => $employeeName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'device_id' => $deviceId,
            'is_active' => true,
        ]);
    }

    public function refresh(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // In production, implement proper refresh token validation
            // For now, generate a new token for the current user
            $token = auth('api')->refresh();

            return response()->json([
                'success' => true,
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed',
            ], 401);
        }
    }

    public function me()
    {
        $employee = auth('api')->user();

        return response()->json([
            'success' => true,
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->name,
                'email' => $employee->email,
                'is_active' => $employee->is_active,
                'last_seen_at' => $employee->last_seen_at,
            ],
        ]);
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out',
        ]);
    }

    private function createRefreshToken($employee)
    {
        // Generate a simple refresh token (in production, use proper implementation)
        return base64_encode($employee->id . '|' . time() . '|' . str()->random(32));
    }
}
