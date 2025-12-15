<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function getLatestLocations(Request $request)
    {
        $activeOnly = $request->boolean('active_only', false);
        $employeeId = $request->input('employee_id');

        $query = Employee::with(['latestLocation'])
            ->where('is_active', true);

        if ($employeeId) {
            $query->where('id', $employeeId);
        }

        $employees = $query->get();

        $data = $employees->map(function ($employee) use ($activeOnly) {
            $location = $employee->latestLocation;

            if (!$location) {
                return null;
            }

            // Skip locations with empty/invalid coordinates
            if (empty($location->lat) || empty($location->lng)) {
                return null;
            }

            $isOnline = $employee->isOnline();

            if ($activeOnly && !$isOnline) {
                return null;
            }

            return [
                'employee_id' => $employee->id,
                'name' => $employee->name,
                'email' => $employee->email,
                'lat' => (float) $location->lat,
                'lng' => (float) $location->lng,
                'accuracy' => $location->accuracy ? (float) $location->accuracy : null,
                'battery' => $location->battery,
                'recorded_at' => $location->recorded_at->toISOString(),
                'received_at' => $location->received_at->toISOString(),
                'is_online' => $isOnline,
                'last_seen' => $employee->last_seen_at ? $employee->last_seen_at->diffForHumans() : null,
            ];
        })->filter()->values();

        return response()->json([
            'success' => true,
            'data' => $data,
            'count' => $data->count(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    public function getEmployeeHistory(Request $request, $employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        $hours = $request->input('hours', 24);

        $locations = EmployeeLocation::where('employee_id', $employeeId)
            ->where('recorded_at', '>=', now()->subHours($hours))
            ->orderBy('recorded_at', 'desc')
            ->get()
            ->map(function ($location) {
                return [
                    'lat' => (float) $location->lat,
                    'lng' => (float) $location->lng,
                    'accuracy' => $location->accuracy ? (float) $location->accuracy : null,
                    'battery' => $location->battery,
                    'recorded_at' => $location->recorded_at->toISOString(),
                ];
            });

        return response()->json([
            'success' => true,
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->name,
            ],
            'data' => $locations,
        ]);
    }

    public function getStats()
    {
        $onlineThreshold = now()->subMinutes(config('tracking.online_threshold_minutes', 10));

        $totalEmployees = Employee::where('is_active', true)->count();
        $onlineEmployees = Employee::where('is_active', true)
            ->where('last_seen_at', '>=', $onlineThreshold)
            ->count();

        $totalLocations = EmployeeLocation::count();
        $locationsToday = EmployeeLocation::whereDate('recorded_at', today())->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_employees' => $totalEmployees,
                'online_employees' => $onlineEmployees,
                'offline_employees' => $totalEmployees - $onlineEmployees,
                'total_locations' => $totalLocations,
                'locations_today' => $locationsToday,
            ],
        ]);
    }
}
