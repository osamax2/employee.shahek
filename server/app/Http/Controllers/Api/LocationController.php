<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployeeLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
            'speed' => 'nullable|numeric|min:0',
            'heading' => 'nullable|numeric|between:0,360',
            'battery' => 'nullable|integer|between:0,100',
            'timestamp' => 'required|date',
            'device_os' => 'nullable|string|max:50',
            'device_version' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $employee = auth('api')->user();

        // Create location record
        $location = EmployeeLocation::create([
            'employee_id' => $employee->id,
            'latitude' => $request->lat,
            'longitude' => $request->lng,
            'accuracy' => $request->accuracy,
            'speed' => $request->speed,
            'heading' => $request->heading,
            'battery_level' => $request->battery,
            'recorded_at' => $request->timestamp,
        ]);

        // Update employee's last seen timestamp
        $employee->update([
            'last_seen_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location recorded successfully',
            'data' => [
                'id' => $location->id,
                'recorded_at' => $location->recorded_at,
            ],
        ], 201);
    }

    public function index(Request $request)
    {
        $employee = auth('api')->user();

        $locations = EmployeeLocation::where('employee_id', $employee->id)
            ->orderBy('recorded_at', 'desc')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $locations,
        ]);
    }
}
