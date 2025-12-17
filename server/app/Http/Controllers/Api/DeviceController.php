<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceController extends Controller
{
    /**
     * Register a new device
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'device_name' => 'nullable|string',
            'device_model' => 'nullable|string',
            'device_manufacturer' => 'nullable|string',
            'os_name' => 'nullable|string',
            'os_version' => 'nullable|string',
            'app_version' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Get current authenticated employee
            $employee = auth('api')->user();

            // Check if device already exists
            $device = Device::where('device_id', $request->device_id)->first();

            if ($device) {
                // Update existing device
                $device->update([
                    'employee_id' => $employee ? $employee->id : null,
                    'device_name' => $request->device_name,
                    'device_model' => $request->device_model,
                    'device_manufacturer' => $request->device_manufacturer,
                    'os_name' => $request->os_name,
                    'os_version' => $request->os_version,
                    'app_version' => $request->app_version,
                    'is_active' => true,
                    'last_seen_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Device updated successfully',
                    'device' => $device,
                ]);
            }

            // Create new device
            $device = Device::create([
                'employee_id' => $employee ? $employee->id : null,
                'device_id' => $request->device_id,
                'device_name' => $request->device_name,
                'device_model' => $request->device_model,
                'device_manufacturer' => $request->device_manufacturer,
                'os_name' => $request->os_name,
                'os_version' => $request->os_version,
                'app_version' => $request->app_version,
                'is_active' => true,
                'last_seen_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Device registered successfully',
                'device' => $device,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register device',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get device information
     */
    public function show(Request $request)
    {
        try {
            $employee = auth('api')->user();
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $devices = Device::where('employee_id', $employee->id)
                ->orderBy('last_seen_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'devices' => $devices,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve devices',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Heartbeat to update device status
     * This endpoint is called periodically by the mobile app to indicate it's still active
     * When the app is uninstalled/closed, heartbeats stop and the device will be marked offline
     */
    public function heartbeat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $device = Device::where('device_id', $request->device_id)->first();

            if (!$device) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found',
                ], 404);
            }

            // Update device last_seen_at
            $device->update([
                'last_seen_at' => now(),
                'is_active' => true,
            ]);

            // Also update employee's last_seen_at if device is linked to an employee
            if ($device->employee_id) {
                $employee = Employee::find($device->employee_id);
                if ($employee) {
                    $employee->update([
                        'last_seen_at' => now(),
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Device heartbeat recorded',
                'timestamp' => now()->toISOString(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update device status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
